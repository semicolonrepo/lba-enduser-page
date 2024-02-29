<?php

namespace App\Http\Controllers;

use App\Services\CampaignService;
use App\Services\VoucherClaimService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\TransactionModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class VoucherController extends Controller
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

    function pay($brand, $campaign, $productId) {
        try {
            $campaignData = $this->campaignService->getCampaign($brand, $campaign);
            $productOffer = $this->campaignService->getProductOffer($campaignData->id, $productId);
            $utmSource = request()->query('utm_source');

            //check session first
            if(session('customer_user_gmail') != null ||  session('customer_user_wa') != null || session('partner_id') != null) {

                $total_amount = ($productOffer->normal_price - $productOffer->subsidi_price);
                $exp_time = env('MIDTRANS_EXP_IN_MINUTE');

                $authGmail = DB::table('auth_gmail')->where('uuid', session('customer_user_gmail'))->first();
                $authWA = DB::table('auth_wa')->where('uuid', session('customer_user_wa'))->first();

                //check if transaction data exist
                $getLastTransaction = DB::table('voucher_transaction')
                ->where('campaign_id', $campaignData->id)
                ->where('product_id', $productOffer->id)
                ->where('transaction_amount', $total_amount)
                ->where('status', 'waiting')
                ->where('expired_time', '>', Carbon::now());

                ($authGmail != null) ? $getLastTransaction->where('customer_email', $authGmail->email) : $getLastTransaction->whereNull('customer_email');
                ($authWA != null) ? $getLastTransaction->where('customer_phone', $authWA->phone_number) : $getLastTransaction->whereNull('customer_phone');

                $resultTransaction = $getLastTransaction->first();
                if($resultTransaction != null) {
                    //use old transaction data
                    $snapToken = $resultTransaction->midtrans_snap_token;
                    $sentData = [
                        'baseUri' => env('APP_URL'),
                        'brand' => Str::slug($brand),
                        'productId' => $productOffer->id,
                        'data' => $campaignData,
                        'snapUrl' => env('MIDTRANS_SNAP_URL'),
                        'clientKey' => env('MIDTRANS_CLIENT_KEY'),
                        'snapToken' => $snapToken,
                    ];
            
                    $viewTemplate = $campaignData->page_template_code . '.payment';
                    return view($viewTemplate, $sentData);
                }
                else {
                    //hit new token to Midtrans
                    $customerDetails = [];
                    if($authGmail != null) {
                        $customer_name = $authGmail->name;
                        $customerDetails = [
                            'first_name' => $customer_name,
                            'email'      => $authGmail->email,
                        ];
                    } 
                    else {
                        $customer_name = 'Customer '.$authWA->phone_number;
                        $customerDetails = [
                            'first_name' => $customer_name,
                            'email'      => $authWA->phone_number.'@gmail.com',
                        ];
                    }

                    //insert transaction into DB
                    $transaction = new TransactionModel();
                    $transaction->campaign_id = $campaignData->id;
                    $transaction->product_id = $productOffer->id;
                    $transaction->partner_id = session('partner_id');
                    $transaction->product_name = $productOffer->name;
                    $transaction->normal_price = $productOffer->normal_price;
                    $transaction->subsidy_price = $productOffer->subsidi_price;
                    $transaction->transaction_amount = $total_amount;
                    $transaction->customer_name = $customer_name;
                    $transaction->customer_phone = ($authWA != null) ? $authWA->phone_number : NULL;
                    $transaction->customer_email = ($authGmail != null) ? $authGmail->email : NULL;
                    $transaction->is_auth_wa = ($authWA != null) ? true : false;
                    $transaction->is_auth_gmail = ($authGmail != null) ? true : false;
                    $transaction->expired_time = Carbon::now()->addMinutes($exp_time);

                    if($transaction->save()) {
                        $transaction_code = $transaction->transaction_number;

                        //request snap token to Midtrans
                        $payload = [
                            'transaction_details' => [
                                'order_id'     => $transaction_code,
                                'gross_amount' => $total_amount,
                            ],
                            'customer_details' => $customerDetails,
                            'item_details' => [
                                [
                                    'id'            => $productOffer->id,
                                    'price'         => $total_amount,
                                    'quantity'      => 1,
                                    'name'          => $productOffer->name,
                                    'brand'         => $campaignData->brand,
                                    'category'      => $campaignData->campaign,
                                    'merchant_name' => 'LetsBuyAsia',
                                ],
                            ],
                        ];
                
                        $snapToken = \Midtrans\Snap::getSnapToken($payload);
                        $transaction->midtrans_snap_token = $snapToken;
                        $transaction->save();
                        
                        $sentData = [
                            'baseUri' => env('APP_URL'),
                            'brand' => Str::slug($brand),
                            'productId' => $productOffer->id,
                            'data' => $campaignData,
                            'snapUrl' => env('MIDTRANS_SNAP_URL'),
                            'clientKey' => env('MIDTRANS_CLIENT_KEY'),
                            'snapToken' => $snapToken,
                        ];
                
                        $viewTemplate = $campaignData->page_template_code . '.payment';
                        return view($viewTemplate, $sentData);
                    }
                    else {
                        $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($th->getMessage(), true));
                        Log::error($error);
                        return redirect()->back()->with('failed', 'Terjadi kesalahan!');
                    }
                }
            }
            else {
                //force clear session and redirect back to product
                session()->forget('customer_user_gmail');
                session()->forget('customer_user_wa');
                session()->forget('partner_id');

                $arrayRoute = [
                    'brand' => $brand,
                    'campaign' => $campaign,
                    'productId' => $productId,
                ];

                if ($utmSource) {
                    $arrayRoute['utm_source'] = $utmSource;
                }

                return redirect()->route('product::show', $arrayRoute);
            }
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            $arrayRoute = [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ];

            if ($utmSource) {
                $arrayRoute['utm_source'] = $utmSource;
            }

            return redirect()->route('product::show', $arrayRoute);
        }
    }

    function claim($brand, $campaign, $productId) {
        try {
            $campaignData = $this->campaignService->getCampaign($brand, $campaign);
            $voucher = $this->voucherClaimService->run($campaignData->id, $productId);
            $utmSource = request()->query('utm_source');

            $arrayRoute = [
                'brand' => $brand,
                'campaign' => $campaign,
                'productId' => $productId,
            ];

            if ($voucher) {
                $arrayRoute['voucherCode'] = $voucher->code;
            }

            if ($utmSource) {
                $arrayRoute['utm_source'] = $utmSource;
            }

            if (!$voucher) {
                return redirect()->route('product::show', $arrayRoute)->with('failed', 'Voucher sudah diclaim atau habis!');
            }

            return redirect()->route('voucher::show', $arrayRoute);
        }
        catch (\Throwable $th) {
            $error = sprintf('[%s],[%d] ERROR:[%s]', __METHOD__, __LINE__, json_encode($th->getMessage(), true));
            Log::error($error);
            return redirect()->back()->with('failed', 'Terjadi kesalahan!');
        }
    }

    public function show($brand, $campaign, $productId, $voucherCode, $transactionNumber=NULL) {
        $campaignData = $this->campaignService->getCampaign($brand, $campaign);
        $voucher = $this->voucherService->showVoucher($voucherCode);

        if($transactionNumber != NULL) {
            $authWA = DB::table('voucher_transaction')->where('transaction_number', $transactionNumber)->first()->customer_phone;
            $authGmail = DB::table('voucher_transaction')->where('transaction_number', $transactionNumber)->first()->customer_email;
        }
        else {
            $authWA = optional(DB::table('auth_wa')->where('uuid', session('customer_user_wa'))->first())->phone_number;
            $authGmail = optional(DB::table('auth_gmail')->where('uuid', session('customer_user_gmail'))->first())->email;
        }

        if ($voucherCode && $campaignData && ($authGmail == $voucher->email || $authWA == $voucher->phone_number)) {
            $viewTemplate = $campaignData->page_template_code . '.voucher_redeem';
            return view($viewTemplate, [
                'voucher' => $voucher,
                'data' => $campaignData,
            ]);
        }

        return redirect()->back();
    }
}
