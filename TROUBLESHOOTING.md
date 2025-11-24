# 🔧 Troubleshooting Guide

## ❌ Error: Call to undefined function has_company_logo()

### **Deskripsi Error:**
```
Error: Call to undefined function has_company_logo()
File: resources/views/auth/forgot-password.blade.php:12
```

### **Penyebab:**
Helper functions di `app/Helpers/helpers.php` belum ter-load oleh Laravel autoloader.

### **Solusi:**

#### **1. Regenerate Autoload (Recommended)**
```bash
# Jalankan command berikut di terminal/command prompt
composer dump-autoload

# Clear semua cache Laravel
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

#### **2. Jalankan Batch File (Windows)**
```bash
# Double click atau jalankan file ini:
fix_helpers.bat
```

#### **3. Manual Fix (Jika masih error)**
Jika masih error, cek file `composer.json` pastikan ada:
```json
{
    "autoload": {
        "files": [
            "app/Helpers/helpers.php"
        ]
    }
}
```

#### **4. Verifikasi Helper Functions**
Test di tinker apakah helper sudah ter-load:
```bash
php artisan tinker

# Test function
>>> function_exists('has_company_logo')
=> true

>>> has_company_logo()
=> false (atau true jika logo ada)
```

### **Pencegahan:**
Views auth sudah diperbaiki dengan fallback safety check:
```php
@php
    $hasLogo = function_exists('has_company_logo') && has_company_logo();
    $logoUrl = $hasLogo ? company_logo() : null;
@endphp
@if($hasLogo && $logoUrl)
    <img src="{{ $logoUrl }}" alt="Logo">
@else
    <!-- Fallback SVG logo -->
@endif
```

---

## ❌ Error: Class 'App\Helpers\LogoHelper' not found

### **Solusi:**
```bash
composer dump-autoload
```

---

## ❌ Error: Configuration cache is corrupted

### **Solusi:**
```bash
php artisan config:clear
php artisan cache:clear
```

---

## ❌ Error: Target class [role] does not exist

### **Penyebab:**
Middleware `role` belum didaftarkan di Laravel 11.

### **Solusi:**

#### **1. Jalankan Setup Script (Recommended)**
```bash
# Double click atau jalankan:
setup_admin.bat
```

#### **2. Manual Setup**

**Step 1: Middleware sudah dibuat di:**
- `app/Http/Middleware/RoleMiddleware.php` ✅
- Sudah didaftarkan di `bootstrap/app.php` ✅

**Step 2: Tambah kolom role ke database:**
```sql
-- Jalankan query ini di database:
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'client', 'staff') NOT NULL DEFAULT 'client';
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_users_role (role);
```

**Step 3: Buat admin user:**
```bash
# Via command:
php artisan admin:create

# Atau manual di database:
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

**Step 4: Clear cache:**
```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

#### **3. Verifikasi Setup**
```sql
-- Cek kolom role ada:
DESCRIBE users;

-- Cek admin users:
SELECT id, name, email, role FROM users WHERE role = 'admin';
```

---

## ❌ Error: Table 'service_upgrade_requests' doesn't exist

### **Solusi:**
Jalankan query SQL manual:
```sql
-- Copy paste dari file: database_queries/create_service_upgrade_requests.sql
CREATE TABLE service_upgrade_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    -- ... (lihat file SQL lengkap)
);
```

---

## ❌ Error: CSRF token mismatch

### **Solusi:**
1. **Cek meta tag di layout:**
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

2. **Cek JavaScript CSRF:**
   ```javascript
   headers: {
       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
   }
   ```

3. **Clear session:**
   ```bash
   php artisan session:flush
   ```

---

## ❌ Error: 500 Internal Server Error

### **Debug Steps:**
1. **Enable debug mode:**
   ```env
   APP_DEBUG=true
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Check web server logs:**
   - Apache: `/var/log/apache2/error.log`
   - Nginx: `/var/log/nginx/error.log`

---

## 🔧 General Debugging Commands

### **Clear All Caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload
```

### **Check Configuration:**
```bash
php artisan config:show
php artisan route:list
php artisan tinker
```

### **Database Check:**
```bash
php artisan migrate:status
php artisan db:show
```

### **Permission Check (Linux/Mac):**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

---

## 📞 Getting Help

Jika masih mengalami masalah:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true`
3. Check web server error logs
4. Pastikan semua dependencies ter-install: `composer install`
5. Pastikan database connection benar di `.env`
