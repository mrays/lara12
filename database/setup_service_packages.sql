-- Create domain_extensions table
CREATE TABLE IF NOT EXISTS `domain_extensions` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `extension` varchar(10) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `duration_years` int(11) NOT NULL DEFAULT 1,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `domain_extensions_extension_duration_years_unique` (`extension`,`duration_years`)
);

-- Create service_packages table
CREATE TABLE IF NOT EXISTS `service_packages` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `base_price` decimal(15,2) NOT NULL,
    `features` json DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `domain_extension_id` bigint(20) UNSIGNED DEFAULT NULL,
    `domain_duration_years` int(11) DEFAULT NULL,
    `is_domain_free` tinyint(1) NOT NULL DEFAULT 0,
    `domain_discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `service_packages_domain_extension_id_foreign` (`domain_extension_id`),
    CONSTRAINT `service_packages_domain_extension_id_foreign` FOREIGN KEY (`domain_extension_id`) REFERENCES `domain_extensions` (`id`) ON DELETE SET NULL
);

-- Create service_package_free_domains table
CREATE TABLE IF NOT EXISTS `service_package_free_domains` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `service_package_id` bigint(20) UNSIGNED NOT NULL,
    `domain_extension_id` bigint(20) UNSIGNED NOT NULL,
    `duration_years` int(11) NOT NULL,
    `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
    `is_free` tinyint(1) NOT NULL DEFAULT 0,
    `sort_order` int(11) NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `service_package_free_domains_service_package_id_foreign` (`service_package_id`),
    KEY `service_package_free_domains_domain_extension_id_foreign` (`domain_extension_id`),
    CONSTRAINT `service_package_free_domains_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `service_package_free_domains_domain_extension_id_foreign` FOREIGN KEY (`domain_extension_id`) REFERENCES `domain_extensions` (`id`) ON DELETE CASCADE
);

-- Insert sample domain extensions
INSERT INTO `domain_extensions` (`extension`, `price`, `duration_years`, `is_active`, `created_at`, `updated_at`) VALUES
('.com', 150000.00, 1, 1, NOW(), NOW()),
('.id', 200000.00, 1, 1, NOW(), NOW()),
('.net', 180000.00, 1, 1, NOW(), NOW()),
('.org', 170000.00, 1, 1, NOW(), NOW());

-- Insert sample service packages
INSERT INTO `service_packages` (`name`, `description`, `base_price`, `features`, `is_active`, `created_at`, `updated_at`) VALUES
('Business Website Exclusive Type S', '2 GB storage • 5 GB monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia.', 4500000.00, '{"storage": "2 GB", "monthly_traffic": "5 GB", "websites": "1", "email_accounts": "1", "revisions": "2", "cpanel": true, "ssl": true, "domain": true}', 1, NOW(), NOW()),
('Business Website Professional Type M', '3,5 GB storage • Unlimited monthly traffic • "No Limit Sub Features" • 1 situs web • 2 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia.', 7580000.00, '{"storage": "3.5 GB", "monthly_traffic": "Unlimited", "websites": "1", "email_accounts": "2", "revisions": "3", "cpanel": true, "ssl": true, "domain": true, "sub_features": "No Limit"}', 1, NOW(), NOW()),
('Business Website Enterprise Type L', '5 GB storage • Unlimited monthly traffic • Advanced features • Multiple websites • Premium support • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Unlimited revisi • Login cPanel tersedia.', 12000000.00, '{"storage": "5 GB", "monthly_traffic": "Unlimited", "websites": "Multiple", "email_accounts": "5", "revisions": "Unlimited", "cpanel": true, "ssl": true, "domain": true, "premium_support": true}', 1, NOW(), NOW());
