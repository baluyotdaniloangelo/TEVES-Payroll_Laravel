-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               11.3.2-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table teves_payroll_system.activity_log
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) unsigned DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.activity_log: ~0 rows (approximately)
DELETE FROM `activity_log`;

-- Dumping structure for table teves_payroll_system.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table teves_payroll_system.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.migrations: ~7 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2024_11_03_162853_create_activity_log_table', 1),
	(6, '2024_11_03_162854_add_event_column_to_activity_log_table', 1),
	(7, '2024_11_03_162855_add_batch_uuid_column_to_activity_log_table', 1);

-- Dumping structure for table teves_payroll_system.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table teves_payroll_system.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table teves_payroll_system.teves_branch_table
CREATE TABLE IF NOT EXISTS `teves_branch_table` (
  `branch_id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(50) DEFAULT NULL,
  `branch_initial` varchar(20) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `branch_tin` varchar(50) DEFAULT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `branch_contact_number` varchar(50) DEFAULT NULL,
  `branch_owner` varchar(50) DEFAULT NULL,
  `branch_owner_title` varchar(50) DEFAULT NULL,
  `branch_logo` varchar(50) DEFAULT NULL,
  `image_reference` longblob DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`branch_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_branch_table: ~0 rows (approximately)
DELETE FROM `teves_branch_table`;

-- Dumping structure for table teves_payroll_system.teves_deduction_type_table
CREATE TABLE IF NOT EXISTS `teves_deduction_type_table` (
  `deduction_id` int(11) NOT NULL AUTO_INCREMENT,
  `deduction_description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`deduction_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_deduction_type_table: ~0 rows (approximately)
DELETE FROM `teves_deduction_type_table`;

-- Dumping structure for table teves_payroll_system.teves_department_table
CREATE TABLE IF NOT EXISTS `teves_department_table` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(20) DEFAULT NULL,
  `branch_idx` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`department_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_department_table: ~0 rows (approximately)
DELETE FROM `teves_department_table`;

-- Dumping structure for table teves_payroll_system.teves_employee_deduction_logs
CREATE TABLE IF NOT EXISTS `teves_employee_deduction_logs` (
  `deduction_logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `deduction_idx` int(11) DEFAULT NULL,
  `employee_idx` int(11) DEFAULT NULL,
  `branch_idx` int(11) DEFAULT NULL COMMENT 'Employee''s Branch Assignment',
  `department_idx` int(11) DEFAULT NULL COMMENT 'Employee''s Department Assignment',
  `deduction_date` date DEFAULT NULL,
  `deduction_amount` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`deduction_logs_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_employee_deduction_logs: ~0 rows (approximately)
DELETE FROM `teves_employee_deduction_logs`;

-- Dumping structure for table teves_payroll_system.teves_employee_logs
CREATE TABLE IF NOT EXISTS `teves_employee_logs` (
  `employee_logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_idx` int(11) DEFAULT NULL,
  `branch_idx` int(11) DEFAULT NULL COMMENT 'Employee''s Branch Assignment',
  `department_idx` int(11) DEFAULT NULL COMMENT 'Employee''s Department Assignment',
  `current_rate` double DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `override_shift` varchar(5) DEFAULT NULL,
  `log_in` datetime DEFAULT NULL,
  `breaktime_start` datetime DEFAULT NULL,
  `breaktime_end` datetime DEFAULT NULL,
  `log_out` datetime DEFAULT NULL,
  `total_hours` double DEFAULT 0,
  `total_regular_hours` double DEFAULT 0,
  `total_breaktime_hours` double DEFAULT 0,
  `total_tardiness_hours` double DEFAULT 0,
  `total_undertime_hours` double DEFAULT 0,
  `total_night_differential_hours` double DEFAULT 0 COMMENT 'Covered Hours',
  `holiday_idx` int(11) DEFAULT NULL,
  `holiday_type` int(11) DEFAULT NULL,
  `log_type` varchar(50) DEFAULT NULL COMMENT 'Regular Log(1st 8 hrs), Regular Overtime, Restdat Overtime',
  `basic_pay` double DEFAULT 0,
  `overtime_pay` double DEFAULT 0,
  `day_off_pay` double DEFAULT 0,
  `night_differential_pay` double DEFAULT 0,
  `regular_holiday_pay` double DEFAULT 0,
  `special_holiday_pay` double DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`employee_logs_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_employee_logs: ~0 rows (approximately)
DELETE FROM `teves_employee_logs`;

-- Dumping structure for table teves_payroll_system.teves_employee_table
CREATE TABLE IF NOT EXISTS `teves_employee_table` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_number` varchar(100) NOT NULL,
  `employee_last_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `employee_first_name` varchar(255) NOT NULL,
  `employee_middle_name` varchar(255) DEFAULT NULL,
  `employee_extension_name` varchar(255) DEFAULT NULL,
  `employee_full_name` varchar(255) DEFAULT NULL,
  `employee_birthday` varchar(50) NOT NULL,
  `employee_position` varchar(50) NOT NULL DEFAULT '',
  `employee_picture` longtext DEFAULT NULL,
  `employee_phone` varchar(50) DEFAULT NULL,
  `employee_email` varchar(255) DEFAULT NULL,
  `branch_idx` int(11) NOT NULL DEFAULT 0,
  `department_idx` int(11) NOT NULL DEFAULT 0,
  `employee_status` varchar(100) NOT NULL DEFAULT 'Active',
  `employee_rate` double NOT NULL DEFAULT 0,
  `sss_contribution` double NOT NULL DEFAULT 0,
  `philhealth_contribution` double NOT NULL DEFAULT 0,
  `pagibig_contribution` double NOT NULL DEFAULT 0,
  `time_in` time NOT NULL,
  `break_time_in` time NOT NULL,
  `break_time_out` time NOT NULL,
  `time_out` time NOT NULL,
  `total_shift_hours` double NOT NULL DEFAULT 0,
  `total_breaktime_hours` double NOT NULL DEFAULT 0,
  `restday_monday` int(11) NOT NULL DEFAULT 0,
  `restday_tuesday` int(11) NOT NULL DEFAULT 0,
  `restday_wednesday` int(11) NOT NULL DEFAULT 0,
  `restday_thursday` int(11) NOT NULL DEFAULT 0,
  `restday_friday` int(11) NOT NULL DEFAULT 0,
  `restday_saturday` int(11) NOT NULL DEFAULT 0,
  `restday_sunday` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by_user_idx` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`employee_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table teves_payroll_system.teves_employee_table: ~0 rows (approximately)
DELETE FROM `teves_employee_table`;

-- Dumping structure for table teves_payroll_system.teves_holiday_table
CREATE TABLE IF NOT EXISTS `teves_holiday_table` (
  `holiday_id` int(11) NOT NULL AUTO_INCREMENT,
  `holiday_description` varchar(255) DEFAULT NULL,
  `holiday_date` date DEFAULT NULL,
  `holiday_type` varchar(255) DEFAULT NULL COMMENT 'Regular, Special Non-Working, ec',
  `holiday_recursive` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`holiday_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_holiday_table: ~0 rows (approximately)
DELETE FROM `teves_holiday_table`;

-- Dumping structure for table teves_payroll_system.teves_user_table
CREATE TABLE IF NOT EXISTS `teves_user_table` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` text NOT NULL,
  `user_real_name` text NOT NULL,
  `user_job_title` text NOT NULL,
  `user_password` text NOT NULL,
  `user_type` varchar(100) NOT NULL,
  `user_email_address` varchar(100) DEFAULT NULL,
  `user_branch_access_type` varchar(100) NOT NULL COMMENT 'All or Selected',
  `created_at` text NOT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `updated_at` text NOT NULL,
  `updated_by_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1263 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_user_table: ~1 rows (approximately)
DELETE FROM `teves_user_table`;
INSERT INTO `teves_user_table` (`user_id`, `user_name`, `user_real_name`, `user_job_title`, `user_password`, `user_type`, `user_email_address`, `user_branch_access_type`, `created_at`, `created_by_user_id`, `updated_at`, `updated_by_user_id`) VALUES
	(1262, 'admin', 'aaa', 'a', '$2y$10$3vi5KR06E1/NSLSpix9a1OsUcQ/7t4v9pejwBLuOa9jWEkXlGMS1i', 'Admin', NULL, '1', '00:00:00 00:00:00', 1, '00:00:00 00:00:00', NULL);

-- Dumping structure for table teves_payroll_system.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table teves_payroll_system.users: ~0 rows (approximately)
DELETE FROM `users`;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
