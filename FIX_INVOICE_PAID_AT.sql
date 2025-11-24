-- SQL Query untuk memperbaiki tabel invoices
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Cek struktur tabel invoices saat ini
DESCRIBE `invoices`;

-- 2. Tambahkan kolom paid_at jika diperlukan (optional)
-- Uncomment baris di bawah jika ingin menambahkan kolom paid_at
-- ALTER TABLE `invoices` ADD COLUMN `paid_at` TIMESTAMP NULL AFTER `status`;

-- 3. Update existing paid invoices dengan paid_at (jika kolom ditambahkan)
-- Uncomment baris di bawah jika kolom paid_at sudah ditambahkan
-- UPDATE `invoices` SET `paid_at` = `updated_at` WHERE `status` IN ('Paid', 'Lunas') AND `paid_at` IS NULL;

-- 4. Verifikasi struktur tabel
DESCRIBE `invoices`;

-- 5. Cek data sample
SELECT id, invoice_no, status, created_at, updated_at FROM `invoices` LIMIT 5;
