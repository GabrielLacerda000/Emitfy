<?php

namespace App\Gates;

use App\Models\User;

class InvoiceGate
{
    public static function hasActiveSubscription(User $user): bool
    {
        if ($user->bypass_billing) {
            return true;
        }

        return $user->activeSubscription()->exists();
    }

    public static function canSendInvoice(User $user): bool
    {
        return self::hasActiveSubscription($user);
    }

    public static function canViewPdf(User $user): bool
    {
        return self::hasActiveSubscription($user);
    }

    public static function canChangeStatus(User $user): bool
    {
        return self::hasActiveSubscription($user);
    }
}
