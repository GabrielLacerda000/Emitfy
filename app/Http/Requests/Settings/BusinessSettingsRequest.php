<?php

namespace App\Http\Requests\Settings;

use App\Concerns\BusinessSettingsValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BusinessSettingsRequest extends FormRequest
{
    use BusinessSettingsValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->businessSettingsRules();
    }
}
