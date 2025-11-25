-- Create domain_extensions table
CREATE TABLE `domain_extensions` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `extension` varchar(10) NOT NULL COMMENT 'Domain extension like .com, .id, .org',
    `duration_years` int NOT NULL COMMENT 'Duration in years (1-10)',
    `price` decimal(10,2) NOT NULL COMMENT 'Price in currency',
    `description` text NULL COMMENT 'Optional description',
    `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 = active, 0 = inactive',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_extension_duration` (`extension`, `duration_years`),
    KEY `idx_extension` (`extension`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Domain extension pricing table';

-- Insert sample data
INSERT INTO `domain_extensions` (`extension`, `duration_years`, `price`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('com', 1, 150000.00, 'Domain .com untuk 1 tahun', 1, NOW(), NOW()),
('com', 2, 280000.00, 'Domain .com untuk 2 tahun', 1, NOW(), NOW()),
('com', 3, 400000.00, 'Domain .com untuk 3 tahun', 1, NOW(), NOW()),
('id', 1, 100000.00, 'Domain .id untuk 1 tahun', 1, NOW(), NOW()),
('id', 2, 180000.00, 'Domain .id untuk 2 tahun', 1, NOW(), NOW()),
('org', 1, 120000.00, 'Domain .org untuk 1 tahun', 1, NOW(), NOW()),
('net', 1, 130000.00, 'Domain .net untuk 1 tahun', 1, NOW(), NOW()),
('info', 1, 110000.00, 'Domain .info untuk 1 tahun', 1, NOW(), NOW()),
('biz', 1, 140000.00, 'Domain .biz untuk 1 tahun', 1, NOW(), NOW()),
('co', 1, 200000.00, 'Domain .co untuk 1 tahun', 1, NOW(), NOW());
