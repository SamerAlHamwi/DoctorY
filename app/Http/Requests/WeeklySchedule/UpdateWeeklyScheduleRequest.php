<?php

namespace App\Http\Requests\WeeklySchedule;

use Illuminate\Foundation\Http\FormRequest;

class  UpdateWeeklyScheduleRequest extends FormRequest
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
            'day_of_week' => 'sometimes|string|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time' => 'sometimes|date_format:H:i:s',
            'end_time' => 'sometimes|date_format:H:i:s|after:start_time',
            'reservation_duration' => 'sometimes|integer|min:10',
            'location_en'=> 'sometimes|string|min:3',
            'location_ar'=> 'sometimes|string|min:3',
        ];
    }
}
