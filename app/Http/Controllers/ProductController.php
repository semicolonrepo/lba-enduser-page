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
    ) {}

    public function show($brand, $campaign, $productId) {
        session()->forget('partner_id');
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if($campaignData && $productId) {
            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('products.id', $productId)
                ->first();

            if($productData) {

                $retailPartner = VouchersModel::select('providers.id', 'providers.name')
                    ->join('providers', 'vouchers.provider_id', '=', 'providers.id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                    ->where('providers.is_active', true)
                    ->distinct('providers.name')
                    ->get();

                $retailInternal = VouchersModel::select('providers.id', 'providers.name')
                    ->leftJoin('providers', 'vouchers.provider_id', '=', 'providers.id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                    ->whereNull('vouchers.provider_id')
                    ->get();

                $sentData = [
                    'data' => $campaignData,
                    'product' => $productData,
                    'retailer' => $retailPartner,
                    'internal' => $retailInternal,
                ];

                switch($campaignData->page_template_id) {
                    case 1:
                        return view('lba-1.product', $sentData);
                    case 2:
                        return view('lba-2.product', $sentData);
                    default:
                        return view('welcome_custom', ['message' => 'Campaign not found.']);
                }
            } else {
                return view('welcome_custom', ['message' => 'Product not found.']);
            }
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }
}
