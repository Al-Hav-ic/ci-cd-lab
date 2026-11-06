<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Проміжна таблиця зв'язку M:M «замовлення ↔ знижки».
        Schema::create('discount_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('discount_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Запобігання дублюванню однакових зв'язків.
            $table->unique(['order_id', 'discount_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_order');
    }
};
