<?php

namespace App\Http\Controllers;

use App\Services\YoutubeActivityService;
use Illuminate\Http\Request;

class YoutubeEmbedController extends Controller
{
    public function __construct(
        private YoutubeActivityService $youtubeActivityService,
    ) {}

    public function clicked(Request $request, $campaignId, $productId) {
        $request->validate([
            'link_video' => 'required|string',
        ]);

        $this->youtubeActivityService->store($campaignId, $productId, $request->input('link_video'));

        return true;
    }
}
