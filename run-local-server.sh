#!/bin/bash
# For macOS / Linux users
# Run: chmod +x run-local-server.sh && ./run-local-server.sh

PORT=8000
HOST="127.0.0.1"

echo "========================================"
echo "SIM-Inventaris Local Test Server"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP tidak ditemukan. Pastikan PHP terinstall."
    exit 1
fi

echo "✅ PHP ditemukan:"
php -v | head -n 1
echo ""

PROJECT_DIR=$(pwd)
echo "📁 Direktori Proyek: $PROJECT_DIR"
echo ""

echo "🚀 Memulai server di http://$HOST:$PORT"
echo ""
echo "Akses aplikasi:"
echo "  - Prototype (JSON DB): http://$HOST:$PORT/index.php"
echo "  - Login: Gunakan password 'admin123'"
echo ""
echo "Data tersimpan di:"
echo "  - Database: $PROJECT_DIR/database.json"
echo "  - Activity Log: $PROJECT_DIR/activity_logs.json"
echo ""
echo "Tekan CTRL+C untuk menghentikan server."
echo ""
echo "========================================"
echo ""

php -S "$HOST:$PORT"
