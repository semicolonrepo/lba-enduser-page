<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            if ($voucher) {
                return redirect()->route('voucher::show',[
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                    'voucherCode' => $voucher->code,
                ]);
            }

            return redirect()->back()->with('failed', 'Voucher sudah diclaim atau habis!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function show($brand, $campaign, $productId, $voucherCode) {
        $campaignData = $this->campaignService-> getCampaign($brand, $campaign);
        $voucher = $this->voucherService->showVoucher($voucherCode);

        if ($voucherCode && $campaignData) {
            switch($campaignData->page_template_id) {
                case 1:
                    return view('lba-1.voucher_redeem', [
                        'voucher' => $voucher,
                        'data' => $campaignData,
                    ]);
                case 2:
                    return view('lba-2.voucher_redeem', [
                        'voucher' => $voucher,
                        'data' => $campaignData,
                    ]);
                default:
                    return view('welcome_custom', ['message' => 'Campaign not found.']);
            }
        }

        return redirect()->back();
    }
}
