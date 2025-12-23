<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->paragraph(3),
            'message_type' => $this->faker->randomElement(['text', 'image', 'file', 'system']),
            'is_read' => $this->faker->randomElement([0, 1]),
            'read_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'conversation_id' => null, // Will be set in the seeder
            'user_id' => null, // Will be set in the seeder
            'contact_id' => null, // Will be set in the seeder
            'guid' => null, // Will be set in the seeder
        ];
    }
}


