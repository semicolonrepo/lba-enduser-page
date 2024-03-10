<?php

namespace App\Http\Controllers;

use App\Models\CampaignProductsModel;
use App\Models\VouchersModel;
use App\Services\CampaignService;
use App\Services\VoucherService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    public function show($brand, $campaign, $productId) {
        $validateAuth = $this->voucherService->validateAuth($brand, $campaign);
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if($campaignData && $productId) {
            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type', 'campaign_products.normal_price', 'campaign_products.subsidi_price', 'campaign_products.questionares_json')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('products.id', $productId)
                ->where('campaign_products.campaign_id', $campaignData->id)
                ->first();

            if($productData) {

                $retailPartner = VouchersModel::select('providers.id', 'providers.name', 'providers.photo')
                    ->join('providers', 'vouchers.provider_id', '=', 'providers.id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                    ->where('providers.is_active', true)
                    ->where('vouchers.is_active', true)
                    ->distinct('providers.name')
                    ->get();

                $retailInternal = VouchersModel::select('providers.id', 'providers.name')
                    ->leftJoin('providers', 'vouchers.provider_id', '=', 'providers.id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                    ->whereNull('vouchers.provider_id')
                    ->get();

                $merchantCities = VouchersModel::select('indonesia_cities.name')
                    ->join('voucher_term_indonesia_cities', 'voucher_term_indonesia_cities.voucher_id', 'vouchers.id')
                    ->join('indonesia_cities', 'indonesia_cities.id', 'voucher_term_indonesia_cities.indonesia_city_id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                    ->distinct()
                    ->get();

                $sentData = [
                    'brand' => $brand,
                    'data' => $campaignData,
                    'product' => $productData,
                    'retailer' => $retailPartner,
                    'internal' => $retailInternal,
                    'merchantCities' => $merchantCities,
                    'authData' => $validateAuth,
                ];

                $viewTemplate = $campaignData->page_template_code . '.product';
                return view($viewTemplate, $sentData);
            } else {
                return view('welcome_custom', ['message' => 'Product not found.']);
            }
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }
}
