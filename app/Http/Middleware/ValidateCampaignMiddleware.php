<?php

namespace App\Http\Middleware;

use App\Services\CampaignService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCampaignMiddleware
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
        $campaignData = $this->campaignService->getCampaign($request->route('brand'), $request->route('campaign'));

        if (!$campaignData) {
            return response(view('welcome_custom', ['message' => 'Campaign not found.']));
        }
        return $next($request);
    }
}
