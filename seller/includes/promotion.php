<?php
class PromotionManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getPromotions($sellerId, $filters = []) {
        try {
            $conditions = ['seller_id = ?'];
            $params = [$sellerId];
            
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active') {
                    $conditions[] = 'is_active = 1 AND (starts_at IS NULL OR starts_at <= NOW()) AND (expires_at IS NULL OR expires_at >= NOW())';
                } elseif ($filters['status'] === 'scheduled') {
                    $conditions[] = 'is_active = 1 AND starts_at > NOW()';
                } elseif ($filters['status'] === 'expired') {
                    $conditions[] = 'expires_at < NOW()';
                } elseif ($filters['status'] === 'inactive') {
                    $conditions[] = 'is_active = 0';
                }
            }
            
            if (!empty($filters['type'])) {
                $conditions[] = 'type = ?';
                $params[] = $filters['type'];
            }
            
            if (!empty($filters['search'])) {
                $conditions[] = '(code LIKE ? OR description LIKE ?)';
                $params[] = '%' . $filters['search'] . '%';
                $params[] = '%' . $filters['search'] . '%';
            }
            
            $limit = min((int)($filters['limit'] ?? 20), 100);
            $offset = max((int)($filters['offset'] ?? 0), 0);
            
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
            
            // Get total count
            $countSql = "SELECT COUNT(*) FROM promo_codes $whereClause";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            // Get promotions with usage stats
            $sql = "
                SELECT pc.*, 
                       COALESCE(pcu.usage_count, 0) as actual_usage_count,
                       COALESCE(pcu.total_discount, 0) as total_discount_given,
                       CASE 
                           WHEN pc.is_active = 0 THEN 'inactive'
                           WHEN pc.starts_at > NOW() THEN 'scheduled'
                           WHEN pc.expires_at < NOW() THEN 'expired'
                           ELSE 'active'
                       END as computed_status
                FROM promo_codes pc
                LEFT JOIN (
                    SELECT promo_code_id, 
                           COUNT(*) as usage_count,
                           SUM(discount_amount) as total_discount
                    FROM promo_code_usage 
                    GROUP BY promo_code_id
                ) pcu ON pc.id = pcu.promo_code_id
                $whereClause
                ORDER BY pc.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'promotions' => $promotions,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error retrieving promotions: ' . $e->getMessage()
            ];
        }
    }
    
    public function createPromotion($sellerId, $data) {
        try {
            // Validate required fields
            $required = ['code', 'type', 'value'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return ['success' => false, 'message' => "Field '$field' is required"];
                }
            }
            
            // Check if code already exists
            $checkStmt = $this->pdo->prepare("SELECT id FROM promo_codes WHERE code = ?");
            $checkStmt->execute([$data['code']]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Promotion code already exists'];
            }
            
            // Insert promotion
            $sql = "
                INSERT INTO promo_codes (
                    seller_id, code, description, type, value, minimum_order_amount,
                    usage_limit, user_limit, starts_at, expires_at, is_active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $sellerId,
                $data['code'],
                $data['description'] ?? '',
                $data['type'],
                $data['value'],
                $data['minimum_order_amount'] ?? 0,
                $data['usage_limit'] ?? null,
                $data['user_limit'] ?? 1,
                $data['starts_at'] ?? null,
                $data['expires_at'] ?? null,
                $data['is_active'] ?? 1
            ]);
            
            return [
                'success' => true,
                'promotion_id' => $this->pdo->lastInsertId(),
                'message' => 'Promotion created successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating promotion: ' . $e->getMessage()
            ];
        }
    }
    
    public function updatePromotion($sellerId, $promotionId, $data) {
        try {
            // Check if promotion exists and belongs to seller
            $checkStmt = $this->pdo->prepare("SELECT id FROM promo_codes WHERE id = ? AND seller_id = ?");
            $checkStmt->execute([$promotionId, $sellerId]);
            if (!$checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Promotion not found'];
            }
            
            // If updating code, check uniqueness
            if (!empty($data['code'])) {
                $codeCheckStmt = $this->pdo->prepare("SELECT id FROM promo_codes WHERE code = ? AND id != ?");
                $codeCheckStmt->execute([$data['code'], $promotionId]);
                if ($codeCheckStmt->fetch()) {
                    return ['success' => false, 'message' => 'Promotion code already exists'];
                }
            }
            
            // Build update query
            $updateFields = [];
            $params = [];
            
            $allowedFields = [
                'code', 'description', 'type', 'value', 'minimum_order_amount',
                'usage_limit', 'user_limit', 'starts_at', 'expires_at', 'is_active'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $updateFields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($updateFields)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            $params[] = $promotionId;
            $params[] = $sellerId;
            
            $sql = "UPDATE promo_codes SET " . implode(', ', $updateFields) . " WHERE id = ? AND seller_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'message' => 'Promotion updated successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error updating promotion: ' . $e->getMessage()
            ];
        }
    }
    
    public function deletePromotion($sellerId, $promotionId) {
        try {
            // Check if promotion has been used
            $usageStmt = $this->pdo->prepare("SELECT COUNT(*) FROM promo_code_usage WHERE promo_code_id = ?");
            $usageStmt->execute([$promotionId]);
            $usageCount = $usageStmt->fetchColumn();
            
            if ($usageCount > 0) {
                // If used, just deactivate
                $stmt = $this->pdo->prepare("UPDATE promo_codes SET is_active = 0 WHERE id = ? AND seller_id = ?");
                $stmt->execute([$promotionId, $sellerId]);
                $message = 'Promotion deactivated (had usage history)';
            } else {
                // If not used, delete
                $stmt = $this->pdo->prepare("DELETE FROM promo_codes WHERE id = ? AND seller_id = ?");
                $stmt->execute([$promotionId, $sellerId]);
                $message = 'Promotion deleted successfully';
            }
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Promotion not found'];
            }
            
            return [
                'success' => true,
                'message' => $message
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error deleting promotion: ' . $e->getMessage()
            ];
        }
    }
    
    public function getPromotionStats($sellerId) {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_promotions,
                    SUM(CASE WHEN is_active = 1 AND (starts_at IS NULL OR starts_at <= NOW()) AND (expires_at IS NULL OR expires_at >= NOW()) THEN 1 ELSE 0 END) as active_promotions,
                    SUM(CASE WHEN starts_at > NOW() THEN 1 ELSE 0 END) as scheduled_promotions,
                    SUM(CASE WHEN expires_at < NOW() THEN 1 ELSE 0 END) as expired_promotions,
                    COALESCE(SUM(pcu.total_discount), 0) as total_discount_given,
                    COALESCE(SUM(pcu.usage_count), 0) as total_uses
                FROM promo_codes pc
                LEFT JOIN (
                    SELECT promo_code_id, 
                           COUNT(*) as usage_count,
                           SUM(discount_amount) as total_discount
                    FROM promo_code_usage 
                    GROUP BY promo_code_id
                ) pcu ON pc.id = pcu.promo_code_id
                WHERE pc.seller_id = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$sellerId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'stats' => $stats
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error retrieving promotion stats: ' . $e->getMessage()
            ];
        }
    }
}
?>