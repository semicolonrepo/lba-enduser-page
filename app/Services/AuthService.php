<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AuthService
{
    function getActiveSession() {
        $customerGmailSession = session('customer_user_gmail');
        $customerWaSession = session('customer_user_wa');
        $authGmail = null;
        $authWA = null;

        if ($customerGmailSession) {
            $authGmail = DB::table('auth_gmail')
            ->where('uuid', session('customer_user_gmail'))->first();
        }

        if ($customerWaSession) {
            $authWA = DB::table('auth_wa')
            ->where('uuid', session('customer_user_wa'))->first();
        }

        return (object) [
           'auth_gmail' => $authGmail,
           'auth_wa' => $authWA
        ];
    }
}
