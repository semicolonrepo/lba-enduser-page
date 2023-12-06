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
                    'brands.id as brand_id',
                    'brands.photo as brand_logo',
                    'campaigns.template_primary_color',
                    'campaigns.template_secondary_color',
                    'campaigns.template_header_json',
                    'campaigns.template_body_json',
                    'campaigns.template_footer_json',
                    'campaigns.template_background',
                    'campaigns.template_cover_json',
                    'campaigns.template_thankyou_json',
                    'page_templates.code as page_template_code',
                )
        ->join('page_templates', 'page_templates.id', '=', 'campaigns.page_template_id')
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('brands.name', $brand)
        ->where('campaigns.slug', $campaignSlug)
        ->where('campaigns.is_active', true)
        ->where('campaigns.is_publish', true)
        ->where('campaigns.expires_at', '>', date('Y-m-d H:i:s'))
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
            'page_templates.code as page_template_code',
        )
        ->join('page_templates', 'page_templates.id', '=', 'campaigns.page_template_id')
        ->join('brands', 'campaigns.brand_id', '=', 'brands.id')
        ->where('campaigns.template_token', $token)
        ->first();

        return $campaignData;
    }

    public function getProducts($campaignId) {
        $products = CampaignProductsModel::select('products.*', 'deal_offers.name as type', 'campaign_products.normal_price', 'campaign_products.subsidi_price')
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
