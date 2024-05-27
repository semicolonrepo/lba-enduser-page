<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GoogleRecaptchaService
{
    function verify(?string $token) {
        $secretKey = config('services.google_recaptcha.secret_key');
        $url = config('services.google_recaptcha.verify_url');

        $client = new Client();

        try {
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'secret' => $secretKey,
                    'response' => $token,
                ],
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
