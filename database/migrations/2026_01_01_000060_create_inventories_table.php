<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->timestamps();

            // MariaDB does not support functional indexes, so we use a virtual
            // generated column to enforce UNIQUE(product_id, variant_id) with NULLs.
            $table->unsignedBigInteger('variant_id_key')
                  ->virtualAs('COALESCE(variant_id, 0)');
            $table->unique(['product_id', 'variant_id_key'], 'inventories_product_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
