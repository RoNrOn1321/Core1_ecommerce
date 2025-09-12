<?php
// Customer Live Chat Support API
require_once __DIR__ . '/../../auth/functions.php';

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
$module = $endpointParts[0] ?? ''; // support
$resource = $endpointParts[1] ?? ''; // chat
$action = $endpointParts[2] ?? ''; // rooms, messages, etc.
$id = $endpointParts[3] ?? null; // room ID or message ID

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
    switch ($action) {
        case 'rooms':
            if ($requestMethod === 'GET') {
                // Get customer's chat rooms
                getChatRooms($pdo, $customerId);
            } elseif ($requestMethod === 'POST') {
                // Create new chat room
                createChatRoom($pdo, $customerId, $input);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;
            
        case 'messages':
            $roomId = $id;
            if ($requestMethod === 'GET') {
                // Get messages for a chat room
                if ($roomId) {
                    getChatMessages($pdo, $customerId, $roomId);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Room ID required']);
                }
            } elseif ($requestMethod === 'POST') {
                // Send message to a chat room
                if ($roomId) {
                    sendChatMessage($pdo, $customerId, $roomId, $input);
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Room ID required']);
                }
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;
            
        case 'status':
            if ($requestMethod === 'GET') {
                // Check chat availability
                getChatStatus($pdo);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
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

function getChatStatus($pdo) {
    try {
        // Check business hours (simplified - in production you'd check against actual agent availability)
        $currentHour = (int)date('H');
        $currentDay = (int)date('w'); // 0 = Sunday, 6 = Saturday
        
        // Business hours: Mon-Fri 9-18, Sat 10-16
        $isBusinessHours = (($currentDay >= 1 && $currentDay <= 5) && ($currentHour >= 9 && $currentHour < 18)) ||
                          (($currentDay === 6) && ($currentHour >= 10 && $currentHour < 16));
        
        // Get queue count (simulate)
        $queueCount = rand(0, 5); // In production, this would be actual queue count
        $estimatedWait = $queueCount * 2; // minutes
        
        echo json_encode([
            'success' => true,
            'data' => [
                'available' => $isBusinessHours,
                'queue_count' => $queueCount,
                'estimated_wait_minutes' => $estimatedWait,
                'business_hours' => [
                    'monday_friday' => '9:00 AM - 6:00 PM',
                    'saturday' => '10:00 AM - 4:00 PM',
                    'sunday' => 'Closed'
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function getChatRooms($pdo, $customerId) {
    try {
        // Get customer's chat sessions
        $stmt = $pdo->prepare("
            SELECT cs.*, 
                   (SELECT COUNT(*) FROM chat_messages WHERE session_id = cs.id) as message_count,
                   (SELECT created_at FROM chat_messages WHERE session_id = cs.id ORDER BY created_at DESC LIMIT 1) as last_message_at
            FROM chat_sessions cs
            WHERE cs.user_id = ?
            ORDER BY cs.started_at DESC
            LIMIT 10
        ");
        
        $stmt->execute([$customerId]);
        $rooms = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $rooms
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function createChatRoom($pdo, $customerId, $data) {
    try {
        // Check if customer has an active chat session
        $stmt = $pdo->prepare("
            SELECT id FROM chat_sessions 
            WHERE user_id = ? AND status = 'active'
            ORDER BY started_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$customerId]);
        $existingSession = $stmt->fetch();
        
        if ($existingSession) {
            // Return existing active session
            echo json_encode([
                'success' => true,
                'message' => 'Existing chat session found',
                'data' => [
                    'session_id' => $existingSession['id'],
                    'status' => 'active'
                ]
            ]);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Create new chat session
        $stmt = $pdo->prepare("
            INSERT INTO chat_sessions (
                user_id, status, started_at
            ) VALUES (?, 'waiting', NOW())
        ");
        
        $stmt->execute([$customerId]);
        $sessionId = $pdo->lastInsertId();
        
        // Add initial system message
        $welcomeMessage = $data['initial_message'] ?? 'Hello! I need help with my order/product/account.';
        
        $stmt = $pdo->prepare("
            INSERT INTO chat_messages (
                session_id, sender_type, sender_id, message, created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $sessionId,
            'customer',
            $customerId,
            $welcomeMessage
        ]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Chat session created successfully',
            'data' => [
                'session_id' => $sessionId,
                'status' => 'waiting',
                'queue_position' => rand(1, 3), // Simulated queue position
                'estimated_wait' => rand(1, 5) // minutes
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function getChatMessages($pdo, $customerId, $sessionId) {
    try {
        // Verify the chat session belongs to the customer
        $stmt = $pdo->prepare("
            SELECT id, status FROM chat_sessions 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$sessionId, $customerId]);
        $session = $stmt->fetch();
        
        if (!$session) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Chat session not found']);
            return;
        }
        
        // Get messages
        $stmt = $pdo->prepare("
            SELECT cm.*,
                   CASE 
                       WHEN cm.sender_type = 'customer' THEN CONCAT(u.first_name, ' ', u.last_name)
                       WHEN cm.sender_type = 'agent' THEN 'Support Agent'
                       ELSE 'System'
                   END as sender_name
            FROM chat_messages cm
            LEFT JOIN users u ON cm.sender_type = 'customer' AND cm.sender_id = u.id
            WHERE cm.session_id = ?
            ORDER BY cm.created_at ASC
        ");
        
        $stmt->execute([$sessionId]);
        $messages = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'session_id' => $sessionId,
                'session_status' => $session['status'],
                'messages' => $messages,
                'message_count' => count($messages)
            ]
        ]);
        
    } catch (Exception $e) {
        throw $e;
    }
}

function sendChatMessage($pdo, $customerId, $sessionId, $data) {
    try {
        // Validate input
        if (empty($data['message'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message content is required']);
            return;
        }
        
        $message = trim($data['message']);
        if (strlen($message) > 1000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message is too long (max 1000 characters)']);
            return;
        }
        
        // Verify the chat session belongs to the customer
        $stmt = $pdo->prepare("
            SELECT id, status FROM chat_sessions 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([$sessionId, $customerId]);
        $session = $stmt->fetch();
        
        if (!$session) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Chat session not found']);
            return;
        }
        
        if ($session['status'] === 'ended') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Chat session has ended']);
            return;
        }
        
        $pdo->beginTransaction();
        
        // Insert the message
        $stmt = $pdo->prepare("
            INSERT INTO chat_messages (
                session_id, sender_type, sender_id, message, created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $sessionId,
            'customer',
            $customerId,
            $message
        ]);
        
        $messageId = $pdo->lastInsertId();
        
        // Update session timestamp and status (if waiting, set to active)
        $newStatus = $session['status'] === 'waiting' ? 'active' : $session['status'];
        $stmt = $pdo->prepare("UPDATE chat_sessions SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $sessionId]);
        
        $pdo->commit();
        
        // Get the created message
        $stmt = $pdo->prepare("
            SELECT cm.*,
                   CONCAT(u.first_name, ' ', u.last_name) as sender_name
            FROM chat_messages cm
            LEFT JOIN users u ON cm.sender_id = u.id
            WHERE cm.id = ?
        ");
        $stmt->execute([$messageId]);
        $messageData = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'message_id' => $messageId,
                'session_id' => $sessionId,
                'message' => $messageData['message'],
                'sender_name' => $messageData['sender_name'],
                'created_at' => $messageData['created_at'],
                'session_status' => $newStatus
            ]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

?>