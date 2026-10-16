<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>SmartFun Diagnostik 500</title></head><body style="font-family:sans-serif;padding:30px;background:#f8fafc;color:#1e293b;">';
echo '<h2 style="color:#4f46e5;">🔍 Diagnostik Error 500 SmartFun</h2>';

// 1. PHP Version
echo "<h3>1. Versi PHP Web Server</h3>";
echo "<p>Versi: <strong>" . PHP_VERSION . "</strong> (ID: " . PHP_VERSION_ID . ")</p>";
if (PHP_VERSION_ID < 80200) {
    echo "<p style='color:red;'>⚠️ PERINGATAN: Versi PHP di bawah 8.2. Laravel 13 membutuhkan minimal PHP 8.2 / 8.3.</p>";
} else {
    echo "<p style='color:green;'>✅ Versi PHP mencukupi untuk Laravel.</p>";
}

// 2. Storage Permissions
echo "<h3>2. Hak Akses Folder Writable</h3>";
$paths = [
    'storage' => __DIR__ . '/../storage',
    'storage/logs' => __DIR__ . '/../storage/logs',
    'storage/framework' => __DIR__ . '/../storage/framework',
    'storage/framework/views' => __DIR__ . '/../storage/framework/views',
    'storage/framework/sessions' => __DIR__ . '/../storage/framework/sessions',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
];

foreach ($paths as $name => $path) {
    if (!file_exists($path)) {
        @mkdir($path, 0777, true);
    }
    $isWritable = is_writable($path);
    echo "<p>$name: " . ($isWritable ? "<span style='color:green;'>✅ Writable</span>" : "<span style='color:red;'>❌ Tidak Writable</span>") . " ($path)</p>";
}

// 3. Autoload & Bootstrap Test
echo "<h3>3. Pengujian Autoload & Bootstrap Laravel</h3>";
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "<p style='color:red;'>❌ File vendor/autoload.php tidak ditemukan!</p>";
} else {
    try {
        require __DIR__ . '/../vendor/autoload.php';
        echo "<p style='color:green;'>✅ Composer Autoload Berhasil Dimuat.</p>";

        $app = require_once __DIR__ . '/../bootstrap/app.php';
        echo "<p style='color:green;'>✅ Bootstrap Laravel Berhasil Diinisialisasi.</p>";

        // Test DB connection
        try {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            echo "<p style='color:green;'>✅ Koneksi Database MySQL Berhasil! (Driver: " . $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME) . ")</p>";
        } catch (\Throwable $dbe) {
            echo "<p style='color:red;'>❌ Gagal Koneksi Database: " . htmlspecialchars($dbe->getMessage()) . "</p>";
        }

    } catch (\Throwable $e) {
        echo "<div style='background:#fee2e2;border:1px solid #ef4444;padding:15px;border-radius:8px;'>";
        echo "<h4 style='color:#b91c1c;margin:0 0 10px 0;'>Penyebab Error 500:</h4>";
        echo "<strong>Pesan:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris: " . $e->getLine() . ")<br>";
        echo "<pre style='margin-top:10px;background:#fff;padding:10px;overflow:auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
}

echo "</body></html>";
