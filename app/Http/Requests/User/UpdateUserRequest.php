<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class  UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:15|unique:users,phone,' . auth()->id(),
            'old_password' => 'sometimes|string',
            'new_password' => 'sometimes|string|min:5|confirmed',
        ];
    }
}
