-- Check Database Structure for Clients vs Users

-- 1. Check if 'clients' table exists
SHOW TABLES LIKE 'clients';

-- 2. Check if 'users' table exists  
SHOW TABLES LIKE 'users';

-- 3. If clients table exists, check its structure
DESCRIBE clients;

-- 4. Check users table structure
DESCRIBE users;

-- 5. Check current services foreign key
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'services' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 6. Check if client_id = 8 exists in users table
SELECT id, name, email, role FROM users WHERE id = 8;

-- 7. Check if client_id = 8 exists in clients table (if exists)
-- SELECT id, name, email FROM clients WHERE id = 8;

-- 8. List all users with role 'client'
SELECT id, name, email, role FROM users WHERE role = 'client' ORDER BY id;

-- 9. Check current services data
SELECT id, client_id, product, status FROM services LIMIT 5;
