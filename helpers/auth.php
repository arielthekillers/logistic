<?php

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user details
 */
function auth_user() {
    if (!is_logged_in()) return null;
    return $_SESSION['user'] ?? [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? 'Operator',
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'operator_hub',
        'hub_id' => $_SESSION['user_hub_id'] ?? null,
    ];
}

/**
 * Get current user role
 */
function auth_role() {
    return $_SESSION['user_role'] ?? 'guest';
}

/**
 * Check if current user has a specific role
 */
function has_role($role) {
    return auth_role() === $role;
}

/**
 * Require login, redirect to login page if unauthorized
 */
function require_auth() {
    if (!is_logged_in()) {
        header('Location: ' . url('/login'));
        exit;
    }
}

/**
 * Require specific roles
 */
function require_roles(array $allowedRoles) {
    require_auth();
    $currentRole = auth_role();
    if (!in_array($currentRole, $allowedRoles)) {
        http_response_code(403);
        echo "<h1 style='text-align:center; margin-top:50px; font-family:sans-serif;'>403 - Akses Ditolak (Peran Tidak Diizinkan)</h1>";
        exit;
    }
}
