<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тестування модерації відгуків (лаб. 7, зауваження рев'ю):
 * нові відгуки завжди потрапляють на модерацію, повторний відгук для того
 * ж покупця+товару — помилка валідації, а статус схвалення змінює лише
 * модератор (роль admin/manager).
 */
class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_review_is_created_as_pending_for_moderation(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/manage/reviews', [
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'rating' => 5,
                'text' => 'Чудовий товар, задоволено. Рекомендую!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'is_approved' => false,
        ]);
    }

    public function test_duplicate_review_for_same_product_and_customer_is_validation_error(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $existing = Review::factory()->create([
            'product_id' => $product->id,
            'customer_id' => $customer->id,
            'rating' => 4,
            'text' => 'Досить хороший товар, витративи все.',
            'is_approved' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->post('/manage/reviews', [
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'rating' => 5,
                'text' => 'Ще один відгук товару, не повинен успіхати.',
            ])
            ->assertSessionHasErrors('customer_id');

        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', ['id' => $existing->id]);
    }

    public function test_moderator_can_approve_review(): void
    {
        $review = Review::factory()->create([
            'product_id' => Product::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'rating' => 4,
            'text' => 'Досить хороший товар, витративи все.',
            'is_approved' => false,
        ]);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put("/manage/reviews/{$review->id}", [
                'product_id' => $review->product_id,
                'customer_id' => $review->customer_id,
                'rating' => 4,
                'text' => 'Досить хороший товар, витративи все.',
                'is_approved' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => true,
        ]);
    }

    public function test_non_moderator_cannot_approve_review(): void
    {
        $review = Review::factory()->create([
            'product_id' => Product::factory()->create()->id,
            'customer_id' => Customer::factory()->create()->id,
            'rating' => 4,
            'text' => 'Досить хороший товар, витративи все.',
            'is_approved' => false,
        ]);

        $this->actingAs(User::factory()->create())
            ->put("/manage/reviews/{$review->id}", [
                'product_id' => $review->product_id,
                'customer_id' => $review->customer_id,
                'rating' => 4,
                'text' => 'Досить хороший товар, витративи все.',
                'is_approved' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_approved' => false,
        ]);
    }
}
