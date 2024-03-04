<?php

namespace App\Http\Middleware;

use App\Services\CampaignService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCampaignHasRatingMiddleware
{
    public function __construct(
        private CampaignService $campaignService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $brand = $request->route('brand');
        $campaign =  $request->route('campaign');
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);

        if (empty(json_decode($campaignData->formbuilder_rating_json))) {
            return redirect()->route('index', [
                'brand' => $brand,
                'campaign' => $campaign,
            ]);
        }

        return $next($request);
    }
}
