-- Update existing invoices with 'Sent' status to 'gagal'
UPDATE invoices SET status = 'gagal' WHERE status = 'Sent';

-- Show the updated records for verification
SELECT id, number, status FROM invoices WHERE status = 'gagal' ORDER BY id DESC LIMIT 10;
