<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Збереження історії замовлень (лаб. 7, зауваження рев'ю).
 *
 * Раніше order_items.product_id мав ON DELETE CASCADE: видалення товару
 * знищувало рядки замовлень разом з історією покупок. Тепер посилання
 * необов'язкове, а видалення товару лише обнуляє product_id — назва
 * (product_name) і ціна (unit_price) уже збережені як знімок у позиції.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropForeign(['product_id']);
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
