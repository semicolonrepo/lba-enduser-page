<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Services\CampaignService;
use App\Services\RatingService;
use App\Services\VoucherService;

class RatingController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
        private RatingService $ratingService,
    ) {}

    public function show($brandSlug, $campaignSlug, $voucherCode) {
        try {
            $campaign = $this->campaignService->getCampaign($brandSlug, $campaignSlug);
            $voucher = $this->voucherService->showVoucher($voucherCode);

            $viewTemplate = $campaign->page_template_code . '.rating';
            return view($viewTemplate, [
                'brandSlug' => $brandSlug,
                'campaignSlug' => $campaignSlug,
                'voucher' => $voucher,
                'data' => $campaign,
            ]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function store(StoreRatingRequest $request, $brand, $campaign, $voucherCode) {
        try {
            $this->ratingService->store($brand, $campaign, $voucherCode, $request->all());

            return redirect()->back()->with('success', 'Berhasil memberikan rating!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('failed', 'Terjadi kesalahan');
        }
    }
}
