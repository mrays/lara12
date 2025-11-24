# Manual Update Billing Cycle Guide

## 📋 **Langkah Manual Update:**

### **1. Cek Data yang Ada:**
```sql
-- Lihat billing cycle yang ada saat ini
SELECT billing_cycle, COUNT(*) as count 
FROM services 
WHERE billing_cycle IS NOT NULL 
GROUP BY billing_cycle 
ORDER BY count DESC;
```

### **2. Update via Admin Panel:**
1. **Buka** `/admin/services`
2. **Klik Edit** pada setiap service
3. **Pilih Billing Cycle** dari dropdown baru
4. **Save** perubahan

### **3. Bulk Update via Database:**
```sql
-- Update semua Monthly ke 1 Bulan
UPDATE services SET billing_cycle = '1 Bulan' WHERE billing_cycle = 'Monthly';

-- Update semua Quarterly ke 3 Bulan  
UPDATE services SET billing_cycle = '3 Bulan' WHERE billing_cycle = 'Quarterly';

-- Update semua Semi-Annually ke 6 Bulan
UPDATE services SET billing_cycle = '6 Bulan' WHERE billing_cycle = 'Semi-Annually';

-- Update semua Annually ke 1 Tahun
UPDATE services SET billing_cycle = '1 Tahun' WHERE billing_cycle = 'Annually';

-- Update semua Biennially ke 2 Tahun
UPDATE services SET billing_cycle = '2 Tahun' WHERE billing_cycle = 'Biennially';
```

## 🎯 **Mapping Old → New:**

| **Old Value** | **New Value** |
|---------------|---------------|
| Monthly | 1 Bulan |
| Quarterly | 3 Bulan |
| Semi-Annually | 6 Bulan |
| Annually | 1 Tahun |
| Biennially | 2 Tahun |
| One Time | 1 Bulan |
| NULL/Empty | 1 Tahun (default) |

## ✅ **Verification:**
```sql
-- Cek hasil update
SELECT billing_cycle, COUNT(*) as count 
FROM services 
GROUP BY billing_cycle 
ORDER BY count DESC;

-- Pastikan tidak ada old values
SELECT * FROM services 
WHERE billing_cycle IN ('Monthly', 'Quarterly', 'Semi-Annually', 'Annually', 'Biennially', 'One Time');
```
