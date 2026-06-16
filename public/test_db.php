<?php
require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "Database connection successful!\n";
    
    $stmt = $conn->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();
    echo "MySQL Version: " . $version . "\n";
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
