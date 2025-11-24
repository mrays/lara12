-- Fix untuk error "Data truncated for column 'status'"
-- Jalankan query ini satu per satu

-- 1. Cek data status yang ada sekarang
SELECT DISTINCT status FROM `services`;

-- 2. Lihat semua data dengan status yang bermasalah
SELECT id, name, status FROM `services` WHERE status NOT IN ('Aktif', 'Pending', 'Dibatalkan', 'Disuspen', 'Sedang Dibuat', 'Ditutup');

-- 3. Update semua data yang tidak sesuai ke status yang valid
-- Update 'Active' menjadi 'Aktif'
UPDATE `services` SET `status` = 'Aktif' WHERE `status` = 'Active';

-- Update 'Inactive' menjadi 'Pending'  
UPDATE `services` SET `status` = 'Pending' WHERE `status` = 'Inactive';

-- Update 'Suspended' menjadi 'Disuspen'
UPDATE `services` SET `status` = 'Disuspen' WHERE `status` = 'Suspended';

-- Update 'Cancelled' menjadi 'Dibatalkan'
UPDATE `services` SET `status` = 'Dibatalkan' WHERE `status` = 'Cancelled';

-- Update 'Terminated' menjadi 'Ditutup'
UPDATE `services` SET `status` = 'Ditutup' WHERE `status` = 'Terminated';

-- Update semua status kosong atau NULL menjadi 'Pending'
UPDATE `services` SET `status` = 'Pending' WHERE `status` IS NULL OR `status` = '';

-- Update semua status lain yang tidak dikenal menjadi 'Pending'
UPDATE `services` SET `status` = 'Pending' WHERE `status` NOT IN ('Aktif', 'Pending', 'Dibatalkan', 'Disuspen', 'Sedang Dibuat', 'Ditutup');

-- 4. Verifikasi semua data sudah sesuai
SELECT DISTINCT status FROM `services`;

-- 5. Sekarang baru ubah ke ENUM (setelah semua data sudah sesuai)
ALTER TABLE `services` 
MODIFY COLUMN `status` ENUM('Aktif', 'Pending', 'Dibatalkan', 'Disuspen', 'Sedang Dibuat', 'Ditutup') 
NOT NULL DEFAULT 'Pending';

-- 6. Verifikasi hasil akhir
DESCRIBE `services`;
SELECT id, name, status FROM `services` LIMIT 10;
