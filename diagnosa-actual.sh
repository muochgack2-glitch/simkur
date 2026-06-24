#!/bin/bash

# ========================================
# DIAGNOSA ACTUAL - TANGKAP BUKTI NYATA
# ========================================

echo "========================================"
echo "   DIAGNOSA ACTUAL KONDISI HOSTING     "
echo "========================================"
echo ""

cd /www/wwwroot/simkur

echo "=== 1. ISI .ENV (SESSION SECTION) ==="
echo "-------------------------------------"
grep -E "SESSION_|APP_KEY|APP_DEBUG" .env
echo ""

echo "=== 2. CONFIG ACTUAL DI LARAVEL ==="
echo "-------------------------------------"
php artisan tinker --execute="
echo 'session.driver: ' . config('session.driver') . PHP_EOL;
echo 'session.lifetime: ' . config('session.lifetime') . PHP_EOL;
echo 'session.secure: ' . var_export(config('session.secure'), true) . PHP_EOL;
echo 'session.same_site: ' . config('session.same_site') . PHP_EOL;
echo 'session.cookie: ' . config('session.cookie') . PHP_EOL;
echo 'session.path: ' . config('session.path') . PHP_EOL;
echo 'session.domain: ' . var_export(config('session.domain'), true) . PHP_EOL;
echo 'session.http_only: ' . var_export(config('session.http_only'), true) . PHP_EOL;
echo 'app.key: ' . (config('app.key') ? 'SET' : 'NOT SET') . PHP_EOL;
echo 'app.debug: ' . var_export(config('app.debug'), true) . PHP_EOL;
echo 'app.env: ' . config('app.env') . PHP_EOL;
"
echo ""

echo "=== 3. CACHE FILES YANG ADA ==="
echo "-------------------------------------"
ls -lah bootstrap/cache/config.php 2>/dev/null && echo "CONFIG CACHE EXISTS!" || echo "No config cache"
ls -lah bootstrap/cache/routes-v7.php 2>/dev/null && echo "ROUTE CACHE EXISTS!" || echo "No route cache"
echo ""

echo "=== 4. SESSION DIRECTORY ==="
echo "-------------------------------------"
ls -ld storage/framework/sessions
ls -lah storage/framework/sessions/ | head -5
echo "Total files: $(ls -1 storage/framework/sessions/ | wc -l)"
echo ""

echo "=== 5. TEST SESSION WRITE & READ ==="
echo "-------------------------------------"
php artisan tinker --execute="
session()->start();
\$id1 = session()->getId();
echo 'Session ID: ' . \$id1 . PHP_EOL;

session()->put('test_key', 'test_value_' . time());
session()->save();
echo 'After save - Session ID: ' . session()->getId() . PHP_EOL;

\$value = session()->get('test_key');
echo 'Retrieved value: ' . var_export(\$value, true) . PHP_EOL;

if (\$value === null) {
    echo 'ERROR: Session NOT persisting!' . PHP_EOL;
} else {
    echo 'SUCCESS: Session IS persisting!' . PHP_EOL;
}
"
echo ""

echo "=== 6. TEST AUTH ==="
echo "-------------------------------------"
php artisan tinker --execute="
\$user = \App\Models\User::where('username', 'admin')->first();
if (\$user) {
    echo 'User found: ' . \$user->username . ' (ID: ' . \$user->id . ')' . PHP_EOL;
    echo 'User active: ' . (\$user->is_active ? 'YES' : 'NO') . PHP_EOL;
    echo 'Password hash: ' . substr(\$user->password, 0, 20) . '...' . PHP_EOL;
    
    \$check = Hash::check('password', \$user->password);
    echo 'Password check: ' . (\$check ? 'VALID' : 'INVALID') . PHP_EOL;
    
    \$attempt = Auth::attempt(['username' => 'admin', 'password' => 'password']);
    echo 'Auth attempt: ' . (\$attempt ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    if (\$attempt) {
        echo 'Auth user ID: ' . Auth::id() . PHP_EOL;
        echo 'Auth check: ' . (Auth::check() ? 'YES' : 'NO') . PHP_EOL;
    }
} else {
    echo 'ERROR: User admin not found!' . PHP_EOL;
}
"
echo ""

echo "=== 7. LIVEWIRE CHECK ==="
echo "-------------------------------------"
php artisan route:list | grep -i login
echo ""

echo "=== 8. LAST 20 LINES LARAVEL LOG ==="
echo "-------------------------------------"
tail -20 storage/logs/laravel.log
echo ""

echo "=== 9. PHP-FPM STATUS ==="
echo "-------------------------------------"
systemctl status php-fpm-83 --no-pager | head -10
echo ""

echo "=== 10. NGINX CONFIG CHECK ==="
echo "-------------------------------------"
cat /www/server/panel/vhost/nginx/simkur.smkpgriblora.sch.id.conf | grep -A 5 "location.*\.php"
echo ""

echo "========================================"
echo "         DIAGNOSA SELESAI              "
echo "========================================"
echo ""
echo "SILAKAN KIRIM OUTPUT INI SEMUA!"
echo ""
