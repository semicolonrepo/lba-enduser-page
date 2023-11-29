<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class QontakGatewayChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification)
    {
        $voucher = $notification->toWhatsapp($notifiable);

        if ($voucher->phone_number) {
            $description = strip_tags(html_entity_decode($voucher->description));
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

            $url = 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct';
            $ch = curl_init($url);
            $payload = json_encode([
                "to_number"=> $voucher->phone_number,
                "to_name"=> $voucher->phone_number,
                "message_template_id"=> "352fd5c6-dbcb-40b3-a767-d46765605e63",
                "channel_integration_id"=> "25d03edb-006f-4758-8c16-8e44b9c92ca2",
                "language"=> [
                    "code"=> "id"
                ],
                "parameters"=> [
                    "body"=> [
                        [
                            "key"=> "1",
                            "value"=> "produk",
                            "value_text"=> $voucher->product_name,
                        ],
                        [
                            "key"=> "2",
                            "value"=> "voucher",
                            "value_text"=> $voucher->code,
                        ],
                        [
                            "key"=> "3",
                            "value"=> "usage",
                            "value_text"=> $description
                        ]
                    ]
                ]
            ]);

            $authorization = "Authorization: Bearer $resultAuth->access_token";
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
            $wa = curl_exec($ch);
            json_decode($wa);
            curl_close($ch);

            DB::table('third_party_logs')->insert([
                'third_party_code' => 'QONTAK_TEMPLATE',
                'url' => $url,
                'request_body' => $payload,
                'response_body' => $wa,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);
        }

    }
}
