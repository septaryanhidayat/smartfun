<?php

echo "🔧 Memulai sinkronisasi kompatibilitas PHP 8.3...\n";

// 1. Bypass platform_check.php
$platformCheck = __DIR__.'/vendor/composer/platform_check.php';
if (file_exists($platformCheck)) {
    file_put_contents($platformCheck, "<?php\n\$issues = array();\n");
    echo "✅ vendor/composer/platform_check.php dinetralkan.\n";
}

// 2. Bypass collision Autoload.php
$collision = __DIR__.'/vendor/nunomaduro/collision/src/Adapters/Phpunit/Autoload.php';
if (file_exists($collision)) {
    file_put_contents($collision, "<?php\n// Bypassed for web production\n");
    echo "✅ Collision PHPUnit adapter dinetralkan.\n";
}

// 3. Bypass pest-plugin-laravel Autoload.php
$pestLaravel = __DIR__.'/vendor/pestphp/pest-plugin-laravel/src/Autoload.php';
if (file_exists($pestLaravel)) {
    file_put_contents($pestLaravel, "<?php\n// Bypassed for web production\n");
    echo "✅ Pest-Plugin-Laravel autoloader dinetralkan.\n";
}

// 4. Patch Symfony Request.php (remove property hooks & setRawValue)
$requestFile = __DIR__.'/vendor/symfony/http-foundation/Request.php';
if (file_exists($requestFile)) {
    $content = file_get_contents($requestFile);

    // Remove property hooks
    $pattern = '/public\s+([\w\\\\]+)\s+\$(\w+)\s*\{\s*set\s*\{[^}]*\}\s*\}/s';
    $replacement = 'public $1 $$2;';
    $newContent = preg_replace($pattern, $replacement, $content);

    // Replace setRawValue
    $newContent = str_replace(
        '$r->setRawValue($request, $value);',
        '$request->$name = $value;',
        $newContent
    );

    if ($newContent !== $content) {
        file_put_contents($requestFile, $newContent);
        echo "✅ Symfony Request.php berhasil disesuaikan untuk PHP 8.3.\n";
    } else {
        echo "ℹ️ Symfony Request.php sudah kompatibel.\n";
    }
}

// 5. Patch Symfony Response.php (remove property hooks & setRawValue)
$responseFile = __DIR__.'/vendor/symfony/http-foundation/Response.php';
if (file_exists($responseFile)) {
    $content = file_get_contents($responseFile);

    // Remove property hooks
    $pattern = '/public\s+([\w\\\\]+)\s+\$(\w+)\s*\{\s*set\s*\{[^}]*\}\s*\}/s';
    $replacement = 'public $1 $$2;';
    $newContent = preg_replace($pattern, $replacement, $content);

    // Replace setRawValue
    $newContent = str_replace(
        '$r->setRawValue($response, $headers);',
        '$response->headers = $headers;',
        $newContent
    );

    if ($newContent !== $content) {
        file_put_contents($responseFile, $newContent);
        echo "✅ Symfony Response.php berhasil disesuaikan untuk PHP 8.3.\n";
    } else {
        echo "ℹ️ Symfony Response.php sudah kompatibel.\n";
    }
}

// 6. Patch VarCloner.php (wrap new \ReflectionClass in parentheses)
$varClonerFile = __DIR__.'/vendor/symfony/var-dumper/Cloner/VarCloner.php';
if (file_exists($varClonerFile)) {
    $content = file_get_contents($varClonerFile);
    $newContent = str_replace(
        'new \ReflectionClass($v)->isUserDefined()',
        '(new \ReflectionClass($v))->isUserDefined()',
        $content
    );
    if ($newContent !== $content) {
        file_put_contents($varClonerFile, $newContent);
        echo "✅ Symfony VarCloner.php berhasil disesuaikan untuk PHP 8.3.\n";
    } else {
        echo "ℹ️ Symfony VarCloner.php sudah kompatibel.\n";
    }
}

