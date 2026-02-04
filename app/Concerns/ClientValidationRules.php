<?php

namespace App\Concerns;

use App\Models\Client;
use Illuminate\Validation\Rule;

trait ClientValidationRules
{
    /**
     * Get the validation rules used to validate clients.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>>
     */
    protected function clientRules(?string $clientId = null, string $userId): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($clientId, $userId),
            'company_name' => $this->companyNameRules(),
            'notes' => $this->notesRules(),
        ];
    }

    /**
     * Get the validation rules used to validate client names.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate client emails.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function emailRules(?string $clientId, string $userId): array
    {
        $uniqueRule = Rule::unique(Client::class)
            ->where('user_id', $userId);

        if ($clientId !== null) {
            $uniqueRule->ignore($clientId);
        }

        return ['required', 'string', 'email', 'max:255', $uniqueRule];
    }

    /**
     * Get the validation rules used to validate client company names.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function companyNameRules(): array
    {
        return ['nullable', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate client notes.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function notesRules(): array
    {
        return ['nullable', 'string', 'max:5000'];
    }
}
