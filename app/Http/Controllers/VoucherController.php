<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherClaimService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
        private VoucherClaimService $voucherClaimService,
    ) {}

    function claim($brand, $campaign, $productId) {
        try {
            $campaignData = $this->campaignService->getCampaign($brand, $campaign);
            $voucher = $this->voucherClaimService->run($campaignData->id, $productId);
            $utmSource = request()->query('utm_source');

            $arrayRoute = [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ];

            if ($voucher) {
                $arrayRoute['voucherCode'] = $voucher->code;
            }

            if ($utmSource) {
                $arrayRoute['utm_source'] = $utmSource;
            }

            if (!$voucher) {
                return redirect()->route('product::show', $arrayRoute)->with('failed', 'Voucher sudah diclaim atau habis!');
            }

            return redirect()->route('voucher::show', $arrayRoute);
        }
        catch (\Throwable $th) {
            $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($th->getMessage(), true));
            Log::error($error);
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function show($brand, $campaign, $productId, $voucherCode) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);
        $voucher = $this->voucherService->showVoucher($voucherCode);

        if ($voucherCode && $campaignData) {
            $viewTemplate = $campaignData->page_template_code . '.voucher_redeem';
            return view($viewTemplate, [
                'voucher' => $voucher,
                'data' => $campaignData,
                'brand' => $brand,
                'campaign' => $campaign,
                'voucherCode' => $voucherCode,
            ]);
        }

        return redirect()->back();
    }
}
