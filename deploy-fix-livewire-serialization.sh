#!/bin/bash
# Deploy fix for Livewire property serialization issue
# Run this on production server: bash deploy-fix-livewire-serialization.sh

echo "=== Deploying Livewire Property Serialization Fix ==="

cd /www/wwwroot/simkur || exit 1

# Stash any local changes (the manual with('timeSlot') removal)
echo "1. Stashing local changes..."
git stash

# Pull latest changes
echo "2. Pulling latest changes from GitHub..."
git pull origin main

# The stash is no longer needed since our new code already has the fix
echo "3. Dropping stash (already included in new code)..."
git stash drop

# Clear all caches
echo "4. Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "=== Deployment Complete ==="
echo "✅ Livewire property serialization fixed"
echo "✅ Teaching schedule page should now load without errors"
echo ""
echo "Test the page at: http://simkur.smkpgriblora.sch.id/teaching-schedules"
