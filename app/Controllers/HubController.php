<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\HubModel;

class HubController extends Controller {

    public function index() {
        require_auth();
        $hubModel = new \App\Models\HubModel();
        $userModel = new \App\Models\UserModel();

        $hubs = [];
        $users = [];
        $hubUsers = [];
        try {
            $hubs = $hubModel->findAll('id', 'DESC');
            $users = $userModel->findAll();
            
            // Group users by hub_id
            foreach ($users as $u) {
                if (!empty($u['hub_id'])) {
                    $hubUsers[$u['hub_id']][] = $u;
                }
            }
        } catch (\Throwable $e) {}

        $this->view('hubs/index', [
            'hubs' => $hubs,
            'hubUsers' => $hubUsers
        ]);
    }

    public function store() {
        require_roles(['admin']);

        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'BRANCH_HUB');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = standardize_phone($_POST['phone'] ?? '');

        if (empty($code) || empty($name) || empty($city)) {
            $_SESSION['error_flash'] = "Kode Hub, Nama, dan Kota wajib diisi.";
            $this->redirect('/hubs');
            return;
        }

        $photoPath = null;
        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoPath = $this->handleImageUpload($_FILES['photo']);
        }

        $hubModel = new HubModel();

        try {
            $hubModel->create([
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'city' => $city,
                'address' => $address,
                'phone' => $phone,
                'photo' => $photoPath
            ]);
            $_SESSION['success_flash'] = "Data Gudang/Hub {$name} berhasil ditambahkan.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal menambah Hub: " . $e->getMessage();
        }

        $this->redirect('/hubs');
    }

    public function update() {
        require_roles(['admin']);

        $id = $_POST['hub_id'] ?? '';
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'BRANCH_HUB');
        $city = trim($_POST['city'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = standardize_phone($_POST['phone'] ?? '');

        if (empty($id) || empty($code) || empty($name) || empty($city)) {
            $_SESSION['error_flash'] = "Data tidak valid.";
            $this->redirect('/hubs');
            return;
        }

        $hubModel = new HubModel();
        $existing = $hubModel->find($id);

        if (!$existing) {
            $_SESSION['error_flash'] = "Gudang/Hub tidak ditemukan.";
            $this->redirect('/hubs');
            return;
        }

        $data = [
            'code' => $code,
            'name' => $name,
            'type' => $type,
            'city' => $city,
            'address' => $address,
            'phone' => $phone
        ];

        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoPath = $this->handleImageUpload($_FILES['photo']);
            if ($photoPath) {
                $data['photo'] = $photoPath;
                // Delete old photo if exists
                if (!empty($existing['photo']) && file_exists(dirname(__DIR__, 2) . '/' . $existing['photo'])) {
                    unlink(dirname(__DIR__, 2) . '/' . $existing['photo']);
                }
            }
        } elseif (isset($_POST['delete_photo']) && $_POST['delete_photo'] === '1') {
            // Delete photo explicitly
            $data['photo'] = null;
            if (!empty($existing['photo']) && file_exists(dirname(__DIR__, 2) . '/' . $existing['photo'])) {
                unlink(dirname(__DIR__, 2) . '/' . $existing['photo']);
            }
        }

        try {
            $hubModel->update($id, $data);
            $_SESSION['success_flash'] = "Data Gudang/Hub berhasil diperbarui.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal memperbarui Hub: " . $e->getMessage();
        }

        $this->redirect('/hubs');
    }

    public function delete() {
        require_roles(['admin']);
        
        $id = $_POST['hub_id'] ?? '';
        
        if (empty($id)) {
            $this->redirect('/hubs');
            return;
        }

        $hubModel = new HubModel();
        $existing = $hubModel->find($id);

        if ($existing) {
            try {
                if (!empty($existing['photo']) && file_exists(dirname(__DIR__, 2) . '/' . $existing['photo'])) {
                    unlink(dirname(__DIR__, 2) . '/' . $existing['photo']);
                }
                $hubModel->delete($id);
                $_SESSION['success_flash'] = "Gudang/Hub berhasil dihapus.";
            } catch (\Throwable $e) {
                $_SESSION['error_flash'] = "Gagal menghapus Hub: " . $e->getMessage();
            }
        }
        
        $this->redirect('/hubs');
    }

    private function handleImageUpload($file) {
        $uploadDir = dirname(__DIR__, 2) . '/uploads/hubs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . uniqid() . '.jpg';
        $destination = $uploadDir . $fileName;
        
        // Check if image
        $mime = mime_content_type($file['tmp_name']);
        if (strpos($mime, 'image/') !== 0) {
            return null;
        }

        // Compress and resize
        list($width, $height) = getimagesize($file['tmp_name']);
        $newWidth = 800; // max width
        $newHeight = ($height / $width) * $newWidth;

        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($file['tmp_name']);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($file['tmp_name']);
                break;
        }

        if (!$image) {
            return null;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparency
        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save as JPG with 80% quality
        imagejpeg($newImage, $destination, 80);
        
        imagedestroy($image);
        imagedestroy($newImage);

        return 'uploads/hubs/' . $fileName;
    }
}
