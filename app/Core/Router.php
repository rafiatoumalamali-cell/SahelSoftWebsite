<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        // Extract path relative to public index.php if needed, or simple parsing
        // For localhost/SahelSoftWebsite/public/home, we need just /home
        // This is a simple implementation.
        
        // Remove query string
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }

        // Normalize path for subdirectory installation
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        // Handle both /SahelSoftWebsite/public and /SahelSoftWebsite setups
        if ($scriptName !== '/' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        
        // Additional normalization for localhost without subdirectory
        if (strpos($path, '/SahelSoftWebsite') === 0) {
            $path = substr($path, strlen('/SahelSoftWebsite'));
        }
        
        if ($path === '' || $path === false) {
            $path = '/';
        }

        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            // Try to find matching route with parameters
            $callback = $this->findMatchingRoute($method, $path);
        }

        if ($callback === false) {
            http_response_code(404);
            echo "404 Not Found";
            return;
        }

        if (is_string($callback)) {
            // Assume Controller@method format
            $parts = explode('@', $callback);
            $controllerName = "App\\Controllers\\" . $parts[0];
            $methodName = $parts[1];
            
            $controller = new $controllerName();
            echo $controller->$methodName();
        } else {
            echo call_user_func($callback);
        }
    }

    private function findMatchingRoute($method, $path) {
        foreach ($this->routes[$method] as $route => $callback) {
            // Convert route pattern to regex
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                // Remove the full match from the beginning
                array_shift($matches);
                
                if (is_string($callback)) {
                    $parts = explode('@', $callback);
                    $controllerName = "App\\Controllers\\" . $parts[0];
                    $methodName = $parts[1];
                    
                    $controller = new $controllerName();
                    
                    // Call the method with parameters
                    return call_user_func_array([$controller, $methodName], $matches);
                }
                
                return $callback;
            }
        }
        
        return false;
    }
}
