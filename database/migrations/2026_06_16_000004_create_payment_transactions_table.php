<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bang nhat ky giao dich thanh toan (doi soat VNPay/Momo).
     * Bo sung moi - khong anh huong bang/code cu.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 30);                  // vnpay, momo, cod...
            $table->string('transaction_code')->nullable(); // ma giao dich tu cong
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('response_code', 50)->nullable();
            $table->text('response_message')->nullable();
            $table->json('payload')->nullable();            // raw callback de doi soat
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('transaction_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};