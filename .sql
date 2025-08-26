-- UPDATE learner
-- SET name = 'Jahid Vai Pro',
--     email = 'Admin@gmail.com',
--     phone = '+8801000000000'
-- WHERE id = 4;



-- DELETE FROM course_progress
-- WHERE id = 4;


-- DELETE FROM course_progress
-- WHERE id IN (4, 13, 14, 15);

-- DELETE FROM certificate
-- WHERE id in (13);


-- UPDATE course_progress
-- SET permodule = 0,
--     perquestion = 0,
--     progress = 0,
--     mark = 0,
--     total_mark = 0,
--     is_completed = 0
-- WHERE id = 5;

-- Create a Table (TEXT)
-- ALTER TABLE payment
-- ADD COLUMN status TEXT NULL AFTER payment_type;

-- Create a Table (BIGINT)
-- ALTER TABLE payment
-- ADD COLUMN transaction_id BIGINT NULL AFTER status;


-- Delete a data of table
-- DELETE FROM payment;
-- DELETE FROM learner;
-- DELETE FROM course_progress;

-- Drop a Table Form Databe
-- DROP TABLE IF EXISTS `payment`;


-- CREATE TABLE `payment` (
--     `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     `payment_unique_id` VARCHAR(255) NOT NULL UNIQUE,
--     `learner_secret_id` VARCHAR(255) NULL,
--     `name` VARCHAR(255) NOT NULL,
--     `email` VARCHAR(255) NULL,
--     `phone` VARCHAR(255) NULL,
--     `city` VARCHAR(255) NULL,
--     `address` VARCHAR(255) NULL,
--     `postal_code` VARCHAR(255) NULL,
--     `payment_type` VARCHAR(255) NULL,
--     `country` VARCHAR(255) NULL,
--     `course_unique_id` VARCHAR(255) NULL,
--     `whom` VARCHAR(255) NULL,
--     `quantity` INT NULL,
--     `course_title` VARCHAR(255) NULL,
--     `price` DECIMAL(10,2) NULL,
--     `media` VARCHAR(255) NULL,
--     `message` TEXT NULL,
--     `status` VARCHAR(255) NULL,
--     `transaction_id` VARCHAR(255) NULL,
--     `account_id` VARCHAR(255) NULL,
--     `paypal_email` VARCHAR(255) NULL,
--     `selected_location` VARCHAR(255) NULL,
--     `selected_date` VARCHAR(255) NULL,
--     `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
--     `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Create Table 
-- CREATE TABLE `course_section` (
--     `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     `course_unique_id` BIGINT UNSIGNED NOT NULL,
--     `section_unique_id` BIGINT UNSIGNED NOT NULL,
--     `created_at` TIMESTAMP NULL DEFAULT NULL,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





-- CREATE TABLE `course` (
--     `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     `unique_id` BIGINT UNSIGNED NOT NULL,
--     `title` VARCHAR(255) NOT NULL,
--     `meta_title` VARCHAR(255) DEFAULT NULL,
--     `meta_keywords` TEXT DEFAULT NULL,
--     `meta_description` TEXT DEFAULT NULL,
--     `slug` VARCHAR(255) UNIQUE NOT NULL,
--     `canonical_url` VARCHAR(255) DEFAULT NULL,
--     `robots_meta` VARCHAR(50) DEFAULT NULL,
--     `schema_markup` JSON DEFAULT NULL,
--     `description` LONGTEXT DEFAULT NULL,
--     `duration` VARCHAR(100) DEFAULT NULL,
--     `enroll` INT UNSIGNED DEFAULT 0,
--     `price` DECIMAL(10,2) DEFAULT 0.00,
--     `image` VARCHAR(255) DEFAULT NULL,
--     `week` TEXT DEFAULT NULL,
--     `created_at` TIMESTAMP NULL DEFAULT NULL,
--     `updated_at` TIMESTAMP NULL DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ALTER TABLE learner 
-- ADD COLUMN login_token VARCHAR(255) NULL UNIQUE AFTER password;