#!/bin/bash

echo "=== Fix Livewire Issues ==="
echo ""

echo "Step 1: Git pull latest code..."
git pull origin main
echo ""

echo "Step 2: Clear all Laravel caches..."
php artisan optimize:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo ""

echo "Step 3: Publish Livewire assets..."
php artisan livewire:publish --assets
echo ""

echo "Step 4: Clear and rebuild optimizations..."
php artisan optimize
echo ""

echo "Step 5: Fix permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
echo ""

echo "=== Done! ==="
echo ""
echo "Next steps:"
echo "1. Hard refresh browser (Ctrl+Shift+R atau Ctrl+F5)"
echo "2. Clear browser cache jika masih error"
echo "3. Coba lagi ubah tanggal di form"
