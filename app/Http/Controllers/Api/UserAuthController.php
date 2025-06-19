<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\LoginUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAuthController extends Controller
{

    public function register(StoreUserRequest $request, UserService $userService)
    {
        // Validate request
        $validator = $request->validated();

        // Create user
        $user = $userService->create($validator);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);


        return $this->success([
            'token' => $token,
            'user' => $user
        ],"User registered successfully",201);
    }



public function login(LoginUserRequest $request)
    {
        // Validate request
        $validator = $request->validated();

        // Get only phone and password
        $credentials = $request->only('phone', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->error(
                    'Invalid phone or password',null,401
                    );
            }
        } catch (JWTException $e) {
            return $this->error(
                    'Could not create token',null,500);
        }

        // Authenticated successfully
        return $this->success(
            ['token' => $token,
            'user' => Auth::user()],
            'Login successful',200);
    }

public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success(null,"User logged out successfully",200);

        } catch (JWTException $e) {
            return $this->error('Failed to logout, please try again',null,500);
        }
    }
}
