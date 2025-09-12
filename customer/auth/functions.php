<?php
// Customer authentication functions
session_start();
require_once __DIR__ . '/../config/database.php';

class CustomerAuth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($email, $password, $firstName, $lastName, $phone = null) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new customer
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, phone, email_verified, status)
                VALUES (?, ?, ?, ?, ?, 0, 'active')
            ");
            
            $stmt->execute([$email, $hashedPassword, $firstName, $lastName, $phone]);
            
            $customerId = $this->pdo->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'Registration successful! Welcome to Core1 E-commerce.',
                'customer_id' => $customerId
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, password_hash, first_name, last_name, phone, email_verified, status
                FROM users WHERE email = ?
            ");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();
            
            if (!$customer || !password_verify($password, $customer['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            if ($customer['status'] !== 'active') {
                return ['success' => false, 'message' => 'Account is suspended'];
            }
            
            // Create session
            $sessionId = bin2hex(random_bytes(32));
            $stmt = $this->pdo->prepare("
                INSERT INTO customer_sessions (id, customer_id, ip_address, user_agent, payload)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $payload = json_encode([
                'customer_id' => $customer['id'],
                'email' => $customer['email'],
                'name' => $customer['first_name'] . ' ' . $customer['last_name'],
                'login_time' => time()
            ]);
            
            $stmt->execute([
                $sessionId,
                $customer['id'],
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                $payload
            ]);
            
            // Set session data
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['customer_name'] = $customer['first_name'] . ' ' . $customer['last_name'];
            $_SESSION['session_id'] = $sessionId;
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'customer' => [
                    'id' => $customer['id'],
                    'email' => $customer['email'],
                    'name' => $customer['first_name'] . ' ' . $customer['last_name'],
                    'is_verified' => $customer['email_verified']
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        try {
            if (isset($_SESSION['session_id'])) {
                // Remove session from database
                $stmt = $this->pdo->prepare("DELETE FROM customer_sessions WHERE id = ?");
                $stmt->execute([$_SESSION['session_id']]);
            }
            
            // Clear session data
            session_unset();
            session_destroy();
            
            return ['success' => true, 'message' => 'Logged out successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Logout failed: ' . $e->getMessage()];
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['customer_id']);
    }
    
    public function getCurrentCustomer() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, first_name, last_name, phone, profile_image, email_verified, created_at
                FROM users WHERE id = ?
            ");
            $stmt->execute([$_SESSION['customer_id']]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function verifyEmail($token) {
        // Email verification not implemented for basic auth
        return ['success' => false, 'message' => 'Email verification not implemented'];
    }
    
    public function requestPasswordReset($email) {
        // Password reset not implemented for basic auth
        return ['success' => false, 'message' => 'Password reset not implemented'];
    }
    
    public function resetPassword($token, $newPassword) {
        // Password reset not implemented for basic auth
        return ['success' => false, 'message' => 'Password reset not implemented'];
    }
}

// Initialize auth instance
$customerAuth = new CustomerAuth($pdo);

// Helper function to redirect if not logged in
function requireLogin() {
    global $customerAuth;
    if (!$customerAuth->isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Helper function to redirect if logged in
function requireGuest() {
    global $customerAuth;
    if ($customerAuth->isLoggedIn()) {
        header('Location: account/dashboard.php');
        exit;
    }
}
?>