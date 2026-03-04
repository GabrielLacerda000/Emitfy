<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'provider',
        'provider_customer_id',
        'provider_subscription_id',
        'provider_payment_id',
        'status',
        'metadata',
    ];

    public function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
