<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $doctors = Doctor::orderByDesc('reviews')->
                        paginate($perPage, ['*'], 'page', $page);

        return $this->success([
            'doctors' => $doctors->items(),
            'pagination' => [
                'current_page' => $doctors->currentPage(),
                'has_next' => $doctors->hasMorePages(),
                'has_previous' => $doctors->currentPage() > 1,
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
            ]
        ]);
    }

    public function search(Request $request)
    {
        $query = Doctor::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('specialty_en')) {
            $query->where('specialty_en', 'like', '%' . $request->specialty_en . '%');
        }

        if ($request->filled('specialty_ar')) {
            $query->where('specialty_ar', 'like', '%' . $request->specialty_ar . '%');
        }

        // Pagination (default 10 per page)
        $perPage = $request->get('per_page', 10);
        $doctors = $query->paginate($perPage);

        return $this->success([
            'doctors' => $doctors->items(),
            'pagination' => [
                'current_page' => $doctors->currentPage(),
                'has_next' => $doctors->hasMorePages(),
                'has_previous' => $doctors->currentPage() > 1,
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
                'last_page' => $doctors->lastPage(),
            ]
        ]);
    }


       public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $doctor = auth('doctor')->user();
        $doctor->name = $request->name;
        $doctor->save();

        return $this->success($doctor,'Name updated successfully');
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15|unique:users,phone,' . auth()->id(),
        ]);

        $doctor = auth('doctor')->user();
        $doctor->phone = $request->phone;
        $doctor->save();

        return $this->success($doctor,'Phone updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $doctor = auth('doctor')->user();

        if (!Hash::check($request->old_password, $doctor->password)) {
            return $this->error('Old password is incorrect',null,401);
        }

        $doctor->password = bcrypt($request->new_password);
        $doctor->save();

        return $this->success($doctor,'Password updated successfully');
    }

    public function updateSpecialty(Request $request){
        $request->validate([
            'specialty_en' => 'required|string|max:255',
            'specialty_ar' => 'required|string|max:255',
        ]);

        $doctor = auth('doctor')->user();
        $doctor->specialty_en = $request->specialty_en;
        $doctor->specialty_ar = $request->specialty_ar;
        $doctor->save();

        return $this->success($doctor,'specialty updated successfully');
    }

    public function addReview($id)
    {
        $doctor = Doctor::find($id);

        if (! $doctor) {
            return $this->error('Doctor not found', null, 404);
        }

        $doctor->increment('reviews');

        return $this->success($doctor, 'Review added successfully');
    }

    public function unReview($id)
    {
        $doctor = Doctor::find($id);

        if (! $doctor) {
            return $this->error('Doctor not found', null, 404);
        }

        if ($doctor->reviews > 0) {
            $doctor->decrement('reviews');
        }

        return $this->success($doctor, 'Review removed successfully');
    }


    public function uploadImage(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);

        $doctor = auth()->user();

        // Delete old image if it exists
        if ($doctor->photo && Storage::disk('public')->exists($doctor->photo)) {
            Storage::disk('public')->delete($doctor->photo);
        }

        // Store new image
        $path = $request->file('photo')->store('doctor_photos', 'public');

        // Update doctor profile
        $doctor->photo = $path;
        $doctor->save();

        return $this->success([
            'photo_url' => asset("image/$path"),
            'doctor' => $doctor,
        ], 'Profile image updated successfully.');
    }

    public function updateDoctor(UpdateDoctorRequest $request)
    {
        $doctor = auth('doctor')->user();

        $validated = $request->validated();

        if (isset($validated['new_password'])) {
            if (!isset($validated['old_password']) || !Hash::check($validated['old_password'], $doctor->password)) {
                return $this->error('Old password is incorrect',null,401);
            }
            $validated['password'] = bcrypt($validated['new_password']);

            unset($validated['old_password'], $validated['new_password']);
        }


        $doctor->update($validated);

        return $this->success($doctor, 'Doctor profile updated successfully');
    }

}
