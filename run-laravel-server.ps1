# SIM-Inventaris Laravel Development Server
# Runs Laravel on port 8001 for testing

$port = 8001
$host = "127.0.0.1"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SIM-Inventaris Laravel Test Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if PHP is installed
$phpCheck = php -v 2>$null
if (-not $phpCheck) {
    Write-Host "❌ PHP tidak ditemukan. Pastikan PHP terinstall dan ditambahkan ke PATH." -ForegroundColor Red
    exit 1
}

Write-Host "✅ PHP ditemukan:" -ForegroundColor Green
Write-Host $phpCheck[0]
Write-Host ""

# Get current directory
$projectDir = (Get-Location).Path
Write-Host "📁 Direktori Proyek: $projectDir" -ForegroundColor Yellow
Write-Host ""

# Check if Laravel is installed
if (-not (Test-Path "app\Http\Controllers\BorrowingController.php")) {
    Write-Host "❌ Aplikasi Laravel tidak lengkap atau belum ter-install." -ForegroundColor Red
    Write-Host "Pastikan sudah:" -ForegroundColor Yellow
    Write-Host "  1. composer install (install dependencies)" -ForegroundColor White
    Write-Host "  2. php artisan migrate (setup database)" -ForegroundColor White
    Write-Host "  3. php artisan key:generate (generate app key)" -ForegroundColor White
    Write-Host ""
    exit 1
}

# Display access instructions
Write-Host "🚀 Memulai Laravel server di http://$host:$port" -ForegroundColor Green
Write-Host ""
Write-Host "Akses aplikasi:" -ForegroundColor Cyan
Write-Host "  - Laravel API: http://$host:$port" -ForegroundColor White
Write-Host "  - API Borrowing: http://$host:$port/api/borrowing" -ForegroundColor White
Write-Host "  - API Assets: http://$host:$port/api/assets" -ForegroundColor White
Write-Host ""
Write-Host "Database:" -ForegroundColor Cyan
Write-Host "  - Pastikan .env sudah konfigurasi dengan benar" -ForegroundColor White
Write-Host "  - Run migration: php artisan migrate" -ForegroundColor White
Write-Host ""
Write-Host "Tekan CTRL+C untuk menghentikan server." -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan

# Start Laravel development server
php artisan serve --host "$host" --port "$port"
