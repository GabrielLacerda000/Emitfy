<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'provider',
        'external_payment_id',
        'amount',
        'status',
        'paid_at',
        'raw_payload',
        'pix_code',
        'qr_code_base64',
        'expires_at',
    ];

    public function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'paid_at'     => 'datetime',
            'expires_at'  => 'datetime',
            'raw_payload' => 'array',
            'status'      => PaymentStatus::class,
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
