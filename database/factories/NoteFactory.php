<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'details' => $this->faker->paragraphs(2, true),
            'color' => $this->faker->randomElement(['#FF5733', '#33FF57', '#3357FF', '#FF33F5', '#F5FF33']),
            'user_id' => null, // Will be set in the seeder
        ];
    }
}


