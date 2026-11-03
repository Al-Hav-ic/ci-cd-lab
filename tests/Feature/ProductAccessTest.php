<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ProductAccessTest extends TestCase
{
    /**
     * @var array<string, mixed>
     */
    private array $payload = [
        'name' => 'Ноутбук Dell XPS 15',
        'price' => 45000,
        'stock_qty' => 5,
        'manufacturer' => 'Dell',
    ];

    public function test_request_without_client_header_is_blocked_by_middleware(): void
    {
        $response = $this->postJson('/products', $this->payload);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'access_denied',
            ]);
    }

    public function test_request_with_valid_header_passes_middleware(): void
    {
        $response = $this->withHeaders([
            'X-App-Client' => 'AutomatedTestRunner',
        ])->postJson('/products', $this->payload);

        $response->assertStatus(201)
            ->assertHeader('X-Security-Policy', 'Enforced')
            ->assertJsonPath('data.name', 'Ноутбук Dell XPS 15')
            ->assertJsonPath('data.price', 45000);
    }

    public function test_index_returns_product_list(): void
    {
        $this->getJson('/products')
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(3, 'data');
    }

    public function test_show_returns_404_for_unknown_product(): void
    {
        $this->getJson('/products/999')
            ->assertStatus(404)
            ->assertJsonPath('status', 'error');
    }

    public function test_store_validates_required_fields(): void
    {
        $this->withHeaders(['X-App-Client' => 'AutomatedTestRunner'])
            ->postJson('/products', ['name' => 'X'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price', 'stock_qty', 'manufacturer']);
    }
}
