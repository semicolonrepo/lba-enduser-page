<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class LimitClaimCampaignProductRule implements ValidationRule
{

    protected $campaignId;
    protected $productId;

    public function __construct($campaignId, $productId)
    {
        $this->campaignId = $campaignId;
        $this->productId = $productId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $campaignProduct = DB::table('campaign_products')
            ->where('campaign_id', $this->campaignId)
            ->where('product_id', $this->productId)
            ->select('limit_claim')
            ->first();

        if (!$campaignProduct || $campaignProduct->limit_claim < $value) {
            $fail("The :attribute must be less than or equal to $campaignProduct->limit_claim");
        }
    }
}
