<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GmailService;
use Illuminate\Support\Facades\Storage;

class GmailLongTokenCommand extends Command
{
    protected $signature = 'gmail:long-token {action=status : Action to perform (url|status|regenerate)}';
    protected $description = 'Generate Gmail OAuth2 token with 1-2 months duration';

    public function handle()
    {
        $action = $this->argument('action');
        $gmailService = new GmailService();

        switch ($action) {
            case 'url':
                $this->generateAuthUrl($gmailService);
                break;
            case 'status':
                $this->checkTokenStatus();
                break;
            case 'regenerate':
                $this->regenerateToken($gmailService);
                break;
            default:
                $this->error('Invalid action. Use: url, status, or regenerate');
        }
    }

    private function generateAuthUrl($gmailService)
    {
        $this->info('🔗 Generating Gmail OAuth2 URL for long-term token (1-2 months)...');
        $this->line('');
        
        $authUrl = $gmailService->getAuthUrl();
        
        $this->info('📋 Copy this URL and open in browser:');
        $this->line($authUrl);
        $this->line('');
        
        $this->warn('⚠️  Important steps:');
        $this->line('1. Open the URL above in your browser');
        $this->line('2. Login to your Google account');
        $this->line('3. Grant permissions to the application');
        $this->line('4. You will be redirected to callback URL');
        $this->line('5. Token will be automatically saved');
        $this->line('');
        
        $this->info('💡 This will generate a token that lasts 1-2 months!');
    }

    private function checkTokenStatus()
    {
        $this->info('🔍 Checking Gmail token status...');
        $this->line('');

        if (!Storage::exists('gmail_token.json')) {
            $this->error('❌ No token found. Run: php artisan gmail:long-token url');
            return;
        }

        $token = json_decode(Storage::get('gmail_token.json'), true);
        
        if (isset($token['created']) && isset($token['expires_in'])) {
            $createdAt = $token['created'];
            $expiresIn = $token['expires_in'];
            $expiresAt = $createdAt + $expiresIn;
            
            $this->info('✅ Gmail OAuth2 token: FOUND');
            $this->info('📅 Created: ' . date('Y-m-d H:i:s', $createdAt));
            $this->info('⏰ Expires: ' . date('Y-m-d H:i:s', $expiresAt));
            $this->info('⏳ Duration: ' . round($expiresIn / 86400, 1) . ' days');
            
            if (time() > $expiresAt) {
                $this->warn('⚠️  Token has EXPIRED!');
                $this->line('Run: php artisan gmail:long-token regenerate');
            } else {
                $remainingDays = round(($expiresAt - time()) / 86400, 1);
                $this->info('✅ Token is ACTIVE (' . $remainingDays . ' days remaining)');
            }
        } else {
            $this->warn('⚠️  Token format is old or incomplete');
            $this->line('Run: php artisan gmail:long-token regenerate');
        }

        // Check refresh token
        if (isset($token['refresh_token'])) {
            $this->info('🔄 Refresh token: AVAILABLE');
        } else {
            $this->warn('⚠️  No refresh token found');
        }
    }

    private function regenerateToken($gmailService)
    {
        $this->warn('🔄 Regenerating Gmail token...');
        $this->line('');

        if (Storage::exists('gmail_token.json')) {
            $token = json_decode(Storage::get('gmail_token.json'), true);
            
            if (isset($token['refresh_token'])) {
                $this->info('🔄 Using refresh token to generate new access token...');
                
                try {
                    // This will be handled by GmailService automatically
                    $gmailService->setAccessToken();
                    $this->info('✅ Token refreshed successfully!');
                    $this->call('gmail:long-token', ['action' => 'status']);
                } catch (\Exception $e) {
                    $this->error('❌ Failed to refresh token: ' . $e->getMessage());
                    $this->line('');
                    $this->warn('🔗 Generate new token:');
                    $this->call('gmail:long-token', ['action' => 'url']);
                }
            } else {
                $this->warn('⚠️  No refresh token available. Need to re-authenticate.');
                $this->call('gmail:long-token', ['action' => 'url']);
            }
        } else {
            $this->error('❌ No token found.');
            $this->call('gmail:long-token', ['action' => 'url']);
        }
    }
}
