-- =====================================================
-- Query untuk mengubah struktur tabel client_data
-- Jalankan query ini di phpMyAdmin SATU PER SATU
-- =====================================================

-- =====================================================
-- SKEMA BARU:
-- - ClientData: hanya menyimpan info client (name, address, whatsapp, user_id, status, notes)
-- - Domain: menyimpan info domain dengan client_id, server_id, domain_register_id
-- - 1 Client bisa memiliki banyak Domain (1-5 domain)
-- =====================================================

-- STEP 1: Hapus foreign key constraint terlebih dahulu
-- Jalankan satu per satu, abaikan error jika constraint tidak ada

ALTER TABLE `client_data` DROP FOREIGN KEY `client_data_domain_id_foreign`;

ALTER TABLE `client_data` DROP FOREIGN KEY `client_data_server_id_foreign`;

ALTER TABLE `client_data` DROP FOREIGN KEY `client_data_domain_register_id_foreign`;

-- STEP 2: Hapus kolom yang tidak diperlukan lagi
ALTER TABLE `client_data` DROP COLUMN `domain_id`;

ALTER TABLE `client_data` DROP COLUMN `server_id`;

ALTER TABLE `client_data` DROP COLUMN `domain_register_id`;

-- 3. Hapus kolom expiration yang lama (jika masih ada)
-- ALTER TABLE `client_data` DROP COLUMN IF EXISTS `website_service_expired`;
-- ALTER TABLE `client_data` DROP COLUMN IF EXISTS `domain_expired`;
-- ALTER TABLE `client_data` DROP COLUMN IF EXISTS `hosting_expired`;

-- =====================================================
-- Verifikasi perubahan (opsional)
-- =====================================================
-- DESCRIBE `client_data`;

-- =====================================================
-- Pastikan tabel domains memiliki kolom client_id
-- =====================================================
-- Cek apakah kolom client_id sudah ada di tabel domains
-- Jika belum, jalankan:
-- ALTER TABLE `domains` ADD COLUMN `client_id` INT NULL AFTER `domain_name`;
-- ALTER TABLE `domains` ADD CONSTRAINT `domains_client_id_foreign` 
--     FOREIGN KEY (`client_id`) REFERENCES `client_data` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
