# 📧 Email Troubleshooting Guide - Exputra Cloud

## 🚨 Masalah: Email Forgot Password Tidak Terkirim

### ✅ **Solusi Cepat (Quick Fix)**

#### 1. **Jalankan SQL Fix**
```sql
-- Copy paste ke phpMyAdmin
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_password_reset_tokens_email (email),
    INDEX idx_password_reset_tokens_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2. **Update .env File**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=client@exputra.cloud
MAIL_PASSWORD=your-gmail-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="client@exputra.cloud"
MAIL_FROM_NAME="Exputra Group"
```

#### 3. **Update config/mail.php**
```php
'default' => env('MAIL_MAILER', 'smtp'), // Bukan 'log'
```

### 🔧 **Langkah-langkah Troubleshooting**

#### **Step 1: Periksa Database**
```bash
# Cek apakah table password_reset_tokens ada
SELECT * FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'cloud' 
AND TABLE_NAME = 'password_reset_tokens';
```

#### **Step 2: Test Email Configuration**
```bash
# Jalankan test command (jika PHP CLI tersedia)
php artisan email:test client@exputra.cloud
```

#### **Step 3: Periksa Log Error**
```bash
# Lihat log Laravel
tail -f storage/logs/laravel.log
```

#### **Step 4: Test Manual**
```php
// Jalankan di tinker atau test file
Mail::raw('Test email', function($message) {
    $message->to('client@exputra.cloud')->subject('Test');
});
```

### 📋 **Checklist Konfigurasi**

- [ ] **Database Table**: `password_reset_tokens` exists
- [ ] **Mail Driver**: `MAIL_MAILER=smtp` (bukan `log`)
- [ ] **SMTP Settings**: Host, port, encryption benar
- [ ] **Email Credentials**: Username dan password valid
- [ ] **From Address**: Email pengirim valid
- [ ] **Firewall**: Port 587/465 tidak diblokir

### 🔍 **Diagnosis Masalah**

#### **Masalah 1: Table Missing**
```
Error: Table 'cloud.password_reset_tokens' doesn't exist
```
**Solusi**: Jalankan SQL create table di atas

#### **Masalah 2: Mail Driver Log**
```
Email masuk ke log file, bukan terkirim
```
**Solusi**: Set `MAIL_MAILER=smtp` di .env

#### **Masalah 3: SMTP Authentication**
```
Error: SMTP Authentication failed
```
**Solusi**: 
- Gunakan Gmail App Password (bukan password biasa)
- Enable 2FA di Gmail
- Generate App Password di Google Account

#### **Masalah 4: Connection Timeout**
```
Error: Connection timeout
```
**Solusi**:
- Cek firewall/antivirus
- Coba port 465 dengan SSL
- Gunakan SMTP provider lain

### 🛠 **Tools untuk Testing**

#### **1. Test Email Command**
```bash
php artisan email:test your-email@domain.com
```

#### **2. Manual Test File**
```php
// Buat file test_email.php
<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

Mail::raw('Test from manual script', function($message) {
    $message->to('client@exputra.cloud')->subject('Manual Test');
});
echo "Email sent!";
```

#### **3. Database Check**
```sql
-- Cek semua tables
SHOW TABLES LIKE '%password%';

-- Cek struktur table
DESCRIBE password_reset_tokens;

-- Test insert
INSERT INTO password_reset_tokens VALUES 
('test@example.com', 'test123', NOW());
```

### 📧 **Alternatif SMTP Providers**

Jika Gmail tidak work, coba:

#### **1. Mailtrap (Development)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
```

#### **2. SendGrid**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
```

#### **3. Mailgun**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-key
```

### 🚀 **Quick Test Commands**

```bash
# 1. Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# 2. Test email config
php artisan tinker --execute="config('mail.default');"

# 3. Clear config cache
php artisan config:clear

# 4. Test forgot password
curl -X POST http://localhost:8000/forgot-password \
  -H "Content-Type: application/json" \
  -d '{"email":"client@exputra.cloud"}'
```

### 📞 **Support**

Jika masih bermasalah:
1. Cek file `storage/logs/laravel.log` untuk error detail
2. Jalankan `php artisan email:test` untuk diagnosis
3. Pastikan semua step di checklist sudah dilakukan
4. Contact admin dengan error message lengkap

---
**Last Updated**: 2025-01-25  
**Status**: ✅ Ready for production
