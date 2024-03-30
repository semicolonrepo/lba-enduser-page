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
        private CampaignProductService $campaignProductService,
    ) {}

    public function run($campaignId, $productId) {
        $validate = $this->validate($campaignId, $productId);

        if (!$validate['status']) {
            return [
                'message' => $validate['message'],
                'status' => false
            ];
        }

        $vouchers = $this->findVoucherSql->take(session('claim_qty'))->get();
        $this->claim($vouchers, $campaignId, $productId);
        session()->forget('partner_id');

        return [
            'message' => 'Berhasil',
            'status' => true,
            'data' => $vouchers,
        ];
    }

    protected function claim($vouchers, $campaignId, $productId) {
        DB::transaction(function () use ($vouchers, $campaignId, $productId) {
            $isCampaignAuthByGmail = $this->campaignService->getCampaignAuths($campaignId, 'GMAIL')->first();
            $isCampaignAuthByWA = $this->campaignService->getCampaignAuths($campaignId, 'WHATSAPP')->first();
            $sessionWA = $this->getAuthSession('customer_user_wa');
            $sessionGmail = $this->getAuthSession('customer_user_gmail');

            $formCampaignProduct = $this->campaignProductService->getFormSettingArray($campaignId, $productId) ?? [];
            $formCampaignProductJson = $this->campaignProductService->sanitizeFormJson($formCampaignProduct, session('voucher_claim_request_session'));

            $claimIdentifier = $campaignId . $productId . uniqid();
            foreach ($vouchers as $key => $voucher) {
                DB::table('voucher_generates')->where('voucher_generates.code', $voucher->code)
                ->update([
                    "product_id" => $productId,
                    "email" => ($isCampaignAuthByGmail) ? $sessionGmail->email : null,
                    "phone_number" =>($isCampaignAuthByWA) ? $sessionWA->phone_number : null,
                    "claim_date" => date('Y-m-d H:i:s'),
                    "ip_address" => request()->ip(),
                    "campaign_product_form_json" => $formCampaignProductJson,
                    "claim_identifier" => $claimIdentifier,
                ]);

                $formCampaignProductArray = $this->campaignProductService->sanitizeFormArray($formCampaignProduct, session('voucher_claim_request_session'), $voucher->code);
                DB::table('campaign_product_questionares')
                    ->insert($formCampaignProductArray);
            }
        });

        foreach ($vouchers as $key => $voucher) {
            $voucher = $this->voucherService->showVoucher($voucher->code);
            $this->sendNotif($voucher);
        }
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

        if (!$this->validateLimitationRule($campaignId)) {
            return [
                'message' => 'Voucher sudah diclaim atau habis!',
                'status' => false
            ];
        }

        if (!$this->validateEligible($campaignId, $productId)) {
            return [
                'message' => 'Voucher sudah diclaim atau habis!',
                'status' => false
            ];
        }

        if (!$this->validateLimitUsage($campaignId)) {
            return [
                'message' => 'Voucher sudah diclaim atau habis!',
                'status' => false
            ];
        }

        if (!$this->validateLimitClaimQty($campaignId, $productId)) {
            $totalVoucherClaimed = $this->getTotalVoucherClaimed($campaignId, $productId);
            $totalLimitVoucherClaim = $this->getTotalLimitVoucherClaim($campaignId);
            $totalRemains = $totalLimitVoucherClaim - $totalVoucherClaimed;

            return [
                'message' => "Voucher yang anda bisa ambil tersisa $totalRemains",
                'status' => false
            ];
        }

        if (!$this->validatePartner()) {
            return [
                'message' => 'Voucher sudah diclaim atau habis!',
                'status' => false
            ];
        }

        if (!$this->validateProduct($campaignId, $productId)) {
            return [
                'message' => 'Voucher sudah diclaim atau habis!',
                'status' => false
            ];
        }

        return [
            'message' => 'Berhasil claim voucher!',
            'status' => true
        ];
    }

    protected function validateLimitIpAddress($campaignId) {
        $campaign = DB::table('campaigns')
            ->where('campaigns.id', $campaignId)
            ->select(
                'ip_address_limit_voucher',
                'ip_address_limit_time',
            )
            ->first();

        if (empty($campaign)) {
            return true;
        }

        $claimedInTime = DB::table('voucher_generates')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
            ->where('vouchers.campaign_id', $campaignId)
            ->where('voucher_generates.ip_address', request()->ip())
            ->where('voucher_generates.claim_date', '>=', now()->subMinutes($campaign->ip_address_limit_time))
            ->count();

        if ($campaign->ip_address_limit_voucher && $claimedInTime >= $campaign->ip_address_limit_voucher) {
            return false;
        }

        return true;
    }

    protected function validateLimitBrowserType($campaignId) {
        $campaign = DB::table('campaigns')
            ->where('campaigns.id', $campaignId)
            ->select(
                'browser_type_limit_voucher',
                'browser_type_limit_time',
            )
            ->first();

        if (empty($campaign)) {
            return true;
        }

        $claimedInTime = DB::table('voucher_generates')
            ->join('vouchers', 'vouchers.id', '=', 'voucher_generates.voucher_id')
            ->where('vouchers.campaign_id', $campaignId)
            ->where('voucher_generates.browser', request()->header('user-agent'))
            ->where('voucher_generates.claim_date', '>=', now()->subMinutes($campaign->browser_type_limit_time))
            ->count();

        if ($campaign->browser_type_limit_voucher && $claimedInTime >= $campaign->browser_type_limit_voucher) {
            return false;
        }

        return true;
    }

    protected function validateLimitationRule($campaignId){
        $ruleSetting = DB::table('campaigns')
            ->where('campaigns.id', $campaignId)
            ->select(
                'rule_ip_and_browser_limit',
            )
            ->first();

        if ($ruleSetting->rule_ip_and_browser_limit === 'OR' && (!$this->validateLimitIpAddress($campaignId) || !$this->validateLimitBrowserType($campaignId))) {
            return false;
        }

        if ($ruleSetting->rule_ip_and_browser_limit === 'AND' && (!$this->validateLimitIpAddress($campaignId) && !$this->validateLimitBrowserType($campaignId))) {
            return false;
        }

        return true;
    }

    protected function validatePartner() {
        $partner = session('partner_id');

        if (empty($partner)) {
            return false;
        }

        if ($partner === 'internal') {
            $isValid = $this->findVoucherSql->whereNull('vouchers.provider_id');
        }

        if ($partner !== 'internal') {
            $isValid = $this->findVoucherSql->where('vouchers.provider_id', $partner);
        }

        if ($isValid->get()->isEmpty()) {
            return false;
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

    protected function validateLimitClaimQty($campaignId, $productId) {
        $totalVoucherClaimed = $this->getTotalVoucherClaimed($campaignId, $productId);
        $totalLimitVoucherClaim = $this->getTotalLimitVoucherClaim($campaignId);
        $claimQty = session('claim_qty');

        $isValid = $this->findVoucherSql->havingRaw("($totalLimitVoucherClaim - $totalVoucherClaimed) >= $claimQty");

        if ($isValid->get()->isEmpty()) {
            return false;
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
                DB::raw('MIN(voucher_generates.id) as id'),
                'voucher_generates.code',
                'voucher_generates.phone_number',
                'voucher_generates.email',
                'voucher_generates.claim_date',
                'vouchers.id as voucher_id',
                'vouchers.title as voucher_title',
                'vouchers.limit_usage_user',
                'vouchers.expires_at',
                'providers.name as provider_name',
                'voucher_generates.claim_identifier',
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
                'voucher_generates.claim_identifier',
            );
    }

    protected function getAuthSession($sessionKey) {
        return DB::table('auth_' . Str::afterLast($sessionKey, '_'))
            ->where('uuid', session($sessionKey))
            ->first();
    }

    protected function getTotalLimitVoucherClaim($campaignId) {
        $vouchers = DB::table('vouchers')
            ->where('campaign_id', $campaignId)
            ->where('expires_at', '>', date('Y-m-d H:i:s'))
            ->where('is_active', true);

        $partner = session('partner_id');
        if ($partner === 'internal') {
            $vouchers->whereNull('provider_id');
        }

        if ($partner !== 'internal') {
            $vouchers->where('provider_id', $partner);
        }

        return $vouchers->sum('limit_usage_user');
    }

    protected function getTotalVoucherClaimed($campaignId, $productId) {
        $sessionWA = $this->getAuthSession('customer_user_wa');
        $sessionGmail = $this->getAuthSession('customer_user_gmail');

        $voucherGenerates = DB::table('voucher_generates as vg')
            ->join('vouchers as v', 'v.id', '=', 'vg.voucher_id')
            ->join('campaigns as c', 'c.id', '=', 'v.campaign_id')
            ->leftJoin('campaign_products as cp', 'cp.campaign_id', '=', 'c.id')
            ->leftJoin('voucher_term_products as vtp', 'vtp.voucher_id', '=', 'vg.voucher_id')
            ->where('cp.product_id', $productId)
            ->where('v.campaign_id', $campaignId)
            ->where('vtp.product_id', $productId);

        $partner = session('partner_id');
        if ($partner === 'internal') {
            $voucherGenerates->whereNull('v.provider_id');
        }

        if ($partner !== 'internal') {
            $voucherGenerates->where('v.provider_id', $partner);
        }

        $isCampaignAuthByGmail = $this->campaignService->getCampaignAuths($campaignId, 'GMAIL')->first();
        $isCampaignAuthByWA = $this->campaignService->getCampaignAuths($campaignId, 'WHATSAPP')->first();

        if ($isCampaignAuthByGmail && $isCampaignAuthByWA) {
            $voucherGenerates->where(function ($query) use ($sessionGmail, $sessionWA) {
                $query->where('vg.email', $sessionGmail->email)
                    ->orWhere('vg.phone_number', $sessionWA->phone_number);
            });
        } elseif ($isCampaignAuthByGmail) {
            $voucherGenerates->where('vg.email', $sessionGmail->email);
        } elseif ($isCampaignAuthByWA) {
            $voucherGenerates->where('vg.phone_number', $sessionWA->phone_number);
        }

        return $voucherGenerates->count();
    }
}
