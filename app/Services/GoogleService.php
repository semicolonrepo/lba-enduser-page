<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleService
{
    public function saveSession() {
        $googleUser = Socialite::driver('google')->user();

        if (!Str::endsWith($googleUser->getEmail(), '@gmail.com')) {
            return false;
        }

        $authGmailId = DB::table('auth_gmail')->insertGetId([
            'uuid' => Str::uuid(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $authGmailUuid = DB::table('auth_gmail')->where('id', $authGmailId)->value('uuid');
        Session::put('customer_user_gmail', $authGmailUuid);

        return true;
    }
}
