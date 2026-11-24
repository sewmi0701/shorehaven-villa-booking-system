@echo off
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0DOWNLOAD_ROOM_PHOTOS.ps1"
echo.
pause
