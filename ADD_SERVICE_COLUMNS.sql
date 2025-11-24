-- SQL Query untuk menambahkan kolom baru ke tabel services
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Cek struktur tabel services saat ini
DESCRIBE `services`;

-- 2. Tambahkan kolom untuk Overview/Login Information
ALTER TABLE `services` 
ADD COLUMN IF NOT EXISTS `username` VARCHAR(255) NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `password` VARCHAR(255) NULL AFTER `username`,
ADD COLUMN IF NOT EXISTS `server` VARCHAR(255) NULL AFTER `password`,
ADD COLUMN IF NOT EXISTS `login_url` TEXT NULL AFTER `server`;

-- 3. Tambahkan kolom untuk Billing Information
ALTER TABLE `services` 
ADD COLUMN IF NOT EXISTS `billing_cycle` VARCHAR(50) NULL DEFAULT 'Monthly' AFTER `login_url`,
ADD COLUMN IF NOT EXISTS `setup_fee` DECIMAL(15,2) NULL DEFAULT 0 AFTER `price`;

-- 4. Tambahkan kolom untuk Additional Details
ALTER TABLE `services` 
ADD COLUMN IF NOT EXISTS `notes` TEXT NULL AFTER `description`;

-- 5. Update existing records dengan default values
-- Update billing_cycle
UPDATE `services` SET `billing_cycle` = 'Monthly' WHERE `billing_cycle` IS NULL;

-- Update setup_fee
UPDATE `services` SET `setup_fee` = 0 WHERE `setup_fee` IS NULL;

-- Update server
UPDATE `services` SET `server` = 'Default Server' WHERE `server` IS NULL OR `server` = '';

-- 6. Verifikasi perubahan
DESCRIBE `services`;

-- 7. Cek kolom yang ada di tabel services
SHOW COLUMNS FROM `services`;

-- 8. Cek data sample dengan kolom yang benar
SELECT id, product, domain, username, password, server, login_url, billing_cycle, setup_fee FROM `services` LIMIT 5;

-- 9. Cek semua data untuk memastikan update berhasil
SELECT COUNT(*) as total_services, 
       COUNT(username) as has_username,
       COUNT(server) as has_server,
       COUNT(billing_cycle) as has_billing_cycle
FROM `services`;
