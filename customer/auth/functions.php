<?php
// Customer authentication functions
session_start();
require_once '../config/database.php';

class CustomerAuth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function register($email, $password, $firstName, $lastName, $phone = null) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Insert new customer
            $stmt = $this->pdo->prepare("
                INSERT INTO customers (email, password, first_name, last_name, phone, verification_token)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$email, $hashedPassword, $firstName, $lastName, $phone, $verificationToken]);
            
            $customerId = $this->pdo->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'Registration successful',
                'customer_id' => $customerId,
                'verification_token' => $verificationToken
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, password, first_name, last_name, phone, is_verified
                FROM customers WHERE email = ?
            ");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();
            
            if (!$customer || !password_verify($password, $customer['password'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
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
                    'is_verified' => $customer['is_verified']
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
                SELECT id, email, first_name, last_name, phone, is_verified, created_at
                FROM customers WHERE id = ?
            ");
            $stmt->execute([$_SESSION['customer_id']]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    public function verifyEmail($token) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE customers 
                SET is_verified = 1, verification_token = NULL 
                WHERE verification_token = ?
            ");
            $stmt->execute([$token]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Email verified successfully'];
            } else {
                return ['success' => false, 'message' => 'Invalid verification token'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()];
        }
    }
    
    public function requestPasswordReset($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return ['success' => false, 'message' => 'Email not found'];
            }
            
            $resetToken = bin2hex(random_bytes(32));
            $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $this->pdo->prepare("
                UPDATE customers 
                SET reset_token = ?, reset_expires = ?
                WHERE email = ?
            ");
            $stmt->execute([$resetToken, $resetExpires, $email]);
            
            return [
                'success' => true,
                'message' => 'Password reset token generated',
                'reset_token' => $resetToken
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Password reset request failed: ' . $e->getMessage()];
        }
    }
    
    public function resetPassword($token, $newPassword) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id FROM customers 
                WHERE reset_token = ? AND reset_expires > NOW()
            ");
            $stmt->execute([$token]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return ['success' => false, 'message' => 'Invalid or expired reset token'];
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                UPDATE customers 
                SET password = ?, reset_token = NULL, reset_expires = NULL
                WHERE id = ?
            ");
            $stmt->execute([$hashedPassword, $customer['id']]);
            
            return ['success' => true, 'message' => 'Password reset successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Password reset failed: ' . $e->getMessage()];
        }
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