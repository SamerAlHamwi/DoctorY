<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\reservation;
use App\Models\WeeklySchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DoctorAvailabilityController extends Controller
{
    public function index(Request $request ,Doctor $doctor)
    {
        $week = $request->input('week', 1);

        $startDate = Carbon::today();
        $endDate = Carbon::today()->addWeeks($week); // Show availability for next 2 weeks

        $schedules = WeeklySchedule::where('doctor_id', $doctor->id)->get();

        $availability = [];

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayName = strtolower($date->format('l'));

            $daySchedules = $schedules->where('day_of_week', $dayName);

            foreach ($daySchedules as $schedule) {
                $start = Carbon::parse("{$date->format('Y-m-d')} {$schedule->start_time}");
                $end = Carbon::parse("{$date->format('Y-m-d')} {$schedule->end_time}");

                while ($start->copy()->addMinutes($schedule->reservation_duration)->lessThanOrEqualTo($end)) {
                    $slotTime = $start->format('Y-m-d H:i:s');

                    $isBooked = reservation::where('doctor_id', $doctor->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->where('start_time', $start->format('H:i:s'))
                        ->exists();

                    if (! $isBooked) {
                        $availability[] = [
                            'slot' => $slotTime,
                            'location_en' => $schedule->location_en,
                            'location_ar' => $schedule->location_ar,
                        ];
                    }

                    $start->addMinutes($schedule->reservation_duration);
                }
            }
        }

        return $this->success($availability);
    }
}
