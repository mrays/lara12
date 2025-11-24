<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('Halo! Ini adalah test email dari aplikasi Laravel Anda. Jika Anda menerima email ini, berarti konfigurasi Gmail SMTP sudah berhasil!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Gmail SMTP Configuration');
            });
            
            $this->info("✅ Test email berhasil dikirim ke: {$email}");
            $this->info("Silakan cek inbox atau spam folder.");
            
        } catch (\Exception $e) {
            $this->error("❌ Gagal mengirim email: " . $e->getMessage());
            $this->info("\n🔧 Tips troubleshooting:");
            $this->info("1. Pastikan App Password Gmail sudah benar");
            $this->info("2. Pastikan 2FA aktif di akun Gmail");
            $this->info("3. Periksa konfigurasi MAIL_* di file .env");
            $this->info("4. Coba restart server jika baru mengubah .env");
        }
    }
}
