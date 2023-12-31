<?php

namespace App\Http\Middleware;

use App\Services\CampaignService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use hisorange\BrowserDetect\Parser as Browser;

class ActivityLogMiddleware
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

        $authWA = DB::table('auth_wa')
        ->where('uuid', session('customer_user_wa'))->first();
        $authGmail = DB::table('auth_gmail')
        ->where('uuid', session('customer_user_gmail'))->first();
        $campaignData = $this->campaignService->getCampaign($request->route('brand'), $request->route('campaign'));

        $data = [
            'ip_address' => $request->ip(),
            'browser' => $request->header('user-agent'),
            "email" => $authGmail->email ?? null,
            "phone_number" => $authWA->phone_number ?? null,
            'brand_slug' => $request->route('brand') ?? null,
            'campaign_slug' => $request->route('campaign') ?? null,
            'product_id' => $request->route('productId') ?? null,
            'brand_id' => $campaignData->brand_id,
            'campaign_id' => $campaignData->id,
            'full_url' => $request->fullUrl(),
            'device_type' => Browser::deviceType(),
            'platform_name' => Browser::platformName(),
            'device_model' => Browser::deviceModel(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        DB::table('activity_logs')
            ->insert($data);

        return $next($request);
    }
}
