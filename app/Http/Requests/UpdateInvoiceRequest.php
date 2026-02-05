<?php

namespace App\Http\Requests;

use App\Concerns\InvoiceValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
        return $this->invoiceRules($this->route('invoice')->id, $this->user()->id);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_id' => 'client',
            'issue_date' => 'issue date',
            'due_date' => 'due date',
            'tax' => 'tax amount',
            'notes' => 'notes',
            'status' => 'status',
            'items' => 'items',
            'items.*.description' => 'item description',
            'items.*.quantity' => 'item quantity',
            'items.*.unit_price' => 'item unit price',
        ];
    }
}
