# Database Upgrade Guide

## Analisis Database Saat Ini

Berdasarkan database dump yang Anda berikan, berikut adalah analisis struktur database yang sudah ada:

### Tabel yang Sudah Ada:

#### 1. `clients` 
```sql
- id (bigint, primary key)
- name (varchar 191)
- email (varchar 191, unique)
- phone (varchar 50)
- status (enum: Active, Suspended, Cancelled)
- created_at, updated_at (timestamp)
```

#### 2. `invoices` (struktur lama)
```sql
- id (bigint, primary key)
- client_id (bigint, foreign key)
- invoice_no (varchar 50, unique)
- merchant_order_id (varchar 255)
- reference (varchar 255)
- due_date (date)
- amount (decimal 12,2)
- status (enum: Paid, Unpaid, Past Due)
- created_at, updated_at (timestamp)
```

#### 3. `services`
```sql
- id (bigint, primary key)
- client_id (bigint, foreign key)
- product (varchar 191)
- domain (varchar 191)
- price (decimal 12,2)
- billing_cycle (varchar 50)
- registration_date (date)
- due_date (date)
- ip (varchar 45)
- status (enum: Active, Suspended, Cancelled)
- created_at, updated_at (timestamp)
```

#### 4. `users`
```sql
- id (bigint, primary key)
- name (varchar 255)
- email (varchar 255, unique)
- email_verified_at (timestamp)
- password (varchar 255)
- role (varchar 50, default: client)
- remember_token (varchar 100)
- created_at, updated_at (timestamp)
```

### Data yang Sudah Ada:
- **3 clients** (Administrator, John Doe, Jane Customer)
- **2 invoices** (INV-2025-0001: Rp 1,680,000 Unpaid, INV-2024-0002: Rp 120,000 Paid)
- **3 services** (2 Unlimited L hosting, 1 Starter Hosting)
- **6 users** (mix admin dan client)

## Masalah yang Perlu Diperbaiki

### 1. Tabel `clients` 
- ❌ Tidak ada field `address` dan `company`
- ❌ Status enum tidak konsisten dengan sistem baru

### 2. Tabel `invoices`
- ❌ Struktur terlalu sederhana, tidak mendukung:
  - Tax calculations
  - Discount amounts
  - Multiple line items
  - Detailed descriptions
  - Payment tracking
- ❌ Status enum tidak sesuai dengan sistem baru
- ❌ Tidak ada relasi ke services

### 3. Tabel `services`
- ❌ Tidak ada field `notes`
- ❌ `billing_cycle` sebagai varchar, seharusnya enum
- ❌ Status enum tidak konsisten

### 4. Missing Tables
- ❌ Tidak ada tabel `invoice_items` untuk detail items

## Solusi Upgrade

### Opsi 1: Step-by-Step Upgrade (Recommended)
Gunakan file: `step_by_step_upgrade.sql`

**Keuntungan:**
- ✅ Aman, bisa dijalankan bertahap
- ✅ Data asli dibackup
- ✅ Bisa diverifikasi setiap langkah
- ✅ Mudah rollback jika ada masalah

**Langkah-langkah:**
1. Backup data asli
2. Enhance tabel clients
3. Enhance tabel services  
4. Buat struktur invoice baru
5. Migrate data lama ke struktur baru
6. Tambah foreign key constraints
7. Buat sample data baru
8. Buat views untuk reporting
9. Verifikasi hasil

### Opsi 2: Complete Upgrade
Gunakan file: `simple_database_upgrade.sql`

**Keuntungan:**
- ✅ Upgrade lengkap sekaligus
- ✅ Otomatis backup
- ✅ Includes performance indexes
- ✅ Includes reporting views

### Opsi 3: Advanced Upgrade  
Gunakan file: `upgrade_existing_database.sql`

**Keuntungan:**
- ✅ Upgrade paling lengkap
- ✅ Advanced features
- ✅ Performance optimizations
- ✅ Comprehensive reporting

## Cara Menjalankan Upgrade

