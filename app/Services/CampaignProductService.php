<?php

namespace App\Services;

use App\Models\CampaignProductsModel;
use Illuminate\Support\Facades\DB;

class CampaignProductService
{
    public function __construct(
        private CampaignService $campaignService,
        private VoucherService $voucherService,
    ) {}

    public function showProduct($campaignId, $productId) {
        $product = CampaignProductsModel::select('products.*', 'deal_offers.name as type', 'campaign_products.normal_price', 'campaign_products.subsidi_price', 'campaign_products.questionares_json')
            ->join('products', 'campaign_products.product_id', '=', 'products.id')
            ->join('deal_offers', 'campaign_products.deal_offer_id', '=', 'deal_offers.id')
            ->where('campaign_products.campaign_id', $campaignId)
            ->where('campaign_products.product_id', $productId)
            ->first();

        return $product;
    }

    public function getFormSettingArray($campaignId, $productId) {
        $campaignProduct = $this->showProduct($campaignId, $productId);

        return json_decode($campaignProduct->questionares_json);
    }

    public function sanitizeFormArray(Array $formSettings, Array $formRequests, String $voucherCode) {
        $voucher = $this->voucherService->showVoucher($voucherCode);
        $resultArray = [];

        foreach ($formSettings as $key => $form) {
            $question = $form->label;
            $answer = null;

            if (array_key_exists($form->name, $formRequests)) {
                $answer = is_array($formRequests[$form->name]) ? implode(',', $formRequests[$form->name]) : $formRequests[$form->name];
            }

            $resultArray[] = [
                'product_id' => $voucher->product_id,
                'campaign_id' => $voucher->campaign_id,
                'voucher_generate_id' => $voucher->id,
                'voucher_generate_code' => $voucher->code,
                'question' => $question,
                'answer' => $answer,
                'type' => $form->type,
                'phone_number' => $voucher->phone_number,
                'email' => $voucher->email,
                'form_name_identifier' => $form->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

       return $resultArray;
    }

    public function sanitizeFormJson(Array $formSettings, Array $formRequests) {
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
