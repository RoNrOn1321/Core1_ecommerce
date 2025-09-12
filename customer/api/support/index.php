<?php
// Customer Support API Router
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
$resource = $endpointParts[1] ?? ''; // tickets, chat, messages

// Route to appropriate handler
switch ($resource) {
    case 'tickets':
        require_once __DIR__ . '/tickets.php';
        break;
        
    case 'messages':
        require_once __DIR__ . '/messages.php';
        break;
        
    case 'chat':
        require_once __DIR__ . '/chat.php';
        break;
        
    case '':
    case 'status':
        // Support system status/health check
        getSupportStatus();
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'success' => false, 
            'message' => 'Support API endpoint not found',
            'available_endpoints' => [
                '/support/tickets' => 'Ticket management',
                '/support/messages/{ticket_id}' => 'Ticket messages',
                '/support/chat/rooms' => 'Chat rooms',
                '/support/chat/messages/{room_id}' => 'Chat messages',
                '/support/status' => 'Support system status'
            ]
        ]);
        break;
}

function getSupportStatus() {
    try {
        // Initialize auth system  
        global $pdo;
        
        // Check database connectivity
        $stmt = $pdo->query("SELECT COUNT(*) as ticket_count FROM support_tickets");
        $ticketCount = $stmt->fetch()['ticket_count'];
        
        // Check business hours
        $currentHour = (int)date('H');
        $currentDay = (int)date('w'); // 0 = Sunday, 6 = Saturday
        
        $isBusinessHours = (($currentDay >= 1 && $currentDay <= 5) && ($currentHour >= 9 && $currentHour < 18)) ||
                          (($currentDay === 6) && ($currentHour >= 10 && $currentHour < 16));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'system_status' => 'operational',
                'support_available' => $isBusinessHours,
                'total_tickets' => (int)$ticketCount,
                'business_hours' => [
                    'monday_friday' => '9:00 AM - 6:00 PM',
                    'saturday' => '10:00 AM - 4:00 PM',  
                    'sunday' => 'Closed'
                ],
                'current_time' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get(),
                'available_services' => [
                    'tickets' => true,
                    'live_chat' => $isBusinessHours,
                    'email_support' => true
                ],
                'contact_info' => [
                    'email' => 'support@lumino.com',
                    'phone' => '+63 912 345 6789'
                ]
            ]
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Support system status check failed',
            'error' => $e->getMessage()
        ]);
    }
}

?>