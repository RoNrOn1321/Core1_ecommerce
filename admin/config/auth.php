<?php
// Authentication helper functions

function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit();
    }
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function getAdminId() {
    return $_SESSION['admin_id'] ?? null;
}

function getAdminName() {
    return $_SESSION['admin_name'] ?? 'Admin';
}

function getAdminRole() {
    return $_SESSION['admin_role'] ?? 'admin';
}

function getAdminEmail() {
    return $_SESSION['admin_email'] ?? '';
}

function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
    header('Location: login.php');
    exit();
}

function hasPermission($permission) {
    $role = getAdminRole();
    
    // Super admin has all permissions
    if ($role === 'super_admin') {
        return true;
    }
    
    // Define role permissions
    $permissions = [
        'admin' => ['view_dashboard', 'manage_users', 'manage_sellers', 'manage_products', 'manage_orders', 'view_reports'],
        'support_agent' => ['view_dashboard', 'manage_support', 'view_orders'],
        'content_manager' => ['view_dashboard', 'manage_products', 'manage_content']
    ];
    
    return in_array($permission, $permissions[$role] ?? []);
}
?>