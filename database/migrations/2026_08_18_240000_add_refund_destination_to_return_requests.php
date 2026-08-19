<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->string('refund_bank_name')->nullable()->after('refund_method');
            $table->text('refund_bank_account')->nullable()->after('refund_bank_name');
            $table->string('refund_account_name')->nullable()->after('refund_bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn(['refund_bank_name', 'refund_bank_account', 'refund_account_name']);
        });
    }
};
