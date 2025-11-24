-- Fix Foreign Key Constraint Issue
-- The services table has foreign key pointing to 'clients' table but we're using 'users' table

-- Step 1: Check current foreign key constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'cloud' 
AND TABLE_NAME = 'services' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Step 2: Drop the existing foreign key constraint
ALTER TABLE services 
DROP FOREIGN KEY services_client_id_foreign;

-- Step 3: Add new foreign key constraint pointing to users table
ALTER TABLE services 
ADD CONSTRAINT services_client_id_foreign 
FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE;

-- Step 4: Verify the change
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'cloud' 
AND TABLE_NAME = 'services' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Step 5: Check if client_id = 8 exists in users table
SELECT id, name, email, role FROM users WHERE id = 8;

-- Step 6: If client doesn't exist, check available clients
SELECT id, name, email, role FROM users WHERE role = 'client' ORDER BY id;
