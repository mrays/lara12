<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimpleTestController extends Controller
{
    /**
     * Test basic connectivity to Duitku
     */
    public function testConnection()
    {
        $results = [];
        
        // Test 1: Check environment variables
        $results['env_check'] = [
            'DUITKU_MERCHANT_CODE' => env('DUITKU_MERCHANT_CODE') ? 'SET' : 'NOT SET',
            'DUITKU_API_KEY' => env('DUITKU_API_KEY') ? 'SET (' . strlen(env('DUITKU_API_KEY')) . ' chars)' : 'NOT SET',
            'DUITKU_ENV' => env('DUITKU_ENV', 'NOT SET'),
            'DUITKU_RETURN_URL' => env('DUITKU_RETURN_URL') ? 'SET' : 'NOT SET',
            'DUITKU_CALLBACK_URL' => env('DUITKU_CALLBACK_URL') ? 'SET' : 'NOT SET'
        ];
        
        // Test 2: Check config values
        $results['config_check'] = [
            'merchant_code' => config('services.duitku.merchant_code'),
            'api_key' => config('services.duitku.api_key') ? 'SET' : 'NOT SET',
            'env' => config('services.duitku.env'),
            'return_url' => config('services.duitku.return_url'),
            'callback_url' => config('services.duitku.callback_url')
        ];
        
        // Test 3: Test URLs
        $env = config('services.duitku.env', 'sandbox');
        $baseUrl = $env === 'production' 
            ? 'https://passport.duitku.com/webapi/api/merchant/' 
            : 'https://sandbox.duitku.com/webapi/api/merchant/';
        
        $results['url_info'] = [
            'environment' => $env,
            'base_url' => $baseUrl,
            'inquiry_url' => $baseUrl . 'v2/inquiry'
        ];
        
        // Test 4: Basic connectivity test
        try {
            Log::info('Testing connection to: ' . $baseUrl);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Exputra-Test/1.0'
                ])
                ->get($baseUrl);
            
            $results['connectivity'] = [
                'status' => 'success',
                'http_code' => $response->status(),
                'response_size' => strlen($response->body()),
                'headers' => $response->headers()
            ];
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $results['connectivity'] = [
                'status' => 'connection_error',
                'error' => $e->getMessage(),
                'type' => 'Connection Exception'
            ];
        } catch (\Exception $e) {
            $results['connectivity'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
                'type' => get_class($e)
            ];
        }
        
        // Test 5: System requirements
        $results['system_check'] = [
            'php_version' => PHP_VERSION,
            'curl_enabled' => function_exists('curl_version'),
            'openssl_enabled' => extension_loaded('openssl'),
            'json_enabled' => function_exists('json_encode'),
            'mbstring_enabled' => extension_loaded('mbstring'),
            'curl_version' => function_exists('curl_version') ? curl_version()['version'] : 'Not available'
        ];
        
        // Test 6: Simple signature test
        $merchantCode = config('services.duitku.merchant_code');
        $apiKey = config('services.duitku.api_key');
        
        if ($merchantCode && $apiKey) {
            $testOrderId = 'TEST-' . time();
            $testAmount = 100000;
            $testSignature = md5($merchantCode . $testOrderId . $testAmount . $apiKey);
            
            $results['signature_test'] = [
                'merchant_code' => $merchantCode,
                'test_order_id' => $testOrderId,
                'test_amount' => $testAmount,
                'signature' => $testSignature,
                'signature_length' => strlen($testSignature)
            ];
        } else {
            $results['signature_test'] = [
                'error' => 'Missing merchant code or API key'
            ];
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Test actual Duitku API call with minimal data
     */
    public function testApiCall()
    {
        $merchantCode = config('services.duitku.merchant_code');
        $apiKey = config('services.duitku.api_key');
        
        if (!$merchantCode || !$apiKey) {
            return response()->json([
                'error' => 'Missing merchant code or API key',
                'merchant_code' => $merchantCode ? 'SET' : 'NOT SET',
                'api_key' => $apiKey ? 'SET' : 'NOT SET'
            ]);
        }
        
        $env = config('services.duitku.env', 'sandbox');
        $baseUrl = $env === 'production' 
            ? 'https://passport.duitku.com/webapi/api/merchant/' 
            : 'https://sandbox.duitku.com/webapi/api/merchant/';
        
        $merchantOrderId = 'TEST-' . time();
        $paymentAmount = 100000;
        
        // Generate signature
        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);
        
        // Minimal test data
        $testData = [
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => 'SP',
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => 'Test Payment',
            'customerName' => 'Test Customer',
            'customerEmail' => 'test@example.com',
            'customerPhone' => '081234567890',
            'returnUrl' => config('services.duitku.return_url', 'https://example.com/return'),
            'callbackUrl' => config('services.duitku.callback_url', 'https://example.com/callback'),
            'signature' => $signature,
            'expiryPeriod' => 120,
            'additionalParam' => ''
        ];
        
        try {
            Log::info('Testing Duitku API call', [
                'url' => $baseUrl . 'v2/inquiry',
                'merchant_code' => $merchantCode,
                'order_id' => $merchantOrderId
            ]);
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Exputra-Test/1.0'
                ])
                ->post($baseUrl . 'v2/inquiry', $testData);
            
            $result = [
                'status' => 'success',
                'http_code' => $response->status(),
                'response_body' => $response->body(),
                'response_json' => $response->json(),
                'request_data' => $testData
            ];
            
            Log::info('Duitku API test response', $result);
            
            return response()->json($result, 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            $error = [
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'error_type' => get_class($e),
                'request_data' => $testData
            ];
            
            Log::error('Duitku API test failed', $error);
            
            return response()->json($error, 500, [], JSON_PRETTY_PRINT);
        }
    }
}
