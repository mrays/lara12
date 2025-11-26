-- =====================================================
-- Query untuk mengubah struktur tabel client_data
-- Jalankan query ini di phpMyAdmin
-- =====================================================

-- 1. Tambah kolom domain_id
ALTER TABLE `client_data` ADD COLUMN `domain_id` INT NULL AFTER `whatsapp`;

-- 2. Tambah foreign key constraint untuk domain_id
ALTER TABLE `client_data` ADD CONSTRAINT `client_data_domain_id_foreign` 
    FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Hapus kolom expiration yang lama
ALTER TABLE `client_data` DROP COLUMN `website_service_expired`;
ALTER TABLE `client_data` DROP COLUMN `domain_expired`;
ALTER TABLE `client_data` DROP COLUMN `hosting_expired`;

-- =====================================================
-- Verifikasi perubahan (opsional, untuk mengecek hasil)
-- =====================================================
-- DESCRIBE `client_data`;
