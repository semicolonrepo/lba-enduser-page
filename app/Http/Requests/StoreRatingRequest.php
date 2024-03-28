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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->ratingForms as $key => $form) {
            $messages[$form->name . '.required'] = "The $form->label field is required.";
            $messages[$form->name . '.numeric'] = "The $form->label field must be a number.";
            $messages[$form->name . '.string'] = "The $form->label field must be a string.";
            $messages[$form->name . '.array'] = "The $form->label field must be a array.";
        }

        return $messages;
    }
}
