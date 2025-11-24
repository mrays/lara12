-- Tambah status bahasa Indonesia ke ENUM services
-- Jalankan query ini untuk menambahkan status yang belum ada

-- Update ENUM services untuk menambahkan status bahasa Indonesia
ALTER TABLE `services` 
MODIFY COLUMN `status` ENUM(
    'Active', 
    'Suspended', 
    'Terminated', 
    'Pending',
    'Dibatalkan',
    'Disuspen', 
    'Sedang Dibuat',
    'Ditutup'
) NOT NULL DEFAULT 'Pending';

-- Verifikasi perubahan
DESCRIBE `services`;

-- Cek data existing
SELECT DISTINCT status FROM `services`;
