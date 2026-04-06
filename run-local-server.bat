@echo off
REM SIM-Inventaris Local Development Server
REM Runs the prototype on a local PHP development server for testing

setlocal enabledelayedexpansion

set PORT=8000
set HOST=127.0.0.1

echo.
echo ========================================
echo SIM-Inventaris Local Test Server
echo ========================================
echo.

REM Check if PHP is installed
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [X] PHP tidak ditemukan. Pastikan PHP terinstall dan ditambahkan ke PATH.
    pause
    exit /b 1
)

echo [OK] PHP ditemukan:
php -v | findstr /R "PHP"
echo.

REM Get current directory
set "PROJECTDIR=%CD%"
echo Direktori Proyek: %PROJECTDIR%
echo.

REM Display access instructions
echo [*] Memulai server di http://%HOST%:%PORT%
echo.
echo Akses aplikasi:
echo   - Prototype (JSON DB): http://%HOST%:%PORT%/index.php
echo   - Login: Gunakan password 'admin123'
echo.
echo Data tersimpan di:
echo   - Database: %PROJECTDIR%\database.json
echo   - Activity Log: %PROJECTDIR%\activity_logs.json
echo.
echo Tekan CTRL+C untuk menghentikan server.
echo.
echo ========================================
echo.

REM Start the PHP development server
php -S %HOST%:%PORT% -t %PROJECTDIR%
