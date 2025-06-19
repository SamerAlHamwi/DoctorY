<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\DoctorService;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\LoginDoctorRequest;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class DoctorAuthController extends Controller
{

    public function register(StoreDoctorRequest $request, DoctorService $doctorService)
    {
        // Validate request
        $validator = $request->validated();

        // Create Doctor
        $doctor = $doctorService->create($validator);

        // Generate JWT token
        $token = JWTAuth::fromUser($doctor);


        return $this->success([
            'token' => $token,
            'doctor' => $doctor
        ], "doctor registered successfully", 201);
    }


    public function login(LoginDoctorRequest $request)
    {
        $credentials = $request->only('phone', 'password');

        try {
            // Set the guard for 'doctor'
            if (!$token = Auth::guard('doctor')->attempt($credentials)) {
                return $this->error('Invalid phone or password', null, 401);
            }
        } catch (JWTException $e) {
            return $this->error('Could not create token', null, 500);
        }

        return $this->success([
            'token' => $token,
            'doctor' => Auth::guard('doctor')->user()
        ], 'Login successful', 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success(null, "doctor logged out successfully", 200);

        } catch (JWTException $e) {
            return $this->error('Failed to logout, please try again', null, 500);
        }
    }
}
