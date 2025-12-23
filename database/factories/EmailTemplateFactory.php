<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'details' => $this->faker->paragraph(),
            'language' => 'en',
            'html' => '<html><body><h1>' . $this->faker->sentence() . '</h1><p>' . $this->faker->paragraph() . '</p></body></html>',
        ];
    }
}



