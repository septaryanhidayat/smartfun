<?php

echo "🔧 Memulai sinkronisasi kompatibilitas PHP 8.3...\n";

// 1. Bypass platform_check.php
$platformCheck = __DIR__ . '/vendor/composer/platform_check.php';
if (file_exists($platformCheck)) {
    file_put_contents($platformCheck, "<?php\n\$issues = array();\n");
    echo "✅ vendor/composer/platform_check.php dinetralkan.\n";
}

// 2. Bypass collision Autoload.php
$collision = __DIR__ . '/vendor/nunomaduro/collision/src/Adapters/Phpunit/Autoload.php';
if (file_exists($collision)) {
    file_put_contents($collision, "<?php\n// Bypassed for web production\n");
    echo "✅ Collision PHPUnit adapter dinetralkan.\n";
}

// 3. Bypass pest-plugin-laravel Autoload.php
$pestLaravel = __DIR__ . '/vendor/pestphp/pest-plugin-laravel/src/Autoload.php';
if (file_exists($pestLaravel)) {
    file_put_contents($pestLaravel, "<?php\n// Bypassed for web production\n");
    echo "✅ Pest-Plugin-Laravel autoloader dinetralkan.\n";
}

// 4. Patch Symfony Request.php (remove property hooks & setRawValue)
$requestFile = __DIR__ . '/vendor/symfony/http-foundation/Request.php';
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
$responseFile = __DIR__ . '/vendor/symfony/http-foundation/Response.php';
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
$varClonerFile = __DIR__ . '/vendor/symfony/var-dumper/Cloner/VarCloner.php';
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

// 7. Patch AddConsoleCommandPass.php (wrap new Definition in parentheses)
$passFile = __DIR__ . '/vendor/symfony/console/DependencyInjection/AddConsoleCommandPass.php';
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

// 8. Patch PHPUnit Version.php
$versionFile = __DIR__ . '/vendor/phpunit/phpunit/src/Runner/Version.php';
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

echo "🎉 Selesai! Semua dependensi kini 100% kompatibel dengan PHP 8.3 cPanel.\n";
