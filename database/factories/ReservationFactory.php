<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'doctor_id' => \App\Models\Doctor::factory(),
            'start_time'=> fake()->time(),
            'date' => fake()->date(),
            'location_en'=> fake()->address(),
            'location_ar'=> fake()->address(),
            'is_complete' => false,
        ];
    }
}

