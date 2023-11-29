<?php
namespace App\Channels;

use Illuminate\Notifications\Notification;

class ZenzivaGatewayChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification)
    {
        $voucher = $notification->toWhatsapp($notifiable);

        if ($voucher->phone_number) {
            $description = strip_tags(html_entity_decode($voucher->description));
            $requestBody = array(
                'userkey' => '9dda04a90ac8',
                'passkey' => 'befd58623c2d3f7311c62da8',
                'to' => $voucher->phone_number,
                'message' => "Selamat, kamu berhasil klaim voucher untuk produk $voucher->product_name.\nKode Voucher anda adalah: $voucher->code.\n\nCara penggunaan sebagai berikut: $description"
            );

            $curlHandle = curl_init();
            curl_setopt($curlHandle, CURLOPT_URL, 'https://console.zenziva.net/wareguler/api/sendWA/');
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
        }

    }
}
