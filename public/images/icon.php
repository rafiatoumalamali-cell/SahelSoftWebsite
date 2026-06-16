<?php
// Serve a simple PNG icon with dynamic sizing
header('Content-Type: image/png');
header('Cache-Control: public, max-age=3600');

// Get size from parameter, default to 192
$size = isset($_GET['size']) ? (int)$_GET['size'] : 192;
$size = in_array($size, [192, 512]) ? $size : 192;

// Create PNG
$img = imagecreatetruecolor($size, $size);

// Define colors
$bgColor = imagecolorallocate($img, 15, 118, 110); // #0f766e
$textColor = imagecolorallocate($img, 255, 255, 255); // white

// Fill background
imagefill($img, 0, 0, $bgColor);

// Add text
$text = "SS";
$font = 5; // Built-in font

// Calculate text position to center it
$textWidth = imagefontwidth($font) * strlen($text);
$textHeight = imagefontheight($font);
$x = ($size - $textWidth) / 2;
$y = ($size - $textHeight) / 2;

imagestring($img, $font, $x, $y, $text, $textColor);

// Output the image
imagepng($img);
imagedestroy($img);
?>
