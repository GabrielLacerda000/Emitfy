<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PAID = 'paid';
    case OVERDUE = 'overdue';

    /**
     * Get human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
        };
    }

    /**
     * Get color for UI badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::PAID => 'green',
            self::OVERDUE => 'red',
        };
    }

    /**
     * Check if this is a terminal status (cannot transition to other statuses).
     */
    public function isTerminal(): bool
    {
        return $this === self::PAID;
    }

    /**
     * Check if transition to another status is allowed.
     */
    public function canTransitionTo(InvoiceStatus $status): bool
    {
        // PAID is terminal - cannot transition to any other status
        if ($this->isTerminal()) {
            return false;
        }

        return true;
    }
}
