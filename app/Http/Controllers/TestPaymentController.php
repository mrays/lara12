<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestPaymentController extends Controller
{
    protected $duitkuService;

    public function __construct(DuitkuService $duitkuService)
    {
        $this->duitkuService = $duitkuService;
    }

    /**
     * Test Duitku configuration and connectivity
     */
    public function testConfig()
    {
        $config = [
            'merchant_code' => config('services.duitku.merchant_code'),
            'api_key' => config('services.duitku.api_key') ? 'SET (' . strlen(config('services.duitku.api_key')) . ' chars)' : 'NOT SET',
            'env' => config('services.duitku.env'),
            'return_url' => config('services.duitku.return_url'),
            'callback_url' => config('services.duitku.callback_url'),
        ];

        // Test basic connectivity
        $env = config('services.duitku.env', 'sandbox');
        $baseUrl = $env === 'production' 
            ? 'https://passport.duitku.com/webapi/api/merchant/' 
            : 'https://sandbox.duitku.com/webapi/api/merchant/';
        
        $testUrl = $baseUrl . 'v2/inquiry';
        
        // Test connection
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get($baseUrl);
            $connectivity = [
                'status' => 'success',
                'message' => 'Can connect to Duitku',
                'response_code' => $response->status()
            ];
        } catch (\Exception $e) {
            $connectivity = [
                'status' => 'error',
                'message' => 'Cannot connect to Duitku: ' . $e->getMessage()
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Duitku configuration test',
            'config' => $config,
            'test_url' => $testUrl,
            'connectivity' => $connectivity,
            'payment_methods' => $this->duitkuService->getPaymentMethods(),
            'php_version' => PHP_VERSION,
            'curl_enabled' => function_exists('curl_version'),
            'openssl_enabled' => extension_loaded('openssl')
        ]);
    }

    /**
     * Test payment creation with sample data
     */
    public function testPayment(Request $request)
    {
        try {
            // Find a test invoice
            $invoice = Invoice::first();
            
            if (!$invoice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No invoice found for testing'
                ]);
            }

            $paymentMethod = $request->get('method', 'SP');
            
            // Test customer data
            $customerData = [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'phone' => '081234567890'
            ];

            Log::info('Testing Duitku Payment', [
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_amount,
                'method' => $paymentMethod
            ]);

            $result = $this->duitkuService->createPayment($invoice, $paymentMethod, $customerData);

            return response()->json([
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['success'] ? 'Payment created successfully' : $result['message'],
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Test Payment Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Test payment failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test callback processing
     */
    public function testCallback(Request $request)
    {
        try {
            // Sample callback data
            $callbackData = [
                'merchantCode' => config('services.duitku.merchant_code'),
                'amount' => '100000',
                'merchantOrderId' => 'TEST-' . time(),
                'productDetail' => 'Test Payment',
                'additionalParam' => '',
                'paymentCode' => 'SP',
                'resultCode' => '00',
                'merchantUserId' => 'test@example.com',
                'reference' => 'TEST-REF-' . time(),
                'signature' => ''
            ];

            // Generate signature for test
            $signature = md5(
                $callbackData['merchantCode'] . 
                $callbackData['amount'] . 
                $callbackData['merchantOrderId'] . 
                config('services.duitku.api_key')
            );
            
            $callbackData['signature'] = $signature;

            Log::info('Testing Duitku Callback', $callbackData);

            $result = $this->duitkuService->handleCallback($callbackData);

            return response()->json([
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
                'callback_data' => $callbackData
            ]);

        } catch (\Exception $e) {
            Log::error('Test Callback Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Test callback failed: ' . $e->getMessage()
            ]);
        }
    }
}
