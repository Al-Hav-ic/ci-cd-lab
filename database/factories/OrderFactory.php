<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->bothify('ORD-####-????'),
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'completed', 'cancelled']),
            'total_amount' => 0,
            'discount_amount' => 0,
            'delivery_address' => fake()->address(),
        ];
    }
}
