<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL, we need to modify the enum
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'received', 'cancelled') DEFAULT 'pending'");
            DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'received', 'cancelled')");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled') DEFAULT 'pending'");
            DB::statement("ALTER TABLE order_status_histories MODIFY COLUMN status ENUM('pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled')");
        }
    }
};
