<?php

namespace App\Http\Requests\WeeklySchedule;

use Illuminate\Foundation\Http\FormRequest;

class  StoreWeeklyScheduleRequest extends FormRequest
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
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'day_of_week' => 'required|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'reservation_duration' => 'required|integer|min:10',
            'location_en'=> 'required|string|min:3',
            'location_ar'=> 'required|string|min:3',
        ];
    }
}
