<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class WeeklySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'day_of_week',     // e.g., sunday, monday
        'start_time',      // e.g., 08:00:00
        'end_time',        // e.g., 14:00:00
        'location_en',
        'location_ar',
        'reservation_duration' // in minutes
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
