<?php

namespace App\Models;

use Database\Factories\SubscriptionsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    /** @use HasFactory<SubscriptionsFactory> */
    use HasFactory;

    protected static function newFactory(): SubscriptionsFactory
    {
        return SubscriptionsFactory::new();
    }

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'billing_cycle',
        'current_period_end',
    ];

    public function casts(): array
    {
        return [
            'current_period_end' => 'datetime',
            'plan_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function providers(): HasMany
    {
        return $this->hasMany(SubscriptionProvider::class);
    }

    public function activeProvider(): HasOne
    {
        return $this->hasOne(SubscriptionProvider::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
