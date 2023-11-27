<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

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

        $customerGmailSession = session('customer_user_gmail');
        $authByGmail = (clone $campaignAuths)->where('auth_settings.code', 'GMAIL')->first();
        if (!$customerGmailSession && $authByGmail) {
            $isAuthGmail = false;
        }

        $customerWaSession = session('customer_user_wa');
        $authByWA = (clone $campaignAuths)->where('auth_settings.code', 'WHATSAPP')->first();
        if (!$customerWaSession && $authByWA) {
            $isAuthWA = false;
        }

        return (object) [
            'isAuthGmail' => $isAuthGmail,
            'isAuthWA' => $isAuthWA,
        ];
    }

    public function claimVoucher($campaignId, $productId) {
        $partner = session('partner_id');

        $authWA = DB::table('auth_wa')
            ->where('uuid', session('customer_user_wa'))->first();
        $authGmail = DB::table('auth_gmail')
            ->where('uuid', session('customer_user_gmail'))->first();

        $countVouvherUsedRaw = "SELECT COUNT(*) FROM voucher_generates as subquery WHERE subquery.voucher_id = vouchers.id";
        $arrayVoucherUsedBinding = [];
        $campaignAuths = $this->campaignService->getCampaignAuths($campaignId);
        $authByGmail = (clone $campaignAuths)->where('auth_settings.code', 'GMAIL')->first();
        if ($authByGmail) {
            $countVouvherUsedRaw .= " AND subquery.email = ?";
            $arrayVoucherUsedBinding[] = $authGmail->email;
        }

        $authByWA = (clone $campaignAuths)->where('auth_settings.code', 'WHATSAPP')->first();
        if ($authByWA) {
            $countVouvherUsedRaw .= " AND subquery.phone_number = ?";
            $arrayVoucherUsedBinding[] = $authWA->phone_number;
        }

        $voucherSql = $this->getVouchersGenerates($campaignId)
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
            )->havingRaw("($countVouvherUsedRaw) < vouchers.limit_usage_user", $arrayVoucherUsedBinding);

        if ($partner === 'internal') {
            $voucher = (clone $voucherSql)
                ->whereNull('vouchers.provider_id')
                ->first();
        }

        if (!empty($partner) && $partner !== 'internal') {
            $voucher = (clone $voucherSql)
                ->where('vouchers.provider_id', $partner)
                ->first();
        }

        if (!$voucher) {
            session()->forget('partner_id');
            return false;
        }

        DB::table('voucher_generates')->where('voucher_generates.code', $voucher->code)
        ->update([
            "product_id" => $productId,
            "email" => $authGmail->email ?? null,
            "phone_number" => $authWA->phone_number ?? null,
            "claim_date" => date('Y-m-d H:i:s'),
        ]);

        session()->forget('partner_id');

        return $voucher;
    }

    public function showVoucher($voucherGenerateCode) {
       return DB::table('voucher_generates')
        ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
        ->join('campaigns', 'campaigns.id', '=', 'vouchers.campaign_id')
        ->leftJoin('providers', 'providers.id', '=', 'vouchers.provider_id')
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
            'providers.name as provider_name',
        )->first();
    }
}
