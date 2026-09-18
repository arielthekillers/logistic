<?php

namespace App\Core;

class App {
    protected $router;

    public function __construct() {
        $this->router = new Router();
        $this->registerRoutes();
    }

    protected function registerRoutes() {
        // Public / Tracking Routes
        $this->router->get('/', ['App\Controllers\TrackingController', 'index']);
        $this->router->get('/tracking', ['App\Controllers\TrackingController', 'index']);
        $this->router->get('/tracking/search', ['App\Controllers\TrackingController', 'search']);
        $this->router->get('/api/track', ['App\Controllers\TrackingController', 'apiTrack']);

        // Auth Routes
        $this->router->get('/login', ['App\Controllers\AuthController', 'showLogin']);
        $this->router->post('/login', ['App\Controllers\AuthController', 'processLogin']);
        $this->router->get('/logout', ['App\Controllers\AuthController', 'logout']);

        // Dashboard & Backoffice Routes (Protected)
        $this->router->get('/dashboard', ['App\Controllers\DashboardController', 'index']);
        
        // Settings
        $this->router->get('/settings', ['App\Controllers\SettingsController', 'index']);
        $this->router->post('/settings/update', ['App\Controllers\SettingsController', 'update']);

        // Checkpoints / Data Pengiriman Routes
        $this->router->get('/shipments', ['App\Controllers\ShipmentController', 'index']);
        $this->router->get('/shipments/create', ['App\Controllers\ShipmentController', 'create']);
        $this->router->post('/shipments/store', ['App\Controllers\ShipmentController', 'store']);
        $this->router->get('/shipments/detail', ['App\Controllers\ShipmentController', 'detail']);
        $this->router->get('/shipments/label', ['App\Controllers\ShipmentController', 'printLabel']);
        $this->router->get('/shipments/barcode', ['App\Controllers\ShipmentController', 'barcode']);

        // Scanner / Operasional Routes
        $this->router->get('/scanner', ['App\Controllers\ScannerController', 'index']);
        $this->router->get('/scanner/autocomplete', ['App\Controllers\ScannerController', 'autocomplete']);
        $this->router->post('/scanner/process', ['App\Controllers\ScannerController', 'processScan']);

        // Hubs / Gudang Routes
        $this->router->get('/hubs', ['App\Controllers\HubController', 'index']);
        $this->router->post('/hubs/store', ['App\Controllers\HubController', 'store']);
        $this->router->post('/hubs/update', ['App\Controllers\HubController', 'update']);
        $this->router->post('/hubs/delete', ['App\Controllers\HubController', 'delete']);

        // Users / Operators Routes
        $this->router->get('/users', ['App\Controllers\UserController', 'index']);
        $this->router->post('/users/store', ['App\Controllers\UserController', 'store']);
        $this->router->post('/users/update', ['App\Controllers\UserController', 'update']);
        $this->router->post('/users/delete', ['App\Controllers\UserController', 'delete']);

        // User Profile & Avatar Routes
        $this->router->get('/profile', ['App\Controllers\ProfileController', 'index']);
        $this->router->post('/profile/update', ['App\Controllers\ProfileController', 'update']);
        $this->router->post('/profile/change-password', ['App\Controllers\ProfileController', 'changePassword']);

        // Reports / Laporan Routes
        $this->router->get('/reports', ['App\Controllers\ReportController', 'index']);
        $this->router->get('/reports/export', ['App\Controllers\ReportController', 'exportCsv']);
    }

    public function run() {
        try {
            echo $this->router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
        } catch (\Throwable $e) {
            if (defined('APP_ENV') && APP_ENV === 'development') {
                echo "<div style='font-family: sans-serif; padding: 20px; background: #fff5f5; border: 1px solid #feb2b2; border-radius: 8px;'>";
                echo "<h2 style='color: #c53030; margin-top: 0;'>Application Error</h2>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
                echo "<pre style='background: #edf2f7; padding: 10px; border-radius: 4px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                echo "</div>";
            } else {
                error_log("App Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                http_response_code(500);
                echo "<h1>500 Internal Server Error</h1>";
            }
        }
    }
}
