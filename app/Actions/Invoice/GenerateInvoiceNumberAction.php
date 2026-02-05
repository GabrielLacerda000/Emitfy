<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GenerateInvoiceNumberAction
{
    /**
     * Generate a unique invoice number for a user.
     *
     * Format: INV-YYYYMM-XXX (e.g., INV-202602-001)
     */
    public function __invoke(User $user, string $issueDate): string
    {
        return DB::transaction(function () use ($user, $issueDate) {
            $date = \Carbon\Carbon::parse($issueDate);
            $yearMonth = $date->format('Ym');
            $prefix = "INV-{$yearMonth}-";

            // Get the last invoice for this user in this month with row locking
            $lastInvoice = Invoice::where('user_id', $user->id)
                ->where('number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderBy('number', 'desc')
                ->first();

            if ($lastInvoice) {
                // Extract the sequence number and increment
                $lastSequence = (int) substr($lastInvoice->number, -3);
                $sequence = $lastSequence + 1;
            } else {
                // First invoice of the month
                $sequence = 1;
            }

            // Pad to 3 digits
            return $prefix.str_pad($sequence, 3, '0', STR_PAD_LEFT);
        });
    }
}
