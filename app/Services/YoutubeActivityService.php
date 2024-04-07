<?php

namespace App\Services;

use App\Models\CampaignYoutubeActivity;
use Illuminate\Support\Facades\DB;

class YoutubeActivityService
{
    public function __construct(
        private AuthService $authService,
    ) {}

    function store($campaignId, $productId, $linkVideo) {
        $activeSession = $this->authService->getActiveSession();

        CampaignYoutubeActivity::create([
            'campaign_id' => $campaignId,
            'link' => $linkVideo,
            "email" => ($activeSession->auth_gmail) ? $activeSession->auth_gmail->email : null,
            "phone_number" => ($activeSession->auth_wa) ? $activeSession->auth_wa->phone_number : null,
            'product_id' => $productId
        ]);
    }
}
