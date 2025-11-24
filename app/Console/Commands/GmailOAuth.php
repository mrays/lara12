<?php

namespace App\Console\Commands;

use App\Services\GmailService;
use Illuminate\Console\Command;

class GmailOAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gmail:auth {action : Action to perform (url|test|status)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Gmail OAuth2 authentication';

    private $gmailService;

    public function __construct(GmailService $gmailService)
    {
        parent::__construct();
        $this->gmailService = $gmailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'url':
                $this->generateAuthUrl();
                break;
            case 'test':
                $this->testEmailSending();
                break;
            case 'status':
                $this->checkAuthStatus();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: url, test, status");
                return 1;
        }

        return 0;
    }

    private function generateAuthUrl()
    {
        try {
            $authUrl = $this->gmailService->getAuthUrl();
            
            $this->info("🔗 Gmail OAuth2 Authentication URL:");
            $this->line($authUrl);
            $this->info("\n📋 Langkah-langkah:");
            $this->info("1. Copy URL di atas dan buka di browser");
            $this->info("2. Login dengan akun Gmail yang ingin digunakan");
            $this->info("3. Berikan izin akses ke aplikasi");
            $this->info("4. Setelah berhasil, Anda akan diarahkan ke callback URL");
            $this->info("5. Jalankan 'php artisan gmail:auth status' untuk cek status");
            
        } catch (\Exception $e) {
            $this->error("❌ Error generating auth URL: " . $e->getMessage());
        }
    }

    private function testEmailSending()
    {
        try {
            if (!$this->gmailService->isAuthenticated()) {
                $this->error("❌ Gmail belum ter-authenticate!");
                $this->info("Jalankan: php artisan gmail:auth url");
                return;
            }

            $email = $this->ask('Masukkan email tujuan untuk test');
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error("❌ Format email tidak valid!");
                return;
            }

            $this->info("📧 Mengirim test email...");
            
            $result = $this->gmailService->sendEmail(
                $email,
                'Test Email - Gmail API OAuth2 dari Laravel',
                $this->getTestEmailBody()
            );

            $this->info("✅ Test email berhasil dikirim!");
            $this->info("Message ID: " . $result->getId());
            $this->info("Silakan cek inbox atau spam folder di: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ Gagal mengirim email: " . $e->getMessage());
            $this->info("\n🔧 Tips troubleshooting:");
            $this->info("1. Pastikan sudah authenticate dengan 'php artisan gmail:auth url'");
            $this->info("2. Periksa konfigurasi Google OAuth2 di .env");
            $this->info("3. Pastikan Gmail API sudah aktif di Google Cloud Console");
        }
    }

    private function checkAuthStatus()
    {
        try {
            if ($this->gmailService->isAuthenticated()) {
                $this->info("✅ Gmail OAuth2 authentication: AKTIF");
                $this->info("📧 Siap mengirim email via Gmail API");
                
                // Cek token info
                if (\Storage::exists('gmail_token.json')) {
                    $token = json_decode(\Storage::get('gmail_token.json'), true);
                    $this->info("🔑 Token tersimpan: " . (\Storage::exists('gmail_token.json') ? 'Ya' : 'Tidak'));
                    if (isset($token['expires_in'])) {
                        $this->info("⏰ Token expires: " . date('Y-m-d H:i:s', time() + $token['expires_in']));
                    }
                }
            } else {
                $this->error("❌ Gmail OAuth2 authentication: TIDAK AKTIF");
                $this->info("Jalankan: php artisan gmail:auth url");
            }
        } catch (\Exception $e) {
            $this->error("❌ Error checking auth status: " . $e->getMessage());
        }
    }

    private function getTestEmailBody()
    {
        return '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                .header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
                .info-box { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🎉 Gmail API OAuth2 Test</h1>
                <p>Laravel Application</p>
            </div>
            <div class="content">
                <div class="success">
                    ✅ Email berhasil dikirim menggunakan Gmail API dengan OAuth2!
                </div>
                <p><strong>Selamat!</strong> Konfigurasi Gmail API dengan OAuth2 sudah berhasil dan berfungsi dengan baik.</p>
                
                <div class="info-box">
                    <h3>🔐 Keuntungan OAuth2:</h3>
                    <ul>
                        <li><strong>Keamanan tinggi:</strong> Tidak perlu menyimpan password Gmail</li>
                        <li><strong>Token refresh:</strong> Token dapat diperbaharui otomatis</li>
                        <li><strong>Kontrol granular:</strong> Hanya akses yang diperlukan</li>
                        <li><strong>Dapat dicabut:</strong> Akses dapat dicabut dari Google Account</li>
                        <li><strong>Audit trail:</strong> Aktivitas dapat dimonitor di Google Console</li>
                    </ul>
                </div>
                
                <h3>📊 Informasi Teknis:</h3>
                <ul>
                    <li><strong>Method:</strong> Gmail API v1</li>
                    <li><strong>Authentication:</strong> OAuth2</li>
                    <li><strong>Scope:</strong> gmail.send</li>
                    <li><strong>Client ID:</strong> ' . substr(config('services.google.client_id'), 0, 20) . '...</li>
                </ul>
                
                <p>Aplikasi Laravel Anda sekarang siap mengirim email melalui Gmail API dengan keamanan OAuth2!</p>
                
                <hr>
                <p><small>Dikirim pada: ' . now()->format('d M Y, H:i:s') . ' dari Laravel Command</small></p>
            </div>
        </body>
        </html>';
    }
}
