# SIM-Inventaris Local Development Server
# Runs the prototype on a local PHP development server for testing

$port = 8000
$host = "127.0.0.1"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "SIM-Inventaris Local Test Server" -ForegroundColor Cyan
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

# Display access instructions
Write-Host "🚀 Memulai server di http://$host:$port" -ForegroundColor Green
Write-Host ""
Write-Host "Akses aplikasi:" -ForegroundColor Cyan
Write-Host "  - Prototype (JSON DB): http://$host:$port/index.php" -ForegroundColor White
Write-Host "  - Login: Gunakan password 'admin123'" -ForegroundColor White
Write-Host ""
Write-Host "Data tersimpan di:" -ForegroundColor Cyan
Write-Host "  - Database: $projectDir\database.json" -ForegroundColor White
Write-Host "  - Activity Log: $projectDir\activity_logs.json" -ForegroundColor White
Write-Host ""
Write-Host "Tekan CTRL+C untuk menghentikan server." -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan

# Start the PHP development server
php -S "$host:$port" -t "$projectDir"
