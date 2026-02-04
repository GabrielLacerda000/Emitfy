<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderSchedules extends Model
{
    /** @use HasFactory<\Database\Factories\ReminderSchedulesFactory> */
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'type',
        'offset_days',
        'sent_at',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
