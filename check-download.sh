#!/bin/bash

echo "=== Diagnostic: Teaching Material Download Issue ==="
echo ""

# 1. Check storage structure
echo "1. Checking storage directories..."
ls -la storage/app/public/ 2>/dev/null || echo "❌ storage/app/public/ not found"
ls -la storage/app/public/teaching-materials/ 2>/dev/null || echo "⚠️  teaching-materials folder not found"

echo ""

# 2. Check storage link
echo "2. Checking storage symlink..."
if [ -L "public/storage" ]; then
    echo "✅ Symlink exists"
    ls -la public/storage
else
    echo "❌ Symlink does NOT exist"
fi

echo ""

# 3. Check teaching materials in database
echo "3. Checking teaching materials in database..."
php artisan tinker --execute="
\$materials = \App\Models\TeachingMaterial::select('id', 'title', 'file_path', 'file_type')->take(5)->get();
foreach (\$materials as \$m) {
    echo 'ID: ' . \$m->id . ' | Title: ' . \$m->title . ' | Path: ' . \$m->file_path . ' | Type: ' . \$m->file_type . PHP_EOL;
    if (\$m->file_path) {
        \$exists = Storage::exists(\$m->file_path);
        echo '  File exists: ' . (\$exists ? 'YES' : 'NO') . PHP_EOL;
        if (\$exists) {
            \$fullPath = Storage::path(\$m->file_path);
            echo '  Full path: ' . \$fullPath . PHP_EOL;
            echo '  File size: ' . filesize(\$fullPath) . ' bytes' . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
"

echo ""

# 4. Check permissions
echo "4. Checking permissions..."
stat -c "storage/app/public: %a" storage/app/public/ 2>/dev/null
stat -c "storage/app/public/teaching-materials: %a" storage/app/public/teaching-materials/ 2>/dev/null

echo ""

# 5. Check route
echo "5. Testing route..."
php artisan route:list --name=teaching-materials.download

echo ""
echo "=== Diagnostic Complete ==="
echo ""
echo "📝 Notes:"
echo "- If storage link missing: php artisan storage:link"
echo "- If permissions wrong: chmod -R 775 storage/"
echo "- If files missing: Check file upload process"
