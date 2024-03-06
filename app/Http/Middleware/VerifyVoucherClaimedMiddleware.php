<?php

namespace App\Http\Middleware;

use App\Services\VoucherService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyVoucherClaimedMiddleware
{

    public function __construct(
        private VoucherService $voucherService,
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
        $voucherCode = $request->route('voucherCode');
        $voucher = $this->voucherService->showVoucher($voucherCode);

        if (!$voucher || !$voucher->claim_date) {
            return redirect()->route('index', [
                'brand' => $brand,
                'campaign' => $campaign,
            ]);
        }

        return $next($request);
    }
}
