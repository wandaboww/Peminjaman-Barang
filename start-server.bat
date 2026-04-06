@echo off
title SIM-Inventaris Server
color 0A
echo ============================================
echo  SIM-Inventaris - PHP Development Server
echo ============================================
echo.
echo Starting server on http://127.0.0.1:8000
echo.
echo Press Ctrl+C to stop the server
echo ============================================
echo.

cd /d "%~dp0"
php -S 127.0.0.1:8000

pause
