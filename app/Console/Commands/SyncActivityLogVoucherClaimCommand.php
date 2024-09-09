<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncActivityLogVoucherClaimCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-activity-log-voucher-claim-command {campaign_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaignId = $this->argument('campaign_id');

        $vouchersGenerates = DB::table('voucher_generates')
            ->join('vouchers', 'vouchers.id', 'voucher_generates.voucher_id')
            ->whereNotNull('voucher_generates.claim_date')
            ->where('vouchers.campaign_id', $campaignId)
            ->get();

        foreach ($vouchersGenerates as $key => $vouchersGenerate) {
            $activityLog = DB::table('activity_logs')
                ->where('campaign_id', $campaignId)
                ->where('voucher_codes', 'like', "%". $vouchersGenerate->code ."%")
                ->first();

            if ($activityLog) {
                continue;
            } else {
                $campaignData = DB::table('campaigns')->where('id', $campaignId)->first();
                $brand = DB::table('brands')->where('id', $campaignData->brand_id)->first();
                $activityLogOpt = DB::table('activity_logs')
                    ->where('email', $vouchersGenerate->email)
                    ->whereOr('phone_number', $vouchersGenerate->phone_number)
                    ->first();

                $slugBrand = Str::slug($brand->name);
                $slugCampaign = Str::slug($campaignData->name);

                $voucherCodes = DB::table('voucher_generates')
                    ->where('claim_identifier', $vouchersGenerate->claim_identifier)
                    ->selectRaw('GROUP_CONCAT(code) as code')
                    ->groupBy('claim_identifier')
                    ->pluck('code')
                    ->first();

                $data = [
                    'ip_address' => $activityLogOpt->ip_address,
                    'browser' => $activityLogOpt->browser,
                    "email" => $vouchersGenerate->email ?? null,
                    "phone_number" => $vouchersGenerate->phone_number ?? null,
                    'brand_slug' => $slugBrand ?? null,
                    'campaign_slug' => $slugCampaign ?? null,
                    'product_id' => $vouchersGenerate->product_id ?? null,
                    'brand_id' => $campaignData->brand_id,
                    'campaign_id' => $campaignData->id,
                    'full_url' => "https://campaign.letsbuyasia.id/{$slugBrand}/{$slugCampaign}/product/{$vouchersGenerate->product_id}/voucher/view/{$vouchersGenerate->claim_identifier}",
                    'device_type' => $activityLogOpt->device_type,
                    'platform_name' => $activityLogOpt->platform_name,
                    'device_model' => $activityLogOpt->device_model,
                    'voucher_codes' => $voucherCodes,
                    'created_at' => $vouchersGenerate->claim_date,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                DB::table('activity_logs')->insert($data);
            }
        }
    }
}
