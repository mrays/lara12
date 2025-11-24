-- SQL Query untuk menambahkan status baru ke invoices
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Cek struktur tabel invoices saat ini
DESCRIBE `invoices`;

-- 2. Cek status yang ada sekarang
SELECT DISTINCT status FROM `invoices`;

-- 3. Update ENUM invoices untuk menambahkan status Indonesia
-- Jika kolom status adalah VARCHAR, skip langkah ini
-- Jika kolom status adalah ENUM, jalankan query berikut:
ALTER TABLE `invoices` 
MODIFY COLUMN `status` ENUM(
    'Paid', 
    'Unpaid', 
    'Overdue', 
    'Cancelled',
    'Sedang Dicek',
    'Lunas',
    'Belum Lunas'
) NOT NULL DEFAULT 'Unpaid';

-- 4. Jika kolom status adalah VARCHAR, tidak perlu ubah struktur
-- Status baru akan langsung bisa digunakan

-- 5. Tambah kolom invoice_no jika belum ada
ALTER TABLE `invoices` 
ADD COLUMN IF NOT EXISTS `invoice_no` VARCHAR(255) NULL AFTER `id`;

-- 6. Tambah kolom paid_at jika belum ada
ALTER TABLE `invoices` 
ADD COLUMN IF NOT EXISTS `paid_at` TIMESTAMP NULL AFTER `status`;

-- 7. Update invoice_no untuk data existing yang kosong
UPDATE `invoices` SET `invoice_no` = CONCAT('INV-', id) WHERE `invoice_no` IS NULL OR `invoice_no` = '';

-- 8. Verifikasi perubahan
DESCRIBE `invoices`;

-- 9. Cek data sample
SELECT id, invoice_no, due_date, total_amount, amount, status, paid_at FROM `invoices` LIMIT 10;
