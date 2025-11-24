<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    private $merchantCode;
    private $apiKey;
    private $baseUrl;
    private $returnUrl;
    private $callbackUrl;

    public function __construct()
    {
        $this->merchantCode = config('services.duitku.merchant_code');
        $this->apiKey = config('services.duitku.api_key');
        $this->returnUrl = config('services.duitku.return_url');
        $this->callbackUrl = config('services.duitku.callback_url');
        
        // Set base URL based on environment
        $env = config('services.duitku.env', 'sandbox');
        $this->baseUrl = $env === 'production' 
            ? 'https://passport.duitku.com/webapi/api/merchant/' 
            : 'https://sandbox.duitku.com/webapi/api/merchant/';
    }

    /**
     * Create payment request to Duitku (Fixed based on working PHP native code)
     */
    public function createPayment(Invoice $invoice, $paymentMethod = 'SP', $customerData = null)
    {
        try {
            $merchantOrderId = $this->generateMerchantOrderId($invoice);
            $amount = (int) $invoice->total_amount;
            
            // Use customer data if provided, otherwise use client data
            $customerName = $customerData['name'] ?? $invoice->client->name;
            $customerEmail = $customerData['email'] ?? $invoice->client->email;
            $customerPhone = $customerData['phone'] ?? $invoice->client->phone ?? '081234567890';
            
            // Prepare payment data EXACTLY like working PHP native code
            $paymentData = [
                'merchantCode' => $this->merchantCode,
                'paymentAmount' => $amount,
                'paymentMethod' => $paymentMethod,
                'merchantOrderId' => $merchantOrderId,
                'productDetails' => $invoice->title,
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,
                'customerPhone' => $customerPhone,
                'returnUrl' => $this->returnUrl,
                'callbackUrl' => $this->callbackUrl,
                'signature' => '', // Will be set below
                'expiryPeriod' => 120, // 2 hours like PHP native
                'additionalParam' => json_encode([
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client_id,
                    'timestamp' => date('Y-m-d H:i:s')
                ])
            ];

            // Generate signature EXACTLY like PHP native
            $signature = $this->generateSignature($paymentData);
            $paymentData['signature'] = $signature;

            Log::info('Duitku Payment Request (Fixed Format)', [
                'merchantCode' => $paymentData['merchantCode'],
                'merchantOrderId' => $merchantOrderId,
                'amount' => $amount,
                'paymentMethod' => $paymentMethod,
                'customerName' => $customerName,
                'customerEmail' => $customerEmail,
                'signature' => $signature
            ]);

            // Debug: Log the full URL and data being sent
            $fullUrl = $this->baseUrl . 'v2/inquiry';
            Log::info('Duitku Request Details', [
                'url' => $fullUrl,
                'merchant_code' => $this->merchantCode,
                'api_key_set' => !empty($this->apiKey),
                'data_keys' => array_keys($paymentData)
            ]);

            // Send request to Duitku with better error handling
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'User-Agent' => 'Exputra-Payment-Gateway/1.0'
                    ])
                    ->post($fullUrl, $paymentData);

                Log::info('Duitku Response Details', [
                    'status_code' => $response->status(),
                    'headers' => $response->headers(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    
                    if (isset($result['statusCode']) && $result['statusCode'] === '00') {
                        // Update invoice with Duitku data
                        $invoice->update([
                            'duitku_merchant_code' => $merchantOrderId,
                            'duitku_reference' => $result['reference'] ?? null,
                            'duitku_payment_url' => $result['paymentUrl'] ?? null,
                            'status' => 'Sent'
                        ]);

                        return [
                            'success' => true,
                            'payment_url' => $result['paymentUrl'],
                            'reference' => $result['reference'] ?? null,
                            'merchant_order_id' => $merchantOrderId,
                            'amount' => $amount
                        ];
                    } else {
                        Log::error('Duitku API Error', $result);
                        return [
                            'success' => false,
                            'message' => $result['statusMessage'] ?? 'Payment creation failed: ' . ($result['statusCode'] ?? 'Unknown error')
                        ];
                    }
                } else {
                    Log::error('Duitku HTTP Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'url' => $fullUrl
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'HTTP Error ' . $response->status() . ': ' . $response->body()
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Duitku Connection Error', [
                    'error' => $e->getMessage(),
                    'url' => $fullUrl
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Connection failed: ' . $e->getMessage()
                ];
            } catch (\Exception $e) {
                Log::error('Duitku Request Exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Request failed: ' . $e->getMessage()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Duitku Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment system error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Handle payment callback from Duitku
     */
    public function handleCallback($data)
    {
        try {
            Log::info('Duitku Callback Received', $data);

            // Verify signature
            if (!$this->verifyCallback($data)) {
                Log::error('Invalid Duitku callback signature');
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            $merchantOrderId = $data['merchantOrderId'];
            $resultCode = $data['resultCode'];
            $amount = $data['amount'];
            $reference = $data['reference'] ?? null;

            // Find invoice by merchant order ID
            $invoice = Invoice::where('duitku_merchant_code', $merchantOrderId)->first();
            
            if (!$invoice) {
                Log::error('Invoice not found for merchant order ID: ' . $merchantOrderId);
                return ['success' => false, 'message' => 'Invoice not found'];
            }

            // Update invoice based on payment status
            if ($resultCode === '00') {
                // Payment successful
                $invoice->update([
                    'status' => 'Paid',
                    'paid_date' => now(),
                    'payment_method' => 'Duitku',
                    'payment_reference' => $reference,
                    'duitku_reference' => $reference
                ]);

                Log::info('Payment successful for invoice: ' . $invoice->number);
                
                return ['success' => true, 'message' => 'Payment successful'];
            } else {
                // Payment failed or cancelled
                $invoice->update([
                    'status' => 'Overdue',
                    'payment_reference' => $reference,
                    'duitku_reference' => $reference
                ]);

                Log::info('Payment failed for invoice: ' . $invoice->number . ' - Code: ' . $resultCode);
                
                return ['success' => true, 'message' => 'Payment status updated'];
            }
        } catch (\Exception $e) {
            Log::error('Duitku Callback Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return ['success' => false, 'message' => 'Callback processing error'];
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($merchantOrderId)
    {
        try {
            $data = [
                'merchantCode' => $this->merchantCode,
                'merchantOrderId' => $merchantOrderId
            ];

            $signature = md5($data['merchantCode'] . $data['merchantOrderId'] . $this->apiKey);
            $data['signature'] = $signature;

            $response = Http::timeout(30)->post($this->baseUrl . 'transactionStatus', $data);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Duitku Status Check Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate merchant order ID
     */
    private function generateMerchantOrderId(Invoice $invoice)
    {
        return 'INV-' . $invoice->id . '-' . time();
    }

    /**
     * Generate signature for payment request
     */
    private function generateSignature($data)
    {
        $signature = md5(
            $data['merchantCode'] . 
            $data['merchantOrderId'] . 
            $data['paymentAmount'] . 
            $this->apiKey
        );
        
        return $signature;
    }

    /**
     * Verify callback signature
     */
    private function verifyCallback($data)
    {
        $calculatedSignature = md5(
            $data['merchantCode'] . 
            $data['amount'] . 
            $data['merchantOrderId'] . 
            $this->apiKey
        );
        
        return hash_equals($calculatedSignature, $data['signature']);
    }


    /**
     * Get available payment methods
     */
    public function getPaymentMethods()
    {
        return [
            'SP' => 'Shopee Pay',
            'NQ' => 'QRIS',
            'OV' => 'OVO',
            'DA' => 'DANA',
            'LK' => 'LinkAja',
            'M2' => 'Mandiri VA',
            'I1' => 'BCA VA',
            'B1' => 'CIMB Niaga VA',
            'BT' => 'Permata Bank VA',
            'A1' => 'ATM Bersama',
            'AG' => 'Bank Transfer',
        ];
    }
}
