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
            $vouchers = $this->voucherClaimService->run($campaignData->id, $productId);
            $utmSource = request()->query('utm_source');

            $arrayRoute = [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ];

            if ($vouchers) {
                $voucher = $this->voucherService->showVoucher($vouchers->first()->code);
                $arrayRoute['voucherIdentifier'] = $voucher->claim_identifier;
            }

            if ($utmSource) {
                $arrayRoute['utm_source'] = $utmSource;
            }

            if (!$vouchers) {
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

    public function show($brand, $campaign, $productId, $voucherIdentifier) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);
        $vouchers = $this->voucherService->showVoucherByIdentifier($voucherIdentifier);

        if ($vouchers && $campaignData) {
            $viewTemplate = $campaignData->page_template_code . '.voucher_redeem';
            return view($viewTemplate, [
                'vouchers' => $vouchers,
                'data' => $campaignData,
                'brand' => $brand,
                'campaign' => $campaign,
                'voucherIdentifier' => $voucherIdentifier,
            ]);
        }

        return redirect()->back();
    }
}
