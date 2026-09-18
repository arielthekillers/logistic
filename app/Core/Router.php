<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$this->normalizePath($path)] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$this->normalizePath($path)] = $callback;
    }

    private function normalizePath($path) {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : $path;
    }

    public function dispatch($uri, $method) {
        $parsedUrl = parse_url($uri, PHP_URL_PATH);
        
        // Remove subdirectory if running under subfolder (e.g., /logistic)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($parsedUrl, $scriptDir) === 0) {
            $parsedUrl = substr($parsedUrl, strlen($scriptDir));
        }

        $path = $this->normalizePath($parsedUrl);
        
        // Strip legacy .php if present
        if (substr($path, -4) === '.php') {
            $path = substr($path, 0, -4);
            $path = $this->normalizePath($path);
        }

        $method = strtoupper($method);

        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];

            if (is_array($callback)) {
                $class = $callback[0];
                $methodName = $callback[1];
                $controller = new $class();
                return $controller->$methodName();
            }

            return call_user_func($callback);
        }

        // Handle 404 Not Found
        http_response_code(404);
        $errorView = __DIR__ . '/../Views/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
            return;
        }
        return "404 - Page Not Found";
    }
}
