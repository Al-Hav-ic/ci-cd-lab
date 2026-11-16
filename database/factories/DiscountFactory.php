<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('SAVE-####'),
            'percent' => fake()->randomFloat(2, 5, 30),
            'valid_from' => now()->subDays(10),
            'valid_to' => now()->addDays(fake()->numberBetween(10, 90)),
            'usage_limit' => fake()->numberBetween(10, 500),
            'used_count' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_from' => now()->subDays(60),
            'valid_to' => now()->subDays(1),
            'is_active' => false,
        ]);
    }
}
