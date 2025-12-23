<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        $statuses = ['Open', 'In Progress', 'Pending', 'Resolved', 'Closed'];
        $name = $this->faker->randomElement($statuses);
        
        return [
            'name' => $name,
            'slug' => strtolower(str_replace(' ', '_', $name)),
        ];
    }
}