### Persiapan
1. **BACKUP DATABASE TERLEBIH DAHULU!**
   ```bash
   mysqldump -u username -p cloud > backup_before_upgrade.sql
   ```

2. **Test di environment development dulu**

### Eksekusi Upgrade

#### Menggunakan phpMyAdmin:
1. Login ke phpMyAdmin
2. Pilih database `cloud`
3. Klik tab "SQL"
4. Copy-paste isi file `step_by_step_upgrade.sql`
5. Jalankan section by section (jangan sekaligus)
6. Verifikasi hasil setiap step

#### Menggunakan MySQL Command Line:
```bash
mysql -u username -p cloud < step_by_step_upgrade.sql
```

### Verifikasi Hasil

Setelah upgrade, jalankan query ini untuk verifikasi:

```sql
-- Cek struktur tabel
DESCRIBE clients;
DESCRIBE services;
DESCRIBE invoices;
DESCRIBE invoice_items;

-- Cek data
SELECT COUNT(*) FROM clients;
SELECT COUNT(*) FROM services;  
SELECT COUNT(*) FROM invoices;
SELECT COUNT(*) FROM invoice_items;

-- Cek migrasi data
SELECT * FROM invoice_summary LIMIT 5;
SELECT * FROM client_stats;
```

## Perubahan pada Laravel Models

Setelah upgrade database, update model Laravel:

### 1. Client Model
```php
protected $fillable = [
    'name', 'email', 'phone', 'address', 'company', 'status'
];
```

### 2. Service Model  
```php
protected $fillable = [
    'client_id', 'product', 'domain', 'price', 'billing_cycle',
    'registration_date', 'due_date', 'ip', 'status', 'notes'
];
```

### 3. Invoice Model
```php
protected $fillable = [
    'client_id', 'service_id', 'number', 'title', 'description',
    'subtotal', 'tax_rate', 'tax_amount', 'discount_amount', 
    'total_amount', 'status', 'issue_date', 'due_date', 'paid_date',
    'payment_method', 'payment_reference', 'notes',
    'duitku_merchant_code', 'duitku_reference', 'duitku_payment_url'
];
```

### 4. InvoiceItem Model (Baru)
```php
protected $fillable = [
    'invoice_id', 'description', 'quantity', 'unit_price', 'total_price'
];
```

## Testing Setelah Upgrade

### 1. Test Basic Functionality
- Login sebagai admin dan client
- Akses dashboard
- Lihat data clients, services, invoices

### 2. Test New Features
- Buat invoice baru dengan multiple items
- Test tax calculations
- Test payment tracking
- Test overdue detection

### 3. Test Data Integrity
- Pastikan semua data lama masih ada
- Pastikan relasi foreign key bekerja
- Pastikan views menampilkan data yang benar

## Rollback Plan

Jika ada masalah, rollback dengan:

```sql
-- Restore dari backup
DROP TABLE invoices;
DROP TABLE invoice_items;
ALTER TABLE invoices_old RENAME TO invoices;

-- Atau restore dari backup file
mysql -u username -p cloud < backup_before_upgrade.sql
```

## Fitur Baru Setelah Upgrade

### 1. Enhanced Invoice System
- ✅ Multiple line items per invoice
- ✅ Tax calculations
- ✅ Discount support
- ✅ Payment tracking
- ✅ Overdue detection
- ✅ Service linking

### 2. Better Client Management
- ✅ Company information
- ✅ Address tracking
- ✅ Enhanced statistics

### 3. Improved Services
- ✅ Notes field
- ✅ Better status tracking
- ✅ Renewal management

### 4. Reporting & Analytics
- ✅ Invoice summary views
- ✅ Client statistics
- ✅ Service analytics
- ✅ Payment tracking

## Support

Jika mengalami masalah:
1. Cek error log MySQL
2. Verifikasi struktur tabel
3. Pastikan foreign key constraints
4. Test dengan data sample

**PENTING: Selalu backup database sebelum menjalankan upgrade!**
