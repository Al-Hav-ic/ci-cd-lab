<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Наповнює БД ієрархією пов'язаних сутностей:
     * категорії → товари, покупці → кошики/замовлення → позиції/платежі,
     * замовлення ↔ знижки (M:M), відгуки.
     */
    public function run(): void
    {
        // 1. 5 категорій, у кожній — 8 товарів (зв'язок 1:M).
        Category::factory()
            ->count(5)
            ->has(Product::factory()->count(8))
            ->create();

        $products = Product::query()->get();

        // 2. Довідник знижок для зв'язку M:M.
        $discounts = Discount::factory()->count(10)->create();

        // 3. Покупці (кожен отримує профіль і обліковий запис).
        $customers = Customer::factory()->count(15)->create();

        // 4. Кошики з позиціями.
        foreach ($customers->take(5) as $customer) {
            $cart = Cart::factory()->create(['customer_id' => $customer->id]);

            foreach ($products->random(3) as $product) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => fake()->numberBetween(1, 3),
                    'unit_price' => $product->price,
                ]);
            }
        }

        // 5. Замовлення з позиціями, знижками та платежем.
        foreach ($customers as $customer) {
            $orders = Order::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['customer_id' => $customer->id]);

            foreach ($orders as $order) {
                $subtotal = 0.0;

                foreach ($products->random(fake()->numberBetween(1, 4)) as $product) {
                    $quantity = fake()->numberBetween(1, 3);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                    ]);

                    $subtotal += $quantity * $product->price;
                }

                $count = fake()->numberBetween(0, 2);
                $orderDiscounts = $count === 0 ? collect() : $discounts->random($count);
                $order->discounts()->sync($orderDiscounts->pluck('id')->all());

                $percent = min(100.0, (float) $orderDiscounts->sum('percent'));
                $discountAmount = round($subtotal * $percent / 100, 2);

                $order->update([
                    'total_amount' => round($subtotal - $discountAmount, 2),
                    'discount_amount' => $discountAmount,
                ]);

                Payment::factory()->create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                ]);
            }
        }

        // 6. Відгуки: унікальні пари product/customer.
        foreach ($products->take(15)->values() as $index => $product) {
            foreach ($customers->slice($index % 10, 3) as $customer) {
                Review::factory()->create([
                    'product_id' => $product->id,
                    'customer_id' => $customer->id,
                    'is_approved' => $index % 3 !== 0,
                ]);
            }
        }
    }
}
