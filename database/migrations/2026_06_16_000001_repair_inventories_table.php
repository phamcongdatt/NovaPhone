<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Khoi phuc bang `inventories` bi hong (loi #1932 - doesn't exist in engine).
     *
     * Cau truc giu NGUYEN nhu migration goc 2026_01_01_000060 de KHONG pha vo
     * code dang dung: App\Models\Inventory, CartService::getAvailableStock(),
     * CheckoutController (inventory->decrement('quantity')).
     */
    public function up(): void
    {
        // Khong bang nao tham chieu toi `inventories` -> drop orphaned table an toan.
        Schema::dropIfExists('inventories');

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->timestamps();

            // Virtual generated column de enforce UNIQUE(product_id, variant_id) ke ca khi variant_id NULL.
            $table->unsignedBigInteger('variant_id_key')
                  ->virtualAs('COALESCE(variant_id, 0)');
            $table->unique(['product_id', 'variant_id_key'], 'inventories_product_variant_unique');
        });

        // Backfill: du lieu ton kho cu da mat cung voi bang.
        // Tao lai 1 dong ton kho cho moi bien the dang ton tai de chuc nang mua hang
        // hoat dong tro lai ngay. (So luong mac dinh 50 - admin co the dieu chinh sau,
        // hoac chay `php artisan db:seed --class=DemoProductSeeder` de lay so lieu demo goc.)
        $now = now();
        $rows = DB::table('product_variants')
            ->select('id as variant_id', 'product_id')
            ->get()
            ->map(fn ($v) => [
                'product_id'          => $v->product_id,
                'variant_id'          => $v->variant_id,
                'quantity'            => 50,
                'reserved_quantity'   => 0,
                'low_stock_threshold' => 5,
                'created_at'          => $now,
                'updated_at'          => $now,
            ])->all();

        if (! empty($rows)) {
            DB::table('inventories')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};