# Setup Gmail SMTP untuk Laravel

## Langkah-langkah Setup Gmail SMTP

### 1. Aktifkan 2-Factor Authentication di Gmail
1. Buka [Google Account Settings](https://myaccount.google.com/)
2. Pilih **Security** di menu kiri
3. Aktifkan **2-Step Verification**

### 2. Generate App Password
1. Setelah 2FA aktif, kembali ke **Security**
2. Cari **App passwords** (mungkin perlu scroll ke bawah)
3. Klik **App passwords**
4. Pilih **Mail** sebagai app dan **Other** sebagai device
5. Masukkan nama aplikasi (misal: "Laravel App")
6. Copy password yang dihasilkan (16 karakter tanpa spasi)

### 3. Update File .env
Ganti nilai berikut di file `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="Your App Name"
```

**PENTING:** 
- Gunakan **App Password** yang dihasilkan, bukan password Gmail biasa
- Jangan gunakan spasi dalam app password
- Pastikan email yang sama untuk `MAIL_USERNAME` dan `MAIL_FROM_ADDRESS`

### 4. Alternative: Menggunakan Google Cloud Console (OAuth2) ✅ SUDAH DIKONFIGURASI

Anda sudah memiliki OAuth2 credentials! Berikut konfigurasi yang sudah disetup:

**Credentials yang Anda miliki:**

**Setup yang sudah dilakukan:**
1. ✅ Package Google API Client sudah ditambahkan ke composer.json
2. ✅ Konfigurasi OAuth2 sudah ditambahkan ke .env dan config/services.php
3. ✅ Service GmailService sudah dibuat
4. ✅ Controller dan routes OAuth2 sudah dibuat
5. ✅ Command artisan untuk OAuth2 sudah dibuat

**Cara menggunakan OAuth2:**

**Via Web Interface:**
1. Buka: `http://localhost:8000/gmail-test`
2. Klik "Authenticate dengan Google"
3. Login dan berikan izin
4. Test kirim email

**Via Command Line:**
```bash
# Generate authentication URL
php artisan gmail:auth url

# Check authentication status
php artisan gmail:auth status

# Send test email
php artisan gmail:auth test
```

### 5. Test Konfigurasi
Jalankan command berikut untuk test email:

```bash
php artisan tinker
```

Kemudian di tinker:
```php
Mail::raw('Test email dari Laravel', function ($message) {
    $message->to('recipient@example.com')
            ->subject('Test Email');
});
```

### 6. Troubleshooting

**Error "Less secure app access":**
- Pastikan menggunakan App Password, bukan password biasa
- Pastikan 2FA sudah aktif

**Error "Authentication failed":**
- Periksa username dan app password
- Pastikan tidak ada spasi dalam app password

**Error "Connection timeout":**
- Periksa firewall/antivirus
- Coba gunakan port 465 dengan SSL:
  ```env
  MAIL_PORT=465
  MAIL_ENCRYPTION=ssl
  ```

### 7. Keamanan
- Jangan commit file `.env` ke repository
- Simpan app password dengan aman
- Gunakan environment variables di production

## Contoh Penggunaan

### Kirim Email Sederhana
```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Halo, ini adalah test email!', function ($message) {
    $message->to('user@example.com')
            ->subject('Test Email dari Laravel');
});
```

### Menggunakan Mailable Class
```bash
php artisan make:mail TestMail
```

```php
// app/Mail/TestMail.php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Test Email')
                    ->view('emails.test');
    }
}
```

Kemudian kirim:
```php
Mail::to('user@example.com')->send(new TestMail());
```
