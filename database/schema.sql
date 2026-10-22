CREATE TABLE IF NOT EXISTS `contact_leads` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(254) NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `message` TEXT NOT NULL,
    `contact_consent` TINYINT(1) NOT NULL DEFAULT 1,
    `contact_consent_at` DATETIME NOT NULL,
    `marketing_consent` TINYINT(1) NOT NULL DEFAULT 0,
    `marketing_consent_at` DATETIME NULL DEFAULT NULL,
    `email_notification_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_contact_leads_email` (`email`),
    KEY `idx_contact_leads_created_at` (`created_at`),
    KEY `idx_contact_leads_marketing_created` (`marketing_consent`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
