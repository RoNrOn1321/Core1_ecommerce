<?php
/**
 * PayMongo Configuration
 * PayMongo PHP integration for Core1 E-commerce
 */

class PayMongo {
    private $publicKey;
    private $secretKey;
    private $baseUrl = 'https://api.paymongo.com/v1';
    
    public function __construct() {
        // Use test keys - replace with production keys in live environment
        $this->publicKey = 'pk_test_fAkaM23LqeC3VcQ65SdQVsJx';
        $this->secretKey = 'sk_test_pjwsYuhwzBmMt8Mg1coog2CN';
    }
    
    /**
     * Create a GCash payment source
     */
    public function createGCashSource($amount, $currency = 'PHP', $redirect_urls = []) {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100, // Convert to centavos
                    'currency' => $currency,
                    'type' => 'gcash',
                    'redirect' => $redirect_urls
                ]
            ]
        ];
        
        return $this->makeRequest('POST', '/sources', $data);
    }
    
    /**
     * Create a payment using a source
     */
    public function createPayment($amount, $sourceId, $description = '', $currency = 'PHP') {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100, // Convert to centavos
                    'currency' => $currency,
                    'description' => $description,
                    'source' => [
                        'id' => $sourceId,
                        'type' => 'source'
                    ]
                ]
            ]
        ];
        
        return $this->makeRequest('POST', '/payments', $data);
    }
    
    /**
     * Retrieve payment details
     */
    public function getPayment($paymentId) {
        return $this->makeRequest('GET', '/payments/' . $paymentId);
    }
    
    /**
     * Retrieve source details
     */
    public function getSource($sourceId) {
        return $this->makeRequest('GET', '/sources/' . $sourceId);
    }
    
    /**
     * Create a payment intent (recommended for newer implementations)
     */
    public function createPaymentIntent($amount, $currency = 'PHP', $paymentMethodTypes = ['gcash']) {
        $data = [
            'data' => [
                'attributes' => [
                    'amount' => $amount * 100, // Convert to centavos
                    'currency' => $currency,
                    'payment_method_allowed' => $paymentMethodTypes,
                    'capture_type' => 'automatic'
                ]
            ]
        ];
        
        return $this->makeRequest('POST', '/payment_intents', $data);
    }
    
    /**
     * Attach payment method to payment intent
     */
    public function attachPaymentMethod($paymentIntentId, $paymentMethodId, $returnUrl = null) {
        $data = [
            'data' => [
                'attributes' => [
                    'payment_method' => $paymentMethodId
                ]
            ]
        ];
        
        // Add return_url for GCash payments
        if ($returnUrl) {
            $data['data']['attributes']['return_url'] = $returnUrl;
        }
        
        return $this->makeRequest('POST', '/payment_intents/' . $paymentIntentId . '/attach', $data);
    }
    
    /**
     * Create GCash payment method
     */
    public function createGCashPaymentMethod($redirectUrl) {
        $data = [
            'data' => [
                'attributes' => [
                    'type' => 'gcash',
                    'details' => [
                        'redirect_url' => $redirectUrl
                    ]
                ]
            ]
        ];
        
        return $this->makeRequest('POST', '/payment_methods', $data);
    }
    
    /**
     * Make HTTP request to PayMongo API
     */
    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->secretKey . ':')
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $error
            ];
        }
        
        $responseData = json_decode($response, true);
        
        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => $responseData,
            'raw_response' => $response
        ];
    }
    
    /**
     * Verify webhook signature (for payment status updates)
     */
    public function verifyWebhookSignature($payload, $signature, $webhookSecret) {
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }
    
    /**
     * Format amount for display (convert centavos to peso)
     */
    public static function formatAmount($centavos) {
        return number_format($centavos / 100, 2);
    }
    
    /**
     * Convert peso to centavos
     */
    public static function toCentavos($peso) {
        return intval($peso * 100);
    }
}
?>