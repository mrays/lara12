-- Create service_package_free_domains table
CREATE TABLE `service_package_free_domains` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `service_package_id` bigint unsigned NOT NULL COMMENT 'Foreign key to service_packages table',
    `domain_extension_id` bigint unsigned NOT NULL COMMENT 'Foreign key to domain_extensions table',
    `duration_years` int NOT NULL COMMENT 'Domain duration in years (1-10)',
    `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Domain discount percentage (0-100)',
    `is_free` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = domain is free, 0 = normal price',
    `sort_order` int NOT NULL DEFAULT 0 COMMENT 'Order in which domain appears in UI',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_package_domain` (`service_package_id`,`domain_extension_id`),
    KEY `service_package_free_domains_service_package_id_sort_order_index` (`service_package_id`,`sort_order`),
    CONSTRAINT `service_package_free_domains_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `service_package_free_domains_domain_extension_id_foreign` FOREIGN KEY (`domain_extension_id`) REFERENCES `domain_extensions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
