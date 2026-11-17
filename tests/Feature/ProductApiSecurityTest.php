<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Feature-тести захисту ендпоінтів (лаб. 8):
 * токен-автентифікація Sanctum (401) та авторизація ProductPolicy (403)
 * як для REST API, так і для вебінтерфейсу на сесіях.
 */
class ProductApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/v1/products', [
            'name' => 'Test Product',
            'sku' => 'TEST-0001',
            'price' => 99.99,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
        ]);

        // Авторизуємося від імені іншого користувача (токен Sanctum).
        Sanctum::actingAs($intruder);

        $this->deleteJson("/api/v1/products/{$product->id}")
            ->assertStatus(403);
    }

    public function test_owner_can_update_own_product_via_api(): void
    {
        $owner = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
        ]);

        Sanctum::actingAs($owner);

        $this->putJson("/api/v1/products/{$product->id}", [
            'name' => 'Оновлений товар',
            'sku' => $product->sku,
            'price' => 123.45,
            'stock_qty' => 5,
            'category_id' => $category->id,
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Оновлений товар');
    }

    public function test_store_binds_product_to_token_owner_and_returns_201(): void
    {
        $owner = User::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($owner);

        $this->postJson('/api/v1/products', [
            'name' => 'Ноутбук Pro 16',
            'sku' => 'NB-2026-01',
            'price' => 45999.99,
            'stock_qty' => 10,
            'category_id' => $category->id,
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.owner_id', $owner->id);

        $this->assertDatabaseHas('products', [
            'sku' => 'NB-2026-01',
            'user_id' => $owner->id,
        ]);
    }

    public function test_login_endpoint_returns_bearer_token(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        // Реальний Bearer-токен у заголовку дає доступ до захищеного ендпоінта.
        $token = $response->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/user')
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id);
    }

    public function test_guest_is_redirected_to_login_when_accessing_create_page(): void
    {
        $this->get('/shop/create')->assertRedirect('/login');
    }

    public function test_authenticated_user_cannot_edit_another_users_product_via_web(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($intruder)
            ->get("/shop/{$product->id}/edit");

        $response->assertStatus(403);
    }
}
