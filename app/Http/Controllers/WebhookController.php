<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CampaignService;
use App\Services\VoucherClaimService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\TransactionModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
        private VoucherClaimService $voucherClaimService,
    ) {
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    /**
     * Function to handle page redirection after payment success
     * Method: GET
     */
    public function redirectSuccessPayment(Request $request) {
        if ($request->has('order_id') && $request->has('status_code') && $request->has('transaction_status')) {
            
            $brand = $request->brand;
            $campaign = $request->campaign;
            $order_id = $request->order_id;
            $status_code = $request->status_code;
            $transaction_status = $request->transaction_status;
    
            //get transaction data
            $getTransaction = DB::table('voucher_transaction')
            ->where('transaction_number', $order_id)
            ->first();

            if($getTransaction != null) {
                //if status is (capture/settlement) same as success in DB
                if(($transaction_status=='capture' || $transaction_status== 'settlement') && $getTransaction->status =='success') {
                    
                    $campaignData = $this->campaignService->getCampaign($brand, $campaign);
                    $voucher = $this->voucherClaimService->run($campaignData->id, $getTransaction->product_id, $order_id);
                    $utmSource = request()->query('utm_source');

                    $arrayRoute = [
                        'brand' => $brand,
                        'campaign' => $campaign,
                        'productId' => $getTransaction->product_id,
                        'voucherCode' => $voucher->code
                    ];
        
                    if ($utmSource) {
                        $arrayRoute['utm_source'] = $utmSource;
                    }

                    $response = [
                        'status' => 200, 
                        'message' => 'OK',
                        'redirect_url' => route('voucher::show', $arrayRoute)
                    ];
                    return response()->json($response, 200);
                }
                else {
                    //check status directly via API
                    $successPayment = $this->isPaymentSuccess($order_id);
                    if($successPayment) {

                        $campaignData = $this->campaignService->getCampaign($brand, $campaign);
                        $voucher = $this->voucherClaimService->run($campaignData->id, $getTransaction->product_id, $order_id);
                        $utmSource = request()->query('utm_source');

                        $arrayRoute = [
                            'brand' => $brand,
                            'campaign' => $campaign,
                            'productId' => $getTransaction->product_id,
                        ];

                        if ($voucher) {
                            $arrayRoute['voucherCode'] = $voucher->code;
                        }
            
                        if ($utmSource) {
                            $arrayRoute['utm_source'] = $utmSource;
                        }

                        $response = [
                            'status' => 200, 
                            'message' => 'OK',
                            'redirect_url' => route('voucher::show', $arrayRoute)
                        ];
                        return response()->json($response, 200);
                    }
                    else {
                        return response()->json(['status' => 404, 'message' => 'Waiting payment!'], 404);
                    }
                }
            }
            else {
                return response()->json(['status' => 404, 'message' => 'Data not found!'], 404);
            }
        } 
        else {
            return response()->json(['status' => 400, 'message' => 'Missing parameter!'], 400);
        }
    }

    /**
     * Function to handle payment notification status from Midtrans
     * Method: POST
     * Content-type: Application/JSON
     */
    public function paymentNotification(Request $request) {
        $userAgent = $request->header('User-Agent');
        $contentType = $request->header('Content-Type');

        if($userAgent==='Veritrans' && $contentType==='application/json') {
            $amount = number_format($request->gross_amount, 0, '.', '');

            //get transaction data
            $getTransaction = DB::table('voucher_transaction')
                ->where('transaction_number', $request->order_id)
                ->where('transaction_amount', $amount)
                ->where('status', 'waiting')
            ->first();

            if($getTransaction != null) {

                $trx_amount = number_format($getTransaction->transaction_amount, 2, '.', '');

                //check signature to make sure security and integrity data
                $lbaSignature = $this->generateSHA512Hash($getTransaction->transaction_number, $request->status_code, $trx_amount, env('MIDTRANS_SERVER_KEY'));

                if($lbaSignature === $request->signature_key) {
                    //set internal payment status
                    if($request->transaction_status=='capture' || $request->transaction_status == 'settlement') {
                        $internalStatus = 'success';
                    }
                    else if($request->transaction_status == 'pending') {
                        $internalStatus = 'waiting';
                    }
                    else {
                        $internalStatus = $request->transaction_status;
                    }

                    //update data into table voucher_transaction
                    $newData = [
                        'status' => $internalStatus,
                        'midtrans_status_code' => $request->status_code,
                        'midtrans_status_message' => $request->status_message,
                        'midtrans_payment_type' => $request->payment_type,
                        'midtrans_status' => $request->transaction_status,
                        'midtrans_payment_time' => $request->transaction_time,
                        'midtrans_response' => $request->getContent(),
                        'updated_at' => Carbon::now()
                    ];
                    $updateTransaction = TransactionModel::where('id', $getTransaction->id)->update($newData);

                    if ($updateTransaction) {
                        $statusCode = 200;
                        $response = [
                            'status' => 200,
                            'message' => 'OK',
                        ];
                    }else {
                        $statusCode = 204;
                        $response = [
                            'status' => 204,
                            'message' => 'Failed to update data!',
                        ];
                    }
                }
                else {
                    //signature not match
                    $statusCode = 401;
                    $response = [
                        'status' => 401,
                        'message' => 'Signature not match!',
                    ];
                }
            }
            else {
                //transaction not found
                $statusCode = 404;
                $response = [
                    'status' => 404,
                    'message' => 'Transaction not found!',
                ];
            }
        }
        else {
            //request not allowed
            $statusCode = 400;
            $response = [
                'status' => 400,
                'message' => 'Something wrong!',
            ];
        }

        return response()->json($response, $statusCode);
    }

    private function generateSHA512Hash($order_id, $status_code, $gross_amount, $serverKey) {
        $dataToHash = $order_id . $status_code . $gross_amount . $serverKey;
        $sha512Hash = hash('sha512', $dataToHash);
    
        return $sha512Hash;
    }

    private function isPaymentSuccess($orderId) {
        $midtransApiEndpoint = env('MIDTRANS_API_URI')."/{$orderId}/status";
        $midtransAuthString = base64_encode(env("MIDTRANS_SERVER_KEY").":");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $midtransAuthString,
        ])->get($midtransApiEndpoint);

        if ($response->successful()) {
            $responseData = $response->json();
            
            if($responseData['transaction_status']=='settlement' || $responseData['transaction_status']=='capture') {
                return true;
            }
        } else {
            return false;
        }
    }

}
