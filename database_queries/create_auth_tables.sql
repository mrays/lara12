-- =====================================================
-- Laravel Authentication Tables
-- =====================================================
-- File: create_auth_tables.sql
-- Description: Membuat semua tabel yang diperlukan untuk sistem auth Laravel
-- Date: 2025-01-25

-- =====================================================
-- 1. Password Reset Tokens Table
-- =====================================================
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) NOT NULL PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    
    INDEX idx_password_reset_tokens_email (email),
    INDEX idx_password_reset_tokens_token (token),
    INDEX idx_password_reset_tokens_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Sessions Table (jika menggunakan database sessions)
-- =====================================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    
    INDEX idx_sessions_user_id (user_id),
    INDEX idx_sessions_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. Personal Access Tokens (jika menggunakan Sanctum)
-- =====================================================
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_personal_access_tokens_tokenable (tokenable_type, tokenable_id),
    INDEX idx_personal_access_tokens_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. Cache Table (jika menggunakan database cache)
-- =====================================================
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    
    INDEX idx_cache_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. Jobs Table (jika menggunakan database queue)
-- =====================================================
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    
    INDEX idx_jobs_queue (queue),
    INDEX idx_jobs_reserved_at (reserved_at),
    INDEX idx_jobs_available_at (available_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_failed_jobs_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek semua tabel yang dibuat
-- SHOW TABLES LIKE '%password_reset_tokens%';
-- SHOW TABLES LIKE '%sessions%';
-- SHOW TABLES LIKE '%personal_access_tokens%';
-- SHOW TABLES LIKE '%cache%';
-- SHOW TABLES LIKE '%jobs%';

-- Cek struktur tabel password_reset_tokens
-- DESCRIBE password_reset_tokens;

-- =====================================================
-- Cleanup Queries (untuk maintenance)
-- =====================================================

-- Cleanup expired password reset tokens
-- DELETE FROM password_reset_tokens WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Cleanup old sessions
-- DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Cleanup expired cache
-- DELETE FROM cache WHERE expiration < UNIX_TIMESTAMP();

-- Cleanup old failed jobs
-- DELETE FROM failed_jobs WHERE failed_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
