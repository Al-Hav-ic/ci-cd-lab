<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'bonus_points' => fake()->numberBetween(0, 5000),
            'loyalty_level' => fake()->randomElement(['basic', 'silver', 'gold']),
            'newsletter_opt_in' => fake()->boolean(60),
        ];
    }
}
