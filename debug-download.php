<?php
/**
 * Debug script untuk troubleshoot 404 download issue
 * Run: php debug-download.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\TeachingMaterial;
use Illuminate\Support\Facades\Storage;

echo "=== Teaching Material Download Debug ===\n\n";

// 1. Check first material
echo "1. Checking teaching materials...\n";
$materials = TeachingMaterial::orderBy('id', 'desc')->take(3)->get();

if ($materials->isEmpty()) {
    echo "❌ No teaching materials found in database!\n";
    exit;
}

echo "Found {$materials->count()} materials:\n\n";

foreach ($materials as $material) {
    echo "ID: {$material->id}\n";
    echo "Title: {$material->title}\n";
    echo "File Type: {$material->file_type}\n";
    echo "File Path: {$material->file_path}\n";
    
    if ($material->file_path) {
        $exists = Storage::disk('local')->exists($material->file_path);
        echo "File exists: " . ($exists ? '✅ YES' : '❌ NO') . "\n";
        
        if ($exists) {
            $fullPath = Storage::disk('local')->path($material->file_path);
            echo "Full path: {$fullPath}\n";
            
            if (file_exists($fullPath)) {
                echo "File size: " . filesize($fullPath) . " bytes\n";
                echo "Readable: " . (is_readable($fullPath) ? '✅ YES' : '❌ NO') . "\n";
            }
        }
        
        echo "Download URL: " . route('teaching-materials.download', $material->id) . "\n";
    } elseif ($material->external_link) {
        echo "External link: {$material->external_link}\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

// 2. Check storage configuration
echo "2. Storage Configuration:\n";
echo "Storage path: " . storage_path('app') . "\n";
echo "Public path: " . public_path('storage') . "\n";
echo "Storage link exists: " . (is_link(public_path('storage')) ? '✅ YES' : '❌ NO') . "\n";

if (is_link(public_path('storage'))) {
    echo "Link target: " . readlink(public_path('storage')) . "\n";
}

echo "\n";

// 3. Check directory permissions
echo "3. Directory Permissions:\n";
$dirs = [
    'storage/app',
    'storage/app/public',
    'storage/app/public/teaching-materials',
    'public/storage',
];

foreach ($dirs as $dir) {
    $path = base_path($dir);
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "{$dir}: {$perms} " . (is_writable($path) ? '✅ Writable' : '❌ Not writable') . "\n";
    } else {
        echo "{$dir}: ❌ Does not exist\n";
    }
}

echo "\n=== Debug Complete ===\n";
