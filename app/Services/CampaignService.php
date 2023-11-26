<?php

namespace App\Services;

use App\Models\CampaignAuthSetting;
use App\Models\CampaignModel;
use App\Models\CampaignProductsModel;
use Illuminate\Support\Str;

class CampaignService
{
    public function getCampaign($brandSlug, $campaignSlug) {
        $brand = Str::title(str_replace('-', ' ', $brandSlug));

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
                    'campaigns.template_background',
                )
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('brands.name', $brand)
        ->where('campaigns.slug', $campaignSlug)
        ->first();

        return $campaignData;
    }

    public function getCampaignByToken($token) {
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
            'campaigns.template_background',
        )
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('campaigns.template_token', $token)
        ->first();

        return $campaignData;
    }

    public function getProducts($campaignId) {
        $products = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
        ->join('products', 'campaign_products.product_id', '=', 'products.id')
        ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
        ->where('campaign_id', $campaignId)
        ->get();

        return $products;
    }

    public function getCampaignAuths($campaignId) {
        $campaignAuths = CampaignAuthSetting::join('auth_settings', 'auth_settings.id', 'campaign_auth_settings.auth_setting_id')
            ->where('auth_settings.is_active', true)
            ->where('campaign_auth_settings.campaign_id', $campaignId)
            ->select('auth_settings.id', 'auth_settings.code');

        return $campaignAuths;
    }
}
