@echo off
REM SIM-Inventaris Laravel Development Server
REM Runs Laravel on port 8001 for testing

setlocal enabledelayedexpansion

set PORT=8001
set HOST=127.0.0.1

echo.
echo ========================================
echo SIM-Inventaris Laravel Test Server
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

REM Check if Laravel is installed
if not exist "app\Http\Controllers\BorrowingController.php" (
    echo [!] Aplikasi Laravel tidak lengkap atau belum ter-install.
    echo Pastikan sudah:
    echo   1. composer install (install dependencies)
    echo   2. php artisan migrate (setup database)
    echo   3. php artisan key:generate (generate app key)
    echo.
    pause
    exit /b 1
)

REM Display access instructions
echo [*] Memulai Laravel server di http://%HOST%:%PORT%
echo.
echo Akses aplikasi:
echo   - Laravel API: http://%HOST%:%PORT%
echo   - API Borrowing: http://%HOST%:%PORT%/api/borrowing
echo   - API Assets: http://%HOST%:%PORT%/api/assets
echo.
echo Database:
echo   - Pastikan .env sudah konfigurasi dengan benar
echo   - Run migration: php artisan migrate
echo.
echo Tekan CTRL+C untuk menghentikan server.
echo.
echo ========================================
echo.

REM Start Laravel development server
php artisan serve --host %HOST% --port %PORT%
