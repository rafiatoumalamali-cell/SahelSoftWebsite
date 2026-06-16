<?php
/**
 * Simple test file to bypass routing entirely
 */

echo "<h1>Direct PHP Test</h1>";
echo "<p>If you can see this, PHP is working.</p>";

echo "<h2>Test ProposalController Directly</h2>";

require_once __DIR__ . '/../app/Config/config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

try {
    $controller = new \App\Controllers\ProposalController();
    echo "✅ Controller created successfully<br>";
    
    $result = $controller->index();
    echo "✅ index() executed<br>";
    echo "Output: " . substr($result, 0, 500) . "...";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}

echo "<h2>Test Links</h2>";
echo "<a href='../debug_full_request.php'>Debug Request Info</a><br>";
echo "<a href='../test_direct_access.php'>Direct Access Tests</a><br>";
?>
