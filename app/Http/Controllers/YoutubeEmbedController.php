<?php

namespace App\Http\Controllers;

use App\Models\CampaignYoutubeActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class YoutubeEmbedController extends Controller
{
    public function clicked(Request $request, $campaignId) {
        $request->validate([
            'link_video' => 'required|string',
        ]);

        $campaignYoutubeActivity = CampaignYoutubeActivity::select("link", "total_click")
            ->where('campaign_id', $campaignId)
            ->where('link',  $request->input('link_video'))
            ->first();

        if ($campaignYoutubeActivity) {
            CampaignYoutubeActivity::where('link',  $request->input('link_video'))
                ->where('campaign_id', $campaignId)
                ->update([
                    'total_click' => $campaignYoutubeActivity->total_click + 1
                ]);
        } else {
            CampaignYoutubeActivity::create([
                'campaign_id' => $campaignId,
                'link' => $request->input('link_video'),
                'total_click' => 1
            ]);
        }

        return true;
    }
}
