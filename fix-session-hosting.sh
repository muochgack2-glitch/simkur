#!/bin/bash

# ========================================
# ULTIMATE SESSION FIX - FOR HOSTING
# ========================================
# Run this script on hosting server at /www/wwwroot/simkur

echo "========================================"
echo "   ULTIMATE SESSION FIX - HOSTING      "
echo "========================================"
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Go to project directory
cd /www/wwwroot/simkur || exit 1

echo -e "${YELLOW}[1] Stopping PHP-FPM...${NC}"
systemctl stop php-fpm-83
echo -e "${GREEN}✓ PHP-FPM stopped${NC}"

echo ""
echo -e "${YELLOW}[2] Deleting ALL cache files...${NC}"
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-v7.php
rm -f bootstrap/cache/events.php
echo -e "${GREEN}✓ Bootstrap cache deleted${NC}"

echo ""
echo -e "${YELLOW}[3] Deleting ALL session files...${NC}"
rm -rf storage/framework/sessions/*
echo -e "${GREEN}✓ Session files deleted${NC}"

echo ""
echo -e "${YELLOW}[4] Deleting ALL cache data...${NC}"
rm -rf storage/framework/cache/data/*
echo -e "${GREEN}✓ Cache data deleted${NC}"

echo ""
echo -e "${YELLOW}[5] Setting correct permissions...${NC}"
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${YELLOW}[6] Current .env SESSION settings:${NC}"
grep "SESSION_" .env

echo ""
echo -e "${YELLOW}[7] Do you want to update .env to use FILE driver? (recommended)${NC}"
echo -e "${YELLOW}   Current: $(grep SESSION_DRIVER .env)${NC}"
read -p "Update to SESSION_DRIVER=file? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    sed -i 's/SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env
    sed -i 's/SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=true/' .env
    sed -i 's/SESSION_SAME_SITE=.*/SESSION_SAME_SITE=lax/' .env
    echo -e "${GREEN}✓ .env updated${NC}"
fi

echo ""
echo -e "${YELLOW}[8] Clearing Laravel cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✓ Laravel cache cleared${NC}"

echo ""
echo -e "${YELLOW}[9] Rebuilding config cache...${NC}"
php artisan config:cache
echo -e "${GREEN}✓ Config cache rebuilt${NC}"

echo ""
echo -e "${YELLOW}[10] Verifying session configuration...${NC}"
php artisan tinker --execute="
echo 'Session Driver: ' . config('session.driver') . PHP_EOL;
echo 'Session Lifetime: ' . config('session.lifetime') . PHP_EOL;
echo 'Session Cookie: ' . config('session.cookie') . PHP_EOL;
echo 'Session Secure: ' . (config('session.secure') ? 'true' : 'false') . PHP_EOL;
echo 'Session Same Site: ' . config('session.same_site') . PHP_EOL;
echo 'App Debug: ' . (config('app.debug') ? 'true' : 'false') . PHP_EOL;
"

echo ""
echo -e "${YELLOW}[11] Testing session persistence...${NC}"
php artisan tinker --execute="
session()->put('test_key', 'test_value_' . time());
\$value = session()->get('test_key');
echo 'Stored value: ' . \$value . PHP_EOL;
session()->save();
echo 'Session ID: ' . session()->getId() . PHP_EOL;
if (\$value) {
    echo 'SUCCESS: Session is working!' . PHP_EOL;
} else {
    echo 'ERROR: Session not persisting!' . PHP_EOL;
}
"

echo ""
echo -e "${YELLOW}[12] Starting PHP-FPM...${NC}"
systemctl start php-fpm-83
systemctl status php-fpm-83 --no-pager
echo -e "${GREEN}✓ PHP-FPM started${NC}"

echo ""
echo -e "${YELLOW}[13] Restarting Nginx...${NC}"
systemctl restart nginx
systemctl status nginx --no-pager
echo -e "${GREEN}✓ Nginx restarted${NC}"

echo ""
echo "========================================"
echo -e "${GREEN}        FIX COMPLETED!                ${NC}"
echo "========================================"
echo ""
echo -e "${YELLOW}IMPORTANT NEXT STEPS:${NC}"
echo "1. Clear ALL browser cookies"
echo "2. Close ALL browser windows"
echo "3. Open NEW incognito/private window"
echo "4. Go to: https://simkur.smkpgriblora.sch.id"
echo "5. Try login with:"
echo "   Username: admin"
echo "   Password: password"
echo ""
echo -e "${YELLOW}TO MONITOR:${NC}"
echo "tail -f storage/logs/laravel.log"
echo ""
echo -e "${YELLOW}IF STILL NOT WORKING:${NC}"
echo "Check if session files are being created:"
echo "ls -lah storage/framework/sessions/"
echo ""
echo "Check session permissions:"
echo "ls -ld storage/framework/sessions"
echo ""
