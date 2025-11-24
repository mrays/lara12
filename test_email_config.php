<?php
/**
 * Test Email Configuration
 * Run this file to test email settings
 */

echo "=== EMAIL CONFIGURATION TEST ===\n\n";

// Load Laravel environment
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test database connection
try {
    echo "1. Testing database connection...\n";
    $count = DB::table('users')->count();
    echo "   ✅ Database connected. Users count: $count\n\n";
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n\n";
}

// Check password_reset_tokens table
try {
    echo "2. Checking password_reset_tokens table...\n";
    $exists = Schema::hasTable('password_reset_tokens');
    if ($exists) {
        echo "   ✅ password_reset_tokens table exists\n\n";
    } else {
        echo "   ❌ password_reset_tokens table missing\n";
        echo "   Creating table...\n";
        
        Schema::create('password_reset_tokens', function ($table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        
        echo "   ✅ password_reset_tokens table created\n\n";
    }
} catch (Exception $e) {
    echo "   ❌ Table check error: " . $e->getMessage() . "\n\n";
}

// Test email configuration
echo "3. Email configuration:\n";
echo "   MAIL_MAILER: " . config('mail.default') . "\n";
echo "   MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "   MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "   MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "   MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// Test sending email
echo "4. Testing email sending...\n";
try {
    $testEmail = 'client@exputra.cloud';
    
    Mail::raw('This is a test email from Exputra Cloud system.', function ($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('Test Email - Exputra Cloud');
    });
    
    echo "   ✅ Email sent successfully to $testEmail\n";
    echo "   Check your email inbox (including spam folder)\n\n";
    
} catch (Exception $e) {
    echo "   ❌ Email sending failed: " . $e->getMessage() . "\n\n";
}

echo "=== TEST COMPLETED ===\n";
