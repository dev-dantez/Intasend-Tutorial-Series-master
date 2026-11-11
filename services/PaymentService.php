<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Payment.php';

use IntaSend\IntaSendPHP\Checkout;
use IntaSend\IntaSendPHP\Collection;
use IntaSend\IntaSendPHP\Transfer;

class PaymentService {
    private $credentials;
    private $paymentModel;
    
    public function __construct() {
        $this->credentials = [
            'token' => Config::get('intasend.secret_key'),
            'publishable_key' => Config::get('intasend.publishable_key'),
        ];
        $this->paymentModel = new Payment();
    }
    
    public function createCheckout($userId, $amount, $currency, $customer, $type = 'CHECKOUT') {
        $referenceNumber = $type . '-' . uniqid() . '-' . time();
        
        // Save to database
        $paymentId = $this->paymentModel->create($userId, $amount, $currency, $type, $referenceNumber);
        
        if (!$paymentId) {
            throw new Exception("Failed to create payment record");
        }
        
        try {
            $checkout = new Checkout();
            $checkout->init($this->credentials);
            
            $baseUrl = get_base_url();
            $redirectUrl = $baseUrl . '/webhook/callback.php';
            
            $response = $checkout->create(
                $amount,
                $currency,
                $customer,
                $baseUrl,
                $redirectUrl,
                $referenceNumber,
                "Payment for " . $type,
                null
            );
            
            log_activity("Checkout created", [
                'user_id' => $userId,
                'reference' => $referenceNumber,
                'amount' => $amount
            ]);
            
            return [
                'success' => true,
                'checkout_url' => $response->url ?? null,
                'reference' => $referenceNumber,
                'payment_id' => $paymentId
            ];
            
        } catch (Exception $e) {
            log_activity("Checkout creation failed", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    public function initiateSTKPush($userId, $phoneNumber, $amount, $narrative = 'Payment') {
        $referenceNumber = 'STK-' . uniqid() . '-' . time();
        
        // Validate phone
        $validPhone = validate_phone($phoneNumber);
        if (!$validPhone) {
            throw new Exception("Invalid phone number format");
        }
        
        // Save to database
        $paymentId = $this->paymentModel->create($userId, $amount, 'KES', 'STK_PUSH', $referenceNumber);
        
        if (!$paymentId) {
            throw new Exception("Failed to create payment record");
        }
        
        try {
            $collection = new Collection();
            $collection->init($this->credentials);
            
            $response = $collection->mpesa_stk_push($amount, $validPhone, $narrative);
            
            if ($response && isset($response->invoice)) {
                $this->paymentModel->updateStatus(
                    $referenceNumber,
                    $response->invoice->state,
                    $response->invoice->invoice_id ?? null,
                    (array)$response
                );
            }
            
            log_activity("STK Push initiated", [
                'user_id' => $userId,
                'phone' => $validPhone,
                'amount' => $amount,
                'reference' => $referenceNumber
            ]);
            
            return [
                'success' => true,
                'response' => $response,
                'reference' => $referenceNumber
            ];
            
        } catch (Exception $e) {
            log_activity("STK Push failed", [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    public function verifyPayment($signature, $checkoutId = null, $invoiceId = null) {
        try {
            $checkout = new Checkout();
            $checkout->init($this->credentials);
            
            $response = $checkout->check_status($signature, $checkoutId, $invoiceId);
            
            if (isset($response->invoice)) {
                $invoice = $response->invoice;
                
                // Find payment by reference or invoice ID
                $payment = null;
                if (isset($invoice->api_ref)) {
                    $payment = $this->paymentModel->findByReference($invoice->api_ref);
                }
                
                if ($payment) {
                    $this->paymentModel->updateStatus(
                        $payment['reference_number'],
                        $invoice->state,
                        $invoice->invoice_id ?? null,
                        (array)$response
                    );
                }
                
                log_activity("Payment verified", [
                    'signature' => $signature,
                    'status' => $invoice->state
                ]);
            }
            
            return $response;
            
        } catch (Exception $e) {
            log_activity("Payment verification failed", [
                'signature' => $signature,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
