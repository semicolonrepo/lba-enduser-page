<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    public function show($brand, $campaign, $voucherCode) {
        try {
            $campaign = $this->campaignService->getCampaign($brand, $campaign);
            $voucher = $this->voucherService->showVoucher($voucherCode);

            $viewTemplate = $campaign->page_template_code . '.rating';
            return view($viewTemplate, [
                'voucher' => $voucher,
                'data' => $campaign,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }
}
