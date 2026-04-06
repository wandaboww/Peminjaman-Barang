@echo off
REM SIM-Inventaris Dual Server Launcher
REM Runs both Prototype (8000) and Laravel (8001) simultaneously

echo.
echo =====================================================
echo SIM-Inventaris - Dual Server Launcher
echo =====================================================
echo.
echo Ini akan menjalankan KEDUA implementasi sekaligus:
echo   - Prototype:  http://127.0.0.1:8000 (Port 8000)
echo   - Laravel:    http://127.0.0.1:8001 (Port 8001)
echo.
echo Tekan ENTER untuk memulai, atau Ctrl+C untuk batal...
pause

REM Start Prototype on port 8000 in new window
echo [1/2] Starting Prototype on port 8000...
start "SIM-Inventaris Prototype (Port 8000)" /D "%CD%" cmd /k "php -S 127.0.0.1:8000"

REM Wait a bit for first server to start
timeout /t 2 /nobreak

REM Start Laravel on port 8001 in new window (if Laravel installed)
if exist "app\Http\Controllers\BorrowingController.php" (
    echo [2/2] Starting Laravel on port 8001...
    start "SIM-Inventaris Laravel (Port 8001)" /D "%CD%" cmd /k "php artisan serve --host 127.0.0.1 --port 8001"
    timeout /t 2 /nobreak
    echo.
    echo =====================================================
    echo ✅ KEDUA SERVER SUDAH BERJALAN!
    echo =====================================================
    echo.
    echo Akses:
    echo   - Prototype: http://127.0.0.1:8000
    echo   - Laravel:   http://127.0.0.1:8001
    echo.
    echo Tutup window terminal untuk menghentikan server.
    echo =====================================================
    echo.
) else (
    echo [2/2] Laravel tidak ditemukan, hanya jalankan Prototype...
    echo.
    echo =====================================================
    echo ✅ PROTOTYPE SERVER SUDAH BERJALAN!
    echo =====================================================
    echo.
    echo Akses:
    echo   - Prototype: http://127.0.0.1:8000
    echo.
    echo Untuk Laravel:
    echo   1. Run: composer install
    echo   2. Run: php artisan migrate
    echo   3. Run: run-laravel-server.bat
    echo =====================================================
    echo.
)

REM Keep this window open
cmd /k
