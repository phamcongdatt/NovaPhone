<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Them index tang toc cho cac cot loc/sap xep thuong dung.
     * Thuan BO SUNG - khong doi cot, khong doi du lieu, khong anh huong code cu.
     */
    private array $indexes = [
        ['products', 'is_active',      'products_is_active_index'],
        ['products', 'is_featured',    'products_is_featured_index'],
        ['products', 'price',          'products_price_index'],
        ['products', 'sold_count',     'products_sold_count_index'],
        ['products', 'deleted_at',     'products_deleted_at_index'],
        ['orders',   'status',         'orders_status_index'],
        ['orders',   'payment_status', 'orders_payment_status_index'],
        ['orders',   'created_at',     'orders_created_at_index'],
        ['reviews',  'is_visible',     'reviews_is_visible_index'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as [$tableName, $column, $indexName]) {
            if (! $this->indexExists($tableName, $indexName)) {
                Schema::table($tableName, fn (Blueprint $t) => $t->index($column, $indexName));
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as [$tableName, $column, $indexName]) {
            if ($this->indexExists($tableName, $indexName)) {
                Schema::table($tableName, fn (Blueprint $t) => $t->dropIndex($indexName));
            }
        }
    }

    private function indexExists(string $tableName, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM `{$tableName}`"))
            ->contains(fn ($row) => $row->Key_name === $indexName);
    }
};