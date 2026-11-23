<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тестування бухгалтерії замовлень та збереження історії (лаб. 7, зауваження рев'ю).
 */
class OrderAccountingTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_total_respects_item_level_discount(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100.0, 'is_active' => true]);
        $discount = Discount::factory()->create(['percent' => 10]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);

        // Позиція: 2 x 100,00 зі знижкою 10% => 180,00.
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 100.0,
            'discount' => 10,
        ]);
        $order->discounts()->sync([$discount->id]);

        // /manage/* закритий auth — і для оновлення замовлення.
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put("/manage/orders/{$order->id}", [
                'customer_id' => $customer->id,
                'status' => 'paid',
                'discount_ids' => [$discount->id],
                'delivery_address' => 'Купівницька вулика, 12',
            ])
            ->assertRedirect();

        // total = 180 - (180 * 10 / 100) = 162; discount_amount = 18.
        $this->assertEquals(162.0, (float) $order->refresh()->total_amount);
        $this->assertEquals(18.0, (float) $order->refresh()->discount_amount);
    }
}
