# 🔄 Sistem Auto Generate Invoice untuk Service Renewal

Sistem ini secara otomatis membuat invoice untuk perpanjangan service berdasarkan billing cycle dan tanggal kedaluwarsa.

## 📋 Aturan Generate Invoice

| Billing Cycle | Waktu Generate Invoice |
|---------------|----------------------|
| **Yearly/Annual** | 3 bulan sebelum habis |
| **Monthly** | 1 minggu sebelum habis |
| **Quarterly** | 3 minggu sebelum habis |
| **Semi-Annual** | 6 minggu sebelum habis |
| **Default** | 1 minggu sebelum habis |

## 🚀 Command yang Tersedia

### 1. Check Service Renewals
```bash
# Cek service yang akan habis dalam 30 hari ke depan
php artisan services:check-renewals

# Cek service yang akan habis dalam 60 hari ke depan
php artisan services:check-renewals --days=60
```

### 2. Generate Service Invoices
```bash
# Dry run - lihat apa yang akan di-generate tanpa benar-benar membuat invoice
php artisan invoices:generate-service-renewals --dry-run

# Generate invoice untuk service yang sudah waktunya
php artisan invoices:generate-service-renewals
```

## ⏰ Scheduler Otomatis

Sistem akan otomatis berjalan setiap hari jam 9:00 pagi untuk:
- ✅ Mengecek service yang perlu di-generate invoice
- ✅ Membuat invoice otomatis
- ✅ Update next_due_date service
- ✅ Log hasil ke `storage/logs/invoice-generation.log`

## 🔧 Setup Scheduler

Untuk mengaktifkan scheduler otomatis, tambahkan cron job:

```bash
# Edit crontab
crontab -e

# Tambahkan baris ini:
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## 📊 Contoh Skenario

### Service Yearly (Rp 1,200,000/tahun)
- **Service expires**: 2025-12-31
- **Invoice generated**: 2025-09-30 (3 bulan sebelum)
- **Due date**: 2025-12-31
- **Next renewal**: 2026-12-31

### Service Monthly (Rp 100,000/bulan)
- **Service expires**: 2025-02-28
- **Invoice generated**: 2025-02-21 (1 minggu sebelum)
- **Due date**: 2025-02-28
- **Next renewal**: 2025-03-28

## 🛡️ Fitur Keamanan

- ✅ **Duplicate Prevention**: Tidak akan membuat invoice duplikat untuk periode yang sama
- ✅ **Status Check**: Hanya service dengan status 'Active' yang diproses
- ✅ **Date Validation**: Memastikan next_due_date valid sebelum generate
- ✅ **Error Handling**: Log error jika ada masalah saat generate invoice

## 📝 Log dan Monitoring

### Log File Location
```
storage/logs/invoice-generation.log
```

### Log Format
```
[2025-01-25 09:00:01] Generated invoice INV-20250125-001 for Service #123
[2025-01-25 09:00:02] Updated service #123 next_due_date to 2026-01-25
```

## 🔍 Troubleshooting

### Command tidak ditemukan
```bash
# Regenerate autoload
composer dump-autoload

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Scheduler tidak berjalan
```bash
# Test scheduler manual
php artisan schedule:run

# Cek cron job
crontab -l
```

### Invoice tidak ter-generate
```bash
# Cek service yang perlu invoice
php artisan services:check-renewals

# Test dry run
php artisan invoices:generate-service-renewals --dry-run
```

## 🎯 Customization

### Mengubah Waktu Generate Invoice

Edit file `app/Helpers/helpers.php` function `calculate_invoice_generation_date()`:

```php
case 'yearly':
    // Ubah dari 3 bulan menjadi 2 bulan
    return $expiry->copy()->subMonths(2);
```

### Mengubah Jadwal Scheduler

Edit file `app/Console/Kernel.php`:

```php
// Ubah dari jam 9 pagi menjadi jam 8 pagi
$schedule->command('invoices:generate-service-renewals')
         ->dailyAt('08:00');

// Atau jalankan 2x sehari
$schedule->command('invoices:generate-service-renewals')
         ->twiceDaily(9, 15); // 9 AM dan 3 PM
```

## 📞 Support

Jika ada masalah dengan sistem auto invoice, hubungi tim development atau cek log file untuk detail error.
