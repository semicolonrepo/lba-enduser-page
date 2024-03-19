<?php

namespace App\Http\Requests;

use App\Services\CampaignProductService;
use App\Services\CampaignService;
use Illuminate\Foundation\Http\FormRequest;

class ClaimVoucherRequest extends FormRequest
{
    protected $campaignProductForms;

    public function __construct(
        private CampaignService $campaignService,
        private CampaignProductService $campaignProductService,
    ) {}

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data before validation.
     */
    protected function prepareForValidation()
    {
        $campaignData = $this->campaignService->getCampaign($this->route('brand'), $this->route('campaign'));
        $this->campaignProductForms = $this->campaignProductService->getFormSettingArray($campaignData->id, $this->route('productId'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $arrayRules = [];
        $arrayRules['partner'] = 'required|string';
        $arrayRules['utm_source'] = 'nullable|string';

        if (!empty($this->campaignProductForms)) {
            foreach ($this->campaignProductForms as $key => $form) {
                if ($form->type === 'checkbox-group') {
                    $arrayRules[$form->name] = 'required|array';
                }

                if ($form->type === 'select') {
                    $arrayRules[$form->name] = 'required|string';
                }

                if ($form->type === 'text') {
                    $arrayRules[$form->name] = 'required|string';
                    $arrayRules[$form->name] .= ($form->subtype === 'email') ? "|email" : '';
                }

                if ($form->type === 'textarea') {
                    $arrayRules[$form->name] = 'required|string';
                }

                if ($form->type === 'number') {
                    $arrayRules[$form->name] = 'required|numeric';
                    $arrayRules[$form->name] .= isset($form->min) ? "|gte:$form->min" : '';
                    $arrayRules[$form->name] .= isset($form->max) ? "|lte:$form->max" : '';
                }
            }
        }

        return $arrayRules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->campaignProductForms as $key => $form) {
            $messages[$form->name . '.required'] = "The $form->label field is required.";
            $messages[$form->name . '.numeric'] = "The $form->label field must be a number.";
            $messages[$form->name . '.string'] = "The $form->label field must be a string.";
            $messages[$form->name . '.array'] = "The $form->label field must be a array.";
            $messages[$form->name . '.gte'] = "The $form->label field must be must be greater than or equal to :gte";
            $messages[$form->name . '.lte'] = "The $form->label field must be must be less than or equal to :lte.";
        }

        return $messages;
    }
}
