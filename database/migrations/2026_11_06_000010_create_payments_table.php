<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Зв'язок 1:1 — у замовлення рівно один платіж.
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('method', 32);
            $table->decimal('amount', 12, 2);
            $table->string('status', 32)->default('pending')->index();
            $table->string('transaction_id')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
