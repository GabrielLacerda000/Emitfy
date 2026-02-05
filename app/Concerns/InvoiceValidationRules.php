<?php

namespace App\Concerns;

use App\Models\Client;
use Illuminate\Validation\Rule;

trait InvoiceValidationRules
{
    /**
     * Get the validation rules used to validate invoices.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function invoiceRules(?int $invoiceId, string $userId): array
    {
        return [
            'client_id' => $this->clientIdRules($userId),
            'issue_date' => $this->issueDateRules(),
            'due_date' => $this->dueDateRules(),
            'tax' => $this->taxRules(),
            'notes' => $this->notesRules(),
            'status' => $this->statusRules(),
            'items' => $this->itemsRules(),
            'items.*.description' => $this->itemDescriptionRules(),
            'items.*.quantity' => $this->itemQuantityRules(),
            'items.*.unit_price' => $this->itemUnitPriceRules(),
        ];
    }

    /**
     * Get the validation rules used to validate client_id.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function clientIdRules(string $userId): array
    {
        return [
            'required',
            'integer',
            Rule::exists(Client::class, 'id')->where('user_id', $userId),
        ];
    }

    /**
     * Get the validation rules used to validate issue_date.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function issueDateRules(): array
    {
        return ['required', 'date'];
    }

    /**
     * Get the validation rules used to validate due_date.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function dueDateRules(): array
    {
        return ['required', 'date', 'after_or_equal:issue_date'];
    }

    /**
     * Get the validation rules used to validate tax.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function taxRules(): array
    {
        return ['required', 'numeric', 'min:0', 'max:999999.99'];
    }

    /**
     * Get the validation rules used to validate notes.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function notesRules(): array
    {
        return ['nullable', 'string', 'max:5000'];
    }

    /**
     * Get the validation rules used to validate status.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function statusRules(): array
    {
        return ['required', 'string', Rule::in(['draft', 'sent', 'paid', 'overdue'])];
    }

    /**
     * Get the validation rules used to validate items array.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function itemsRules(): array
    {
        return ['required', 'array', 'min:1', 'max:50'];
    }

    /**
     * Get the validation rules used to validate item descriptions.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function itemDescriptionRules(): array
    {
        return ['required', 'string', 'max:500'];
    }

    /**
     * Get the validation rules used to validate item quantities.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function itemQuantityRules(): array
    {
        return ['required', 'integer', 'min:1', 'max:999999'];
    }

    /**
     * Get the validation rules used to validate item unit prices.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function itemUnitPriceRules(): array
    {
        return ['required', 'numeric', 'min:0.01', 'max:999999.99'];
    }
}
