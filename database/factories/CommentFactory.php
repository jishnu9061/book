<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'comment' => $this->faker->paragraph(),
            'ticket_id' => null, // Will be set when creating
            'user_id' => null, // Will be set when creating
        ];
    }
}

