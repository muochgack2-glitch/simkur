<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateFavicon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'favicon:generate {--source=public/images/logo.jpg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate favicon files from logo image using GD';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sourcePath = $this->option('source');
        
        if (!file_exists($sourcePath)) {
            $this->error("Source file not found: {$sourcePath}");
            return Command::FAILURE;
        }

        if (!extension_loaded('gd')) {
            $this->error("GD extension is not loaded. Please install php-gd extension.");
            return Command::FAILURE;
        }

        $this->info("Generating favicon from: {$sourcePath}");

        try {
            // Detect image type and create image resource
            $imageInfo = getimagesize($sourcePath);
            $imageType = $imageInfo[2];
            
            $sourceImage = match($imageType) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
                IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
                IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
                default => throw new \Exception("Unsupported image type")
            };

            if (!$sourceImage) {
                throw new \Exception("Failed to create image from source");
            }

            // Generate different sizes
            $sizes = [
                ['size' => 16, 'name' => 'favicon-16x16.png'],
                ['size' => 32, 'name' => 'favicon-32x32.png'],
                ['size' => 64, 'name' => 'favicon-64x64.png'],
                ['size' => 180, 'name' => 'apple-touch-icon.png'],
                ['size' => 192, 'name' => 'android-chrome-192x192.png'],
                ['size' => 512, 'name' => 'android-chrome-512x512.png'],
            ];

            foreach ($sizes as $sizeInfo) {
                $resized = $this->resizeImage($sourceImage, $sizeInfo['size'], $sizeInfo['size']);
                $outputPath = public_path($sizeInfo['name']);
                imagepng($resized, $outputPath);
                imagedestroy($resized);
                $this->info("✓ Created: {$sizeInfo['name']} ({$sizeInfo['size']}x{$sizeInfo['size']})");
            }

            // Create main favicon.ico (using 32x32 PNG, browsers support it)
            $favicon = $this->resizeImage($sourceImage, 32, 32);
            $faviconPath = public_path('favicon.ico');
            imagepng($favicon, $faviconPath);
            imagedestroy($favicon);
            $this->info("✓ Created: favicon.ico (32x32 PNG format)");

            // Clean up
            imagedestroy($sourceImage);

            $this->newLine();
            $this->info("🎉 Favicon generation completed successfully!");
            $this->info("Files created in public/ directory");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error generating favicon: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Resize image to specified dimensions
     */
    private function resizeImage($sourceImage, int $width, int $height)
    {
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Create new image with transparency support
        $resized = imagecreatetruecolor($width, $height);
        
        // Enable alpha blending
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        
        // Fill with transparent background
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);
        
        // Enable alpha blending for copying
        imagealphablending($resized, true);

        // Copy and resize
        imagecopyresampled(
            $resized,
            $sourceImage,
            0, 0, 0, 0,
            $width, $height,
            $sourceWidth, $sourceHeight
        );

        return $resized;
    }
}
