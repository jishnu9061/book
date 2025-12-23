<?php

namespace Database\Factories;

use App\Models\FrontPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FrontPage>
 */
class FrontPageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FrontPage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(),
            'is_active' => 1,
            'html' => [
                'title' => $this->faker->sentence(5),
                'content' => '<p>' . $this->faker->paragraphs(3, '</p><p>') . '</p>'
            ],
        ];
    }
}
