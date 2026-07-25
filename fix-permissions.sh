#!/bin/bash

echo "=== Fix Storage Permissions ==="
echo ""

echo "Fixing all storage directories..."
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

echo "Setting ownership to www-data..."
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

echo ""
echo "Checking permissions..."
ls -la storage/framework/ | head -10

echo ""
echo "=== Done! ==="
echo ""
echo "Permissions fixed:"
echo "  - storage/framework/views: writable"
echo "  - storage/framework/cache: writable"
echo "  - storage/framework/sessions: writable"
echo "  - storage/logs: writable"
echo "  - bootstrap/cache: writable"
