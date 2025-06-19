<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Models\Doctor;
use App\Models\reservation;
use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class reservationController extends Controller
{

    public function index(){
        $reservations=reservation::get();
        return $this->success([
            'reservations' => $reservations,
        ]);
    }
    /////user
    public function userReservations(Request $request)
    {
        $user = auth('api')->user();

        // Handle pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Query reservations for user
        $query = Reservation::with('doctor')
            ->where('user_id', $user->id);

        // Filter by is_complete if provided
        if ($request->has('is_complete')) {
            $query->where('is_complete', $request->boolean('is_complete'));
        }

        $reservations = $query->paginate($perPage, ['*'], 'page', $page);

        // Customize response to exclude user and show date data only
        $reservations->getCollection()->transform(function ($reservation) {
            $reservation->makeHidden(['user']);
            unset($reservation->user_id);
            return $reservation;
        });

        return $this->success([
            'reservations' => $reservations->items(),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'has_next' => $reservations->hasMorePages(),
                'has_previous' => $reservations->currentPage() > 1,
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
                'last_page' => $reservations->lastPage(),
            ]
        ]);
    }

    public function availableForDoctor(Request $request, Doctor $doctor)
    {
        // Handle pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $reservations = Reservation::where('doctor_id', $doctor->id)
            ->whereNull('user_id')
            ->where('is_complete', false)
            ->orderBy('date')
            ->paginate($perPage, ['*'],'page', $page);

        return $this->success([
            'reservations' => $reservations->items(),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'has_next' => $reservations->hasMorePages(),
                'has_previous' => $reservations->currentPage() > 1,
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
                'last_page' => $reservations->lastPage(),
            ]
        ]);
    }


   /* public function assignToUser($id)
    {
        $user = auth()->user();
        $reservation = Reservation::find($id);

        if (! $reservation) {
            return $this->error('Reservation not found', null, 404);
        }

        if ($reservation->user_id) {
            return $this->error('Reservation already assigned to a user', null, 403);
        }

        $reservation->user_id = $user->id;
        $reservation->save();

        return $this->success($reservation, 'Reservation successfully assigned to user');
    }*/


    public function bookSlot(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i:s',
        ]);

        $user = auth('api')->user();

        // ✅ Limit check: only 3 active reservations allowed
        $activeReservationsCount = Reservation::where('user_id', $user->id)
            ->where('is_complete', false)
            ->count();

        if ($activeReservationsCount >= 3) {
            return $this->error('You can only have up to 3 active reservations.',null,403);
        }

        $doctorId = $request->doctor_id;
        $date = $request->date;
        $startTime = $request->start_time;

        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        // 1. Validate if this time is in the doctor's weekly schedule
        $schedule = WeeklySchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $startTime)
            ->where('end_time', '>', $startTime)
            ->first();

        if (! $schedule) {
            return $this->error('Selected time is not available in doctor schedule',null,422);
        }

        // 2. Ensure no existing reservation exists
        $exists = Reservation::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('start_time', $startTime)
            ->exists();

        if ($exists) {
            return $this->error('This slot is already booked.',null,422);
        }

        // 3. Create reservation
        $reservation = Reservation::create([
            'doctor_id' => $doctorId,
            'user_id' => $user->id,
            'date' => $date,
            'start_time' => $startTime,
            'is_complete' => false,
            'location_en' => $schedule->location_en,
            'location_ar' => $schedule->location_ar,
        ]);

        return $this->success($reservation,'Reservation created successfully.',201);
    }


    public function cancel($id)
    {
        $user = auth()->user();
        $reservation = Reservation::find($id);

        if (! $reservation) {
            return $this->error('Reservation not found', null, 404);
        }

        if ($reservation->user_id !== $user->id) {
            return $this->error('Unauthorized to cancel this reservation', null, 403);
        }

        if ($reservation->is_complete) {
            return $this->error('Completed reservation cannot be canceled', null, 400);
        }

        $reservation->delete();

        return $this->success(null, 'Reservation canceled successfully');
    }



    ////doctor
    public function doctorReservations(Request $request)
    {
        $doctor = auth('doctor')->user();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = Reservation::with('user')
            ->where('doctor_id', $doctor->id);

        if ($request->has('is_complete')) {
            $query->where('is_complete', $request->boolean('is_complete'));
        }

        $reservations = $query->paginate($perPage, ['*'], 'page', $page);

        // Customize response to exclude doctor and show date
        $reservations->getCollection()->transform(function ($reservation) {
            $reservation->makeHidden(['doctor']);
            unset($reservation->doctor_id);
            return $reservation;
        });

        return $this->success([
            'reservations' => $reservations->items(),
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'has_next' => $reservations->hasMorePages(),
                'has_previous' => $reservations->currentPage() > 1,
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
                'last_page' => $reservations->lastPage(),
            ]
        ]);
    }

    public function deleteReservation($id)
    {
        $doctor = auth()->user(); // Doctor is authenticated

        $reservation = Reservation::find($id);

        if (! $reservation) {
            return $this->error('Reservation not found', null, 404);
        }

        if ($reservation->doctor_id !== $doctor->id) {
            return $this->error('You do not have permission to delete this reservation.', null, 403);
        }

        $reservation->delete();

        return $this->success(null, 'Reservation deleted successfully');
    }



    public function bulkCreateReservations(StoreReservationRequest $request)
    {

        $request->validated();

        $doctor = auth()->user();

        // Combine date + time into datetime
        $start = Carbon::parse("{$request->start_date} {$request->start_time}");
        $end = Carbon::parse("{$request->end_date} {$request->end_time}");
        $duration = $request->reservation_duration;

        if ($end->lessThanOrEqualTo($start)) {
            return $this->error('End time must be after start time');
        }

        $reservations = [];

        while ($start->copy()->addMinutes($duration)->lessThanOrEqualTo($end)) {
            $reservations[] = Reservation::create([
                'doctor_id' => $doctor->id,
                'start_time' => $start->format('H:i:s'),
                'date' => $start->format('Y-m-d'),
                'location_en'=> $request->location_en,
                'location_ar'=> $request->location_ar,
                'is_complete' => false,
            ]);

            $start->addMinutes($duration);
        }
        return $this->success($reservations, 'Reservations created successfully');
    }

}

