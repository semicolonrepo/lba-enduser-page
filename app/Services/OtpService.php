<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OtpService
{
    public function __construct() {
        //
    }

    public function getOtpTypes() {
        $thirdParty = DB::table('one_time_password_types')
            ->where('is_active', true)
            ->select('id','name')
            ->get();

        return $thirdParty;
    }

    public function getOtpType($otpTypeId) {
        $thirdParty = DB::table('one_time_password_types')
            ->where('id', $otpTypeId)
            ->where('is_active', true)
            ->select('id', 'name', 'user_key', 'pass_key', 'url', 'third_party_code')
            ->first();

        return $thirdParty;
    }

    public function getOtpTypeByCode($otpTypeCode) {
        $thirdParty = DB::table('one_time_password_types')
            ->where('third_party_code', $otpTypeCode)
            ->where('is_active', true)
            ->select('id', 'name', 'user_key', 'pass_key', 'url', 'third_party_code')
            ->first();

        return $thirdParty;
    }

    function storeOtp($thirdPartyCode, $urlRequest, $requestBody, $responseBody, $otpCode, $otpTypeId, $phoneNumber) {
        DB::transaction(function () use ($thirdPartyCode, $urlRequest, $requestBody, $responseBody , $otpCode, $otpTypeId, $phoneNumber){
            $currentTime = date('Y-m-d H:i:s');

            DB::table('third_party_logs')->insert([
                'third_party_code' => $thirdPartyCode,
                'url' => $urlRequest,
                'request_body' => $requestBody,
                'response_body' => $responseBody,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);

            DB::table('one_time_passwords')->insert([
                'code' => $otpCode,
                'one_time_password_type_id' => $otpTypeId,
                'phone_number' => $phoneNumber,
                'expires_at' => date('Y-m-d H:i:s', strtotime($currentTime) + 300),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);
        });
    }

    public function sendOtp ($phoneNumber) {
        $currentTime = date('Y-m-d H:i:s');
        $url = 'https://service-chat.qontak.com/oauth/token';
        $ch = curl_init($url);
        $payload = json_encode( [
            "username"=> "firda@letsbuyasia.com",
            "password"=> "A1220fird@!",
            "grant_type"=> "password",
            "client_id"=> "RRrn6uIxalR_QaHFlcKOqbjHMG63elEdPTair9B9YdY",
            "client_secret"=> "Sa8IGIh_HpVK1ZLAF0iFf7jU760osaUNV659pBIZR00"
        ]);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        $auth = curl_exec($ch);
        curl_close($ch);
        $resultAuth = json_decode($auth);

        DB::table('third_party_logs')->insert([
            'third_party_code' => 'QONTAK_AUTH',
            'url' => $url,
            'request_body' => $payload,
            'response_body' => $auth,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ]);

        $otpCode = random_int(10000, 99999);
        $url = 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct';
        $ch = curl_init($url);
        $payload = json_encode([
            "to_number"=> (string) $phoneNumber,
            "to_name"=> (string) $phoneNumber,
            "message_template_id"=> "b6018076-9319-4723-b84d-7591d72e0f78",
            "channel_integration_id"=> "25d03edb-006f-4758-8c16-8e44b9c92ca2",
            "language"=> [
                "code"=> "id"
            ],
            "parameters"=> [
                "body"=> [
                    [
                        "key" => "1",
                        "value"=> "kode",
                        "value_text"=> (string) $otpCode,
                    ]
                ],
                "buttons"=> [
                    [
                        "index"=> "0",
                        "type"=> "url",
                        "value"=> (string) $otpCode
                    ]
                ]
            ]
        ]);

        $authorization = "Authorization: Bearer $resultAuth->access_token";
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        $otp = curl_exec($ch);
        $resultOtp = json_decode($otp);
        curl_close($ch);

        DB::table('third_party_logs')->insert([
            'third_party_code' => 'QONTAK_OTP',
            'url' => $url,
            'request_body' => $payload,
            'response_body' => $otp,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ]);

        if ($resultOtp->status === 'success') {
            DB::table('one_time_passwords')->insert([
                'code' => $otpCode,
                'one_time_password_type_id' => 2,
                'phone_number' => $phoneNumber,
                'expires_at' => date('Y-m-d H:i:s', strtotime($currentTime) + 300),
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]);

            return true;
        }

        return false;
    }

    public function validateOtp($otpTypeId, $phoneNumber, $otpCode) {
        $isOtpValid = DB::table('one_time_passwords')
            ->where('one_time_password_type_id', $otpTypeId)
            ->where('code', $otpCode)
            ->where('phone_number', $phoneNumber)
            ->where('is_used', false)
            ->where('expires_at', '>', date('Y-m-d H:i:s'));

        if ((clone $isOtpValid)->exists()) {
            $otp = (clone $isOtpValid)
            ->select('one_time_passwords.id')
            ->first();

            (clone $isOtpValid)->update([
                'is_used' => true
            ]);

            $authWAId = DB::table('auth_wa')->insertGetId([
                'uuid' => Str::uuid(),
                'one_time_password_id' => $otp->id,
                'phone_number' => $phoneNumber,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $authWAUuid = DB::table('auth_wa')->where('id', $authWAId)->value('uuid');
            Session::put('customer_user_wa', $authWAUuid, 60);

            return true;
        }

        return false;
    }
}
