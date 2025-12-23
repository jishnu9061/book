<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement(['active', 'inactive', 'resolved', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'department' => $this->faker->randomElement(['general', 'technical', 'billing', 'support']),
            'source' => $this->faker->randomElement(['website', 'email', 'phone', 'chat']),
            'metadata' => [
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'os' => $this->faker->randomElement(['Windows', 'macOS', 'Linux', 'iOS', 'Android']),
                'ip' => $this->faker->ipv4(),
            ],
            'last_activity' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'contact_id' => null, // Will be set in the seeder
            'slug' => $this->faker->unique()->slug(),
        ];
    }
}


