<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            // Кожен товар має власника-користувача (авторизація на рівні запису, лаб. №8).
            $table->foreignId('user_id')
                ->nullable()
                ->after('category_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
