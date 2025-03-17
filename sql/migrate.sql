drop TABLE IF EXISTS `standards`;
CREATE TABLE `standards` (
                           `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                           `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `description` text COLLATE utf8mb4_unicode_ci NULL,
                           `is_active` tinyint(1) NOT NULL DEFAULT '1',
                           `created_at` timestamp NULL DEFAULT NULL,
                           `updated_at` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `standards_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `standard_revisions`;
CREATE TABLE `standard_revisions` (
                                    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                                    `standard_id` bigint(20) UNSIGNED NOT NULL,
                                    `revision_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `revision_date` date NOT NULL,
                                    `revision_notes` text COLLATE utf8mb4_unicode_ci NULL,
                                    `docx_file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                    `is_current` tinyint(1) NOT NULL DEFAULT '0',
                                    `created_at` timestamp NULL DEFAULT NULL,
                                    `updated_at` timestamp NULL DEFAULT NULL,
                                    PRIMARY KEY (`id`),
                                    KEY `standard_revisions_standard_id_foreign` (`standard_id`),
                                    CONSTRAINT `standard_revisions_standard_id_foreign` FOREIGN KEY (`standard_id`) REFERENCES `standards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `common_sections`;
CREATE TABLE `common_sections` (
                                 `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                                 `standard_revision_id` bigint(20) UNSIGNED NOT NULL,
                                 `section_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `section_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `content` text COLLATE utf8mb4_unicode_ci NULL,
                                 `display_order` int(11) NOT NULL,
                                 `created_at` timestamp NULL DEFAULT NULL,
                                 `updated_at` timestamp NULL DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 KEY `common_sections_standard_revision_id_foreign` (`standard_revision_id`),
                                 CONSTRAINT `common_sections_standard_revision_id_foreign` FOREIGN KEY (`standard_revision_id`) REFERENCES `standard_revisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `standard_sections`;
CREATE TABLE `standard_sections` (
                                   `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `standard_revision_id` bigint(20) UNSIGNED NOT NULL,
                                   `clause_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `clause_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                   `description` text COLLATE utf8mb4_unicode_ci NULL,
                                   `display_order` int(11) NOT NULL,
                                   `is_mandatory` tinyint(1) NOT NULL DEFAULT '1',
                                   `created_at` timestamp NULL DEFAULT NULL,
                                   `updated_at` timestamp NULL DEFAULT NULL,
                                   PRIMARY KEY (`id`),
                                   KEY `standard_sections_standard_revision_id_foreign` (`standard_revision_id`),
                                   CONSTRAINT `standard_sections_standard_revision_id_foreign` FOREIGN KEY (`standard_revision_id`) REFERENCES `standard_revisions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
                           `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                           `standard_section_id` bigint(20) UNSIGNED NOT NULL,
                           `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
                           `question_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `options` text COLLATE utf8mb4_unicode_ci NULL,
                           `is_required` tinyint(1) NOT NULL DEFAULT '1',
                           `display_order` int(11) NOT NULL,
                           `created_at` timestamp NULL DEFAULT NULL,
                           `updated_at` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `questions_standard_section_id_foreign` (`standard_section_id`),
                           CONSTRAINT `questions_standard_section_id_foreign` FOREIGN KEY (`standard_section_id`) REFERENCES `standard_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `audits`;
CREATE TABLE `audits` (
                        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `user_id` bigint(20) UNSIGNED NOT NULL,
                        `standard_revision_id` bigint(20) UNSIGNED NOT NULL,
                        `plan_no` varchar(255) COLLATE utf8mb4_unicode_ci NULL,
                        `audit_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `audit_date` date NOT NULL,
                        `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `created_at` timestamp NULL DEFAULT NULL,
                        `updated_at` timestamp NULL DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `audits_uuid_unique` (`uuid`),
                        KEY `audits_user_id_foreign` (`user_id`),
                        KEY `audits_standard_revision_id_foreign` (`standard_revision_id`),
                        CONSTRAINT `audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
                        CONSTRAINT `audits_standard_revision_id_foreign` FOREIGN KEY (`standard_revision_id`) REFERENCES `standard_revisions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `responses`;
CREATE TABLE `responses` (
                           `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                           `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `audit_id` bigint(20) UNSIGNED NOT NULL,
                           `question_id` bigint(20) UNSIGNED NOT NULL,
                           `response_text` text COLLATE utf8mb4_unicode_ci NULL,
                           `is_compliant` tinyint(1) NULL,
                           `evidence` text COLLATE utf8mb4_unicode_ci NULL,
                           `is_synced` tinyint(1) NOT NULL DEFAULT '1',
                           `sync_timestamp` timestamp NULL DEFAULT NULL,
                           `created_at` timestamp NULL DEFAULT NULL,
                           `updated_at` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `responses_uuid_unique` (`uuid`),
                           KEY `responses_audit_id_foreign` (`audit_id`),
                           KEY `responses_question_id_foreign` (`question_id`),
                           CONSTRAINT `responses_audit_id_foreign` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE,
                           CONSTRAINT `responses_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

drop TABLE IF EXISTS `nonconformities`;
CREATE TABLE `nonconformities` (
                                 `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                                 `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `audit_id` bigint(20) UNSIGNED NOT NULL,
                                 `standard_section_id` bigint(20) UNSIGNED NOT NULL,
                                 `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `correction` text COLLATE utf8mb4_unicode_ci NULL,
                                 `corrective_action` text COLLATE utf8mb4_unicode_ci NULL,
                                 `due_date` date NULL DEFAULT NULL,
                                 `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                 `is_synced` tinyint(1) NOT NULL DEFAULT '1',
                                 `sync_timestamp` timestamp NULL DEFAULT NULL,
                                 `created_at` timestamp NULL DEFAULT NULL,
                                 `updated_at` timestamp NULL DEFAULT NULL,
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `nonconformities_uuid_unique` (`uuid`),
                                 KEY `nonconformities_audit_id_foreign` (`audit_id`),
                                 KEY `nonconformities_standard_section_id_foreign` (`standard_section_id`),
                                 CONSTRAINT `nonconformities_audit_id_foreign` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE,
                                 CONSTRAINT `nonconformities_standard_section_id_foreign` FOREIGN KEY (`standard_section_id`) REFERENCES `standard_sections` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
