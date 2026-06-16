<?php
/**
 * Generate proper PNG icons for PWA
 * Access this file via browser: http://localhost/SahelSoftWebsite/public/images/create-pwa-icons.php
 */

header('Content-Type: text/plain');

// Check if GD library is available
if (!extension_loaded('gd')) {
    echo "❌ GD library is not available. Cannot generate PNG icons.\n";
    echo "Please enable GD extension in php.ini\n";
    exit;
}

try {
    // Generate 192x192 icon
    $im192 = imagecreatetruecolor(192, 192);
    $bg192 = imagecolorallocate($im192, 15, 118, 110); // #0f766e
    $white192 = imagecolorallocate($im192, 255, 255, 255);
    imagefill($im192, 0, 0, $bg192);

    // Add "SS" text centered
    $font = 5; // Built-in font
    $text = 'SS';
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (192 - $textWidth) / 2;
    $y = (192 - $textHeight) / 2;
    imagestring($im192, $font, $x, $y, $text, $white192);

    $result192 = imagepng($im192, __DIR__ . '/icon-192x192.png');
    imagedestroy($im192);

    // Generate 512x512 icon
    $im512 = imagecreatetruecolor(512, 512);
    $bg512 = imagecolorallocate($im512, 15, 118, 110); // #0f766e
    $white512 = imagecolorallocate($im512, 255, 255, 255);
    imagefill($im512, 0, 0, $bg512);

    // Add "SS" text centered
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (512 - $textWidth) / 2;
    $y = (512 - $textHeight) / 2;
    imagestring($im512, $font, $x, $y, $text, $white512);

    $result512 = imagepng($im512, __DIR__ . '/icon-512x512.png');
    imagedestroy($im512);

    if ($result192 && $result512) {
        echo "✅ PNG icons generated successfully!\n\n";
        echo "Files created:\n";
        echo "- icon-192x192.png (192x192)\n";
        echo "- icon-512x512.png (512x512)\n\n";
        echo "File sizes:\n";
        echo "- icon-192x192.png: " . filesize(__DIR__ . '/icon-192x192.png') . " bytes\n";
        echo "- icon-512x512.png: " . filesize(__DIR__ . '/icon-512x512.png') . " bytes\n\n";
        echo "✅ Icons are now ready for PWA installation!\n";
    } else {
        echo "❌ Failed to generate icons\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
