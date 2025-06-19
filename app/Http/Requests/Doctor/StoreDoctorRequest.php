<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'phone' => 'required|string|unique:doctors',
            'password' => 'required|string|min:6|confirmed',
            'specialty_en' => 'required|string|max:50',
            'specialty_ar' => 'required|string|max:50',
            'price' => 'sometimes|integer',
        ];
    }
}
