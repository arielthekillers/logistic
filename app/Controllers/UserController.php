<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\HubModel;

class UserController extends Controller {

    public function index() {
        require_roles(['admin']);
        $userModel = new UserModel();
        $hubModel = new HubModel();

        $users = [];
        $hubs = [];
        try {
            $users = $userModel->getAllWithHub();
            $hubs = $hubModel->findAll('name', 'ASC');
        } catch (\Throwable $e) {}

        $this->view('users/index', [
            'users' => $users,
            'hubs'  => $hubs
        ]);
    }

    public function store() {
        require_roles(['admin']);

        $name     = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = trim($_POST['role'] ?? 'operator_hub');
        $hubId    = !empty($_POST['hub_id']) ? (int)$_POST['hub_id'] : null;

        if (empty($name) || empty($username) || empty($password)) {
            $_SESSION['error_flash'] = "Nama, Username, dan Password wajib diisi.";
            $this->redirect('/users');
            return;
        }

        $userModel = new UserModel();

        try {
            $userModel->create([
                'name'     => $name,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => $role,
                'hub_id'   => $hubId
            ]);
            $_SESSION['success_flash'] = "User {$name} ({$username}) berhasil dibuat.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal membuat user: " . $e->getMessage();
        }

        $this->redirect('/users');
    }

    public function update() {
        require_roles(['admin']);

        $userId   = (int)($_POST['user_id'] ?? 0);
        $name     = trim($_POST['name'] ?? '');
        $role     = trim($_POST['role'] ?? 'operator_hub');
        $hubId    = !empty($_POST['hub_id']) ? (int)$_POST['hub_id'] : null;
        $phone    = standardize_phone($_POST['phone'] ?? '');
        $newPass  = trim($_POST['new_password'] ?? '');

        if (!$userId || empty($name)) {
            $_SESSION['error_flash'] = "Data tidak valid untuk update user.";
            $this->redirect('/users');
            return;
        }

        $userModel = new UserModel();
        $data = [
            'name'   => $name,
            'role'   => $role,
            'hub_id' => $hubId,
            'phone'  => $phone
        ];

        if (!empty($newPass)) {
            if (strlen($newPass) < 6) {
                $_SESSION['error_flash'] = "Password baru minimal 6 karakter.";
                $this->redirect('/users');
                return;
            }
            $data['password'] = password_hash($newPass, PASSWORD_DEFAULT);
        }

        try {
            $userModel->update($userId, $data);
            $_SESSION['success_flash'] = "User {$name} berhasil diperbarui.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal update user: " . $e->getMessage();
        }

        $this->redirect('/users');
    }

    public function delete() {
        require_roles(['admin']);

        $userId = (int)($_POST['user_id'] ?? 0);

        if (!$userId) {
            $_SESSION['error_flash'] = "User tidak ditemukan.";
            $this->redirect('/users');
            return;
        }

        // Prevent self-delete
        if ($userId === (int)($_SESSION['user_id'] ?? 0)) {
            $_SESSION['error_flash'] = "Anda tidak bisa menghapus akun Anda sendiri.";
            $this->redirect('/users');
            return;
        }

        $userModel = new UserModel();
        try {
            $userModel->delete($userId);
            $_SESSION['success_flash'] = "User berhasil dihapus.";
        } catch (\Throwable $e) {
            $_SESSION['error_flash'] = "Gagal menghapus user: " . $e->getMessage();
        }

        $this->redirect('/users');
    }
}
