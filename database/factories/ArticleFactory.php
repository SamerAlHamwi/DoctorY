<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Integer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title_en' => fake()->title(),
            'title_ar'=> fake()->title(),
            'body_en' => Str::random(500),
            'body_ar'=> Str::random(500),
            'author'=> $this->faker->name(),
            'photo'=> 'img1.jpg',
        ];
    }
}
