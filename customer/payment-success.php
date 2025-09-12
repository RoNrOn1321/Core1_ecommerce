<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - Core1 E-commerce</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-gray-50">

<?php 
// Require authentication
require_once 'auth/functions.php';
requireLogin();

// Get parameters
$orderId = $_GET['order_id'] ?? null;
$paymentIntentId = $_GET['payment_intent_id'] ?? null;

if (!$orderId || !$paymentIntentId) {
    header('Location: index.php');
    exit;
}
?>

<?php include 'components/navbar.php'; ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Processing Card -->
        <div id="processingCard" class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-amber-600 mx-auto mb-4"></div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Processing Your Payment</h1>
            <p class="text-gray-600 mb-4">Please wait while we verify your GCash payment...</p>
            <div class="text-sm text-gray-500">
                <p>Order ID: #<?php echo htmlspecialchars($orderId); ?></p>
                <p>This may take a few moments. Please do not close this page.</p>
            </div>
        </div>

        <!-- Success Card (Hidden initially) -->
        <div id="successCard" class="bg-white rounded-lg shadow-sm p-8 text-center" style="display: none;">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
            <p class="text-gray-600 mb-6">Thank you for your purchase. Your order has been confirmed.</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="text-sm text-gray-600">
                    <p><strong>Order Number:</strong> <span id="orderNumber">-</span></p>
                    <p><strong>Amount Paid:</strong> ₱<span id="amountPaid">0.00</span></p>
                    <p><strong>Payment Method:</strong> GCash</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="#" id="viewReceiptBtn"
                   class="flex-1 bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                    <i class="fas fa-receipt mr-2"></i>
                    View Receipt
                </a>
                <a href="products.php" 
                   class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Continue Shopping
                </a>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-clock mr-1"></i>
                    Redirecting to receipt in <span id="countdown">5</span> seconds...
                </p>
                <button onclick="cancelAutoRedirect()" class="text-sm text-amber-600 hover:text-amber-700 underline mt-2">
                    Cancel auto-redirect
                </button>
            </div>
        </div>

        <!-- Failed Card (Hidden initially) -->
        <div id="failedCard" class="bg-white rounded-lg shadow-sm p-8 text-center" style="display: none;">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-times-circle text-red-600 text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Payment Failed</h1>
            <p class="text-gray-600 mb-6">Your payment could not be processed. Please try again.</p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="text-sm text-gray-600">
                    <p><strong>Order Number:</strong> <span id="failedOrderNumber">-</span></p>
                    <p><strong>Status:</strong> Payment Failed</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button onclick="retryPayment()" 
                        class="flex-1 bg-amber-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                    <i class="fas fa-retry mr-2"></i>
                    Try Again
                </button>
                <a href="checkout.php" 
                   class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Checkout
                </a>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/customer-api.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderId = <?php echo json_encode($orderId); ?>;
    const paymentIntentId = <?php echo json_encode($paymentIntentId); ?>;
    
    // Start payment verification
    verifyPayment();
    
    async function verifyPayment() {
        try {
            const response = await fetch('/Core1_ecommerce/customer/api/payments/verify-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_intent_id: paymentIntentId
                })
            });
            
            const result = await response.json();
            
            if (result.success && result.data) {
                const data = result.data;
                
                if (data.payment_status === 'paid') {
                    // Payment successful
                    showSuccess(data);
                } else if (data.payment_status === 'failed') {
                    // Payment failed
                    showFailure(data);
                } else {
                    // Still processing, check again in 3 seconds
                    setTimeout(verifyPayment, 3000);
                }
            } else {
                // Error occurred, check again or show failure
                setTimeout(verifyPayment, 5000);
            }
            
        } catch (error) {
            console.error('Payment verification error:', error);
            // Retry after 5 seconds
            setTimeout(verifyPayment, 5000);
        }
    }
    
    function showSuccess(data) {
        document.getElementById('processingCard').style.display = 'none';
        document.getElementById('successCard').style.display = 'block';
        
        document.getElementById('orderNumber').textContent = data.order_number;
        document.getElementById('amountPaid').textContent = data.amount.toFixed(2);
        
        // Set up receipt button
        const receiptBtn = document.getElementById('viewReceiptBtn');
        receiptBtn.href = `receipt.php?order=${data.order_id}`;
        
        // Update page title
        document.title = 'Payment Successful - Core1 E-commerce';
    }
    
    function showFailure(data) {
        document.getElementById('processingCard').style.display = 'none';
        document.getElementById('failedCard').style.display = 'block';
        
        document.getElementById('failedOrderNumber').textContent = data.order_number;
        
        // Update page title
        document.title = 'Payment Failed - Core1 E-commerce';
    }
    
    // Global function for retry button
    window.retryPayment = function() {
        window.location.href = `checkout.php?retry_order=${orderId}`;
    };
});

// Auto-redirect to receipt page after successful payment with countdown
let autoRedirectTimer;
let countdownTimer;
let countdownValue = 5;

function startAutoRedirect(orderId) {
    // Show countdown
    const countdownElement = document.getElementById('countdown');
    countdownValue = 5;
    
    countdownTimer = setInterval(function() {
        countdownValue--;
        if (countdownElement) {
            countdownElement.textContent = countdownValue;
        }
        
        if (countdownValue <= 0) {
            clearInterval(countdownTimer);
            clearTimeout(autoRedirectTimer);
            window.location.href = `receipt.php?order=${orderId}`;
        }
    }, 1000);
    
    // Backup timer in case countdown fails
    autoRedirectTimer = setTimeout(function() {
        window.location.href = `receipt.php?order=${orderId}`;
    }, 5000);
}

function cancelAutoRedirect() {
    if (autoRedirectTimer) {
        clearTimeout(autoRedirectTimer);
    }
    if (countdownTimer) {
        clearInterval(countdownTimer);
    }
    
    // Hide countdown
    const countdownContainer = document.querySelector('.mt-6.text-center');
    if (countdownContainer) {
        countdownContainer.style.display = 'none';
    }
}

// Update the success function to start auto-redirect
document.addEventListener('DOMContentLoaded', function() {
    // Store original showSuccess function
    const originalShowSuccess = window.showSuccess;
    window.showSuccess = function(data) {
        originalShowSuccess(data);
        startAutoRedirect(data.order_id);
    };
    
    // Make cancelAutoRedirect available globally
    window.cancelAutoRedirect = cancelAutoRedirect;
});
</script>

</body>
</html>