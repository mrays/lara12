# Debug: "Failed to connect to payment gateway"

## 🔍 **Langkah Debug Sistematis:**

### Step 1: Cek Konfigurasi Dasar
```bash
# Akses URL ini di browser:
GET /debug/connection
```

**Yang harus dicek:**
- ✅ `DUITKU_MERCHANT_CODE` = "DS16902" (SET)
- ✅ `DUITKU_API_KEY` = SET (32 chars)
- ✅ `DUITKU_ENV` = "sandbox"
- ✅ `DUITKU_RETURN_URL` = SET
- ✅ `DUITKU_CALLBACK_URL` = SET

### Step 2: Test Koneksi Dasar
```bash
# Cek connectivity di response /debug/connection
"connectivity": {
    "status": "success",
    "http_code": 200
}
```

**Jika gagal:**
- ❌ Firewall blocking
- ❌ DNS resolution issue
- ❌ SSL/TLS problem
- ❌ Network connectivity

### Step 3: Test API Call Langsung
```bash
# Akses URL ini:
GET /debug/api-call
```

**Response yang diharapkan:**
```json
{
    "status": "success",
    "http_code": 200,
    "response_json": {
        "statusCode": "00" atau error message
    }
}
```

## 🛠️ **Kemungkinan Masalah & Solusi:**

### 1. **Environment Variables Tidak Terbaca**
**Gejala:** Config menunjukkan "NOT SET"

**Solusi:**
```bash
# Clear config cache
php artisan config:clear
php artisan cache:clear

# Restart server
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. **SSL/TLS Certificate Error**
**Gejala:** "SSL certificate problem" atau "cURL error 60"

**Solusi:**
```php
// Temporary fix - add to DuitkuService
Http::withOptions([
    'verify' => false  // ONLY for testing
])->post($url, $data);
```

### 3. **Firewall/Network Blocking**
**Gejala:** "Connection timeout" atau "Connection refused"

**Solusi:**
```bash
# Test manual dengan curl
curl -v https://sandbox.duitku.com/webapi/api/merchant/

# Cek firewall rules
# Pastikan port 443 (HTTPS) terbuka
```

### 4. **Wrong API Endpoint**
**Gejala:** HTTP 404 atau "Not Found"

**Solusi:**
```php
// Pastikan URL benar
Sandbox: https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry
Production: https://passport.duitku.com/webapi/api/merchant/v2/inquiry
```

### 5. **Invalid Credentials**
**Gejala:** HTTP 401 atau "Unauthorized"

**Solusi:**
```bash
# Cek credentials di .env
DUITKU_MERCHANT_CODE=DS16902
DUITKU_API_KEY=792f56c9e2277927191c4c4924f06b40

# Pastikan tidak ada spasi atau karakter aneh
```

### 6. **Request Format Error**
**Gejala:** HTTP 400 atau "Bad Request"

**Solusi:**
```php
// Pastikan Content-Type header benar
'Content-Type' => 'application/json'

// Pastikan data di-encode sebagai JSON
Http::post($url, $data); // Laravel otomatis encode
```

## 🔧 **Debug Commands:**

### Manual Test dengan cURL:
```bash
curl -X POST https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "merchantCode": "DS16902",
    "paymentAmount": 100000,
    "paymentMethod": "SP",
    "merchantOrderId": "TEST-123",
    "productDetails": "Test",
    "customerName": "Test",
    "customerEmail": "test@example.com",
    "customerPhone": "081234567890",
    "returnUrl": "https://example.com/return",
    "callbackUrl": "https://example.com/callback",
    "signature": "SIGNATURE_HERE",
    "expiryPeriod": 120,
    "additionalParam": ""
  }'
```

### Laravel Tinker Test:
```php
php artisan tinker

// Test config
config('services.duitku.merchant_code')
config('services.duitku.api_key')

// Test HTTP client
use Illuminate\Support\Facades\Http;
$response = Http::get('https://sandbox.duitku.com/webapi/api/merchant/');
$response->status()
$response->body()
```

### Check Laravel Logs:
```bash
# Monitor logs real-time
tail -f storage/logs/laravel.log

# Look for:
[ERROR] Duitku Connection Error
[ERROR] Duitku HTTP Error
[INFO] Duitku Request Details
```

## 📋 **Checklist Debug:**

- [ ] ✅ Environment variables set correctly
- [ ] ✅ Config cache cleared
- [ ] ✅ Network connectivity OK
- [ ] ✅ SSL certificates valid
- [ ] ✅ Firewall allows HTTPS
- [ ] ✅ API endpoint URL correct
- [ ] ✅ Request headers correct
- [ ] ✅ Credentials valid
- [ ] ✅ Signature generation correct

## 🎯 **Quick Fix Commands:**

```bash
# 1. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Check environment
php artisan env

# 3. Test connectivity
curl -I https://sandbox.duitku.com/

# 4. Check logs
tail -20 storage/logs/laravel.log
```

## 📞 **Jika Masih Error:**

1. **Akses `/debug/connection`** - cek semua config
2. **Akses `/debug/api-call`** - test API langsung
3. **Cek `storage/logs/laravel.log`** - lihat error detail
4. **Test manual dengan cURL** - isolate masalah
5. **Cek network/firewall** - pastikan koneksi OK

**Paling sering masalahnya:**
- Environment variables tidak terbaca
- SSL certificate issue
- Network/firewall blocking
- Wrong API endpoint

**Silakan akses `/debug/connection` dan `/debug/api-call` untuk diagnosis lengkap!** 🔍
