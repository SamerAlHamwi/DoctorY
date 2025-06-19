<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth('api')->user();
        $user->name = $request->name;
        $user->save();

        return $this->success($user,'Name updated successfully');
    }

    public function updatePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:15|unique:users,phone,' . auth()->id(),
        ]);

        $user = auth('api')->user();
        $user->phone = $request->phone;
        $user->save();

        return $this->success($user,'Phone updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth('api')->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return $this->error('Old password is incorrect',null,401);
        }

        $user->password = bcrypt($request->new_password);
        $user->save();

        return $this->success($user,'Password updated successfully');
    }


    public function updateUser(UpdateUserRequest $request)
    {
        $user = auth('api')->user();

        $validated = $request->validated();


        if (isset($validated['new_password'])) {
            if (!isset($validated['old_password']) || !Hash::check($validated['old_password'], $user->password)) {
                return $this->error('Old password is incorrect',null,401);
            }
            $validated['password'] = bcrypt($validated['new_password']);
        }

        unset($validated['old_password'], $validated['new_password']);

        $user->update($validated);

        return $this->success($user, 'User updated successfully');
    }



}
