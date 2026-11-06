<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'name' => 'Молоко Hollandia 45 % 150 г',
            'sku' => 'MILK-0045',
            'price' => 89.90,
            'stock_qty' => 15,
            'is_active' => true,
        ];
    }

    public function test_request_without_client_header_is_blocked_by_middleware(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/products', $this->payload($category));

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'access_denied',
            ]);
    }

    public function test_request_with_valid_header_passes_middleware(): void
    {
        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'X-App-Client' => 'AutomatedTestRunner',
        ])->postJson('/products', $this->payload($category));

        $response->assertStatus(201)
            ->assertHeader('X-Security-Policy', 'Enforced')
            ->assertJsonPath('data.name', 'Молоко Hollandia 45 % 150 г')
            ->assertJsonPath('data.price', 89.9);

        $this->assertDatabaseHas('products', [
            'sku' => 'MILK-0045',
            'category_id' => $category->id,
        ]);
    }

    public function test_index_returns_product_list(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/products')
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('count', 3)
            ->assertJsonCount(3, 'data.data');
    }

    public function test_show_returns_404_for_unknown_product(): void
    {
        $this->getJson('/products/9999')
            ->assertStatus(404);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->withHeaders(['X-App-Client' => 'AutomatedTestRunner'])
            ->postJson('/products', ['name' => 'X'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'category_id', 'sku', 'price']);
    }
}
