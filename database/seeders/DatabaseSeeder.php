<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         \App\Models\User::factory()->create([
             'name' => 'Test User',
             'phone' => '0987237799',
             'password'=>bcrypt('000000'),
             'rule'=> 'user',
         ]);
         \App\Models\User::factory()->create([
             'name' => 'Test User2',
             'phone' => '0987237798',
             'password'=>bcrypt('000000'),
             'rule'=> 'user',
         ]);
         \App\Models\Doctor::factory()->create([
             'name' => 'Test User2',
             'phone' => '0987237798',
             'password'=>bcrypt('000000'),
             'specialty_en'=> 'dentist',
             'specialty_ar'=> 'طبيب اسنان',
             'photo'=> 'image.jpeg',
             'reviews'=> 20,
             'price'=> 10000,
         ]);
         \App\Models\reservation::factory()->create([
             'user_id' => 1,
             'doctor_id' => 2,
             'start_time'=> '16:46:33',
             'date'=> '2025-05-19',
             'location_en'=> 'daraa',
             'location_ar'=> 'درعا',
             'is_complete'=> true,
         ]);
         \App\Models\Article::factory()->create([
            'title_en' => 'title',
            'title_ar'=> 'عنوان',
            'body_en' => 'body.....',
            'body_ar'=> 'body.....',
            'author'=> 'ahmad',
            'photo'=> 'img1.jpg',
         ]);
    }
}
