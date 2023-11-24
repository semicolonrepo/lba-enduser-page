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
        $slug = $this->getSlugFromURL($request->url());

        $campaignData = CampaignModel::select(
                        'campaigns.id',
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
            ->where('campaigns.slug', $slug)
        ->first();

        if($campaignData != null) {
            $productData = CampaignProductsModel::select('products.*', 'deal_offers.name as type')
                ->join('products', 'campaign_products.product_id', '=', 'products.id')
                ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
                ->where('campaign_id', $campaignData->id)
            ->get();

            return view('lba-1.index', [
                'data' => $campaignData,
                'product' => $productData
            ]);
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

    /** Private methods */
    private function getSlugFromURL($url) {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));

        $brand = $segments[0];
        $campaign = $segments[1];
        $slugResult = $brand."-".$campaign;

        return $slugResult;
    }

}
