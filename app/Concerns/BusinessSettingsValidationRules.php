<?php

namespace App\Concerns;

trait BusinessSettingsValidationRules
{
    protected function businessSettingsRules(): array
    {
        return [
            'currency' => $this->currencyRules(),
            'logo_url' => $this->logoUrlRules(),
        ];
    }

    protected function currencyRules(): array
    {
        return [
            'required',
            'string',
            'size:3',
            'regex:/^[A-Z]{3}$/',
            'in:' . implode(',', $this->getSupportedCurrencies()),
        ];
    }

    protected function logoUrlRules(): array
    {
        return [
            'nullable',
            'string',
            'url',
            'max:2048',
        ];
    }

    protected function getSupportedCurrencies(): array
    {
        return [
            'USD', 'EUR', 'GBP', 'JPY', 'CNY', 'AUD', 'CAD', 'CHF',
            'HKD', 'SGD', 'SEK', 'KRW', 'NOK', 'NZD', 'INR', 'MXN',
            'TWD', 'ZAR', 'BRL', 'DKK', 'PLN', 'THB', 'IDR', 'HUF',
            'CZK', 'ILS', 'CLP', 'PHP', 'AED', 'COP', 'SAR', 'MYR',
            'RON', 'ARS', 'VND', 'PKR', 'EGP', 'NGN', 'BDT', 'RUB',
        ];
    }
}
