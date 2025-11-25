# SQL Files for Database Setup

This directory contains SQL scripts for setting up sample data without using Laravel migrations or seeders.

## Files

### 1. `safe_sample_data.sql` ⭐ **RECOMMENDED**
- **Safe version** with table structure checking
- Only uses essential columns (no foreign keys)
- Shows table structure before insertion
- Best if you're unsure about your table schema

### 2. `simple_sample_data.sql`
- **Simple version** with no dependencies
- Creates 6 sample clients with NULL references
- Works even if domain_registers, servers, or users tables are empty
- Good for quick testing

### 3. `sample_client_data.sql`
- **Complete version** with dependencies on other tables
- Creates sample domain registers, servers, and users
- Creates 6 sample clients with various expiration scenarios
- Use this if you have the related tables set up

## Quick Start

### Option 1: Use Safe Version (Recommended)
```bash
# Execute the safe version
mysql -u username -p database_name < database/sql/safe_sample_data.sql
```

### Option 2: Laravel Application
1. Access `/admin/domain-expiration` in your browser
2. Click "Load Sample Data" button
3. The system will automatically execute the SQL file

### Option 3: Copy-Paste
1. Open `safe_sample_data.sql` in a text editor
2. Copy the content
3. Paste in phpMyAdmin, DBeaver, or MySQL Workbench
4. Execute

## Troubleshooting

### Common Errors & Solutions

#### Error: `#1054 - Unknown column 'location' in 'INSERT INTO'`
**Cause:** Column doesn't exist in your table
**Solution:** Use `safe_sample_data.sql` - it checks table structure first

#### Error: `#1146 - Table 'database.client_data' doesn't exist`
**Cause:** The client_data table doesn't exist
**Solution:** Create the table first, then run the SQL

#### Error: `#1364 - Field 'id' doesn't have a default value`
**Cause:** ID column is not auto-increment
**Solution:** Add id column to INSERT or make it auto-increment

### Check Your Table Structure
If you're getting column errors, run this first:
```sql
DESCRIBE client_data;
```

Or use the safe version which shows the structure automatically.

## Sample Data Scenarios

The SQL creates 6 sample clients with different expiration scenarios:

| Client | Domain Status | Days Until | Status |
|--------|----------------|------------|---------|
| PT. Teknologi Maju | Expiring Soon | 15 days | Warning |
| CV. Karya Digital | Expired | -5 days | Expired |
| UD. Jaya Abadi | Safe | 6 months | Active |
| PT. Solusi Bisnis | Critical | 3 days | Warning |
| CV. Media Kreatif | Safe | 4 months | Active |
| PT. Inovasi Teknologi | Expired | -15 days | Expired |

## Date Calculations

The SQL uses MySQL date functions to calculate relative dates:

- `DATE_ADD(CURDATE(), INTERVAL 15 DAY)` - 15 days from now
- `DATE_SUB(CURDATE(), INTERVAL 5 DAY)` - 5 days ago
- `DATE_ADD(CURDATE(), INTERVAL 6 MONTH)` - 6 months from now

## Expected Output

After successful execution, you should see:

```
+--------------------------------------+
| table_check                           |
+--------------------------------------+
| OK: client_data table found.          |
+--------------------------------------+

+--------------------------------------+
| message                               |
+--------------------------------------+
| Sample Data Created Successfully!     |
+--------------------------------------+

+--------------+----------------+----------------+-------------+
| total_clients| expired_domains| expiring_soon  | safe_domains |
+--------------+----------------+----------------+-------------+
| 6            | 2              | 2              | 2           |
+--------------+----------------+----------------+-------------+
```

## SQL Features

### Safe Version (`safe_sample_data.sql`)
- ✅ Table existence check
- ✅ Shows table structure
- ✅ Only uses essential columns
- ✅ No foreign key dependencies
- ✅ Error prevention

### Simple Version (`simple_sample_data.sql`)
- ✅ No dependencies
- ✅ NULL foreign keys
- ✅ Quick execution
- ✅ Basic statistics

### Complete Version (`sample_client_data.sql`)
- ✅ Creates reference data
- ✅ Full relationships
- ✅ Complete setup
- ⚠️ Requires all tables

## Notes

- Uses `INSERT IGNORE` to prevent duplicate entries
- All dates are calculated relative to current date
- Compatible with MySQL 5.7+ and MariaDB 10.2+
- Safe version works with any table structure

## Still Having Issues?

1. **Check table exists:** `SHOW TABLES LIKE 'client_data';`
2. **Check columns:** `DESCRIBE client_data;`
3. **Use safe version:** It will show you exactly what columns exist
4. **Create table first:** If table doesn't exist, create it based on Laravel migration
