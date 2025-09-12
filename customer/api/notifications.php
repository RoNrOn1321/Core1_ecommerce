<?php
// Customer Notifications API - Universal notification system
require_once __DIR__ . '/../auth/functions.php';

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request information
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove base path to get API endpoint
$basePath = '/Core1_ecommerce/customer/api';
$endpoint = str_replace($basePath, '', $requestPath);
$endpoint = trim($endpoint, '/');

// Split endpoint into parts
$endpointParts = explode('/', $endpoint);
$resource = $endpointParts[0] ?? ''; // notifications
$action = $endpointParts[1] ?? ''; // unread, mark-read, etc.
$id = $endpointParts[2] ?? null; // specific notification ID

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Initialize auth system
$auth = new CustomerAuth($pdo);

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$customerId = $_SESSION['customer_id'];

try {
    switch ($requestMethod) {
        case 'GET':
            if ($action === 'unread') {
                getUnreadNotifications($pdo, $customerId);
            } elseif ($action === 'count') {
                getNotificationCount($pdo, $customerId);
            } elseif ($action === 'all') {
                getAllNotifications($pdo, $customerId);
            } elseif ($action === 'types') {
                getNotificationsByType($pdo, $customerId);
            } elseif ($id) {
                getNotification($pdo, $customerId, $id);
            } else {
                getAllNotifications($pdo, $customerId);
            }
            break;
            
        case 'POST':
            if ($action === 'mark-read') {
                if ($id) {
                    markNotificationAsRead($pdo, $customerId, $id);
                } else {
                    markAllNotificationsAsRead($pdo, $customerId, $input);
                }
            } elseif ($action === 'mark-unread') {
                markNotificationAsUnread($pdo, $customerId, $id);
            } elseif ($action === 'archive') {
                archiveNotifications($pdo, $customerId, $input);
            } elseif ($action === 'create') {
                createNotification($pdo, $customerId, $input);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            break;
            
        case 'DELETE':
            if ($id) {
                deleteNotification($pdo, $customerId, $id);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}

function getUnreadNotifications($pdo, $customerId) {
    try {
        $limit = min((int)($_GET['limit'] ?? 20), 50);
        $types = $_GET['types'] ?? '';
        
        $whereConditions = ['user_id = ?', 'is_read = FALSE', 'is_archived = FALSE'];
        $params = [$customerId];
        
        if (!empty($types)) {
            $typeList = explode(',', $types);
            $placeholders = str_repeat('?,', count($typeList) - 1) . '?';
            $whereConditions[] = "type IN ($placeholders)";
            $params = array_merge($params, $typeList);
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $stmt = $pdo->prepare("
            SELECT * FROM user_notifications 
            WHERE $whereClause
            ORDER BY 
                CASE priority 
                    WHEN 'urgent' THEN 1 
                    WHEN 'high' THEN 2 
                    WHEN 'medium' THEN 3 
                    ELSE 4 
                END,
                created_at DESC 
            LIMIT ?
        ");
        
        $params[] = $limit;
        $stmt->execute($params);
        $notifications = $stmt->fetchAll();
        
        // Get counts by type
        $typeCountStmt = $pdo->prepare("
            SELECT type, COUNT(*) as count 
            FROM user_notifications 
            WHERE user_id = ? AND is_read = FALSE AND is_archived = FALSE
            GROUP BY type
        ");
        $typeCountStmt->execute([$customerId]);
        $typeCounts = [];
        while ($row = $typeCountStmt->fetch()) {
            $typeCounts[$row['type']] = (int)$row['count'];
        }
        
        // Get total unread count
        $totalStmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM user_notifications 
            WHERE user_id = ? AND is_read = FALSE AND is_archived = FALSE
        ");
        $totalStmt->execute([$customerId]);
        $totalUnread = (int)$totalStmt->fetch()['total'];
        
        echo json_encode([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'total_unread' => $totalUnread,
                'counts_by_type' => $typeCounts
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function getNotificationCount($pdo, $customerId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_unread,
                SUM(CASE WHEN type = 'order' THEN 1 ELSE 0 END) as order_count,
                SUM(CASE WHEN type = 'support' THEN 1 ELSE 0 END) as support_count,
                SUM(CASE WHEN type = 'product' THEN 1 ELSE 0 END) as product_count,
                SUM(CASE WHEN type = 'promotion' THEN 1 ELSE 0 END) as promotion_count,
                SUM(CASE WHEN type = 'payment' THEN 1 ELSE 0 END) as payment_count,
                SUM(CASE WHEN type = 'shipping' THEN 1 ELSE 0 END) as shipping_count,
                SUM(CASE WHEN type = 'system' THEN 1 ELSE 0 END) as system_count,
                SUM(CASE WHEN type = 'account' THEN 1 ELSE 0 END) as account_count
            FROM user_notifications 
            WHERE user_id = ? AND is_read = FALSE AND is_archived = FALSE
        ");
        
        $stmt->execute([$customerId]);
        $counts = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'data' => $counts
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function getAllNotifications($pdo, $customerId) {
    try {
        $limit = min((int)($_GET['limit'] ?? 50), 100);
        $offset = (int)($_GET['offset'] ?? 0);
        $type = $_GET['type'] ?? '';
        $includeRead = $_GET['include_read'] === 'true';
        
        $whereConditions = ['user_id = ?', 'is_archived = FALSE'];
        $params = [$customerId];
        
        if (!$includeRead) {
            $whereConditions[] = 'is_read = FALSE';
        }
        
        if (!empty($type)) {
            $whereConditions[] = 'type = ?';
            $params[] = $type;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM user_notifications WHERE $whereClause");
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetch()['total'];
        
        // Get notifications
        $stmt = $pdo->prepare("
            SELECT * FROM user_notifications 
            WHERE $whereClause
            ORDER BY is_read ASC, created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);
        $notifications = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'pagination' => [
                    'total' => $totalCount,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $totalCount
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function markNotificationAsRead($pdo, $customerId, $notificationId) {
    try {
        $stmt = $pdo->prepare("
            UPDATE user_notifications 
            SET is_read = TRUE, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        
        $stmt->execute([$notificationId, $customerId]);
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Notification not found'
            ]);
        }
        
    } catch (Exception $e) {
        throw $e;
    }
}

function markAllNotificationsAsRead($pdo, $customerId, $data) {
    try {
        $types = $data['types'] ?? [];
        
        $whereConditions = ['user_id = ?', 'is_read = FALSE'];
        $params = [$customerId];
        
        if (!empty($types) && is_array($types)) {
            $placeholders = str_repeat('?,', count($types) - 1) . '?';
            $whereConditions[] = "type IN ($placeholders)";
            $params = array_merge($params, $types);
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $stmt = $pdo->prepare("
            UPDATE user_notifications 
            SET is_read = TRUE, read_at = NOW() 
            WHERE $whereClause
        ");
        
        $stmt->execute($params);
        $affectedRows = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notifications marked as read',
            'data' => [
                'updated_count' => $affectedRows
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function createNotification($pdo, $customerId, $data) {
    try {
        // Validate required fields
        $requiredFields = ['type', 'title', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                return;
            }
        }
        
        // Validate type
        $validTypes = ['order', 'support', 'product', 'promotion', 'system', 'payment', 'shipping', 'account'];
        if (!in_array($data['type'], $validTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid notification type']);
            return;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO user_notifications (
                user_id, type, title, message, data, related_id, related_type, 
                priority, action_url, expires_at, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $jsonData = !empty($data['data']) ? json_encode($data['data']) : null;
        $expiresAt = !empty($data['expires_at']) ? $data['expires_at'] : null;
        
        $stmt->execute([
            $customerId,
            $data['type'],
            $data['title'],
            $data['message'],
            $jsonData,
            $data['related_id'] ?? null,
            $data['related_type'] ?? null,
            $data['priority'] ?? 'medium',
            $data['action_url'] ?? null,
            $expiresAt
        ]);
        
        $notificationId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification created successfully',
            'data' => [
                'notification_id' => $notificationId
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function archiveNotifications($pdo, $customerId, $data) {
    try {
        $notificationIds = $data['notification_ids'] ?? [];
        
        if (empty($notificationIds) || !is_array($notificationIds)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Notification IDs required']);
            return;
        }
        
        $placeholders = str_repeat('?,', count($notificationIds) - 1) . '?';
        $params = array_merge([$customerId], $notificationIds);
        
        $stmt = $pdo->prepare("
            UPDATE user_notifications 
            SET is_archived = TRUE 
            WHERE user_id = ? AND id IN ($placeholders)
        ");
        
        $stmt->execute($params);
        $affectedRows = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => 'Notifications archived',
            'data' => [
                'archived_count' => $affectedRows
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function deleteNotification($pdo, $customerId, $notificationId) {
    try {
        $stmt = $pdo->prepare("DELETE FROM user_notifications WHERE id = ? AND user_id = ?");
        $stmt->execute([$notificationId, $customerId]);
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Notification deleted'
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Notification not found'
            ]);
        }
        
    } catch (Exception $e) {
        throw $e;
    }
}

?>