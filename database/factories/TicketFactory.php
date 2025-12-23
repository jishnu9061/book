<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'uid' => 'TKT-' . $this->faker->unique()->numberBetween(100000, 999999),
            'subject' => $this->faker->sentence(4),
            'details' => $this->faker->paragraphs(3, true),
            'email' => $this->faker->email(),
            'created_by' => $this->faker->name(),
            'location' => $this->faker->city(),
            'client_type' => $this->faker->randomElement([0, 1]),
            'open' => now(),
            'due' => $this->faker->optional()->dateTimeBetween('now', '+30 days'),
            'user_id' => null, // Will be set in the seeder
            'contact_id' => null, // Will be set in the seeder
            'status_id' => null, // Will be set in the seeder
            'priority_id' => null, // Will be set in the seeder
            'department_id' => null, // Will be set in the seeder
            'category_id' => null, // Will be set in the seeder
            'type_id' => null, // Will be set in the seeder
            'assigned_to' => null, // Will be set in the seeder
        ];
    }
}


