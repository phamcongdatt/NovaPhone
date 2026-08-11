<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Mã theo danh mục hành chính mới: tỉnh 2 ký tự, xã/phường 5 ký tự.
            // Các cột nullable để toàn bộ địa chỉ cũ vẫn dùng được cho đến khi người dùng cập nhật.
            $table->char('province_code', 2)->nullable()->index();
            $table->char('ward_code', 5)->nullable()->index();
            $table->string('administrative_version', 32)->nullable();
            $table->timestamp('validated_at')->nullable();

            // Cấu trúc hành chính mới không còn cấp quận/huyện.
            $table->string('district')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->char('shipping_province_code', 2)->nullable()->index();
            $table->char('shipping_ward_code', 5)->nullable()->index();
            $table->string('administrative_version', 32)->nullable();

            $table->string('shipping_district')->nullable()->change();
        });

        Schema::create('administrative_provinces', function (Blueprint $table) {
            $table->char('code', 2)->primary();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('source_version', 32);
            $table->timestamps();
        });

        Schema::create('administrative_wards', function (Blueprint $table) {
            $table->char('code', 5)->primary();
            $table->char('province_code', 2)->index();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('source_version', 32);
            $table->timestamps();

            $table->index(['province_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrative_wards');
        Schema::dropIfExists('administrative_provinces');

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['province_code', 'ward_code', 'administrative_version', 'validated_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_province_code', 'shipping_ward_code', 'administrative_version']);
        });

        // Giữ district/shipping_district ở trạng thái nullable khi rollback để không làm mất
        // dữ liệu V2 đã được lưu mà không có quận/huyện.
    }
};
