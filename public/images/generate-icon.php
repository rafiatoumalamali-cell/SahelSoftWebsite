<?php
// Create a simple 192x192 PNG icon
$img = imagecreatetruecolor(192, 192);
$bg = imagecolorallocate($img, 14, 159, 110); // Green background
$text = imagecolorallocate($img, 255, 255, 255); // White text

// Fill background
imagefill($img, 0, 0, $bg);

// Add simple text
imagettftext($img, 48, 0, 50, 100, $text, __DIR__ . '/arial.ttf', 'SS');

header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);
?>
