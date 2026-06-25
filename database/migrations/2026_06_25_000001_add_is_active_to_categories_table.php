<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('status');
            });

            DB::table('categories')
                ->where('status', 'inactive')
                ->update(['is_active' => false]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
