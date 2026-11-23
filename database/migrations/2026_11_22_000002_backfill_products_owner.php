<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill owner_id for legacy products (лаб. 8, зауваження рев'ю).
     *
     * Раніше products.user_id був nullable без значення: існуючі товари
     * не мали власників, тому ProductPolicy не міг коректно відхиляв доступ.
     * Тепер існуючі товари прив'язується до першого адміністратора.
     */
    public function up(): void
    {
        $fallbackOwnerId = (int) (
            DB::table('users')
                ->where('role', 'admin')
                ->min('id')
            ?? DB::table('users')->min('id')
        );

        if ($fallbackOwnerId > 0) {
            DB::table('products')
                ->whereNull('user_id')
                ->update(['user_id' => $fallbackOwnerId]);
        }
    }

    public function down(): void
    {
        // Оновлення user_id уже відбулося без rollback-обмежень для даних.
    }
};
