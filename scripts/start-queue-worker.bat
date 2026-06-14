@echo off
REM NovaPhone - khoi dong Queue Worker (tu khoi dong lai khi thoat/crash)
REM Double-click file nay de chay worker gui mail nen.

cd /d "%~dp0.."
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0queue-worker.ps1"
pause
