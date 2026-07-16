<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mở rộng precision cho các benchmark score trên database đã migrate trước đó.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('antutu_score', 10, 2)->nullable()->change();
            $table->decimal('geekbench_single', 10, 2)->nullable()->change();
            $table->decimal('geekbench_multi', 10, 2)->nullable()->change();
        });
    }

    /**
     * Khôi phục precision cũ.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('antutu_score', 8, 2)->nullable()->change();
            $table->decimal('geekbench_single', 8, 2)->nullable()->change();
            $table->decimal('geekbench_multi', 8, 2)->nullable()->change();
        });
    }
};
