<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller as BaseController;
use App\Models\CampaignModel;
use App\Models\CampaignProductsModel;
use App\Models\VouchersModel;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Request $request)
    {
        $campaignData = $this->getCampaign($request);
        if($campaignData != null) {

            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('campaign_id', $campaignData->id)
            ->get();

            $sentData = [
                'is_preview' => false,
                'data' => $campaignData,
                'product' => $productData,
                'uri' => [
                    'segment1' => $request->segment(1),
                    'segment2' => $request->segment(2),
                ]
            ];

            switch($campaignData->page_template_id) {
                case 1: //template 1
                    return view('lba-1.index', $sentData);
                default:
                    return view('welcome_custom', ['message' => 'Campaign not found.']);
            }
        }
        else {
            return view('welcome_custom', ['message' => 'Campaign not found.']);
        }
    }

    public function preview($token)
    {
        $campaignData = $this->getCampaignForPreview($token);
        if($campaignData != null) {

            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('campaign_id', $campaignData->id)
            ->get();

            $sentData = [
                'is_preview' => true,
                'data' => $campaignData,
                'product' => $productData,
                'uri' => [
                    'segment1' => $campaignData->brand,
                    'segment2' => $campaignData->campaign,
                ]
            ];

            switch($campaignData->page_template_id) {
                case 1: //template 1
                    return view('lba-1.index', $sentData);
                default:
                    return view('welcome_custom', ['message' => 'Campaign not found.']);
            }
        }
        else {
            return view('welcome_custom', ['message' => 'Campaign not found.']);
        }
    }

    public function product(Request $request)
    {
        $campaignData = $this->getCampaign($request);
        if($campaignData != null && $request->has('id')) {

            $productId = $request->query('id');

            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('products.id', $productId)
            ->first();

            if($productData != null) {

                $retailPartner = VouchersModel::select('providers.id', 'providers.name')
                    ->join('providers', 'vouchers.provider_id', '=', 'providers.id')
                    ->where('vouchers.campaign_id', $campaignData->id)
                ->get();

                $sentData = [
                    'data' => $campaignData,
                    'product' => $productData,
                    'retailer' => $retailPartner
                ];

                switch($campaignData->page_template_id) {
                    case 1: //template 1
                        return view('lba-1.product', $sentData);
                    default:
                        return view('welcome_custom', ['message' => 'Campaign not found.']);
                }
            }
            else {
                return view('welcome_custom', ['message' => 'Product not found.']);
            }
        }
        else {
            return view('welcome_custom', ['message' => 'Campaign not found.']);
        }
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

    /** Private Methods */
    private function getCampaign(Request $request) {
        $brand = Str::title(str_replace('-', ' ', $request->segment(1)));
        
        $campaignData = CampaignModel::select(
                    'campaigns.id',
                    'campaigns.name as campaign',
                    'campaigns.description as campaign_detail',
                    'campaigns.page_template_id',
                    'campaigns.slug',
                    'brands.name as brand',
                    'brands.photo as brand_logo',
                    'campaigns.template_primary_color',
                    'campaigns.template_secondary_color',
                    'campaigns.template_header_json',
                    'campaigns.template_body_json',
                    'campaigns.template_footer_json',
                )
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('brands.name', $brand)
        ->where('campaigns.slug', $request->segment(2))
        ->first();

        return $campaignData;
    }

    private function getCampaignForPreview($token) {
        
        $campaignData = CampaignModel::select(
                    'campaigns.id',
                    'campaigns.name as campaign',
                    'campaigns.description as campaign_detail',
                    'campaigns.page_template_id',
                    'campaigns.slug',
                    'brands.name as brand',
                    'brands.photo as brand_logo',
                    'campaigns.template_primary_color',
                    'campaigns.template_secondary_color',
                    'campaigns.template_header_json',
                    'campaigns.template_body_json',
                    'campaigns.template_footer_json',
                )
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('campaigns.template_token', $token)
        ->first();

        return $campaignData;
    }

}
