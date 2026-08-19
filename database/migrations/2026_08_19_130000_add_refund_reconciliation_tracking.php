<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->timestamp('refund_requested_at')->nullable()->after('refund_reference');
            $table->timestamp('last_refund_checked_at')->nullable()->after('refund_requested_at');
            $table->unsignedInteger('refund_check_attempts')->default(0)->after('last_refund_checked_at');
            $table->text('refund_failure_reason')->nullable()->after('refund_check_attempts');
        });

        DB::table('return_requests')
            ->where('status', 'refund_processing')
            ->whereNull('refund_requested_at')
            ->update(['refund_requested_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn([
                'refund_requested_at', 'last_refund_checked_at',
                'refund_check_attempts', 'refund_failure_reason',
            ]);
        });
    }
};
