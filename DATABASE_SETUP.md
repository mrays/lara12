# 🗃️ Database Setup - Service Upgrade System

## 📋 Manual Database Setup (Tanpa Migration)

Karena Anda lebih suka menggunakan query SQL langsung daripada migration Laravel, berikut adalah panduan untuk setup database secara manual.

## 🚀 Cara Menjalankan Query

### **Option 1: Via phpMyAdmin**
1. Buka phpMyAdmin di browser
2. Pilih database project Anda
3. Klik tab "SQL"
4. Copy paste isi file `database_queries/create_service_upgrade_requests.sql`
5. Klik "Go" untuk execute

### **Option 2: Via MySQL Command Line**
```bash
# Login ke MySQL
mysql -u username -p

# Pilih database
USE nama_database_anda;

# Jalankan file SQL
SOURCE /path/to/project/database_queries/create_service_upgrade_requests.sql;

# Atau copy paste query langsung
```

### **Option 3: Via MySQL Workbench**
1. Buka MySQL Workbench
2. Connect ke database server
3. Buka file SQL atau copy paste query
4. Execute query

## 📁 File SQL yang Tersedia

### **create_service_upgrade_requests.sql**
- **Location**: `database_queries/create_service_upgrade_requests.sql`
- **Purpose**: Membuat tabel `service_upgrade_requests`
- **Features**:
  - ✅ Primary key auto increment
  - ✅ Foreign key constraints ke `services` dan `users`
  - ✅ Indexes untuk performance
  - ✅ ENUM validation untuk status dan reason
  - ✅ Timestamps dengan auto update

## 🔧 Query SQL Lengkap

```sql
CREATE TABLE service_upgrade_requests (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    current_plan VARCHAR(255) NOT NULL,
    requested_plan VARCHAR(255) NOT NULL,
    current_price DECIMAL(10,2) NOT NULL,
    requested_price DECIMAL(10,2) NOT NULL,
    upgrade_reason ENUM('need_more_resources', 'additional_features', 'business_growth', 'performance_improvement', 'other') NOT NULL,
    additional_notes TEXT NULL,
    status ENUM('pending', 'approved', 'rejected', 'processing') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    processed_by BIGINT UNSIGNED NULL,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key Constraints
    CONSTRAINT fk_service_upgrade_requests_service_id 
        FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    
    CONSTRAINT fk_service_upgrade_requests_client_id 
        FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    
    CONSTRAINT fk_service_upgrade_requests_processed_by 
        FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes untuk performance
    INDEX idx_service_upgrade_requests_status_created (status, created_at),
    INDEX idx_service_upgrade_requests_client_service (client_id, service_id),
    INDEX idx_service_upgrade_requests_service_id (service_id),
    INDEX idx_service_upgrade_requests_client_id (client_id),
    INDEX idx_service_upgrade_requests_processed_by (processed_by),
    INDEX idx_service_upgrade_requests_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## ✅ Verifikasi Setup

### **1. Cek Struktur Tabel**
```sql
DESCRIBE service_upgrade_requests;
```

### **2. Cek Foreign Key Constraints**
```sql
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'service_upgrade_requests' 
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### **3. Cek Indexes**
```sql
SHOW INDEX FROM service_upgrade_requests;
```

### **4. Test Insert Data**
```sql
INSERT INTO service_upgrade_requests (
    service_id, 
    client_id, 
    current_plan, 
    requested_plan, 
    current_price, 
    requested_price, 
    upgrade_reason, 
    additional_notes
) VALUES (
    1, 
    2, 
    'Basic Plan', 
    'Premium Plan', 
    100000.00, 
    250000.00, 
    'business_growth', 
    'Need more storage and bandwidth'
);
```

## 🔍 Query Management Berguna

### **Cek Pending Requests**
```sql
SELECT 
    sur.id,
    sur.current_plan,
    sur.requested_plan,
    sur.current_price,
    sur.requested_price,
    u.name as client_name,
    s.product as service_name,
    sur.created_at
FROM service_upgrade_requests sur
JOIN users u ON sur.client_id = u.id
JOIN services s ON sur.service_id = s.id
WHERE sur.status = 'pending'
ORDER BY sur.created_at DESC;
```

### **Statistik Status**
```sql
SELECT 
    status,
    COUNT(*) as total,
    AVG(requested_price - current_price) as avg_price_increase
FROM service_upgrade_requests 
GROUP BY status;
```

### **Cek Duplicate Pending**
```sql
SELECT 
    service_id,
    client_id,
    COUNT(*) as pending_count
FROM service_upgrade_requests 
WHERE status = 'pending'
GROUP BY service_id, client_id
HAVING COUNT(*) > 1;
```

## 🚨 Troubleshooting

### **Error: Table doesn't exist**
- Pastikan query CREATE TABLE sudah dijalankan
- Cek nama database yang benar

### **Error: Foreign key constraint fails**
- Pastikan tabel `services` dan `users` sudah ada
- Cek apakah ada data yang referensi ke ID yang tidak exist

### **Error: Duplicate entry**
- Cek apakah sudah ada data dengan ID yang sama
- Gunakan `DROP TABLE IF EXISTS` jika perlu recreate

## 📊 Sample Data untuk Testing

Jika ingin insert sample data untuk testing:

```sql
INSERT INTO service_upgrade_requests (
    service_id, client_id, current_plan, requested_plan, 
    current_price, requested_price, upgrade_reason, additional_notes, status
) VALUES 
(1, 2, 'Basic Plan', 'Premium Plan', 100000.00, 250000.00, 'business_growth', 'Need more storage and bandwidth for growing business', 'pending'),
(2, 3, 'Standard Plan', 'Enterprise Plan', 200000.00, 500000.00, 'need_more_resources', 'Current plan is not sufficient for our needs', 'pending'),
(3, 4, 'Basic Plan', 'Standard Plan', 100000.00, 200000.00, 'additional_features', 'Need SSL certificate and advanced security features', 'approved');
```

## 🔄 Backup & Restore

### **Backup Tabel**
```sql
-- Export structure dan data
mysqldump -u username -p database_name service_upgrade_requests > backup_upgrade_requests.sql
```

### **Restore Tabel**
```sql
-- Import dari backup
mysql -u username -p database_name < backup_upgrade_requests.sql
```

Dengan setup manual ini, Anda memiliki kontrol penuh atas struktur database tanpa perlu menggunakan migration Laravel!
