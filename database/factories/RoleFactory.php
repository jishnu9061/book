<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $slug = $this->faker->unique()->randomElement(['admin', 'agent', 'manager', 'customer']);
        
        return [
            'name' => ucfirst($slug),
            'slug' => $slug,
            'access' => json_encode([
                'ticket' => ['read' => true, 'create' => true, 'update' => true, 'delete' => true],
            ]),
        ];
    }
}

