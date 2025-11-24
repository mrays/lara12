# 🧪 Testing Auto Invoice System

## 📝 Manual Testing Steps

### 1. Persiapan Test Data

Pastikan ada service dengan data berikut di database:

```sql
-- Service dengan billing yearly (akan generate invoice 3 bulan sebelum habis)
UPDATE services SET 
    next_due_date = '2025-04-25',  -- 3 bulan dari sekarang
    billing_cycle = 'yearly',
    status = 'Active'
WHERE id = 1;

-- Service dengan billing monthly (akan generate invoice 1 minggu sebelum habis)
UPDATE services SET 
    next_due_date = '2025-02-01',  -- 1 minggu dari sekarang
    billing_cycle = 'monthly',
    status = 'Active'
WHERE id = 2;
```

### 2. Test Commands

#### Cek Service yang Perlu Renewal
```bash
php artisan services:check-renewals
php artisan services:check-renewals --days=90
```

**Expected Output:**
```
🔍 Checking services expiring between 2025-01-25 and 2025-04-25

📋 Found 2 service(s) expiring soon:

+------------+----------+--------+---------------+------------+--------------+-------------------+------------------+
| Service ID | Product  | Client | Billing Cycle | Expires    | Invoice Date | Days Until Invoice| Status           |
+------------+----------+--------+---------------+------------+--------------+-------------------+------------------+
| 1          | Website  | John   | Yearly        | 2025-04-25 | 2025-01-25   | 0                 | 🔥 Generate today! |
| 2          | Hosting  | Jane   | Monthly       | 2025-02-01 | 2025-01-25   | 0                 | 🔥 Generate today! |
+------------+----------+--------+---------------+------------+--------------+-------------------+------------------+

📊 Summary:
   • Services needing invoice today: 2
   • Services needing invoice within 7 days: 0
```

#### Test Dry Run
```bash
php artisan invoices:generate-service-renewals --dry-run
```

**Expected Output:**
```
🔍 Checking for services that need invoice generation...
📋 Found 2 service(s) that need invoice generation:
   • Service #1 - Website (Client: John Doe)
     Billing: yearly | Expires: 2025-04-25
     🔍 [DRY RUN] Would generate invoice for this service

   • Service #2 - Hosting (Client: Jane Smith)
     Billing: monthly | Expires: 2025-02-01
     🔍 [DRY RUN] Would generate invoice for this service

🔍 DRY RUN: 2 invoice(s) would be generated
```

#### Generate Invoice Actual
```bash
php artisan invoices:generate-service-renewals
```

**Expected Output:**
```
🔍 Checking for services that need invoice generation...
📋 Found 2 service(s) that need invoice generation:
   • Service #1 - Website (Client: John Doe)
     Billing: yearly | Expires: 2025-04-25
     ✅ Invoice #INV-20250125-001 generated successfully

   • Service #2 - Hosting (Client: Jane Smith)
     Billing: monthly | Expires: 2025-02-01
     ✅ Invoice #INV-20250125-002 generated successfully

✅ Successfully generated 2 invoice(s)
```

### 3. Validasi Database

#### Cek Invoice yang Dibuat
```sql
SELECT 
    i.number,
    i.title,
    i.amount,
    i.due_date,
    i.status,
    s.product,
    u.name as client_name
FROM invoices i
JOIN services s ON i.service_id = s.id
JOIN users u ON i.client_id = u.id
WHERE i.created_at >= CURDATE()
ORDER BY i.created_at DESC;
```

#### Cek Update Service Next Due Date
```sql
SELECT 
    id,
    product,
    billing_cycle,
    next_due_date,
    updated_at
FROM services 
WHERE updated_at >= CURDATE()
ORDER BY updated_at DESC;
```

**Expected Results:**
- Service yearly: next_due_date berubah dari 2025-04-25 menjadi 2026-04-25
- Service monthly: next_due_date berubah dari 2025-02-01 menjadi 2025-03-01

### 4. Test Edge Cases

#### Test Service Tanpa Next Due Date
```sql
UPDATE services SET next_due_date = NULL WHERE id = 3;
```
Command tidak boleh memproses service ini.

#### Test Service Inactive
```sql
UPDATE services SET status = 'Suspended' WHERE id = 4;
```
Command tidak boleh memproses service ini.

#### Test Invoice Sudah Ada
Jalankan command 2x, pastikan tidak ada duplicate invoice.

### 5. Test Scheduler

#### Test Manual Schedule Run
```bash
php artisan schedule:run
```

#### Test Specific Time
```bash
php artisan schedule:test
```

## 🔍 Debugging

### Cek Log
```bash
tail -f storage/logs/invoice-generation.log
tail -f storage/logs/laravel.log
```

### Cek Helper Functions
```php
// Test di tinker
php artisan tinker

// Test calculate_invoice_generation_date
$date = calculate_invoice_generation_date('2025-12-31', 'yearly');
echo $date; // Should be 2025-09-30

$date = calculate_invoice_generation_date('2025-02-28', 'monthly');
echo $date; // Should be 2025-02-21
```

## ✅ Test Checklist

- [ ] Command `services:check-renewals` berjalan tanpa error
- [ ] Command menampilkan service yang benar
- [ ] Dry run menampilkan preview yang akurat
- [ ] Generate invoice membuat invoice dengan benar
- [ ] Service next_due_date ter-update dengan benar
- [ ] Tidak ada duplicate invoice
- [ ] Service inactive/tanpa due date tidak diproses
- [ ] Scheduler berjalan otomatis
- [ ] Log file ter-generate dengan benar
- [ ] Helper functions menghitung tanggal dengan benar

## 🚨 Common Issues

### Issue: Command not found
**Solution:**
```bash
composer dump-autoload
php artisan cache:clear
```

### Issue: Helper function not found
**Solution:**
Pastikan `app/Helpers/helpers.php` sudah di-autoload di `composer.json`:
```json
"autoload": {
    "files": [
        "app/Helpers/helpers.php"
    ]
}
```

### Issue: Scheduler tidak berjalan
**Solution:**
```bash
# Setup cron job
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```
