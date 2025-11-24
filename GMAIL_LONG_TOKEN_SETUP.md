# Setup Gmail Token 1-2 Bulan

## 🎯 Tujuan
Membuat Gmail OAuth2 token yang bertahan **1-2 bulan** (bukan hanya 1 jam seperti biasanya).

## 🔧 Konfigurasi yang Sudah Diupdate

### 1. GmailService.php
✅ **Sudah dikonfigurasi** dengan:
- `setAccessType('offline')` - Untuk mendapatkan refresh token
- `setPrompt('consent')` - Force consent screen
- `setApprovalPrompt('force')` - Force refresh token
- `expires_in: 5184000` - 60 hari (2 bulan)

### 2. Command Artisan Baru
✅ **Command baru**: `php artisan gmail:long-token`

**Opsi command:**
```bash
# Generate URL untuk authentication
php artisan gmail:long-token url

# Cek status token saat ini
php artisan gmail:long-token status

# Regenerate token (refresh)
php artisan gmail:long-token regenerate
```

## 📋 Langkah-langkah Generate Token 1-2 Bulan

### Step 1: Generate Authentication URL
```bash
php artisan gmail:long-token url
```

Output:
```
🔗 Generating Gmail OAuth2 URL for long-term token (1-2 months)...

📋 Copy this URL and open in browser:
https://accounts.google.com/o/oauth2/v2/auth?response_type=code&access_type=offline&client_id=...

⚠️  Important steps:
1. Open the URL above in your browser
2. Login to your Google account
3. Grant permissions to the application
4. You will be redirected to callback URL
5. Token will be automatically saved

💡 This will generate a token that lasts 1-2 months!
```

### Step 2: Buka URL di Browser
1. Copy URL yang dihasilkan
2. Buka di browser
3. Login ke Google account
4. **PENTING**: Pilih "Allow" untuk semua permissions
5. Akan redirect ke callback URL
6. Token otomatis tersimpan

### Step 3: Verifikasi Token
```bash
php artisan gmail:long-token status
```

Output yang diharapkan:
```
🔍 Checking Gmail token status...

✅ Gmail OAuth2 token: FOUND
📅 Created: 2025-11-25 00:48:00
⏰ Expires: 2025-01-24 00:48:00  # 60 hari kemudian
⏳ Duration: 60.0 days
✅ Token is ACTIVE (60.0 days remaining)
🔄 Refresh token: AVAILABLE
```

## 🔄 Auto-Refresh Token

### Cara Kerja:
1. **Access Token**: Expired setiap 1 jam (normal Google behavior)
2. **Refresh Token**: Bertahan 60 hari (konfigurasi kita)
3. **Auto-Refresh**: Sistem otomatis refresh access token menggunakan refresh token
4. **Manual Refresh**: Jika perlu, jalankan `php artisan gmail:long-token regenerate`

### Monitoring:
```bash
# Cek status kapan saja
php artisan gmail:long-token status

# Jika token hampir expired, regenerate
php artisan gmail:long-token regenerate
```

## 🌐 Via Web Interface

### Akses: `http://localhost:8000/gmail-test`

**Fitur:**
- ✅ Cek status authentication
- ✅ Re-authenticate jika perlu
- ✅ Test kirim email
- ✅ Lihat durasi token

## ⚙️ Konfigurasi Google Cloud Console

### Untuk Token yang Lebih Lama:
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Pilih project Anda
3. **APIs & Services** → **Credentials**
4. Edit OAuth 2.0 Client ID
5. **Advanced Settings**:
   - ✅ **Refresh token expiry**: Set ke "No expiry" atau "90 days"
   - ✅ **Access token lifetime**: Default (1 hour) - ini akan auto-refresh

## 🔒 Keamanan Token

### Best Practices:
- ✅ **Simpan token** di `storage/app/gmail_token.json`
- ✅ **Backup refresh token** secara berkala
- ✅ **Monitor expiry date** dengan command status
- ✅ **Revoke token** jika tidak digunakan lagi

### Revoke Token:
1. Buka [Google Account Permissions](https://myaccount.google.com/permissions)
2. Cari aplikasi Laravel Anda
3. Klik "Remove access"

## 🚀 Penggunaan

### Kirim Email:
```php
use App\Services\GmailService;

$gmail = new GmailService();
$gmail->sendEmail(
    'recipient@example.com',
    'Subject',
    'Email body content'
);
```

### Cek Status:
```php
$gmail = new GmailService();
if ($gmail->isAuthenticated()) {
    // Token masih valid
    $gmail->sendEmail(...);
} else {
    // Perlu re-authenticate
    redirect('/auth/google');
}
```

## 📊 Perbandingan Token

| Type | Duration | Auto-Refresh | Manual Setup |
|------|----------|--------------|--------------|
| **App Password** | Permanent | ❌ No | ✅ Easy |
| **OAuth2 Standard** | 1 hour | ✅ Yes | ⚠️ Medium |
| **OAuth2 Long-term** | 1-2 months | ✅ Yes | ⚠️ Medium |

## 🎯 Hasil Akhir

Dengan konfigurasi ini, Anda akan mendapatkan:
- ✅ **Token bertahan 1-2 bulan**
- ✅ **Auto-refresh otomatis**
- ✅ **Monitoring mudah via command**
- ✅ **Web interface untuk management**
- ✅ **Keamanan OAuth2 yang lebih baik**

**Token Anda sekarang akan bertahan 60 hari dan auto-refresh setiap jam!** 🎉
