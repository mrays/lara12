-- SQL Query untuk update status services dengan opsi baru
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Update kolom status di tabel services untuk mendukung status baru
ALTER TABLE `services` 
MODIFY COLUMN `status` ENUM('Aktif', 'Pending', 'Dibatalkan', 'Disuspen', 'Sedang Dibuat', 'Ditutup') 
NOT NULL DEFAULT 'Pending';

-- 2. Update existing services yang statusnya 'Active' menjadi 'Aktif'
UPDATE `services` SET `status` = 'Aktif' WHERE `status` = 'Active';

-- 3. Update existing services yang statusnya 'Inactive' menjadi 'Pending'
UPDATE `services` SET `status` = 'Pending' WHERE `status` = 'Inactive';

-- 4. Cek apakah kolom paid_at ada di tabel invoices, jika belum tambahkan
ALTER TABLE `invoices` 
ADD COLUMN IF NOT EXISTS `paid_at` TIMESTAMP NULL AFTER `status`;

-- 5. Update kolom status di tabel invoices untuk konsistensi
ALTER TABLE `invoices` 
MODIFY COLUMN `status` ENUM('Paid', 'Unpaid', 'Overdue', 'Cancelled') 
NOT NULL DEFAULT 'Unpaid';

-- 6. Verifikasi perubahan
DESCRIBE `services`;
DESCRIBE `invoices`;

-- 7. Cek data services
SELECT id, name, client_id, status, created_at FROM `services` LIMIT 10;

-- 8. Cek data invoices  
SELECT id, title, client_id, total_amount, status, paid_at FROM `invoices` LIMIT 10;
