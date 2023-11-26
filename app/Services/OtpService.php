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

    public function sendOtp($otpTypeId, $phoneNumber) {
        $otpType = $this->getOtpType($otpTypeId);

        $otpCode = random_int(10000, 99999);
        $requestBody = array(
            'userkey' => $otpType->user_key,
            'passkey' => $otpType->pass_key,
            'to' => $phoneNumber,
            'message' => "Untuk dapat lanjut menggunakan Voucher, harap masukan: $otpCode"
        );

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $otpType->url);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $requestBody);
        $response = curl_exec($curlHandle);
        $results = json_decode($response, true);
        curl_close($curlHandle);

        if ($results['status'] == 1) {
            $this->storeOtp($otpType->third_party_code, $otpType->url, json_encode($requestBody), $response, $otpCode, $otpTypeId, $phoneNumber);

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
            ]);

            $authWAUuid = DB::table('auth_wa')->where('id', $authWAId)->value('uuid');
            Session::put('customer_user_wa', $authWAUuid, 60);

            return true;
        }

        return false;
    }
}
