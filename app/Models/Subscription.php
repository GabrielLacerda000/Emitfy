<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionsFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
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
}
