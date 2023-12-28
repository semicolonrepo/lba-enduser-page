<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherService;
use Illuminate\Http\Request;

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
            ]);
        }

        return redirect()->back();
    }
}
