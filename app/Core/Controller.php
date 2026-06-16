<?php

namespace App\Core;

class Controller {
    public function view($view, $data = []) {
        extract($data);
        
        $viewFile = VIEW_PATH . "/$view.php";
        
        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            return ob_get_clean();
        } else {
            return "View '$view' not found.";
        }
    }

    public function redirect($url) {
        header("Location: " . APP_URL . $url);
        exit;
    }
}
