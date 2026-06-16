<?php
/**
 * Generate placeholder screenshots for PWA install UI
 * Access this file via browser: http://localhost/SahelSoftWebsite/public/images/create-pwa-screenshots.php
 */

header('Content-Type: text/plain');

// Check if GD library is available
if (!extension_loaded('gd')) {
    echo "❌ GD library is not available. Cannot generate screenshots.\n";
    echo "Please enable GD extension in php.ini\n";
    exit;
}

try {
    // Generate wide screenshot (1280x720) for desktop
    $imWide = imagecreatetruecolor(1280, 720);
    $bgWide = imagecolorallocate($imWide, 255, 255, 255);
    $headerBg = imagecolorallocate($imWide, 15, 118, 110); // #0f766e
    $textColor = imagecolorallocate($imWide, 51, 51, 51);
    $sidebarBg = imagecolorallocate($imWide, 243, 244, 246);
    
    imagefill($imWide, 0, 0, $bgWide);
    
    // Header
    imagefilledrectangle($imWide, 0, 0, 1280, 80, $headerBg);
    imagestring($imWide, 5, 20, 30, 'SahelSoft Dashboard', 16777215);
    
    // Content area
    imagestring($imWide, 3, 50, 150, 'Welcome to SahelSoft', $textColor);
    imagestring($imWide, 2, 50, 200, 'Professional business management platform', $textColor);
    
    // Sidebar
    imagefilledrectangle($imWide, 0, 80, 250, 720, $sidebarBg);
    imagestring($imWide, 2, 20, 120, 'Dashboard', $textColor);
    imagestring($imWide, 2, 20, 150, 'Projects', $textColor);
    imagestring($imWide, 2, 20, 180, 'Team', $textColor);
    imagestring($imWide, 2, 20, 210, 'Reports', $textColor);
    
    $resultWide = imagepng($imWide, __DIR__ . '/screenshot-wide.png');
    imagedestroy($imWide);

    // Generate narrow screenshot (390x844) for mobile
    $imMobile = imagecreatetruecolor(390, 844);
    $bgMobile = imagecolorallocate($imMobile, 255, 255, 255);
    $headerBgMobile = imagecolorallocate($imMobile, 15, 118, 110);
    $textColorMobile = imagecolorallocate($imMobile, 51, 51, 51);
    $cardBg = imagecolorallocate($imMobile, 243, 244, 246);
    
    imagefill($imMobile, 0, 0, $bgMobile);
    
    // Header
    imagefilledrectangle($imMobile, 0, 0, 390, 80, $headerBgMobile);
    imagestring($imMobile, 5, 100, 30, 'SahelSoft', 16777215);
    
    // Content
    imagestring($imMobile, 3, 20, 150, 'Dashboard', $textColorMobile);
    imagestring($imMobile, 2, 20, 200, 'Welcome back!', $textColorMobile);
    
    // Cards
    imagefilledrectangle($imMobile, 20, 250, 370, 350, $cardBg);
    imagestring($imMobile, 2, 30, 300, 'Projects', $textColorMobile);
    
    imagefilledrectangle($imMobile, 20, 370, 370, 470, $cardBg);
    imagestring($imMobile, 2, 30, 420, 'Team', $textColorMobile);
    
    imagefilledrectangle($imMobile, 20, 490, 370, 590, $cardBg);
    imagestring($imMobile, 2, 30, 540, 'Reports', $textColorMobile);
    
    $resultMobile = imagepng($imMobile, __DIR__ . '/screenshot-mobile.png');
    imagedestroy($imMobile);

    if ($resultWide && $resultMobile) {
        echo "✅ PWA screenshots generated successfully!\n\n";
        echo "Files created:\n";
        echo "- screenshot-wide.png (1280x720) - Desktop\n";
        echo "- screenshot-mobile.png (390x844) - Mobile\n\n";
        echo "File sizes:\n";
        echo "- screenshot-wide.png: " . filesize(__DIR__ . '/screenshot-wide.png') . " bytes\n";
        echo "- screenshot-mobile.png: " . filesize(__DIR__ . '/screenshot-mobile.png') . " bytes\n\n";
        echo "✅ Screenshots are now ready for PWA install UI!\n";
    } else {
        echo "❌ Failed to generate screenshots\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
