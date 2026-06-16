<?php
/**
 * Generate proper PNG icons for PWA
 */

// Generate 192x192 icon
$im192 = imagecreatetruecolor(192, 192);
$bg192 = imagecolorallocate($im192, 15, 118, 110); // #0f766e
$white192 = imagecolorallocate($im192, 255, 255, 255);
imagefill($im192, 0, 0, $bg192);

// Add "SS" text
imagestring($im192, 5, 70, 85, 'SS', $white192);

imagepng($im192, __DIR__ . '/icon-192x192.png');
imagedestroy($im192);

// Generate 512x512 icon
$im512 = imagecreatetruecolor(512, 512);
$bg512 = imagecolorallocate($im512, 15, 118, 110); // #0f766e
$white512 = imagecolorallocate($im512, 255, 255, 255);
imagefill($im512, 0, 0, $bg512);

// Add "SS" text
imagestring($im512, 5, 200, 240, 'SS', $white512);

imagepng($im512, __DIR__ . '/icon-512x512.png');
imagedestroy($im512);

echo "✅ PNG icons generated successfully!<br>";
echo "Files created:<br>";
echo "- icon-192x192.png (192x192)<br>";
echo "- icon-512x512.png (512x512)<br>";
?>
