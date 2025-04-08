<?php

// Include Razorpay PHP SDK
require_once('razorpay-php/Razorpay.php');

use Razorpay\Api\Api;

// Initialize Razorpay API with your key ID and secret key
$keyId = 'rzp_test_uccNt4nYvSk6KJ';
$keySecret = 'FEXpK3fxcp0MSmaBrmwdVZva';
$api = new Api($keyId, $keySecret);

// Process payment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $orderData = [
        'amount' => $amount * 100, // Convert to paisa
        'currency' => 'INR',
        'receipt' => 'order_rcptid_11',
        'payment_capture' => 1 // Auto-capture payment
    ];

    try {
        $order = $api->order->create($orderData);

        // Redirect to Razorpay payment page
        header("Location: " . $order->short_url);
        exit();
    } catch (Exception $e) {
        // Handle error
        echo 'Error: ' . $e->getMessage();
    }
}

?>
