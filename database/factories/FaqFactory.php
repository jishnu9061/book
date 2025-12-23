<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(4),
            'details' => $this->faker->paragraphs(2, true),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}


