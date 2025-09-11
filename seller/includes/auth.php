<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

class SellerAuth {
    private $pdo;
    
    public function __construct($database) {
        $this->pdo = $database;
    }
    
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, u.email, u.password_hash, u.first_name, u.last_name, u.status as user_status
                FROM sellers s 
                JOIN users u ON s.user_id = u.id 
                WHERE u.email = ? AND s.status = 'approved' AND u.status = 'active'
            ");
            $stmt->execute([$email]);
            $seller = $stmt->fetch();
            
            if ($seller && password_verify($password, $seller['password_hash'])) {
                $_SESSION['seller_id'] = $seller['id'];
                $_SESSION['seller_user_id'] = $seller['user_id'];
                $_SESSION['seller_email'] = $seller['email'];
                $_SESSION['seller_name'] = $seller['first_name'] . ' ' . $seller['last_name'];
                $_SESSION['store_name'] = $seller['store_name'];
                $_SESSION['store_slug'] = $seller['store_slug'];
                
                return [
                    'success' => true,
                    'seller' => [
                        'id' => $seller['id'],
                        'user_id' => $seller['user_id'],
                        'email' => $seller['email'],
                        'name' => $seller['first_name'] . ' ' . $seller['last_name'],
                        'store_name' => $seller['store_name'],
                        'store_slug' => $seller['store_slug']
                    ]
                ];
            }
            
            return ['success' => false, 'message' => 'Invalid credentials or account not approved'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function register($userData) {
        try {
            $this->pdo->beginTransaction();
            
            // Create user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, phone, email_verified) 
                VALUES (?, ?, ?, ?, ?, 0)
            ");
            $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $userData['email'],
                $passwordHash,
                $userData['first_name'],
                $userData['last_name'],
                $userData['phone']
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Create seller
            $storeSlug = $this->generateStoreSlug($userData['store_name']);
            $stmt = $this->pdo->prepare("
                INSERT INTO sellers (user_id, store_name, store_slug, store_description, business_type, tax_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $userId,
                $userData['store_name'],
                $storeSlug,
                $userData['store_description'] ?? '',
                $userData['business_type'] ?? 'individual',
                $userData['tax_id'] ?? ''
            ]);
            
            $sellerId = $this->pdo->lastInsertId();
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Registration successful. Your account is pending approval.',
                'seller_id' => $sellerId
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['seller_id']);
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
    }
    
    public function logout() {
        // Clear seller-specific session variables
        unset($_SESSION['seller_id']);
        unset($_SESSION['seller_user_id']);
        unset($_SESSION['seller_email']);
        unset($_SESSION['seller_name']);
        unset($_SESSION['store_name']);
        unset($_SESSION['store_slug']);
        
        // Destroy the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    public function getCurrentSeller() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT s.*, u.email, u.first_name, u.last_name, u.phone
                FROM sellers s 
                JOIN users u ON s.user_id = u.id 
                WHERE s.id = ?
            ");
            $stmt->execute([$_SESSION['seller_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    private function generateStoreSlug($storeName) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $storeName)));
        
        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $stmt = $this->pdo->prepare("SELECT id FROM sellers WHERE store_slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() !== false;
    }
    
    public function changePassword($currentPassword, $newPassword) {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'Not authenticated'];
        }
        
        try {
            // Get current user data
            $stmt = $this->pdo->prepare("
                SELECT u.password_hash 
                FROM users u 
                JOIN sellers s ON u.id = s.user_id 
                WHERE s.id = ?
            ");
            $stmt->execute([$_SESSION['seller_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Verify current password
            if (!password_verify($currentPassword, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Update password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                UPDATE users u 
                JOIN sellers s ON u.id = s.user_id 
                SET u.password_hash = ? 
                WHERE s.id = ?
            ");
            $stmt->execute([$newPasswordHash, $_SESSION['seller_id']]);
            
            return ['success' => true, 'message' => 'Password updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    public function updateProfile($profileData) {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => 'Not authenticated'];
        }
        
        try {
            // Get user_id for this seller
            $stmt = $this->pdo->prepare("SELECT user_id FROM sellers WHERE id = ?");
            $stmt->execute([$_SESSION['seller_id']]);
            $userId = $stmt->fetch()['user_id'];
            
            if (!$userId) {
                return ['success' => false, 'message' => 'Seller not found'];
            }
            
            // Update user profile
            $userFields = [];
            $userParams = [];
            
            $allowedUserFields = ['first_name', 'last_name', 'phone'];
            
            foreach ($allowedUserFields as $field) {
                if (array_key_exists($field, $profileData)) {
                    $userFields[] = "$field = ?";
                    $userParams[] = $profileData[$field];
                }
            }
            
            if (!empty($userFields)) {
                $userParams[] = $userId;
                $sql = "UPDATE users SET " . implode(', ', $userFields) . " WHERE id = ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($userParams);
                
                // Update session data if name changed
                if (isset($profileData['first_name']) || isset($profileData['last_name'])) {
                    $stmt = $this->pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                    $stmt->execute([$userId]);
                    $userData = $stmt->fetch();
                    $_SESSION['seller_name'] = $userData['first_name'] . ' ' . $userData['last_name'];
                }
            }
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>