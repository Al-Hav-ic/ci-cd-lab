<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            // Зв'язок 1:1 — профіль покупця прив'язаний до облікового запису.
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 32)->nullable();
            $table->string('address')->nullable();
            $table->unsignedInteger('bonus_points')->default(0);
            $table->string('loyalty_level', 32)->default('basic');
            $table->boolean('newsletter_opt_in')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
