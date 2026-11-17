<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тести вебінтерфейсу на Blade (лаб. 7):
 * сторінки каталогу, валідація HTML-форм, зв'язки 1:M / M:M у формах.
 */
class CatalogViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_entities(): void
    {
        $category = Category::factory()->create(['name' => 'Молочні продукти']);
        $product = Product::factory()->create([
            'name' => 'Унікальний Тестовий Товар',
            'category_id' => $category->id,
            'is_active' => true,
            'price' => 42.50,
        ]);

        // GET-запит на сторінку списку (SSR через Blade) від імені користувача
        $response = $this->actingAs(User::factory()->create())->get('/shop');

        // Статус 200 + назва товару + категорія через Eager Loading + кнопка створення
        $response->assertStatus(200)
            ->assertSee('Унікальний Тестовий Товар')
            ->assertSee('Молочні продукти')
            ->assertSee('Додати новий запис')
            ->assertSee(number_format($product->price, 2));
    }

    public function test_create_form_validates_input(): void
    {
        // Створення дозволено лише автентифікованим користувачам (лаб. 8).
        $response = $this->actingAs(User::factory()->create())->post('/shop', [
            'name' => '',
            'price' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    public function test_order_edit_form_renders_customer_select_and_discount_checkboxes(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100.0, 'is_active' => true]);
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 100.0,
        ]);

        $discount = Discount::factory()->create(['code' => 'SAVE-TEST-10', 'percent' => 10]);
        $order->discounts()->sync([$discount->id]);

        // Форма редагування: select 1:M (покупець) + чекбокси M:M (знижки)
        $this->get("/manage/orders/{$order->id}/edit")
            ->assertStatus(200)
            ->assertSee('SAVE-TEST-10')
            ->assertSee('type="checkbox"', false)
            ->assertSee('name="customer_id"', false);
    }

    public function test_store_creates_product_through_html_form(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post('/shop', [
            'name' => 'Сир Голландський 45%',
            'sku' => 'CHEESE-0007',
            'price' => '310.50',
            'stock_qty' => '12',
            'category_id' => (string) $category->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('shop.index'))
            ->assertSessionHas('success');

        // Запис автоматично прив'язується до поточного користувача (власник).
        $this->assertDatabaseHas('products', [
            'sku' => 'CHEESE-0007',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);
    }
}
