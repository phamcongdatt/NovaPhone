<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tự động hủy các đơn hàng thanh toán trực tuyến bị treo "pending" quá hạn,
// hoàn kho và giải phóng mã giảm giá. Yêu cầu cron `* * * * * php artisan schedule:run`
// (hoặc `php artisan schedule:work` khi phát triển) được chạy trên server.
Schedule::command('orders:cancel-stale')->everyMinute();
