-- Query yang aman untuk update status services
-- Jalankan satu per satu untuk menghindari error

-- 1. Cek apakah tabel services ada
SHOW TABLES LIKE 'services';

-- 2. Cek struktur tabel services
DESCRIBE `services`;

-- 3. Cek data status yang ada sekarang
SELECT DISTINCT status FROM `services`;

-- 4. Jika kolom status belum ada, buat dulu
-- ALTER TABLE `services` ADD COLUMN `status` VARCHAR(50) DEFAULT 'Pending';

-- 5. Update data existing ke format baru (jika ada)
UPDATE `services` SET `status` = 'Aktif' WHERE `status` = 'Active';
UPDATE `services` SET `status` = 'Aktif' WHERE `status` = 'active';
UPDATE `services` SET `status` = 'Pending' WHERE `status` = 'Inactive';
UPDATE `services` SET `status` = 'Pending' WHERE `status` = 'inactive';
UPDATE `services` SET `status` = 'Pending' WHERE `status` IS NULL OR `status` = '';

-- 6. Sekarang baru ubah ke ENUM (setelah data sudah sesuai)
ALTER TABLE `services` 
MODIFY COLUMN `status` ENUM('Aktif', 'Pending', 'Dibatalkan', 'Disuspen', 'Sedang Dibuat', 'Ditutup') 
NOT NULL DEFAULT 'Pending';

-- 7. Verifikasi hasil
DESCRIBE `services`;
SELECT id, name, status FROM `services` LIMIT 10;
