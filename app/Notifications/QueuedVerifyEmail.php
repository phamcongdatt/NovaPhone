<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Notification xác thực email chạy nền qua queue.
 *
 * Mặc định Illuminate\Auth\Notifications\VerifyEmail KHÔNG implement ShouldQueue
 * nên mail được gửi đồng bộ trong request. Lớp này kế thừa toàn bộ logic tạo link
 * xác thực và chỉ bổ sung ShouldQueue + Queueable để đẩy việc gửi mail ra hàng đợi.
 */
class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;
};