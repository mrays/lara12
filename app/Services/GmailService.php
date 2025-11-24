<?php

namespace App\Services;

use Google\Client;
use Google\Service\Gmail;
use Google\Service\Gmail\Message;
use Illuminate\Support\Facades\Storage;

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
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
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

        // Simpan token ke storage
        Storage::put('gmail_token.json', json_encode($token));
        
        return $token;
    }

    public function setAccessToken($token = null)
    {
        if (!$token) {
            // Load token dari storage
            if (Storage::exists('gmail_token.json')) {
                $token = json_decode(Storage::get('gmail_token.json'), true);
            } else {
                throw new \Exception('No access token found. Please authenticate first.');
            }
        }

        $this->client->setAccessToken($token);

        // Refresh token jika expired
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                Storage::put('gmail_token.json', json_encode($newToken));
                $this->client->setAccessToken($newToken);
            } else {
                throw new \Exception('Access token expired and no refresh token available. Please re-authenticate.');
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
            return $result;
        } catch (\Exception $e) {
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
}

// Helper function untuk base64url encoding
if (!function_exists('base64url_encode')) {
    function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
