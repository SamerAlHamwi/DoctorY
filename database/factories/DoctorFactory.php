<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{

    protected $model = Doctor::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     public function definition()
    {
        return [
            'name' => fake()->name(),
            'phone'=> fake()->phoneNumber(),
            'password' => bcrypt('000000'), // password
            'specialty_en' => 'dentist',
            'specialty_ar' => 'طبيب اسنان',
            'photo' => 'img1.jpg',
            'reviews' => 20,
            'price'=> 10000,
        ];
    }
}
