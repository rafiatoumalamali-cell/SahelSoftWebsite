<?php
/**
 * Convert custom uploaded icon to PWA icon sizes
 * Access this file via browser: http://localhost/SahelSoftWebsite/public/images/convert-custom-icon.php
 */

header('Content-Type: text/plain');

// Path to the uploaded icon
$sourceImage = 'C:\\Users\\Rafiatou Malam Ali\\AppData\\Local\\Packages\\Microsoft.VisualStudioCode_8wekyb3d8bbwe\\AppData\\icon.png';

// Check if GD library is available
if (!extension_loaded('gd')) {
    echo "❌ GD library is not available. Cannot convert icons.\n";
    echo "Please enable GD extension in php.ini\n";
    exit;
}

// Check if source image exists
if (!file_exists($sourceImage)) {
    echo "❌ Source image not found: $sourceImage\n";
    exit;
}

try {
    // Load the source image
    $imageInfo = getimagesize($sourceImage);
    if (!$imageInfo) {
        echo "❌ Could not read source image\n";
        exit;
    }

    echo "Source image loaded: {$imageInfo[0]}x{$imageInfo[1]}\n";
    echo "Image type: {$imageInfo['mime']}\n\n";

    // Create image resource based on type
    switch ($imageInfo[2]) {
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourceImage);
            break;
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourceImage);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourceImage);
            break;
        default:
            echo "❌ Unsupported image type\n";
            exit;
    }

    if (!$source) {
        echo "❌ Could not create image resource\n";
        exit;
    }

    // Generate 192x192 icon
    $im192 = imagecreatetruecolor(192, 192);
    imagealphablending($im192, false);
    imagesavealpha($im192, true);
    
    // Resize source image to 192x192
    imagecopyresampled($im192, $source, 0, 0, 0, 0, 192, 192, $imageInfo[0], $imageInfo[1]);
    
    $result192 = imagepng($im192, __DIR__ . '/icon-192x192.png');
    imagedestroy($im192);

    // Generate 512x512 icon
    $im512 = imagecreatetruecolor(512, 512);
    imagealphablending($im512, false);
    imagesavealpha($im512, true);
    
    // Resize source image to 512x512
    imagecopyresampled($im512, $source, 0, 0, 0, 0, 512, 512, $imageInfo[0], $imageInfo[1]);
    
    $result512 = imagepng($im512, __DIR__ . '/icon-512x512.png');
    imagedestroy($im512);

    // Also update SVG with a reference to the PNG
    $svg192 = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 192">
  <image href="icon-192x192.png" width="192" height="192"/>
</svg>';
file_put_contents(__DIR__ . '/icon-192x192.svg', $svg192);

    $svg512 = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
  <image href="icon-512x512.png" width="512" height="512"/>
</svg>';
file_put_contents(__DIR__ . '/icon-512x512.svg', $svg512);

    imagedestroy($source);

    if ($result192 && $result512) {
        echo "✅ Custom icon converted successfully!\n\n";
        echo "Files created:\n";
        echo "- icon-192x192.png (192x192)\n";
        echo "- icon-512x512.png (512x512)\n";
        echo "- icon-192x192.svg (updated)\n";
        echo "- icon-512x512.svg (updated)\n\n";
        echo "File sizes:\n";
        echo "- icon-192x192.png: " . filesize(__DIR__ . '/icon-192x192.png') . " bytes\n";
        echo "- icon-512x512.png: " . filesize(__DIR__ . '/icon-512x512.png') . " bytes\n\n";
        echo "✅ Your custom icon is now set as the PWA icon!\n";
    } else {
        echo "❌ Failed to convert icons\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
