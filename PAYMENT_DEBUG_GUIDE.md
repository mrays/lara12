# Payment Debug Guide - Duitku Integration

## Masalah yang Diidentifikasi dari Kode PHP Native

Berdasarkan kode PHP native yang bekerja, berikut perbedaan utama yang menyebabkan tombol pay tidak berfungsi:

### 🔍 **Perbedaan Utama:**

#### 1. **Format Request Data**
**PHP Native (Bekerja):**
```php
$paymentData = [
    'merchantCode' => $merchantCode,
    'paymentAmount' => $amount,
    'paymentMethod' => $paymentMethod,
    'merchantOrderId' => $merchantOrderId,
    'productDetails' => $description,
    'additionalParam' => '',
    'merchantUserInfo' => $email,
    'customerVaName' => $customerName,
    'email' => $email,
    'phoneNumber' => $phone,
    'callbackUrl' => $callbackUrl,
    'returnUrl' => $returnUrl,
    'expiryPeriod' => 1440
];
```

**Laravel (Diperbaiki):**
- ✅ Menggunakan format yang sama
- ✅ Menghapus `itemDetails` dan `customerDetail` yang tidak perlu
- ✅ Menggunakan `additionalParam` kosong

#### 2. **Customer Data Handling**
**PHP Native:**
- Form customer data wajib diisi
- Data customer dikirim langsung ke Duitku

**Laravel (Diperbaiki):**
- ✅ Tambah form customer data optional
- ✅ Support override customer data
- ✅ Fallback ke client data jika kosong

#### 3. **Signature Generation**
**Format yang Benar:**
```php
md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey)
```

#### 4. **HTTP Headers**
**Laravel (Diperbaiki):**
```php
Http::timeout(30)
    ->withHeaders([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ])
    ->post($url, $data);
```

## 🛠️ **Cara Debug:**

### 1. **Test Konfigurasi**
```bash
# Akses URL ini untuk cek config
GET /test/payment/config
```

**Response yang diharapkan:**
```json
{
    "status": "success",
    "config": {
        "merchant_code": "DS16902",
        "api_key": "SET",
        "env": "sandbox",
        "return_url": "https://exputra.cloud/payment/return",
        "callback_url": "https://exputra.cloud/payment/callback"
    }
}
```

### 2. **Test Payment Creation**
```bash
# Test buat payment
GET /test/payment/create?method=SP
```

**Response sukses:**
```json
{
    "status": "success",
    "message": "Payment created successfully",
    "data": {
        "success": true,
        "payment_url": "https://sandbox.duitku.com/...",
        "reference": "...",
        "merchant_order_id": "INV-1-1732435200"
    }
}
```

### 3. **Cek Laravel Logs**
```bash
tail -f storage/logs/laravel.log
```

**Log yang dicari:**
```
[2024-11-24 14:30:00] local.INFO: Duitku Payment Request {"merchantCode":"DS16902","merchantOrderId":"INV-1-1732435200","amount":1680000,"paymentMethod":"SP","signature":"..."}
```

### 4. **Debug Steps:**

#### Step 1: Cek Environment
```bash
# Pastikan .env sudah benar
php artisan config:clear
php artisan cache:clear
```

#### Step 2: Test Manual
```php
// Di tinker
php artisan tinker

$invoice = App\Models\Invoice::first();
$service = app(App\Services\DuitkuService::class);
$result = $service->createPayment($invoice, 'SP');
dd($result);
```

#### Step 3: Cek Network
```bash
# Test koneksi ke Duitku
curl -X POST https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry \
  -H "Content-Type: application/json" \
  -d '{"merchantCode":"DS16902"}'
```

## 🔧 **Perbaikan yang Sudah Dilakukan:**

### 1. **DuitkuService.php**
- ✅ Format request sesuai dokumentasi Duitku
- ✅ Signature generation yang benar
- ✅ Error handling yang proper
- ✅ Logging untuk debugging

### 2. **PaymentController.php**
- ✅ Support customer data override
- ✅ Validation yang proper
- ✅ Error handling

### 3. **Payment View**
- ✅ Form customer data seperti PHP native
- ✅ Payment method selection
- ✅ Proper form validation

### 4. **Configuration**
- ✅ Config di services.php
- ✅ Environment variables

## 🚨 **Common Issues & Solutions:**

### Issue 1: "Invalid Signature"
**Solusi:**
```php
// Pastikan signature format benar
$signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);
```

### Issue 2: "Merchant Not Found"
**Solusi:**
```bash
# Cek merchant code di .env
DUITKU_MERCHANT_CODE=DS16902
```

### Issue 3: "Payment URL Empty"
**Solusi:**
```php
// Cek response dari Duitku
Log::info('Duitku Response', $response->json());
```

### Issue 4: "Callback Not Working"
**Solusi:**
```bash
# Pastikan callback URL accessible
curl -X POST https://exputra.cloud/payment/callback
```

## 📋 **Checklist Debugging:**

- [ ] ✅ Config .env sudah benar
- [ ] ✅ Merchant code valid
- [ ] ✅ API key valid  
- [ ] ✅ Callback URL accessible
- [ ] ✅ Return URL accessible
- [ ] ✅ Invoice data valid
- [ ] ✅ Customer data format benar
- [ ] ✅ Signature generation benar
- [ ] ✅ HTTP headers benar
- [ ] ✅ Network connectivity OK

## 🎯 **Testing Flow:**

1. **Akses payment page:** `/payment/invoice/{id}`
2. **Isi customer data** (optional)
3. **Pilih payment method**
4. **Klik Pay button**
5. **Cek logs** untuk request/response
6. **Verify redirect** ke Duitku

## 📞 **Support:**

Jika masih error:
1. Cek `/test/payment/config` - pastikan config OK
2. Cek `/test/payment/create` - test payment creation
3. Cek `storage/logs/laravel.log` - lihat error detail
4. Test dengan Postman/curl untuk isolate masalah

**Payment gateway seharusnya sudah berfungsi dengan perbaikan ini!** 🎉
