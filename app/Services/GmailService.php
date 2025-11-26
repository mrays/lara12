<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GmailService
{
    private $client;
    private $gmail;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope(Gmail::GMAIL_SEND);
        
        // IMPORTANT: These settings ensure we get a refresh token
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent'); // Force consent to get refresh token
        $this->client->setIncludeGrantedScopes(true);
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code)
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception('Error fetching access token: ' . $token['error']);
        }

        // Log token info for debugging
        Log::info('Gmail OAuth Token received', [
            'has_refresh_token' => isset($token['refresh_token']),
            'expires_in' => $token['expires_in'] ?? 'N/A',
        ]);

        // Save token to storage
        Storage::put('gmail_token.json', json_encode($token));
        
        return $token;
    }

    public function setAccessToken($token = null)
    {
        if (!$token) {
            // Load token from storage
            if (Storage::exists('gmail_token.json')) {
                $token = json_decode(Storage::get('gmail_token.json'), true);
            } else {
                throw new \Exception('No access token found. Please authenticate first.');
            }
        }

        $this->client->setAccessToken($token);

        // Auto-refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            Log::info('Gmail token expired, attempting refresh...');
            
            $refreshToken = $this->client->getRefreshToken();
            
            if ($refreshToken) {
                try {
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    
                    // Preserve the refresh token (Google doesn't always return it)
                    if (!isset($newToken['refresh_token']) && $refreshToken) {
                        $newToken['refresh_token'] = $refreshToken;
                    }
                    
                    Storage::put('gmail_token.json', json_encode($newToken));
                    $this->client->setAccessToken($newToken);
                    
                    Log::info('Gmail token refreshed successfully');
                } catch (\Exception $e) {
                    Log::error('Failed to refresh Gmail token: ' . $e->getMessage());
                    throw new \Exception('Access token expired and refresh failed. Please re-authenticate at /gmail-test');
                }
            } else {
                throw new \Exception('Access token expired and no refresh token available. Please re-authenticate at /gmail-test');
            }
        }

        $this->gmail = new Gmail($this->client);
    }

    public function sendEmail($to, $subject, $body, $from = null)
    {
        if (!$this->gmail) {
            $this->setAccessToken();
        }

        $from = $from ?: config('mail.from.address');
        
        $rawMessage = $this->createRawMessage($to, $from, $subject, $body);
        $message = new Message();
        $message->setRaw($rawMessage);

        try {
            $result = $this->gmail->users_messages->send('me', $message);
            Log::info('Email sent via Gmail API', ['to' => $to, 'subject' => $subject]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to send email via Gmail API: ' . $e->getMessage());
            throw new \Exception('Failed to send email: ' . $e->getMessage());
        }
    }

    private function createRawMessage($to, $from, $subject, $body)
    {
        $message = "From: {$from}\r\n";
        $message .= "To: {$to}\r\n";
        $message .= "Subject: {$subject}\r\n";
        $message .= "Content-Type: text/html; charset=utf-8\r\n";
        $message .= "\r\n";
        $message .= $body;

        return base64url_encode($message);
    }

    public function isAuthenticated()
    {
        try {
            $this->setAccessToken();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get token info for display
     */
    public function getTokenInfo()
    {
        if (!Storage::exists('gmail_token.json')) {
            return null;
        }

        $token = json_decode(Storage::get('gmail_token.json'), true);
        
        $expiresAt = isset($token['created']) && isset($token['expires_in']) 
            ? $token['created'] + $token['expires_in'] 
            : null;
        
        $hasRefreshToken = isset($token['refresh_token']) && !empty($token['refresh_token']);
        
        return [
            'has_refresh_token' => $hasRefreshToken,
            'expires_at' => $expiresAt ? date('Y-m-d H:i:s', $expiresAt) : 'Unknown',
            'is_expired' => $expiresAt ? (time() > $expiresAt) : true,
            'can_auto_refresh' => $hasRefreshToken,
        ];
    }
}

// Helper function for base64url encoding
if (!function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
