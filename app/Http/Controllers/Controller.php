<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignProductsModel;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        $campaignData = CampaignModel::select(
                        'campaigns.id',
                        'campaigns.name as campaign',
                        'campaigns.description as campaign_detail',
                        'campaigns.page_template_id',
                        'brands.name as brand',
                        'brands.photo as brand_logo',
                        'template_primary_color',
                        'template_secondary_color',
                        'template_header_json',
                        'template_body_json',
                        'template_footer_json',
                    )
            ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
            ->where('brands.name', $request->segment(1))
            ->where('campaigns.slug', $request->segment(2))
        ->first();

        if($campaignData != null) {
            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('campaign_id', $campaignData->id)
            ->get();

            switch($campaignData->page_template_id) {
                case 1: //template 1
                    return view('lba-1.index', [
                        'data' => $campaignData,
                        'product' => $productData
                    ]);
                default:
                    return view('welcome_custom');
            }
        }
        else {
            return view('welcome_custom');
        }
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
