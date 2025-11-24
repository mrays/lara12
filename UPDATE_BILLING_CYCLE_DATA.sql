-- Update Billing Cycle Data to New Format
-- Run this SQL to convert existing billing cycle data to new enum format

-- Update existing data to match new enum values
UPDATE services 
SET billing_cycle = CASE 
    WHEN billing_cycle IN ('Monthly', 'monthly', '1 month', '1month') THEN '1 Bulan'
    WHEN billing_cycle IN ('Quarterly', 'quarterly', '3 months', '3month') THEN '3 Bulan'
    WHEN billing_cycle IN ('Semi-Annually', 'semi-annually', '6 months', '6month') THEN '6 Bulan'
    WHEN billing_cycle IN ('Annually', 'annually', '1 year', '1year', 'yearly') THEN '1 Tahun'
    WHEN billing_cycle IN ('Biennially', 'biennially', '2 years', '2year') THEN '2 Tahun'
    WHEN billing_cycle IN ('One Time', 'onetime', 'one-time') THEN '1 Bulan' -- Convert one-time to monthly
    ELSE billing_cycle -- Keep existing if not matched
END
WHERE billing_cycle IS NOT NULL;

-- Check what billing cycles exist after update
SELECT billing_cycle, COUNT(*) as count 
FROM services 
WHERE billing_cycle IS NOT NULL 
GROUP BY billing_cycle 
ORDER BY count DESC;

-- Optional: Set NULL billing cycles to default
UPDATE services 
SET billing_cycle = '1 Tahun' 
WHERE billing_cycle IS NULL OR billing_cycle = '';
