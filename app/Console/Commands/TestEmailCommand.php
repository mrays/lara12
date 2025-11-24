<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:test {email?}';

    /**
     * The console command description.
     */
    protected $description = 'Test email configuration and sending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Email Configuration...');
        $this->newLine();

        // 1. Check database connection
        $this->info('1. Testing database connection...');
        try {
            $userCount = DB::table('users')->count();
            $this->info("   ✅ Database connected. Users: {$userCount}");
        } catch (\Exception $e) {
            $this->error("   ❌ Database error: " . $e->getMessage());
            return 1;
        }

        // 2. Check password_reset_tokens table
        $this->info('2. Checking password_reset_tokens table...');
        if (!Schema::hasTable('password_reset_tokens')) {
            $this->warn('   ⚠️  password_reset_tokens table missing');
            $this->info('   Creating table...');
            
            Schema::create('password_reset_tokens', function ($table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
            
            $this->info('   ✅ password_reset_tokens table created');
        } else {
            $this->info('   ✅ password_reset_tokens table exists');
        }

        // 3. Show email configuration
        $this->info('3. Email configuration:');
        $this->table(['Setting', 'Value'], [
            ['MAIL_MAILER', config('mail.default')],
            ['MAIL_HOST', config('mail.mailers.smtp.host')],
            ['MAIL_PORT', config('mail.mailers.smtp.port')],
            ['MAIL_USERNAME', config('mail.mailers.smtp.username')],
            ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')],
            ['MAIL_FROM_ADDRESS', config('mail.from.address')],
            ['MAIL_FROM_NAME', config('mail.from.name')],
        ]);

        // 4. Test email sending
        $testEmail = $this->argument('email') ?: 'client@exputra.cloud';
        $this->info("4. Testing email sending to: {$testEmail}");
        
        try {
            Mail::raw('This is a test email from Exputra Cloud system. If you receive this, email configuration is working correctly!', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('✅ Test Email - Exputra Cloud System');
            });
            
            $this->info('   ✅ Email sent successfully!');
            $this->info('   📧 Check your email inbox (including spam folder)');
            
        } catch (\Exception $e) {
            $this->error('   ❌ Email sending failed: ' . $e->getMessage());
            
            // Provide troubleshooting tips
            $this->newLine();
            $this->warn('🔧 Troubleshooting Tips:');
            $this->line('   1. Check .env file for correct email credentials');
            $this->line('   2. Ensure MAIL_MAILER=smtp (not log)');
            $this->line('   3. Verify Gmail App Password if using Gmail');
            $this->line('   4. Check firewall/antivirus blocking SMTP');
            $this->line('   5. Try different SMTP provider');
            
            return 1;
        }

        $this->newLine();
        $this->info('🎉 Email test completed successfully!');
        return 0;
    }
}
