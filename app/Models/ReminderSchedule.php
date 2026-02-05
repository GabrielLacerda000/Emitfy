<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\ReminderScheduleFactory> */
    use HasFactory;

    protected $table = 'reminder_schedules';

    protected $fillable = [
        'invoice_id',
        'type',
        'offset_days',
        'sent_at',
    ];

    public function casts(): array
    {
        return [
            'offset_days' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
