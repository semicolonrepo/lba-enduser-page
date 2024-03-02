<?php

namespace App\Http\Middleware;

use App\Services\VoucherService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifyVoucherPermissionMiddleware
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

        $authWA = DB::table('auth_wa')
        ->where('uuid', session('customer_user_wa'))->first();
        $authGmail = DB::table('auth_gmail')
        ->where('uuid', session('customer_user_gmail'))->first();

        if ($authGmail && $authGmail->email == $voucher->email) {
            return $next($request);
        }

        if ($authWA && $authWA->phone_number == $voucher->phone_number) {
            return $next($request);
        }

        return redirect()->route('index', [
            'brand' => $brand,
            'campaign' => $campaign,
        ]);
    }
}
