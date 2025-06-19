<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeeklySchedule\StoreWeeklyScheduleRequest;
use App\Http\Requests\WeeklySchedule\UpdateWeeklyScheduleRequest;
use App\Models\WeeklySchedule;
use App\Services\WeeklyScheduleService;
use Illuminate\Support\Facades\Auth;
use Request;

class WeeklyScheduleController extends Controller
{
    public function store(StoreWeeklyScheduleRequest $request, WeeklyScheduleService $weeklyScheduleService)
    {
        $doctor = Auth::user();

        $validated = $request->validated();

        $validated["doctor_id"] = $doctor->id;


        $conflict = WeeklySchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->exists();

        if ($conflict) {
            return $this->error('You already have a schedule that overlaps with this time range.', null, 409);
        }


        $weeklySchedule = $weeklyScheduleService->create($validated);

        return $this->success($weeklySchedule, 'Weekly schedule saved successfully.', 200);
    }


    public function index()
    {
        $doctor = Auth::user();

        $schedules = WeeklySchedule::where('doctor_id', $doctor->id)->get();

        return $this->success($schedules, 'success', 200);
    }



    public function update(UpdateWeeklyScheduleRequest $request, $id)
    {
        $doctor = Auth::user();

        $schedule = WeeklySchedule::where('doctor_id', $doctor->id)->where('id', $id)->first();

        if (!$schedule) {
            return $this->error('Schedule not found or unauthorized.', null, 404);
        }

        $validated = $request->validated();



        $newStart = $validated['start_time'] ?? $schedule->start_time;
        $newEnd = $validated['end_time'] ?? $schedule->end_time;
        $newDay = $validated['day_of_week'] ?? $schedule->day_of_week;


        $conflict = WeeklySchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $newDay)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($newStart, $newEnd) {
                $query->where('start_time', '<', $newEnd)
                    ->where('end_time', '>', $newStart);
            })
            ->exists();

        if ($conflict) {
            return $this->error('This time overlaps with another schedule.', null, 409);
        }


        $schedule->update($validated);

        return $this->success($schedule, 'Schedule updated successfully.', 200);
    }

    public function destroy($id)
    {
        $doctor = Auth::user();

        $schedule = WeeklySchedule::where('doctor_id', $doctor->id)->where('id', $id)->first();

        if (!$schedule) {
            return $this->error('Schedule not found or unauthorized.', null, 404);
        }

        $schedule->delete();

        return $this->success(null, 'Schedule deleted successfully.', 200);
    }
}
