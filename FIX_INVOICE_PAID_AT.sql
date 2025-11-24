-- SQL Query untuk memperbaiki tabel invoices
-- Berdasarkan struktur tabel yang ada, kolom paid_date sudah tersedia

-- 1. Cek struktur tabel invoices saat ini
DESCRIBE `invoices`;

-- 2. Kolom paid_date sudah ada di tabel (tidak perlu ditambahkan)
-- Kolom paid_date terlihat di baris 15 pada struktur tabel

-- 3. Update existing paid invoices dengan paid_date
UPDATE `invoices` SET `paid_date` = `updated_at` 
WHERE `status` IN ('Paid', 'Lunas') AND `paid_date` IS NULL;

-- 4. Verifikasi perubahan
SELECT id, number, status, paid_date, created_at, updated_at FROM `invoices` LIMIT 5;

-- 5. Cek invoices yang sudah dibayar
SELECT id, number, status, paid_date FROM `invoices` 
WHERE `status` IN ('Paid', 'Lunas') LIMIT 5;
