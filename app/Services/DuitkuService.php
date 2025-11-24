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
     * Create payment request to Duitku
     */
    public function createPayment(Invoice $invoice, $paymentMethod = 'SP')
    {
        try {
            $merchantOrderId = $this->generateMerchantOrderId($invoice);
            $amount = (int) $invoice->total_amount;
            
            // Prepare payment data
            $paymentData = [
                'merchantCode' => $this->merchantCode,
                'paymentAmount' => $amount,
                'paymentMethod' => $paymentMethod,
                'merchantOrderId' => $merchantOrderId,
                'productDetails' => $invoice->title,
                'additionalParam' => json_encode([
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->client_id
                ]),
                'merchantUserInfo' => $invoice->client->email,
                'customerVaName' => $invoice->client->name,
                'email' => $invoice->client->email,
                'phoneNumber' => $invoice->client->phone ?? '081234567890',
                'itemDetails' => $this->getItemDetails($invoice),
                'customerDetail' => $this->getCustomerDetails($invoice),
                'callbackUrl' => $this->callbackUrl,
                'returnUrl' => $this->returnUrl,
                'expiryPeriod' => 1440 // 24 hours in minutes
            ];

            // Generate signature
            $signature = $this->generateSignature($paymentData);
            $paymentData['signature'] = $signature;

            Log::info('Duitku Payment Request', $paymentData);

            // Send request to Duitku
            $response = Http::timeout(30)->post($this->baseUrl . 'v2/inquiry', $paymentData);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['statusCode'] === '00') {
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
                        'reference' => $result['reference'],
                        'merchant_order_id' => $merchantOrderId,
                        'amount' => $amount
                    ];
                } else {
                    Log::error('Duitku Error Response', $result);
                    return [
                        'success' => false,
                        'message' => $result['statusMessage'] ?? 'Payment creation failed'
                    ];
                }
            } else {
                Log::error('Duitku HTTP Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to connect to payment gateway'
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
     * Get item details for Duitku
     */
    private function getItemDetails(Invoice $invoice)
    {
        $items = [];
        
        foreach ($invoice->items as $item) {
            $items[] = [
                'name' => $item->description,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity
            ];
        }

        // If no items, create a default item
        if (empty($items)) {
            $items[] = [
                'name' => $invoice->title,
                'price' => (int) $invoice->total_amount,
                'quantity' => 1
            ];
        }

        return $items;
    }

    /**
     * Get customer details for Duitku
     */
    private function getCustomerDetails(Invoice $invoice)
    {
        return [
            'firstName' => $invoice->client->name,
            'lastName' => '',
            'email' => $invoice->client->email,
            'phoneNumber' => $invoice->client->phone ?? '081234567890',
            'billingAddress' => [
                'firstName' => $invoice->client->name,
                'lastName' => '',
                'address' => $invoice->client->address ?? 'Jakarta',
                'city' => 'Jakarta',
                'postalCode' => '12345',
                'phone' => $invoice->client->phone ?? '081234567890',
                'countryCode' => 'ID'
            ],
            'shippingAddress' => [
                'firstName' => $invoice->client->name,
                'lastName' => '',
                'address' => $invoice->client->address ?? 'Jakarta',
                'city' => 'Jakarta',
                'postalCode' => '12345',
                'phone' => $invoice->client->phone ?? '081234567890',
                'countryCode' => 'ID'
            ]
        ];
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
