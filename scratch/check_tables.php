<?php
require_once 'app/Core/Model.php';
$model = new class extends \App\Core\Model {};
$stmt = $model->getPdo()->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Tables: " . implode(', ', $tables) . "\n";
