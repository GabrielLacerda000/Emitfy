<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    /** @use HasFactory<\Database\Factories\SubscriptionsFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan',
        'provider',
        'provider_subscription_id',
        'status',
        'current_period_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
