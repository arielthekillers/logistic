<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class ProfileController extends Controller {

    public function index() {
        require_auth();
        $userModel = new UserModel();
        $userId = auth_user()['id'];

        $user = $userModel->find($userId);
        $fullUser = $userModel->findByUsername($user['username']);

        $this->view('profile/index', [
            'user' => $fullUser
        ]);
    }

    public function update() {
        require_auth();
        $userModel = new UserModel();
        $userId = auth_user()['id'];

        $name = trim($_POST['name'] ?? '');
        $phone = standardize_phone($_POST['phone'] ?? '');

        if (empty($name)) {
            $_SESSION['error_flash'] = "Nama lengkap wajib diisi.";
            $this->redirect('/profile');
            return;
        }

        $user = $userModel->find($userId);
        $avatarPath = $user['avatar'] ?? null;

        // Handle Avatar File Upload & Auto-Resize
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error_flash'] = "Format file avatar harus JPG, PNG, atau WEBP.";
                $this->redirect('/profile');
                return;
            }

            if ($file['size'] > $maxSize) {
                $_SESSION['error_flash'] = "Ukuran file avatar maksimal 5MB.";
                $this->redirect('/profile');
                return;
            }

            $uploadDir = __DIR__ . '/../../uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFilename = 'avatar_' . $userId . '_' . time() . '.jpg';
            $destination = $uploadDir . $newFilename;

            // Resize image to 300x300 using PHP GD
            if ($this->resizeAndSaveImage($file['tmp_name'], $destination, 300, 300, $file['type'])) {
                // Delete old avatar file if exists
                if ($avatarPath && file_exists(__DIR__ . '/../../' . $avatarPath)) {
                    @unlink(__DIR__ . '/../../' . $avatarPath);
                }
                $avatarPath = 'uploads/avatars/' . $newFilename;
            } else {
                $_SESSION['error_flash'] = "Gagal memproses dan meresize foto avatar.";
                $this->redirect('/profile');
                return;
            }
        }

        try {
            $userModel->update($userId, [
                'name' => $name,
                'phone' => $phone,
                'avatar' => $avatarPath
            ]);

            // Refresh Session Data
            $updatedUser = $userModel->findByUsername($user['username']);
            $_SESSION['user_name'] = $updatedUser['name'];
            $_SESSION['user'] = $updatedUser;

            $_SESSION['success_flash'] = "Profil & Foto Avatar berhasil diperbarui!";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal memperbarui profil: " . $e->getMessage();
        }

        $this->redirect('/profile');
    }

    public function changePassword() {
        require_auth();
        $userModel = new UserModel();
        $userId = auth_user()['id'];

        $oldPassword = trim($_POST['old_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (empty($oldPassword) || empty($newPassword)) {
            $_SESSION['error_flash'] = "Password lama dan baru wajib diisi.";
            $this->redirect('/profile');
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error_flash'] = "Konfirmasi password baru tidak cocok.";
            $this->redirect('/profile');
            return;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error_flash'] = "Password baru minimal 6 karakter.";
            $this->redirect('/profile');
            return;
        }

        $user = $userModel->find($userId);

        if (!password_verify($oldPassword, $user['password'])) {
            $_SESSION['error_flash'] = "Password lama Anda salah.";
            $this->redirect('/profile');
            return;
        }

        try {
            $userModel->update($userId, [
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ]);
            $_SESSION['success_flash'] = "Password berhasil diubah!";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal mengubah password: " . $e->getMessage();
        }

        $this->redirect('/profile');
    }

    /**
     * Helper to crop, resize and compress avatar image to exact square dimensions (e.g. 300x300)
     */
    private function resizeAndSaveImage($sourcePath, $destinationPath, $targetWidth = 300, $targetHeight = 300, $mimeType = 'image/jpeg') {
        if (!extension_loaded('gd')) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        list($origWidth, $origHeight) = getimagesize($sourcePath);
        if (!$origWidth || !$origHeight) return false;

        // Create GD Resource from uploaded file
        switch (strtolower($mimeType)) {
            case 'image/png':
                $srcImage = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $srcImage = @imagecreatefromwebp($sourcePath);
                break;
            case 'image/jpeg':
            case 'image/jpg':
            default:
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
        }

        if (!$srcImage) {
            return move_uploaded_file($sourcePath, $destinationPath);
        }

        // Calculate center square crop coordinates
        if ($origWidth > $origHeight) {
            $cropSize = $origHeight;
            $cropX = (int)(($origWidth - $origHeight) / 2);
            $cropY = 0;
        } else {
            $cropSize = $origWidth;
            $cropX = 0;
            $cropY = (int)(($origHeight - $origWidth) / 2);
        }

        // Create canvas for target size
        $dstImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG/WEBP
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        // Perform crop & resample
        imagecopyresampled(
            $dstImage,
            $srcImage,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $cropSize, $cropSize
        );

        // Save as high-quality compressed JPEG (quality 85)
        $success = imagejpeg($dstImage, $destinationPath, 85);

        imagedestroy($srcImage);
        imagedestroy($dstImage);

        return $success;
    }
}
