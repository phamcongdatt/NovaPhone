<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->boolean('is_apply_sale')->default(true)->after('is_active');
            $table->boolean('is_apply_flash_sale')->default(false)->after('is_apply_sale');
            $table->boolean('is_stackable')->default(false)->after('is_apply_flash_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['is_apply_sale', 'is_apply_flash_sale', 'is_stackable']);
        });
    }
};
