<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RatingService
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    public function store($brandSlug, $campaignSlug, $voucherCode, $formRequest) {
        $formSetting = $this->getFormSettingArray($brandSlug, $campaignSlug);
        $sanitizeArray = $this->sanitizeArray($formSetting, $formRequest, $voucherCode);
        $sanitizeJson = $this->sanitizeJson($formSetting, $formRequest);

        DB::transaction(function () use ($voucherCode, $sanitizeArray, $sanitizeJson) {
            DB::table('voucher_generates')
                ->where('code', $voucherCode)
                ->where('is_has_rating', 0)->update([
                    'is_has_rating' => 1,
                    'rating_json' => $sanitizeJson,
                ]);

            DB::table('ratings')->insert($sanitizeArray);
        });
    }

    public function getFormSettingArray($brandSlug, $campaignSlug) {
        $campaign = $this->campaignService->getCampaign($brandSlug, $campaignSlug);
        return json_decode($campaign->formbuilder_rating_json);
    }

    public function sanitizeArray(Array $formSettings, Array $formRequests, String $voucherCode) {
        $voucher = $this->voucherService->showVoucher($voucherCode);
        $resultArray = [];

        foreach ($formSettings as $key => $form) {
            $question = $form->label;
            $answer = null;

            if (array_key_exists($form->name, $formRequests)) {
                $answer = is_array($formRequests[$form->name]) ? implode(',', $formRequests[$form->name]) : $formRequests[$form->name];
            }

            $resultArray[] = [
                'voucher_generate_id' => $voucher->id,
                'campaign_id' => $voucher->campaign_id,
                'voucher_generate_code' => $voucherCode,
                'question' => $question,
                'answer' => $answer,
                'phone_number' => $voucher->phone_number,
                'email' => $voucher->email,
                'type' => $form->type,
                'form_name_identifier' => $form->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

       return $resultArray;
    }

    public function sanitizeJson(Array $formSettings, Array $formRequests) {
        foreach ($formSettings as $key => $form) {
            if (array_key_exists($form->name, $formRequests)) {
                if ($form->type === 'checkbox-group') {
                    foreach ($form->values as $item) {
                        $item->selected = false;

                        if (in_array($item->value, $formRequests[$form->name])) {
                            $item->selected = true;
                        }
                    }
                } else if ($form->type === 'select') {
                    foreach ($form->values as $item) {
                        $item->selected = false;

                        if ($item->value === $formRequests[$form->name]) {
                            $item->selected = true;
                        }
                    }
                } else {
                    $form->value = $formRequests[$form->name];
                }
            }
        }

        return json_encode($formSettings);
    }
}
