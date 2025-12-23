<?php

namespace Database\Factories;

use App\Models\KnowledgeBase;
use Illuminate\Database\Eloquent\Factories\Factory;

class KnowledgeBaseFactory extends Factory
{
    protected $model = KnowledgeBase::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'details' => $this->faker->paragraphs(3, true),
            'type_id' => null, // Will be set in the seeder if needed
        ];
    }
}


