# SIM-Inventaris Server Launcher
# PowerShell Script

Write-Host "============================================" -ForegroundColor Green
Write-Host " SIM-Inventaris - PHP Development Server" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "Starting server on http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Gray
Write-Host "============================================" -ForegroundColor Green
Write-Host ""

# Change to script directory
Set-Location -Path $PSScriptRoot

# Start PHP server
try {
    php -S 127.0.0.1:8000
} catch {
    Write-Host "Error: $_" -ForegroundColor Red
    Read-Host "Press Enter to exit"
}
