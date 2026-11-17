<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'paid', 'failed', 'refunded']);

        return [
            'order_id' => Order::factory(),
            'method' => fake()->randomElement(['card', 'cash', 'online']),
            'amount' => fake()->randomFloat(2, 50, 5000),
            'status' => $status,
            'transaction_id' => Str::upper(Str::random(16)),
            'paid_at' => $status === 'paid' ? now()->subDays(fake()->numberBetween(0, 30)) : null,
        ];
    }
}
