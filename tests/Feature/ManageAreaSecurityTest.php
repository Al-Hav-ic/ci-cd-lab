<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature-тестування захищеності розділу /manage (лаб. 7, зауваження рев'ю).
 *
 * Деструктивні операції змінюють дані всієї предметної області, тому всі
 * маршрути /manage/* реєструвалися посередником auth — гость не повинен
 * бачити та виконувати керування як адміністратор.
 */
class ManageAreaSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_for_manage_area(): void
    {
        $this->get('/manage/categories')->assertRedirect('/login');
        $this->get('/manage/customers')->assertRedirect('/login');
        $this->get('/manage/discounts')->assertRedirect('/login');
        $this->get('/manage/orders')->assertRedirect('/login');
        $this->get('/manage/reviews')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_manage_area(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/manage/categories')
            ->assertStatus(200);
    }
}
