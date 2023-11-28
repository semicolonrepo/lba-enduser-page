<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;

class CampaignController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

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

            $viewTemplate = $campaignData->page_template_code . '.index';
            return view($viewTemplate, $sentData);
        }

        return view('welcome_custom', ['message' => 'Campaign not found.']);
    }
}
