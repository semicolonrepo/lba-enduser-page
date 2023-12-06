<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Services\CampaignService;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

    public function cover($brand, $campaign) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if($campaignData) {
            $productData = $this->campaignService->getProducts($campaignData->id);

            $sentData = [
                'is_preview' => false,
                'data' => $campaignData,
                'product' => $productData,
            ];

            $cover = json_decode($campaignData->template_cover_json, true);
            $containsCarousel = in_array('carousel', array_column($cover['blocks'], 'type') ?? []);
            if($containsCarousel && count($cover['blocks'][0]['data']['items']) > 0 ) {
                //show cover page
                $viewTemplate = $campaignData->page_template_code . '.cover';
                return view($viewTemplate, $sentData);
            }else {
                //redirect to index
                return redirect()->route('index', [
                    'brand' => Str::slug($campaignData->brand),
                    'campaign' => $campaignData->slug,
                ]);
            }
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }

    public function index($brand, $campaign) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if($campaignData) {
            $productData = $this->campaignService->getProducts($campaignData->id);

            $sentData = [
                'is_preview' => false,
                'data' => $campaignData,
                'product' => $productData,
            ];

            $viewTemplate = $campaignData->page_template_code . '.index';
            return view($viewTemplate, $sentData);
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }

    public function preview($token) {
        $campaignData = $this->campaignService->getCampaignByToken($token);

        if($campaignData) {
            $productData = $this->campaignService->getProducts($campaignData->id);

            $sentData = [
                'is_preview' => true,
                'data' => $campaignData,
                'product' => $productData,
            ];

            $cover = json_decode($campaignData->template_cover_json, true);
            $containsCarousel = in_array('carousel', array_column($cover['blocks'], 'type') ?? []);
            if($containsCarousel && count($cover['blocks'][0]['data']['items']) > 0 ) {
                //redirect to coverpage
                $viewTemplate = $campaignData->page_template_code . '.cover';
            }else {
                //redirect to homepage
                $viewTemplate = $campaignData->page_template_code . '.index';
            }

            return view($viewTemplate, $sentData);
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }
}
