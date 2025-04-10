<?php

namespace Database\Factories;

use App\Models\Multimedia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MultimediaFactory extends Factory
{
    protected $model = Multimedia::class;

    public function definition(): array
    {
        return [
            'title'     => $this->faker->sentence(3),
            'file_path' => '/storage/multimedia/' . $this->faker->uuid . '.jpg',
            'type'      => $this->faker->randomElement(['image', 'video']),
            'user_id'   => User::factory(),
        ];
    }
}
