-- Fix Invoice Foreign Key - Simple Version (No information_schema access needed)
-- Run this SQL to fix foreign key constraint without checking information_schema

-- Step 1: Try to drop existing foreign key (ignore error if doesn't exist)
-- Note: Replace 'invoices_client_id_foreign' with actual constraint name if different
SET foreign_key_checks = 0;

-- Step 2: Drop foreign key constraint (try common constraint names)
ALTER TABLE invoices DROP FOREIGN KEY invoices_client_id_foreign;
-- If above fails, try these alternatives:
-- ALTER TABLE invoices DROP FOREIGN KEY fk_invoices_client_id;
-- ALTER TABLE invoices DROP FOREIGN KEY invoices_ibfk_1;

-- Step 3: Add new foreign key constraint pointing to users table
ALTER TABLE invoices 
ADD CONSTRAINT invoices_client_id_foreign 
FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE;

-- Step 4: Re-enable foreign key checks
SET foreign_key_checks = 1;

-- Step 5: Test if client_id = 8 exists in users table
SELECT id, name, email, role FROM users WHERE id = 8;

-- Step 6: If client doesn't exist, list available clients
SELECT id, name, email, role FROM users WHERE role = 'client' ORDER BY id;
