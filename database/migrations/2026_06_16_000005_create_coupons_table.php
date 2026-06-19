<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bang ma giam gia. Bo sung moi - cot orders.discount_amount cu van dung binh thuong.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();   // tran giam cho loai percent
            $table->decimal('min_order_amount', 15, 2)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();   // tong luot dung
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('per_user_limit')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};