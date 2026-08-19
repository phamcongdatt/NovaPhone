<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(8)->after('sale_price');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(10)->after('subtotal');
            $table->decimal('taxable_amount', 15, 2)->default(0)->after('tax_rate');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('taxable_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'taxable_amount', 'tax_amount']);
        });
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn('tax_rate'));
    }
};
