-- SQL Query untuk menambahkan kolom phone dan status ke tabel users
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Cek apakah kolom phone sudah ada, jika belum tambahkan
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `phone` VARCHAR(255) NULL AFTER `email`;

-- 2. Cek apakah kolom status sudah ada, jika belum tambahkan  
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active' AFTER `role`;

-- 3. Update existing users untuk set default status jika NULL
UPDATE `users` SET `status` = 'Active' WHERE `status` IS NULL;

-- 4. Cek struktur tabel setelah perubahan
DESCRIBE `users`;

-- 5. Optional: Set semua user existing sebagai client jika role masih NULL
UPDATE `users` SET `role` = 'client' WHERE `role` IS NULL OR `role` = '';

-- 6. Verifikasi data
SELECT id, name, email, phone, role, status, created_at FROM `users` LIMIT 10;
