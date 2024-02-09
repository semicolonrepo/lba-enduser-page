<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    function claim($brand, $campaign, $productId) {
        try {
            $campaignData = $this->campaignService->getCampaign($brand, $campaign);
            $voucher = $this->voucherService->claimVoucher($campaignData->id, $productId);
            $utmSource = request()->query('utm_source');

            if ($voucher) {

                if($utmSource) {
                    return redirect()->route('voucher::show',[
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                        'voucherCode' => $voucher->code,
                        'utm_source' => $utmSource
                    ]);
                }
                else {
                    return redirect()->route('voucher::show',[
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $productId,
                        'voucherCode' => $voucher->code,
                    ]);
                }
            }

            //return when claim invalid
            if($utmSource) {
                return redirect()->route('product::show',[
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'utm_source' => $utmSource
                ])->with('failed', 'Voucher sudah diclaim atau habis!');
            }
            else {
                return redirect()->route('product::show',[
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ])->with('failed', 'Voucher sudah diclaim atau habis!');
            }
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

        $authWA = DB::table('auth_wa')
        ->where('uuid', session('customer_user_wa'))->first();
        $authGmail = DB::table('auth_gmail')
        ->where('uuid', session('customer_user_gmail'))->first();

        if ($voucherCode && $campaignData && ($authGmail->email == $voucher->email || $authWA->phone_number == $voucher->phone_number)) {
            $viewTemplate = $campaignData->page_template_code . '.voucher_redeem';
            return view($viewTemplate, [
                'voucher' => $voucher,
                'data' => $campaignData,
            ]);
        }

        return redirect()->back();
    }
}
