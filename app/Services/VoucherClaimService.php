<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\ClaimVoucher;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class VoucherClaimService
{
    protected $findVoucherSql;

    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    public function run($campaignId, $productId) {
        $validate = $this->validate($campaignId, $productId);

        if (!$validate) {
            return false;
        }

        $voucher = $this->findVoucherSql->first();
        $this->claim($voucher, $campaignId, $productId);
        session()->forget('partner_id');

        return $voucher;
    }

    protected function claim($voucher, $campaignId, $productId) {
        DB::transaction(function () use ($voucher, $campaignId,$productId) {
            $isCampaignAuthByGmail = $this->campaignService->getCampaignAuths($campaignId, 'GMAIL')->first();
            $isCampaignAuthByWA = $this->campaignService->getCampaignAuths($campaignId, 'WHATSAPP')->first();
            $sessionWA = $this->getAuthSession('customer_user_wa');
            $sessionGmail = $this->getAuthSession('customer_user_gmail');

            DB::table('voucher_generates')->where('voucher_generates.code', $voucher->code)
                ->update([
                    "product_id" => $productId,
                    "email" => ($isCampaignAuthByGmail) ? $sessionGmail->email : null,
                    "phone_number" =>($isCampaignAuthByWA) ? $sessionWA->phone_number : null,
                    "claim_date" => date('Y-m-d H:i:s'),
                    "ip_address" => request()->ip(),
                ]);

            $voucher = $this->voucherService->showVoucher($voucher->code);
            $this->sendNotif($voucher);
        });
    }

    protected function sendNotif($voucher) {
        $notifiable = (new AnonymousNotifiable());
        if ($voucher->email) {
            $notifiable->route('mail', $voucher->email);
        }
        Notification::send($notifiable, new ClaimVoucher($voucher));
    }

    protected function validate($campaignId, $productId) {
        $this->startQuery();

        if (!$this->validateLimitIpAddress($campaignId)) {
            return false;
        }

        if (!$this->validateEligible($campaignId, $productId)) {
            return false;
        }

        if (!$this->validateLimitUsage($campaignId)) {
            return false;
        }

        if (!$this->validateProduct($campaignId, $productId)) {
            return false;
        }

        if (!$this->validatePartner()) {
            return false;
        }

        return true;
    }

    protected function validateLimitIpAddress($campaignId) {
        $campaign = DB::table('campaigns')
            ->where('campaigns.id', $campaignId)
            ->select(
                'ip_address_limit_voucher',
                'ip_address_limit_time',
            )
            ->first();

        $claimedInTime = DB::table('voucher_generates')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
            ->where('vouchers.campaign_id', $campaignId)
            ->where('voucher_generates.ip_address', request()->ip())
            ->where('voucher_generates.claim_date', '>=', now()->subMinutes($campaign->ip_address_limit_time))
            ->count();

        if ($claimedInTime >= $campaign->ip_address_limit_voucher) {
            return false;
        }

        return true;
    }

    protected function validatePartner() {
        $partner = session('partner_id');

        if (empty($partner)) {
            session()->forget('partner_id');
            return false;
        }

        if ($partner === 'internal') {
            $this->findVoucherSql->whereNull('vouchers.provider_id');
        }

        if ($partner !== 'internal') {
            $this->findVoucherSql->where('vouchers.provider_id', $partner);
        }

        return true;
    }

    protected function validateProduct($campaignId, $productId) {
        $voucherProductTerm = DB::table('voucher_term_products')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_term_products.voucher_id')
            ->where('vouchers.campaign_id', $campaignId)
            ->get();

        if ($voucherProductTerm->isNotEmpty()) {
            $isValid = $this->findVoucherSql->where('voucher_term_products.product_id', $productId);

            if ($isValid->get()->isEmpty()) {
                return false;
            }
        }

        return true;
    }

    protected function validateLimitUsage($campaignId) {
        $sessionWA = $this->getAuthSession('customer_user_wa');
        $sessionGmail = $this->getAuthSession('customer_user_gmail');

        $arrayVoucherUsedBinding = [];
        $countVouvherUsedRaw = "SELECT COUNT(*) FROM voucher_generates as subquery WHERE subquery.voucher_id = vouchers.id AND (1!=1";

        $isCampaignAuthByGmail = $this->campaignService->getCampaignAuths($campaignId, 'GMAIL')->first();
        if ($isCampaignAuthByGmail) {
            $countVouvherUsedRaw .= " OR subquery.email = ?";
            $arrayVoucherUsedBinding[] = $sessionGmail->email;
        }

        $isCampaignAuthByWA = $this->campaignService->getCampaignAuths($campaignId, 'WHATSAPP')->first();
        if ($isCampaignAuthByWA) {
            $countVouvherUsedRaw .= " OR subquery.phone_number = ?";
            $arrayVoucherUsedBinding[] = $sessionWA->phone_number;
        }

        $isValid = $this->findVoucherSql
            ->havingRaw("($countVouvherUsedRaw)) < vouchers.limit_usage_user", $arrayVoucherUsedBinding);

        if ($isValid->get()->isEmpty()) {
            return false;
        }

        return true;
    }

    protected function validateEligible($campaignId, $productId) {
        $isValid = $this->findVoucherSql
            ->where('voucher_generates.is_active', true)
            ->whereNull('voucher_generates.claim_date')
            ->whereNull('voucher_usages.voucher_generate_id')
            ->where('vouchers.is_active', true)
            ->where('campaigns.is_active', true)
            ->where('vouchers.expires_at', '>', date('Y-m-d H:i:s'))
            ->where('campaigns.expires_at', '>', date('Y-m-d H:i:s'))
            ->where('campaign_products.product_id', $productId)
            ->where('vouchers.campaign_id', $campaignId);

        if ($isValid->get()->isEmpty()) {
            return false;
        }

        return true;
    }

    protected function startQuery() {
        $this->findVoucherSql = DB::table('voucher_generates')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
            ->join('campaigns', 'campaigns.id', '=', 'vouchers.campaign_id')
            ->leftJoin('providers', 'providers.id', '=', 'vouchers.provider_id')
            ->leftJoin('voucher_usages', 'voucher_usages.voucher_generate_id', '=', 'voucher_generates.id')
            ->leftJoin('campaign_products', 'campaign_products.campaign_id', '=', 'campaigns.id')
            ->leftJoin('voucher_term_products', 'voucher_term_products.voucher_id', '=', 'voucher_generates.voucher_id')
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
            )->groupBy(
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
            );
    }

    protected function getAuthSession($sessionKey) {
        return DB::table('auth_' . Str::afterLast($sessionKey, '_'))
            ->where('uuid', session($sessionKey))
            ->first();
    }
}
