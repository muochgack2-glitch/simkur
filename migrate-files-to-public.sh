#!/bin/bash

echo "=== Migrate Teaching Material Files to Public Storage ==="
echo ""

# Check if private folder exists
if [ ! -d "storage/app/private/teaching-materials" ]; then
    echo "❌ No private/teaching-materials folder found. Nothing to migrate."
    exit 0
fi

echo "📁 Found files in storage/app/private/teaching-materials"
echo ""

# Create public folder if not exists
mkdir -p storage/app/public/teaching-materials

# Count files
FILE_COUNT=$(find storage/app/private/teaching-materials -type f | wc -l)
echo "Found ${FILE_COUNT} files to migrate"
echo ""

# Move files
echo "Moving files..."
rsync -av --remove-source-files storage/app/private/teaching-materials/ storage/app/public/teaching-materials/

# Remove empty directories
find storage/app/private/teaching-materials -type d -empty -delete

echo ""
echo "✅ Files migrated successfully!"
echo ""

# Fix permissions
echo "Fixing permissions..."
chmod -R 775 storage/app/public/teaching-materials/
chown -R www-data:www-data storage/app/public/teaching-materials/

echo ""
echo "=== Migration Complete! ==="
echo ""
echo "📝 Next steps:"
echo "1. Verify files: ls -lah storage/app/public/teaching-materials/"
echo "2. Test download from browser"
echo "3. If working, remove old folder: rm -rf storage/app/private/teaching-materials"
