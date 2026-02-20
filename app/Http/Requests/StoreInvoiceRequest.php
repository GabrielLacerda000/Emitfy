<?php

namespace App\Http\Requests;

use App\Concerns\InvoiceValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    use InvoiceValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->invoiceRules(null, $this->user()->id);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attrs = trans('validation.attributes');

        return [
            'client_id'           => $attrs['client_id'],
            'issue_date'          => $attrs['issue_date'],
            'due_date'            => $attrs['due_date'],
            'tax'                 => $attrs['tax'],
            'notes'               => $attrs['notes'],
            'status'              => $attrs['status'],
            'items'               => $attrs['items'],
            'items.*.description' => $attrs['items.*.description'],
            'items.*.quantity'    => $attrs['items.*.quantity'],
            'items.*.unit_price'  => $attrs['items.*.unit_price'],
        ];
    }
}
