<?php
// Customer Addresses API

// Headers are set in index.php
require_once __DIR__ . '/../config/database.php';

// Start session for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Get actual logged-in user ID from session
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}
$userId = $_SESSION['customer_id'];

// Handle routing when called directly (for backwards compatibility)
if (!isset($action) || !isset($requestMethod) || !isset($id)) {
    $requestUri = $_SERVER['REQUEST_URI'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestPath = parse_url($requestUri, PHP_URL_PATH);
    
    // Remove base path to get API endpoint
    $basePath = '/Core1_ecommerce/customer/api';
    $endpoint = str_replace($basePath, '', $requestPath);
    $endpoint = trim($endpoint, '/');
    
    // Split endpoint into parts
    $endpointParts = explode('/', $endpoint);
    $module = $endpointParts[0] ?? '';
    $action = $endpointParts[1] ?? '';
    $id = $endpointParts[2] ?? null;
    
    // Handle special case for addresses/{id} -> show action
    if ($module === 'addresses' && is_numeric($action)) {
        $id = $action;
        $action = 'show';
    }
    
    // Handle addresses/{id}/default -> default action
    if ($module === 'addresses' && is_numeric($action) && isset($endpointParts[2]) && $endpointParts[2] === 'default') {
        $id = $action;
        $action = 'default';
    }
    
    // Handle POST requests to create addresses
    if ($module === 'addresses' && $requestMethod === 'POST' && empty($action)) {
        $action = 'create';
    }
    
    // Handle PUT/DELETE requests to specific addresses
    if ($module === 'addresses' && is_numeric($action) && ($requestMethod === 'PUT' || $requestMethod === 'DELETE')) {
        $id = $action;
        $action = $requestMethod === 'PUT' ? 'update' : 'delete';
    }
}

switch ($action) {
    case '':
    case 'list':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("
                SELECT 
                    id, type, label, first_name, last_name, company,
                    address_line_1, address_line_2, city, state, 
                    postal_code, country, phone, is_default, 
                    created_at, updated_at
                FROM user_addresses 
                WHERE user_id = ?
                ORDER BY is_default DESC, created_at DESC
            ");
            $stmt->execute([$userId]);
            $addresses = $stmt->fetchAll();
            
            // Format addresses
            foreach ($addresses as &$address) {
                $address['id'] = intval($address['id']);
                $address['is_default'] = (bool) $address['is_default'];
                
                // Create full address string
                $addressParts = array_filter([
                    $address['address_line_1'],
                    $address['address_line_2'],
                    $address['city'],
                    $address['state'],
                    $address['postal_code'],
                    $address['country']
                ]);
                $address['full_address'] = implode(', ', $addressParts);
                
                // Create display name
                $nameParts = array_filter([$address['first_name'], $address['last_name']]);
                $address['full_name'] = implode(' ', $nameParts);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $addresses
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch addresses: ' . $e->getMessage()]);
        }
        break;
        
    case 'show':
        if ($requestMethod !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $addressId = $id;
        
        if (!$addressId || !is_numeric($addressId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid address ID required']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("
                SELECT * FROM user_addresses 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$addressId, $userId]);
            $address = $stmt->fetch();
            
            if (!$address) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Address not found']);
                break;
            }
            
            // Format address
            $address['id'] = intval($address['id']);
            $address['user_id'] = intval($address['user_id']);
            $address['is_default'] = (bool) $address['is_default'];
            
            if ($address['latitude']) $address['latitude'] = floatval($address['latitude']);
            if ($address['longitude']) $address['longitude'] = floatval($address['longitude']);
            
            echo json_encode([
                'success' => true,
                'data' => $address
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch address: ' . $e->getMessage()]);
        }
        break;
        
    case 'create':
    case '':
        if ($requestMethod !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        // Validate required fields
        $required = ['address_line_1', 'city', 'state'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                break 2;
            }
        }
        
        try {
            $pdo->beginTransaction();
            
            $isDefault = !empty($input['is_default']);
            
            // If setting as default, remove default from other addresses
            if ($isDefault) {
                $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
                $stmt->execute([$userId]);
            }
            
            // Insert new address
            $stmt = $pdo->prepare("
                INSERT INTO user_addresses (
                    user_id, type, label, first_name, last_name, company,
                    address_line_1, address_line_2, city, state, postal_code, 
                    country, latitude, longitude, phone, is_default, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $input['type'] ?? 'home',
                $input['label'] ?? null,
                $input['first_name'] ?? null,
                $input['last_name'] ?? null,
                $input['company'] ?? null,
                $input['address_line_1'],
                $input['address_line_2'] ?? null,
                $input['city'],
                $input['state'],
                $input['postal_code'] ?? null,
                $input['country'] ?? 'Philippines',
                $input['latitude'] ?? null,
                $input['longitude'] ?? null,
                $input['phone'] ?? null,
                $isDefault ? 1 : 0
            ]);
            
            $addressId = $pdo->lastInsertId();
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => [
                    'id' => intval($addressId),
                    'is_default' => $isDefault
                ]
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create address: ' . $e->getMessage()]);
        }
        break;
        
    case 'update':
        if ($requestMethod !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $addressId = $id;
        
        if (!$addressId || !is_numeric($addressId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid address ID required']);
            break;
        }
        
        try {
            // Check if address exists and belongs to user
            $stmt = $pdo->prepare("SELECT id FROM user_addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Address not found']);
                break;
            }
            
            $pdo->beginTransaction();
            
            $isDefault = !empty($input['is_default']);
            
            // If setting as default, remove default from other addresses
            if ($isDefault) {
                $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ? AND id != ?");
                $stmt->execute([$userId, $addressId]);
            }
            
            // Build update query dynamically
            $updateFields = [];
            $updateValues = [];
            
            $allowedFields = [
                'type', 'label', 'first_name', 'last_name', 'company',
                'address_line_1', 'address_line_2', 'city', 'state', 
                'postal_code', 'country', 'latitude', 'longitude', 'phone'
            ];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $input)) {
                    $updateFields[] = "$field = ?";
                    $updateValues[] = $input[$field];
                }
            }
            
            if ($isDefault !== null) {
                $updateFields[] = "is_default = ?";
                $updateValues[] = $isDefault ? 1 : 0;
            }
            
            if (!empty($updateFields)) {
                $updateFields[] = "updated_at = NOW()";
                $updateValues[] = $addressId;
                
                $sql = "UPDATE user_addresses SET " . implode(', ', $updateFields) . " WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($updateValues);
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Address updated successfully'
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update address: ' . $e->getMessage()]);
        }
        break;
        
    case 'delete':
        if ($requestMethod !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $addressId = $id;
        
        if (!$addressId || !is_numeric($addressId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid address ID required']);
            break;
        }
        
        try {
            $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Address deleted successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Address not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete address: ' . $e->getMessage()]);
        }
        break;
        
    case 'default':
        if ($requestMethod !== 'PUT') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
        }
        
        $addressId = $id;
        
        if (!$addressId || !is_numeric($addressId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid address ID required']);
            break;
        }
        
        try {
            // Check if address exists and belongs to user
            $stmt = $pdo->prepare("SELECT id FROM user_addresses WHERE id = ? AND user_id = ?");
            $stmt->execute([$addressId, $userId]);
            
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Address not found']);
                break;
            }
            
            $pdo->beginTransaction();
            
            // Remove default from all addresses
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            // Set this address as default
            $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = ?");
            $stmt->execute([$addressId]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Default address updated successfully'
            ]);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to set default address: ' . $e->getMessage()]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Address endpoint not found']);
}
?>