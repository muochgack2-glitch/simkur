#!/bin/bash

echo "=== Fix Nested Folder Structure ==="
echo ""

cd storage/app/public/teaching-materials

# Check for nested folders
echo "Checking for nested folders..."
find . -type d -mindepth 2 -maxdepth 2

# Fix nested structure (move files up one level)
echo ""
echo "Fixing structure..."

for category in */; do
    if [ -d "${category}${category}" ]; then
        echo "Found nested: ${category}${category}"
        echo "Moving files from ${category}${category} to ${category}"
        
        # Move files up one level
        mv "${category}${category}"/* "${category}/" 2>/dev/null || true
        
        # Remove empty nested folder
        rmdir "${category}${category}" 2>/dev/null || true
        
        echo "✅ Fixed ${category}"
    fi
done

echo ""
echo "=== Current structure ==="
ls -lah

echo ""
echo "=== Fix Complete! ==="
