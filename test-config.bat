@echo off
REM SIM-Inventaris - Port Testing Configuration Helper
REM Menampilkan informasi konfigurasi port untuk testing

echo.
echo =====================================================
echo    SIM-Inventaris - Port Configuration
echo =====================================================
echo.
echo KONFIGURASI PORT TESTING:
echo.
echo [Prototype Server]
echo   Host: 127.0.0.1
echo   Port: 8000
echo   URL:  http://127.0.0.1:8000
echo.
echo [Laravel Server]
echo   Host: 127.0.0.1
echo   Port: 8001
echo   URL:  http://127.0.0.1:8001
echo.
echo =====================================================
echo    CARA TESTING:
echo =====================================================
echo.
echo 1. PROTOTYPE ONLY (Recommended untuk testing cepat):
echo    ^> run-local-server.bat
echo    Akses: http://127.0.0.1:8000
echo.
echo 2. DUAL SERVERS (Testing keduanya):
echo    ^> run-dual-servers.bat
echo    Prototype: http://127.0.0.1:8000
echo    Laravel:   http://127.0.0.1:8001
echo.
echo 3. CUSTOM PORT (Manual):
echo    ^> php -S 127.0.0.1:XXXX
echo    (Ganti XXXX dengan port pilihan Anda)
echo.
echo =====================================================
echo    CREDENTIAL TESTING:
echo =====================================================
echo.
echo   Admin Password: admin123
echo   (GANTI di production!)
echo.
echo =====================================================
echo    TEST DATA LOCATION:
echo =====================================================
echo.
echo   Database:       database.json
echo   Activity Logs:  activity_logs.json
echo   Config File:    config.testing.env
echo.
echo =====================================================
echo.
echo Tekan tombol apapun untuk keluar...
pause >nul
