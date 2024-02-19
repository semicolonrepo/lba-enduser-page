<?php

namespace App\Services;

use App\Notifications\ClaimVoucher;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class VoucherService
{

    public function __construct(
        private CampaignService $campaignService,
    ) {}

    public function getVouchers() {
        $vouchers = DB::table('vouchers')
        ->join('campaigns', 'campaigns.id', '=', 'vouchers.campaign_id')
        ->join('brands', 'brands.id', '=', 'campaigns.brand_id')
        ->leftJoin('providers', 'providers.id', '=', 'vouchers.provider_id')
        ->where('vouchers.is_active', 1)
        ->select(
            'vouchers.id as id',
            'vouchers.voucher_code',
            DB::raw(
                "CASE WHEN vouchers.title IS NOT NULL THEN vouchers.title
                ELSE CONCAT('Voucher ', IF(providers.name IS NOT NULL, CONCAT(providers.name, ' '), ''), campaigns.name, ' ', brands.name)
                END as title"
            ),
            'vouchers.description',
            'vouchers.limit_usage_user',
            'vouchers.is_unlimited_user',
            'vouchers.is_unlimited_all',
            'vouchers.is_merchant_all',
            'vouchers.expires_at',
            'vouchers.is_active',
            'vouchers.campaign_id',
            'campaigns.name as campaign_name',
            'vouchers.provider_id',
            'providers.name as provider_name',
        )
        ->selectRaw('(SELECT COUNT(*) FROM voucher_generates WHERE voucher_generates.voucher_id = vouchers.id) as total_generate');

        return $vouchers;
    }

    function getVouchersGenerates($campaignId) {
        $voucherGenerate = DB::table('voucher_generates')
        ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
        ->join('campaigns', 'campaigns.id', '=', 'vouchers.campaign_id')
        ->leftJoin('providers', 'providers.id', '=', 'vouchers.provider_id')
        ->leftJoin('voucher_usages', 'voucher_usages.voucher_generate_id', '=', 'voucher_generates.id')
        ->leftJoin('campaign_products', 'campaign_products.campaign_id', '=', 'campaigns.id')
        ->leftJoin('voucher_term_products', 'voucher_term_products.voucher_id', '=', 'voucher_generates.voucher_id')
        ->where('vouchers.campaign_id', $campaignId)
        ->where('voucher_generates.is_active', true)
        ->whereNull('voucher_generates.claim_date')
        ->whereNull('voucher_usages.voucher_generate_id')
        ->where('vouchers.is_active', true)
        ->where('campaigns.is_active', true)
        ->where('vouchers.expires_at', '>', date('Y-m-d H:i:s'))
        ->where('campaigns.expires_at', '>', date('Y-m-d H:i:s'))
        ->select(
            'voucher_generates.code',
            'voucher_generates.phone_number',
            'voucher_generates.email',
            'voucher_generates.claim_date',
            'vouchers.id as voucher_id',
            'vouchers.title as voucher_title',
            'vouchers.limit_usage_user',
            'vouchers.expires_at',
            'providers.name as provider_name',
        );

        return $voucherGenerate;
    }

    public function validateAuth($brandSlug, $campaignSlug) : object {
        $isAuthGmail = true;
        $isAuthWA = true;
        $campaignData = $this->campaignService->getCampaign($brandSlug, $campaignSlug);
        $campaignAuths = $this->campaignService->getCampaignAuths($campaignData->id);

        $needAuthGmail = false;
        $needAuthWA = false;
        $customerGmailSession = session('customer_user_gmail');
        $authByGmail = (clone $campaignAuths)->where('auth_settings.code', 'GMAIL')->first();
        if ($authByGmail) {
            $needAuthGmail = true;
        }

        if ($customerGmailSession) {
            $authGmail = DB::table('auth_gmail')
            ->where('uuid', session('customer_user_gmail'))->first();
        }

        if (!$customerGmailSession && $authByGmail) {
            $isAuthGmail = false;
        }

        $customerWaSession = session('customer_user_wa');
        $authByWA = (clone $campaignAuths)->where('auth_settings.code', 'WHATSAPP')->first();
        if ($authByWA) {
            $needAuthWA = true;
        }

        if ($customerWaSession) {
            $authWA = DB::table('auth_wa')
            ->where('uuid', session('customer_user_wa'))->first();
        }

        if (!$customerWaSession && $authByWA) {
            $isAuthWA = false;
        }

        return (object) [
            'needAuthGmail' => $needAuthGmail,
            'isAuthGmail' => $isAuthGmail,
            'userGmail' => $authGmail->email ?? null,
            'needAuthWA' => $needAuthWA,
            'isAuthWA' => $isAuthWA,
            'userWA' => $authWA->phone_number ?? null,
        ];
    }

    public function claimVoucher($campaignId, $productId) {
        $partner = session('partner_id');

        $authWA = DB::table('auth_wa')
            ->where('uuid', session('customer_user_wa'))->first();
        $authGmail = DB::table('auth_gmail')
            ->where('uuid', session('customer_user_gmail'))->first();

        $countVouvherUsedRaw = "SELECT COUNT(*) FROM voucher_generates as subquery WHERE subquery.voucher_id = vouchers.id AND (1!=1";
        $arrayVoucherUsedBinding = [];
        $campaignAuths = $this->campaignService->getCampaignAuths($campaignId);
        $authByGmail = (clone $campaignAuths)->where('auth_settings.code', 'GMAIL')->first();
        if ($authByGmail) {
            $countVouvherUsedRaw .= " OR subquery.email = ?";
            $arrayVoucherUsedBinding[] = $authGmail->email;
        }

        $authByWA = (clone $campaignAuths)->where('auth_settings.code', 'WHATSAPP')->first();
        if ($authByWA) {
            $countVouvherUsedRaw .= " OR subquery.phone_number = ?";
            $arrayVoucherUsedBinding[] = $authWA->phone_number;
        }

        $voucherSql = $this->getVouchersGenerates($campaignId)
            ->where('campaign_products.product_id', $productId)
            ->groupBy(
                'voucher_generates.code',
                'voucher_generates.phone_number',
                'voucher_generates.email',
                'voucher_generates.claim_date',
                'vouchers.id',
                'vouchers.expires_at',
                'vouchers.title',
                'vouchers.limit_usage_user',
                'vouchers.provider_id',
                'providers.name',
            )->havingRaw("($countVouvherUsedRaw)) < vouchers.limit_usage_user", $arrayVoucherUsedBinding);

        $voucherProductTerm = DB::table('voucher_term_products')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_term_products.voucher_id')
            ->where('vouchers.campaign_id', $campaignId)
            ->get();

        if ($voucherProductTerm->isNotEmpty()) {
            $voucherSql->where('voucher_term_products.product_id', $productId);
        }

        if ($partner === 'internal') {
            $voucher = (clone $voucherSql)
                ->whereNull('vouchers.provider_id')
                ->first();
        } else if (!empty($partner) && $partner !== 'internal') {
            $voucher = (clone $voucherSql)
                ->where('vouchers.provider_id', $partner)
                ->first();
        } else {
            session()->forget('partner_id');
            return false;
        }

        if (!$voucher) {
            session()->forget('partner_id');
            return false;
        }

        DB::transaction(function () use ($voucher, $productId, $authByGmail, $authByWA, $authGmail, $authWA) {
            DB::table('voucher_generates')->where('voucher_generates.code', $voucher->code)
            ->update([
                "product_id" => $productId,
                "email" => ($authByGmail) ? $authGmail->email : null,
                "phone_number" =>($authByWA) ? $authWA->phone_number : null,
                "claim_date" => date('Y-m-d H:i:s'),
            ]);

            $voucher = $this->showVoucher($voucher->code);
            $notifiable = (new AnonymousNotifiable());
            if ($voucher->email) {
                $notifiable->route('mail', $voucher->email);
            }
            Notification::send($notifiable, new ClaimVoucher($voucher));
            session()->forget('partner_id');
        });

        return $voucher;
    }

    public function showVoucher($voucherGenerateCode) {
       return DB::table('voucher_generates')
        ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
        ->join('campaigns', 'campaigns.id', '=', 'vouchers.campaign_id')
        ->leftJoin('brands', 'brands.id', '=', 'campaigns.brand_id')
        ->leftJoin('providers', 'providers.id', '=', 'vouchers.provider_id')
        ->leftJoin('products', 'products.id', '=', 'voucher_generates.product_id')
        ->leftJoin('auth_gmail', 'auth_gmail.email', '=', 'voucher_generates.email')
        ->where('voucher_generates.code', $voucherGenerateCode)
        ->where('voucher_generates.is_active', true)
        ->where('vouchers.is_active', true)
        ->where('campaigns.is_active', true)
        ->where('vouchers.expires_at', '>', date('Y-m-d H:i:s'))
        ->where('campaigns.expires_at', '>', date('Y-m-d H:i:s'))
        ->select(
            'voucher_generates.code',
            'voucher_generates.phone_number',
            'voucher_generates.email',
            'voucher_generates.claim_date',
            'vouchers.id as voucher_id',
            'vouchers.title as voucher_title',
            'vouchers.limit_usage_user',
            'vouchers.expires_at',
            'vouchers.description',
            'providers.name as provider_name',
            'products.name as product_name',
            'brands.name as brand_name',
            'brands.photo as brand_photo',
            'auth_gmail.name as auth_gmail_name',
            'campaigns.id as campaign_id',
        )->first();
    }
}
