<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'number',
        'status',
        'issue_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'notes',
        'public_token',
        'sent_at',
        'paid_at',
    ];

    public function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reminderSchedules(): HasMany
    {
        return $this->hasMany(ReminderSchedule::class);
    }

    /**
     * Mark the invoice as sent and set the sent_at timestamp.
     */
    public function markAsSent(): bool
    {
        // Cannot transition from PAID status
        if ($this->status === InvoiceStatus::PAID) {
            return false;
        }

        return $this->update([
            'status' => InvoiceStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark the invoice as paid and set the paid_at timestamp.
     * Also deletes any pending (unsent) reminder schedules.
     */
    public function markAsPaid(): bool
    {
        return DB::transaction(function () {
            // Delete pending reminder schedules (sent_at is null)
            $this->reminderSchedules()->whereNull('sent_at')->delete();

            return $this->update([
                'status' => InvoiceStatus::PAID,
                'paid_at' => now(),
            ]);
        });
    }

    /**
     * Mark the invoice as overdue.
     */
    public function markAsOverdue(): bool
    {
        // Cannot transition from PAID status
        if ($this->status === InvoiceStatus::PAID) {
            return false;
        }

        return $this->update([
            'status' => InvoiceStatus::OVERDUE,
        ]);
    }

    /**
     * Check if the invoice is in draft status.
     */
    public function isDraft(): bool
    {
        return $this->status === InvoiceStatus::DRAFT;
    }

    /**
     * Check if the invoice is in sent status.
     */
    public function isSent(): bool
    {
        return $this->status === InvoiceStatus::SENT;
    }

    /**
     * Check if the invoice is in paid status.
     */
    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    /**
     * Check if the invoice is in overdue status.
     */
    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::OVERDUE;
    }

    /**
     * Check if the invoice is past its due date.
     */
    public function isPastDue(): bool
    {
        return $this->status === InvoiceStatus::SENT
            && $this->due_date->isPast();
    }
}
