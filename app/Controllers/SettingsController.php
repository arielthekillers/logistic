<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SettingsModel;

class SettingsController extends Controller {

    public function index() {
        require_roles(['admin']); // Only admin can access settings

        $settingsModel = new SettingsModel();
        $settings = $settingsModel->getAllSettings();

        // Pass settings to view
        $this->view('settings/index', [
            'settings' => $settings
        ]);
    }

    public function update() {
        require_roles(['admin']);

        $settingsModel = new SettingsModel();
        
        $fields = [
            'company_name',
            'company_address',
            'company_phone',
            'barcode_type'
        ];

        try {
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $settingsModel->set($field, trim($_POST[$field]));
                }
            }
            $_SESSION['success_flash'] = "Pengaturan berhasil diperbarui.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal memperbarui pengaturan: " . $e->getMessage();
        }

        $this->redirect('/settings');
    }
}
