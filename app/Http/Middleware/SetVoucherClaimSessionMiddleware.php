<?php

namespace App\Http\Middleware;

use App\Http\Requests\ClaimVoucherRequest;
use App\Services\GoogleRecaptchaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetVoucherClaimSessionMiddleware
{

    public function __construct(
        private GoogleRecaptchaService $googleRecaptchaService,
        private ClaimVoucherRequest $claimVoucherRequest,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        session(['voucher_claim_request_session' => $request->all()]);
        $voucherClaimRequestSession = session('voucher_claim_request_session');

        session(['partner_id' => $voucherClaimRequestSession['partner']]);
        session(['claim_qty' => $voucherClaimRequestSession['claim_qty']]);

        if (isset($voucherClaimRequestSession['g-recaptcha-response'])) {
            session(['g_recaptcha_response' => $voucherClaimRequestSession['g-recaptcha-response']]);
        }

        if(isset($voucherClaimRequestSession['utm_source'])) {
            session(['utm_source_session' => $voucherClaimRequestSession['utm_source']]);
        } else {
            session()->forget('utm_source_session');
        }

        return $next($request);
    }
}
