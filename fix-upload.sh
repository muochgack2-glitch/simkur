#!/bin/bash
echo "=== Fixing Upload Issue ==="

# 1. Update .env
echo "1. Updating .env..."
sed -i 's|APP_URL=http://|APP_URL=https://|g' .env
grep -q "LIVEWIRE_ASSET_URL" .env || echo "LIVEWIRE_ASSET_URL=https://simkur.smkpgriblora.sch.id" >> .env
grep -q "ASSET_URL" .env || echo "ASSET_URL=https://simkur.smkpgriblora.sch.id" >> .env

# 2. Fix AppServiceProvider
echo "2. Fixing AppServiceProvider..."
cat > app/Providers/AppServiceProvider.php << 'PHPCODE'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
PHPCODE

# 3. Set permissions
echo "3. Setting permissions..."
chmod -R 777 storage
chmod -R 777 bootstrap/cache
mkdir -p storage/app/public/teaching-materials
chmod -R 777 storage/app/public

# 4. Storage link
echo "4. Creating storage link..."
rm -f public/storage
php artisan storage:link

# 5. Publish Livewire assets
echo "5. Publishing Livewire assets..."
php artisan livewire:publish --assets

# 6. Clear cache
echo "6. Clearing all cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

# 7. Restart PHP
echo "7. Restarting PHP-FPM..."
systemctl restart php8.3-fpm 2>/dev/null || systemctl restart php-fpm 2>/dev/null || echo "Could not restart PHP-FPM (may need manual restart)"

# 8. Restart Nginx
echo "8. Restarting Nginx..."
systemctl restart nginx 2>/dev/null || echo "Could not restart Nginx (may need manual restart)"

echo ""
echo "=== ✅ Fix Complete! ==="
echo ""
echo "Next steps:"
echo "1. Clear browser cache (Ctrl+Shift+Delete)"
echo "2. Clear browser cookies for simkur.smkpgriblora.sch.id"
echo "3. Close browser completely"
echo "4. Open browser again"
echo "5. Hard refresh page (Ctrl+Shift+R)"
echo "6. Try upload file again"
echo ""
echo "If still not working, check:"
echo "- tail -f storage/logs/laravel.log"
echo "- Browser console (F12 -> Console)"
