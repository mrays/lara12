-- SQL Query untuk membuat tabel service_packages
-- Jalankan query ini di database MySQL/MariaDB Anda

-- 1. Create service_packages table
CREATE TABLE IF NOT EXISTS `service_packages` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `base_price` decimal(15,2) NOT NULL DEFAULT 0,
    `features` json NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `service_packages_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert default service packages based on your data
INSERT INTO `service_packages` (`name`, `description`, `base_price`, `features`, `is_active`, `created_at`, `updated_at`) VALUES
('Business Website Exclusive Type S', '2 GB storage • 5 GB monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia. (Official Website eXputra Designs)', 4500000, JSON_OBJECT("storage", "2 GB", "traffic", "5 GB monthly", "websites", 1, "email_accounts", 1, "revisions", 2, "cpanel", true, "ssl", true, "domain", true), 1, NOW(), NOW()),

('Business Website Exclusive Type M', '2,5 GB storage • Unlimited monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia. (Official Website eXputra Designs)', 5380000, JSON_OBJECT("storage", "2.5 GB", "traffic", "Unlimited", "websites", 1, "email_accounts", 1, "revisions", 2, "cpanel", true, "ssl", true, "domain", true), 1, NOW(), NOW()),

('Business Website Professional Type S', '3 GB storage • Unlimited monthly traffic • 1 situs web • 1 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia. (Official Website eXputra Designs)', 6780000, JSON_OBJECT("storage", "3 GB", "traffic", "Unlimited", "websites", 1, "email_accounts", 1, "email_type", "Pro", "revisions", 3, "cpanel", true, "ssl", true, "domain", true), 1, NOW(), NOW()),

('Business Website Professional Type M', '3,5 GB storage • Unlimited monthly traffic • "No Limit Sub Features" • 1 situs web • 2 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia. (Official Website eXputra Designs)', 7580000, JSON_OBJECT("storage", "3.5 GB", "traffic", "Unlimited", "websites", 1, "email_accounts", 2, "email_type", "Pro", "revisions", 3, "sub_features", "No Limit", "cpanel", true, "ssl", true, "domain", true), 1, NOW(), NOW()),

('Business Website Professional Type L', '6 GB storage • Unlimited monthly traffic • "No Limit Sub Features" • 1 situs web • 3 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia. (Official Website eXputra Designs)', 8580000, JSON_OBJECT("storage", "6 GB", "traffic", "Unlimited", "websites", 1, "email_accounts", 3, "email_type", "Pro", "revisions", 3, "sub_features", "No Limit", "cpanel", true, "ssl", true, "domain", true), 1, NOW(), NOW());

-- 3. Add package_id to services table (if not exists)
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `package_id` bigint(20) UNSIGNED NULL AFTER `product`;
ALTER TABLE `services` ADD COLUMN IF NOT EXISTS `custom_price` decimal(15,2) NULL AFTER `price`;

-- 4. Add foreign key constraint
ALTER TABLE `services` ADD CONSTRAINT `services_package_id_foreign` 
FOREIGN KEY (`package_id`) REFERENCES `service_packages` (`id`) ON DELETE SET NULL;

-- 5. Verify tables
DESCRIBE `service_packages`;
DESCRIBE `services`;

-- 6. Check data
SELECT id, name, base_price FROM `service_packages` LIMIT 5;
