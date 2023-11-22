<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index($brand, $campaign)
    {
        return view('lba-1.index', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function product($brand, $campaign)
    {
        return view('lba-1.product', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function login($brand, $campaign)
    {
        return view('lba-1.auth.login', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function phoneNumber($brand, $campaign)
    {
        return view('lba-1.auth.phone_number', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function otp($brand, $campaign)
    {
        return view('lba-1.auth.otp', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }

    public function voucherRedeem($brand, $campaign)
    {
        return view('lba-1.voucher_redeem', [
            'brand' => $brand,
            'campaign' => $campaign
        ]);
    }
}
