<?php
/**
 * Autoloader for PHPMailer classes in lib folder
 */

spl_autoload_register(function ($class) {
    // PHPMailer namespace
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $classFile = __DIR__ . '/' . substr($class, 20) . '.php';
        if (file_exists($classFile)) {
            require $classFile;
        }
    }
});
