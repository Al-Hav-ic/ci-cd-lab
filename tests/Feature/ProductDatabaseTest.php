<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_products_with_relations(): void
    {
        $category = Category::factory()->create(['name' => 'Смартфони']);
        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'iPhone 15',
            'is_active' => true,
        ]);

        $response = $this->getJson('/catalog');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'iPhone 15'])
            ->assertJsonFragment(['name' => 'Смартфони']);
    }

    public function test_can_create_product_with_category_in_database(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Бездротові навушники',
            'sku' => 'AUDIO-0099',
            'price' => 3200.50,
            'stock_qty' => 7,
            'is_active' => true,
        ];

        $response = $this->withHeaders(['X-App-Client' => 'TestRunner'])
            ->postJson('/catalog', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('products', [
            'sku' => 'AUDIO-0099',
            'name' => 'Бездротові навушники',
            'category_id' => $category->id,
        ]);
    }

    public function test_can_sync_discounts_to_order_in_pivot_table(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100.0]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 100.0,
        ]);

        $ten = Discount::factory()->create(['percent' => 10]);
        $five = Discount::factory()->create(['percent' => 5]);

        $this->putJson("/orders/{$order->id}/discounts", [
            'discount_ids' => [$ten->id, $five->id],
        ])->assertStatus(200)
            ->assertJsonPath('status', 'synced');

        foreach ([$ten, $five] as $discount) {
            $this->assertDatabaseHas('discount_order', [
                'order_id' => $order->id,
                'discount_id' => $discount->id,
            ]);
        }

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'discount_amount' => 30,
            'total_amount' => 170,
        ]);

        // sync() видаляє зайвий зв'язок і перераховує підсумок.
        $this->putJson("/orders/{$order->id}/discounts", [
            'discount_ids' => [$ten->id],
        ])->assertStatus(200);

        $this->assertDatabaseMissing('discount_order', [
            'order_id' => $order->id,
            'discount_id' => $five->id,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_amount' => 180,
        ]);
    }

    public function test_cart_item_is_stored_through_one_to_many_relation(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create(['price' => 25.5]);

        $this->postJson("/carts/{$cart->id}/items", [
            'product_id' => $product->id,
            'quantity' => 2,
        ])->assertStatus(201);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->getJson("/carts/{$cart->id}")
            ->assertStatus(200)
            ->assertJsonPath('total', 51);
    }
}
