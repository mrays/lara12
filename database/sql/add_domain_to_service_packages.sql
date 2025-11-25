-- Add domain integration to service_packages table
ALTER TABLE `service_packages` 
ADD COLUMN `domain_extension_id` bigint unsigned NULL COMMENT 'Foreign key to domain_extensions table for promo domain',
ADD COLUMN `domain_duration_years` int NULL COMMENT 'Domain duration in years for promo (1-10)',
ADD COLUMN `is_domain_free` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = domain is free, 0 = normal price',
ADD COLUMN `domain_discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Domain discount percentage (0-100)',
ADD INDEX `idx_domain_extension` (`domain_extension_id`);

-- Add foreign key constraint
ALTER TABLE `service_packages` 
ADD CONSTRAINT `fk_service_packages_domain_extension` 
FOREIGN KEY (`domain_extension_id`) 
REFERENCES `domain_extensions` (`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- Update some sample packages to include domain promo
UPDATE `service_packages` SET 
    `domain_extension_id` = (SELECT id FROM `domain_extensions` WHERE `extension` = 'com' AND `duration_years` = 1 LIMIT 1),
    `domain_duration_years` = 1,
    `is_domain_free` = 1,
    `domain_discount_percent` = 100.00
WHERE `name` LIKE '%Professional%' OR `name` LIKE '%Business%' LIMIT 2;
