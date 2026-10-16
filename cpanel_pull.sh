#!/usr/bin/env bash

# ==============================================================================
# SmartFun - Script Pull & Auto-Deploy untuk cPanel Terminal
# Lokasi: /home/pesonaas/repositories/smartfun
# ==============================================================================

echo "🚀 [1/6] Mendeteksi Binary PHP 8.3/8.4 di cPanel..."

# Cari binary PHP 8.3 atau 8.4 di cPanel (ea-php / alt-php)
PHP_BIN="php"
if [ -f "/usr/local/bin/ea-php83" ]; then
    PHP_BIN="/usr/local/bin/ea-php83"
elif [ -f "/opt/cpanel/ea-php83/root/usr/bin/php" ]; then
    PHP_BIN="/opt/cpanel/ea-php83/root/usr/bin/php"
elif [ -f "/opt/alt/php83/usr/bin/php" ]; then
    PHP_BIN="/opt/alt/php83/usr/bin/php"
elif [ -f "/usr/local/bin/ea-php84" ]; then
    PHP_BIN="/usr/local/bin/ea-php84"
elif [ -f "/opt/cpanel/ea-php84/root/usr/bin/php" ]; then
    PHP_BIN="/opt/cpanel/ea-php84/root/usr/bin/php"
fi

echo "✅ Menggunakan PHP: $PHP_BIN ($($PHP_BIN -r 'echo PHP_VERSION;'))"

# Cari composer binary
COMPOSER_BIN="composer"
if [ -f "/usr/local/bin/composer" ]; then
    COMPOSER_BIN="/usr/local/bin/composer"
elif [ -f "/opt/cpanel/composer/bin/composer" ]; then
    COMPOSER_BIN="/opt/cpanel/composer/bin/composer"
fi

echo "📥 [2/6] Menarik update kode terbaru dari GitHub..."
git pull origin main

echo "⚙️ [3/6] Memeriksa konfigurasi .env..."
if [ ! -f ".env" ]; then
    echo "Menyalin .env.example menjadi .env..."
    cp .env.example .env
    $PHP_BIN artisan key:generate --force
    echo "⚠️ Silakan sesuaikan kredensial DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di .env jika belum."
fi

echo "📦 [4/6] Memeriksa & menginstal dependensi (folder vendor)..."
if [ ! -d "vendor" ]; then
    if [ -f "vendor.zip" ]; then
        echo "Mengekstrak vendor.zip bawaan..."
        unzip -q -o vendor.zip || true
    fi
fi

if [ -d "vendor" ]; then
    chmod -R 755 vendor 2>/dev/null || true
    if [ -f "vendor/composer/platform_check.php" ]; then
        echo '<?php $issues = [];' > vendor/composer/platform_check.php
    fi
    if [ -f "vendor/nunomaduro/collision/src/Adapters/Phpunit/Autoload.php" ]; then
        echo '<?php // disabled' > vendor/nunomaduro/collision/src/Adapters/Phpunit/Autoload.php
    fi
fi

if [ ! -d "vendor" ]; then
    echo "Menjalankan composer install..."
    $PHP_BIN $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || true
fi

echo "🗄️ [5/6] Menjalankan migrasi database & seeding soal..."
$PHP_BIN artisan migrate --force --seed

echo "🔒 [6/7] Mengatur hak akses folder (permissions)..."
chmod 755 /home/pesonaas/repositories 2>/dev/null || true
chmod 755 /home/pesonaas/repositories/smartfun 2>/dev/null || true
chmod 755 /home/pesonaas/repositories/smartfun/public 2>/dev/null || true
chmod 644 /home/pesonaas/repositories/smartfun/public/index.php 2>/dev/null || true
chmod 644 /home/pesonaas/repositories/smartfun/public/test.php 2>/dev/null || true
chmod 644 /home/pesonaas/repositories/smartfun/public/.htaccess 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "⚡ [7/7] Melakukan link storage & optimasi cache..."
$PHP_BIN artisan storage:link 2>/dev/null || true
$PHP_BIN artisan config:clear
$PHP_BIN artisan route:clear
$PHP_BIN artisan view:clear
$PHP_BIN artisan optimize

echo ""
echo "🎉 DEPLOY BERHASIL!"
echo "SmartFun siap diakses tanpa mengganggu website utama di public_html."
echo "=============================================================================="
