#!/usr/bin/env php
<?php

/**
 * ULTIMATE SESSION FIX
 * 
 * Script ini akan:
 * 1. Menghapus SEMUA file cache (config, route, view, event)
 * 2. Membersihkan session yang ada
 * 3. Memverifikasi permission
 * 4. Me-reload .env dan rebuild cache dengan benar
 * 5. Test session persistence
 */

echo "========================================\n";
echo "   ULTIMATE SESSION FIX - E-KALDIK     \n";
echo "========================================\n\n";

// 1. HAPUS SEMUA FILE CACHE
echo "[1] Menghapus semua file cache...\n";

$cacheFiles = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/routes-v7.php',
    __DIR__ . '/bootstrap/cache/events.php',
    __DIR__ . '/bootstrap/cache/packages.php',
    __DIR__ . '/bootstrap/cache/services.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "  ✓ Deleted: " . basename($file) . "\n";
    }
}

// 2. HAPUS SEMUA SESSION FILES
echo "\n[2] Menghapus semua file session...\n";
$sessionPath = __DIR__ . '/storage/framework/sessions';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "  ✓ " . count($files) . " session files deleted\n";
}

// 3. HAPUS CACHE DIRECTORY
echo "\n[3] Menghapus cache directory...\n";
$cachePath = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cachePath)) {
    deleteDirectory($cachePath);
    mkdir($cachePath, 0755, true);
    echo "  ✓ Cache directory cleared\n";
}

// 4. VERIFIKASI PERMISSIONS
echo "\n[4] Memeriksa permissions...\n";
$dirsToCheck = [
    'storage',
    'storage/framework',
    'storage/framework/sessions',
    'storage/framework/cache',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

foreach ($dirsToCheck as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        chmod($fullPath, 0775);
        echo "  ✓ Set 775: $dir\n";
    }
}

// 5. BACA .ENV DAN TAMPILKAN SESSION CONFIG
echo "\n[5] Membaca konfigurasi .env...\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    // Extract session settings
    preg_match('/SESSION_DRIVER=(.+)$/m', $envContent, $driverMatch);
    preg_match('/SESSION_LIFETIME=(.+)$/m', $envContent, $lifetimeMatch);
    preg_match('/SESSION_SECURE_COOKIE=(.+)$/m', $envContent, $secureMatch);
    preg_match('/SESSION_SAME_SITE=(.+)$/m', $envContent, $samesiteMatch);
    
    echo "  Current SESSION_DRIVER: " . ($driverMatch[1] ?? 'NOT SET') . "\n";
    echo "  Current SESSION_LIFETIME: " . ($lifetimeMatch[1] ?? 'NOT SET') . "\n";
    echo "  Current SESSION_SECURE_COOKIE: " . ($secureMatch[1] ?? 'NOT SET') . "\n";
    echo "  Current SESSION_SAME_SITE: " . ($samesiteMatch[1] ?? 'NOT SET') . "\n";
}

// 6. REBUILD CACHE
echo "\n[6] Rebuilding cache dengan artisan...\n";
passthru('cd ' . __DIR__ . ' && php artisan config:clear 2>&1');
passthru('cd ' . __DIR__ . ' && php artisan cache:clear 2>&1');
passthru('cd ' . __DIR__ . ' && php artisan view:clear 2>&1');
passthru('cd ' . __DIR__ . ' && php artisan route:clear 2>&1');

echo "\n[7] Rebuilding config cache...\n";
passthru('cd ' . __DIR__ . ' && php artisan config:cache 2>&1');

// 7. VERIFIKASI DENGAN ARTISAN TINKER
echo "\n[8] Verifikasi konfigurasi session...\n";
$verifyCommand = 'cd ' . __DIR__ . ' && php artisan tinker --execute="
echo \'Session Driver: \' . config(\'session.driver\') . PHP_EOL;
echo \'Session Lifetime: \' . config(\'session.lifetime\') . PHP_EOL;
echo \'Session Table: \' . config(\'session.table\') . PHP_EOL;
echo \'Session Cookie: \' . config(\'session.cookie\') . PHP_EOL;
echo \'Session Secure: \' . (config(\'session.secure\') ? \'true\' : \'false\') . PHP_EOL;
echo \'Session Same Site: \' . config(\'session.same_site\') . PHP_EOL;
echo \'App Key Set: \' . (config(\'app.key\') ? \'YES\' : \'NO\') . PHP_EOL;
" 2>&1';

passthru($verifyCommand);

// 8. TEST SESSION PERSISTENCE
echo "\n[9] Testing session persistence...\n";
$testCommand = 'cd ' . __DIR__ . ' && php artisan tinker --execute="
session()->put(\'test_key\', \'test_value_\' . time());
echo \'Stored: \' . session()->get(\'test_key\') . PHP_EOL;
session()->save();
echo \'Session ID: \' . session()->getId() . PHP_EOL;
echo \'Session saved successfully\' . PHP_EOL;
" 2>&1';

passthru($testCommand);

echo "\n========================================\n";
echo "           FIX COMPLETED!               \n";
echo "========================================\n\n";

echo "NEXT STEPS:\n";
echo "1. Restart PHP-FPM: systemctl restart php-fpm-83\n";
echo "2. Restart Nginx: systemctl restart nginx\n";
echo "3. Clear browser cookies completely\n";
echo "4. Try login again\n";
echo "5. Monitor log: tail -f storage/logs/laravel.log\n\n";

// Helper function
function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    rmdir($dir);
}
