<?php
class APIResponse {
    public static function success($data = [], $message = 'Success') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
    
    public static function error($message = 'Error', $code = 400, $data = []) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
    
    public static function unauthorized($message = 'Unauthorized') {
        self::error($message, 401);
    }
    
    public static function forbidden($message = 'Forbidden') {
        self::error($message, 403);
    }
    
    public static function notFound($message = 'Not found') {
        self::error($message, 404);
    }
    
    public static function validation($errors) {
        self::error('Validation failed', 422, ['errors' => $errors]);
    }
}

class RequestValidator {
    public static function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = $field . ' is required';
                continue;
            }
            
            if (!empty($value)) {
                if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = $field . ' must be a valid email';
                }
                
                if (isset($rule['min']) && strlen($value) < $rule['min']) {
                    $errors[$field] = $field . ' must be at least ' . $rule['min'] . ' characters';
                }
                
                if (isset($rule['max']) && strlen($value) > $rule['max']) {
                    $errors[$field] = $field . ' must not exceed ' . $rule['max'] . ' characters';
                }
                
                if (isset($rule['numeric']) && $rule['numeric'] && !is_numeric($value)) {
                    $errors[$field] = $field . ' must be numeric';
                }
            }
        }
        
        return $errors;
    }
    
    public static function validateAndRespond($data, $rules) {
        $errors = self::validate($data, $rules);
        if (!empty($errors)) {
            APIResponse::validation($errors);
        }
    }
}
?>