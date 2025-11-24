# Payment Gateway Setup Guide

## Duitku Integration

Sistem payment gateway Duitku telah berhasil diintegrasikan dengan fitur-fitur berikut:

### ✅ Fitur yang Sudah Dibuat:

1. **DuitkuService** - Service class untuk komunikasi dengan API Duitku
2. **PaymentController** - Controller untuk handle payment flow
3. **Payment Routes** - Routes untuk payment processing
4. **Payment Views** - UI untuk memilih metode pembayaran
5. **Invoice Integration** - Method payment di Invoice model
6. **Callback Handling** - Otomatis update status invoice
7. **Status Checking** - Real-time payment status check

### 🔧 Konfigurasi .env

Tambahkan konfigurasi berikut ke file `.env`:

```env
# Duitku Payment Gateway Configuration
DUITKU_MERCHANT_CODE=DS16902
DUITKU_API_KEY=792f56c9e2277927191c4c4924f06b40
DUITKU_ENV=sandbox
DUITKU_RETURN_URL=https://exputra.cloud/payment/return
DUITKU_CALLBACK_URL=https://exputra.cloud/payment/callback
```

### 📋 Metode Pembayaran yang Tersedia:

- **SP** - Shopee Pay
- **NQ** - QRIS
- **OV** - OVO
- **DA** - DANA
- **LK** - LinkAja
- **M2** - Mandiri VA
- **I1** - BCA VA
- **B1** - CIMB Niaga VA
- **BT** - Permata Bank VA
- **A1** - ATM Bersama
- **AG** - Bank Transfer

### 🚀 Cara Menggunakan:

#### 1. Untuk Client:
```php
// Akses payment dari invoice detail
Route: /client/invoices/{invoice}
Button: "Pay Now" → redirect ke /payment/invoice/{invoice}

// Pilih metode pembayaran
Route: /payment/invoice/{invoice}
Form: Pilih metode → submit ke /payment/invoice/{invoice}/process

// Redirect ke Duitku
Otomatis redirect ke payment gateway Duitku
```

#### 2. Untuk Admin:
```php
// Sama seperti client, bisa akses payment
// Plus bisa mark as paid manual
Route: /admin/invoices/{invoice}/mark-paid
```

### 🔄 Payment Flow:

1. **User klik "Pay Now"** → Redirect ke payment selection page
2. **Pilih metode pembayaran** → Submit form ke PaymentController
3. **Create payment di Duitku** → Generate payment URL
4. **Redirect ke Duitku** → User complete payment
5. **Callback dari Duitku** → Auto update invoice status
6. **Return ke website** → Show payment result

### 📡 API Endpoints:

```php
// Payment Routes (Auth Required)
GET  /payment/invoice/{invoice}         - Show payment methods
POST /payment/invoice/{invoice}/process - Process payment
GET  /payment/invoice/{invoice}/status  - Check payment status
POST /payment/invoice/{invoice}/cancel  - Cancel payment

// Public Routes (No Auth)
POST /payment/callback                  - Duitku callback
GET  /payment/return                   - Duitku return URL
```

### 🛠️ Testing:

#### Sandbox Testing:
1. Gunakan `DUITKU_ENV=sandbox`
2. Test dengan invoice yang ada
3. Pilih metode pembayaran
4. Complete payment di sandbox Duitku
5. Verify callback dan status update

#### Production Setup:
1. Ganti `DUITKU_ENV=production`
2. Update URL callback dan return ke domain production
3. Test dengan small amount first

### 🔍 Debugging:

#### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

#### Common Issues:
1. **Invalid Signature** - Check API key dan merchant code
2. **Callback Failed** - Check callback URL accessible
3. **Payment Not Updated** - Check callback processing

#### Debug Mode:
```php
// Tambah di .env untuk debug
LOG_LEVEL=debug

// Check di PaymentController dan DuitkuService
Log::info('Payment Debug', $data);
```

### 📊 Database Changes:

Invoice table sudah memiliki kolom:
- `duitku_merchant_code` - Order ID untuk Duitku
- `duitku_reference` - Reference dari Duitku
- `duitku_payment_url` - URL pembayaran
- `payment_method` - Metode pembayaran
- `payment_reference` - Reference pembayaran
- `paid_date` - Tanggal pembayaran

### 🎯 Features:

#### Invoice Model Methods:
```php
$invoice->canBePaid()           // Cek bisa dibayar
$invoice->isPaid()              // Cek sudah dibayar
$invoice->isOverdue()           // Cek overdue
$invoice->hasPendingPayment()   // Cek ada payment pending
$invoice->getPaymentUrl()       // Get payment URL
$invoice->getFormattedAmount()  // Format amount
```

#### Payment Service Methods:
```php
$duitkuService->createPayment($invoice, $method)  // Create payment
$duitkuService->handleCallback($data)             // Handle callback
$duitkuService->checkPaymentStatus($orderId)      // Check status
$duitkuService->getPaymentMethods()               // Get methods
```

### 🔐 Security:

1. **Signature Verification** - Semua callback diverifikasi signature
2. **CSRF Protection** - Form payment protected
3. **Auth Check** - User hanya bisa bayar invoice sendiri
4. **Input Validation** - Semua input divalidasi
5. **Error Handling** - Proper error handling dan logging

### 📱 UI Features:

1. **Responsive Design** - Mobile friendly payment page
2. **Real-time Status** - Auto check payment status
3. **Multiple Methods** - Visual payment method selection
4. **Progress Indicator** - Show payment progress
5. **Error Messages** - User friendly error messages

### 🚨 Important Notes:

1. **Callback URL** harus bisa diakses dari internet (untuk production)
2. **Return URL** untuk redirect setelah payment
3. **Signature** harus match untuk security
4. **Timeout** payment 24 jam (1440 menit)
5. **Currency** IDR only

### 📞 Support:

Jika ada masalah:
1. Check logs di `storage/logs/laravel.log`
2. Verify Duitku credentials
3. Test callback URL accessibility
4. Check database invoice status

**Payment Gateway Duitku sudah siap digunakan!** 🎉
