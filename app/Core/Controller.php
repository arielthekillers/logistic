<?php

namespace App\Core;

class Controller {

    public function view($view, $data = []) {
        extract($data);
        
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "View not found: " . htmlspecialchars($viewPath);
        }
    }

    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public function redirect($url) {
        if (function_exists('url')) {
            header("Location: " . url($url));
        } else {
            header("Location: " . $url);
        }
        exit;
    }
}
