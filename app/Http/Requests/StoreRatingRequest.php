<?php

namespace App\Http\Requests;

use App\Services\RatingService;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    protected $ratingForms;

    public function __construct(
        private RatingService $ratingService,
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
        $this->ratingForms = $this->ratingService->getFormSettingArray($this->route('brand'), $this->route('campaign'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $arrayRules = [];

        foreach ($this->ratingForms as $key => $form) {
            if ($form->type === 'checkbox-group') {
                $arrayRules[$form->name] = 'required|array';
            }

            if ($form->type === 'starRating') {
                $arrayRules[$form->name] = 'required|numeric';
            }

            if ($form->type === 'select') {
                $arrayRules[$form->name] = 'required|string';
            }

            if ($form->type === 'text') {
                $arrayRules[$form->name] = 'required|string';
            }

            if ($form->type === 'textarea') {
                $arrayRules[$form->name] = 'required|string';
            }
        }

        return $arrayRules;
    }
}
