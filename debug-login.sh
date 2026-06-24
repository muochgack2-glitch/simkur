#!/bin/bash

echo "=== E-KALDIK Login Debug Script ==="
echo ""

cd /www/wwwroot/simkur

echo "1. Testing session driver..."
php artisan tinker --execute="echo 'Session Driver: ' . config('session.driver') . PHP_EOL;"

echo ""
echo "2. Testing session write..."
php artisan tinker --execute="
session()->put('debug_test', 'value123');
session()->save();
echo 'Session write: ' . (session()->get('debug_test') === 'value123' ? 'OK' : 'FAILED') . PHP_EOL;
"

echo ""
echo "3. Testing user authentication..."
php artisan tinker --execute="
\$result = \Auth::attempt(['username' => 'admin', 'password' => 'password']);
echo 'Auth attempt: ' . (\$result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
echo 'Auth check: ' . (\Auth::check() ? 'LOGGED IN' : 'NOT LOGGED IN') . PHP_EOL;
if (\Auth::check()) {
    echo 'User: ' . \Auth::user()->name . PHP_EOL;
}
"

echo ""
echo "4. Testing database session table..."
php artisan tinker --execute="
echo 'Sessions in DB: ' . \DB::table('sessions')->count() . PHP_EOL;
"

echo ""
echo "5. Checking session files..."
echo "Session files count: $(ls -1 storage/framework/sessions/ | wc -l)"
echo "Session folder permissions: $(ls -ld storage/framework/sessions/)"

echo ""
echo "6. Checking .env session config..."
grep SESSION .env

echo ""
echo "7. Testing Livewire component..."
php artisan livewire:list | grep Login

echo ""
echo "8. Recent Laravel errors..."
tail -20 storage/logs/laravel.log | grep -i error

echo ""
echo "=== Debug Complete ==="
echo ""
echo "Sekarang coba login di browser dan jalankan:"
echo "  tail -f storage/logs/laravel.log"
echo ""
echo "Untuk melihat real-time log saat login."
