<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->index('user_id', 'reviews_user_id_index_for_fk');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_user_id_product_id_unique');
            $table->unique(['order_id', 'product_id'], 'reviews_order_product_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id'], 'reviews_user_id_product_id_unique');
            $table->dropUnique('reviews_order_product_unique');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_user_id_index_for_fk');
        });
    }
};
