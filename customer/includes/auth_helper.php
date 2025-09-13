<?php
/**
 * Customer Authentication Helper Functions
 */

session_start();

/**
 * Check if a customer is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['customer_id']) && !empty($_SESSION['customer_id']);
}

/**
 * Get the current logged-in customer ID
 */
function getUserId() {
    return isLoggedIn() ? (int)$_SESSION['customer_id'] : null;
}

/**
 * Get the current logged-in customer data
 */
function getCustomerData() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['customer_id'],
        'first_name' => $_SESSION['customer_first_name'] ?? '',
        'last_name' => $_SESSION['customer_last_name'] ?? '',
        'email' => $_SESSION['customer_email'] ?? '',
        'phone' => $_SESSION['customer_phone'] ?? ''
    ];
}

/**
 * Require authentication (redirect if not logged in)
 */
function requireAuth($redirect_url = '/Core1_ecommerce/customer/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Log in a customer (set session data)
 */
function loginCustomer($customer_data) {
    $_SESSION['customer_id'] = $customer_data['id'];
    $_SESSION['customer_first_name'] = $customer_data['first_name'];
    $_SESSION['customer_last_name'] = $customer_data['last_name'];
    $_SESSION['customer_email'] = $customer_data['email'];
    $_SESSION['customer_phone'] = $customer_data['phone'] ?? '';
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

/**
 * Log out a customer (destroy session)
 */
function logoutCustomer() {
    session_destroy();
    session_start();
}

/**
 * Get customer full name
 */
function getCustomerName() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return trim(($_SESSION['customer_first_name'] ?? '') . ' ' . ($_SESSION['customer_last_name'] ?? ''));
}
?>