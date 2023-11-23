<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignProductsModel;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index($brand, $campaign)
    {
        //get data from models
        $campaignData = CampaignModel::select(
                        'campaigns.name as campaign',
                        'brands.name as brand',
                        'brands.photo as brand_logo',
                        'template_primary_color',
                        'template_secondary_color',
                        'template_header_json',
                        'template_body_json',
                        'template_footer_json',
                    )
                    ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
                    ->where('campaigns.id', 2)
                    ->first();

        $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                    ->join('products', 'campaign_products.product_id', '=', 'products.id')
                    ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                    ->where('campaign_id', 2)
                    ->get();

        return view('lba-1.index', [
            'data' => $campaignData,
            'product' => $productData
        ]);
    }

    public function product($brand, $campaign)
    {
        return view('lba-1.product', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function login($brand, $campaign)
    {
        return view('lba-1.auth.login', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function phoneNumber($brand, $campaign)
    {
        return view('lba-1.auth.phone_number', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function otp($brand, $campaign)
    {
        return view('lba-1.auth.otp', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function voucherRedeem($brand, $campaign)
    {
        return view('lba-1.voucher_redeem', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }
}
