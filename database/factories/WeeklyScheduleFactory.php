<?php

namespace Database\Factories;

use App\Models\WeeklySchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeeklyScheduleFactory extends Factory
{
    protected $model = WeeklySchedule::class;

    public function definition(): array
    {
        return [
            'doctor_id' => \App\Models\Doctor::factory(),
            'start_time'=> fake()->time(),
            'end_time'=> fake()->time(),
            'day_of_week' => fake()->date(),
            'location_en'=> fake()->address(),
            'location_ar'=> fake()->address(),
            'reservation_duration'=> fake()->numberBetween(10,120),
            'is_complete' => false,
        ];
    }
}