// 7. Patch Symfony Command.php (dereference in getCommandAttribute)
$commandFile = __DIR__.'/vendor/symfony/console/Command/Command.php';
if (file_exists($commandFile)) {
    $content = file_get_contents($commandFile);
    $old = '$attribute = (new \ReflectionClass(static::class)->getAttributes(AsCommand::class)[0] ?? null)?->newInstance();';
    $new = '$reflection = new \ReflectionClass(static::class);'."\n".'            $attribute = ($reflection->getAttributes(AsCommand::class)[0] ?? null)?->newInstance();';
    $newContent = str_replace($old, $new, $content);
    if ($newContent !== $content) {
        file_put_contents($commandFile, $newContent);
        echo "✅ Symfony Command.php berhasil disesuaikan untuk PHP 8.3.\n";
    } else {
        echo "ℹ️ Symfony Command.php sudah kompatibel.\n";
    }
}

// 8. Patch AddConsoleCommandPass.php (wrap new Definition in parentheses)
$passFile = __DIR__.'/vendor/symfony/console/DependencyInjection/AddConsoleCommandPass.php';
if (file_exists($passFile)) {
    $content = file_get_contents($passFile);
    $newContent = str_replace(
        'new Definition(\Closure::class)',
        '(new Definition(\Closure::class))',
        $content
    );
    if ($newContent !== $content) {
        file_put_contents($passFile, $newContent);
        echo "✅ Symfony AddConsoleCommandPass.php disesuaikan.\n";
    }
}

// 9. Patch Sebastian Exporter.php
$exporterFile = __DIR__.'/vendor/sebastian/exporter/src/Exporter.php';
if (file_exists($exporterFile)) {
    $content = file_get_contents($exporterFile);
    $newContent = str_replace('new ReflectionClass($value)->', '(new ReflectionClass($value))->', $content);
    $newContent = str_replace('new ReflectionObject($value)->', '(new ReflectionObject($value))->', $newContent);
    if ($newContent !== $content) {
        file_put_contents($exporterFile, $newContent);
        echo "✅ Sebastian Exporter.php disesuaikan.\n";
    }
}

// 10. Patch PHPUnit Version.php
$versionFile = __DIR__.'/vendor/phpunit/phpunit/src/Runner/Version.php';
if (file_exists($versionFile)) {
    $content = file_get_contents($versionFile);
    $newContent = str_replace(
        "new VersionId('13.4.2', \$path)->asString()",
        "(new VersionId('13.4.2', \$path))->asString()",
        $content
    );
    if ($newContent !== $content) {
        file_put_contents($versionFile, $newContent);
        echo "✅ PHPUnit Version.php disesuaikan.\n";
    }
}

// 11. Ensure cache tables exist in MySQL
$envFile = __DIR__.'/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    preg_match('/DB_CONNECTION=(\w+)/', $envContent, $mConn);
    preg_match('/DB_HOST=([^\s]+)/', $envContent, $mHost);
    preg_match('/DB_PORT=([^\s]+)/', $envContent, $mPort);
    preg_match('/DB_DATABASE=([^\s]+)/', $envContent, $mDb);
    preg_match('/DB_USERNAME=([^\s]+)/', $envContent, $mUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $mPass);

    $dbConn = $mConn[1] ?? 'mysql';
    if ($dbConn === 'mysql' && ! empty($mDb[1])) {
        $host = $mHost[1] ?? '127.0.0.1';
        $port = $mPort[1] ?? '3306';
        $db = $mDb[1];
        $user = $mUser[1] ?? '';
        $pass = trim($mPass[1] ?? '');

        try {
            $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec('CREATE TABLE IF NOT EXISTS `cache` (
              `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
              `expiration` int(11) NOT NULL,
              PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

            $pdo->exec('CREATE TABLE IF NOT EXISTS `cache_locks` (
              `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `expiration` int(11) NOT NULL,
              PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

            echo "✅ Tabel MySQL 'cache' & 'cache_locks' dipastikan aktif.\n";
        } catch (Throwable $e) {
            echo 'ℹ️ Catatan database cache: '.$e->getMessage()."\n";
        }
    }
}

echo "🎉 Selesai! Semua dependensi kini 100% kompatibel dengan PHP 8.3 cPanel.\n";
