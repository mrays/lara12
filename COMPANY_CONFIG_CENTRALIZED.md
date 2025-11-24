# Company Information Centralized

## ✅ **Perubahan yang Sudah Dilakukan:**

### **1. Config Central Dibuat:**
- ✅ **File:** `config/company.php`
- ✅ **Berisi:** Nama perusahaan, alamat, nomor WhatsApp, email, dll
- ✅ **Centralized:** Semua informasi perusahaan dalam satu tempat

### **2. Footer Copyright Diupdate:**
- ✅ **File:** `layouts/sneat-dashboard.blade.php`
- ✅ **Dari:** `© 2025 , made with ❤️ by ThemeSelection`
- ✅ **Ke:** `© 2025 Exputra Group` (menggunakan `config('company.copyright')`)

### **3. Nomor WhatsApp Diupdate ke 085186846500:**

**Files yang diupdate:**
- ✅ `layouts/sneat-dashboard.blade.php` - contactSupport function
- ✅ `client/services/manage.blade.php` - contactSupport function  
- ✅ `client/services/index.blade.php` - requestNewService function

**Semua menggunakan:** `config('company.whatsapp_url')` dan `config('company.support_messages')`

### **4. Alamat Perusahaan di Invoice:**

**Files yang diupdate:**
- ✅ `client/invoices/show.blade.php` - Tambah section "Bill From" dengan alamat perusahaan
- ✅ `client/invoices/pdf.blade.php` - Company info menggunakan config
- ✅ `admin/invoices/show.blade.php` - Company info menggunakan config
- ✅ `emails/test.blade.php` - Footer menggunakan company config

### **5. Konfigurasi Company Config:**

```php
// config/company.php
return [
    'name' => 'Exputra Group',
    'copyright' => '© 2025 Exputra Group',
    
    // Contact
    'phone' => '085186846500',
    'whatsapp' => '085186846500', 
    'whatsapp_url' => 'https://wa.me/6285186846500',
    'email' => 'info@exputra.com',
    
    // Address
    'address' => [
        'street' => 'Jl. Teknologi Digital No. 123',
        'city' => 'Jakarta',
        'state' => 'DKI Jakarta', 
        'postal_code' => '12345',
        'country' => 'Indonesia',
        'full' => 'Jl. Teknologi Digital No. 123, Jakarta, DKI Jakarta 12345, Indonesia',
    ],
    
    // Social Media
    'social' => [
        'website' => 'https://exputra.com',
    ],
    
    // Support Messages
    'support_messages' => [
        'default' => 'Hello, I need support with my services.',
        'service_issue' => 'Hello, I need support for my service: {service_name}',
        'payment_issue' => 'Hello, I need help with payment for invoice: {invoice_number}',
        'general_inquiry' => 'Hello, I have a general inquiry about your services.',
    ],
];
```

## 🎯 **Keuntungan Centralized Config:**

### **1. Easy Management:**
- ✅ **Single Source of Truth** - Semua info perusahaan di satu tempat
- ✅ **Easy Updates** - Ubah sekali, apply ke semua tempat
- ✅ **Consistent** - Tidak ada info yang berbeda-beda

### **2. Maintenance:**
- ✅ **No Hard-coding** - Semua menggunakan config helper
- ✅ **Environment Specific** - Bisa berbeda per environment
- ✅ **Version Control** - Tracked di git

### **3. Flexibility:**
- ✅ **Dynamic Messages** - Support messages bisa dikustomisasi
- ✅ **Multiple Formats** - Address bisa full atau per component
- ✅ **Extensible** - Mudah tambah field baru

## 📋 **Cara Menggunakan:**

### **Di Blade Templates:**
```blade
<!-- Company Name -->
{{ config('company.name') }}

<!-- Copyright -->
{{ config('company.copyright') }}

<!-- WhatsApp URL -->
{{ config('company.whatsapp_url') }}

<!-- Full Address -->
{{ config('company.address.full') }}

<!-- Support Message -->
{{ config('company.support_messages.default') }}
```

### **Di Controller:**
```php
$companyName = config('company.name');
$whatsappUrl = config('company.whatsapp_url');
$supportMessage = config('company.support_messages.service_issue');
```

### **Di JavaScript:**
```javascript
// Via Blade
const whatsappUrl = '{{ config('company.whatsapp_url') }}';
const message = '{{ config('company.support_messages.default') }}';
window.open(`${whatsappUrl}?text=${encodeURIComponent(message)}`, '_blank');
```

## 🔄 **Update Instructions:**

### **Untuk mengubah informasi perusahaan:**
1. Edit file `config/company.php`
2. Update nilai yang diinginkan
3. Clear config cache: `php artisan config:clear`
4. Semua perubahan akan apply otomatis

### **Untuk menambah field baru:**
1. Tambah ke `config/company.php`
2. Gunakan `config('company.field_name')` di template
3. No need to update multiple files

## ✅ **Status Implementasi:**

- ✅ **Footer Copyright:** Updated ke "© 2025 Exputra Group"
- ✅ **WhatsApp Number:** Updated ke 085186846500 di semua tempat
- ✅ **Company Address:** Centralized di invoice dan email
- ✅ **Support Messages:** Standardized dan configurable
- ✅ **Email Templates:** Menggunakan company config

**Semua informasi perusahaan sekarang sudah centralized dan mudah dikelola!** 🎉
