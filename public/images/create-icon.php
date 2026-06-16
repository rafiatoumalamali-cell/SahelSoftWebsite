<?php
// Create a simple 192x192 PNG icon
$img = imagecreatetruecolor(192, 192);
$bg = imagecolorallocate($img, 14, 159, 110); // Green background
$text = imagecolorallocate($img, 255, 255, 255); // White text

// Fill background
imagefill($img, 0, 0, $bg);

// Add a simple circle
imagefilledellipse($img, 96, 96, 150, 150, $text);

// Add text
imagestring($img, 5, 70, 90, 'SS', $text);

header('Content-Type: image/png');
imagepng($img, 'icon-192x192.png');
imagedestroy($img);
echo "Icon created successfully";
?>
