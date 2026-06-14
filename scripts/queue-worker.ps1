<#
    NovaPhone - Queue Worker Supervisor (Windows)

    Chạy `php artisan queue:work` trong vòng lặp vô hạn. Nếu worker thoát
    (do crash, hết --max-time, hoặc lệnh queue:restart) thì tự khởi động lại
    sau vài giây. Dùng để mail xác thực luôn được gửi ở chế độ nền.

    Cách dùng:
        - Double-click  scripts\start-queue-worker.bat   (tiện nhất), hoặc
        - powershell -ExecutionPolicy Bypass -File scripts\queue-worker.ps1

    Dừng: đóng cửa sổ hoặc nhấn Ctrl+C.
#>

# Thư mục gốc dự án = thư mục cha của thư mục chứa script này
$ProjectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $ProjectRoot

$RestartDelaySeconds = 3

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host " NovaPhone Queue Worker Supervisor" -ForegroundColor Cyan
Write-Host " Project: $ProjectRoot" -ForegroundColor DarkGray
Write-Host " Nhan Ctrl+C de dung." -ForegroundColor DarkGray
Write-Host "==============================================" -ForegroundColor Cyan

while ($true) {
    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "[$stamp] Khoi dong queue worker..." -ForegroundColor Green

    # --max-time=3600 : worker tu thoat sau 1 gio de tranh ro ri bo nho, roi vong lap khoi dong lai
    # --tries=3       : moi job thu toi da 3 lan truoc khi chuyen sang failed_jobs
    # --sleep=3       : khi khong co job, cho 3s roi kiem tra lai
    & php artisan queue:work --queue=default --tries=3 --sleep=3 --max-time=3600

    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "[$stamp] Worker da dung. Khoi dong lai sau $RestartDelaySeconds giay..." -ForegroundColor Yellow
    Start-Sleep -Seconds $RestartDelaySeconds
}
