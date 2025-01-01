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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_holiday_table: ~9 rows (approximately)
INSERT INTO `teves_holiday_table` (`holiday_id`, `holiday_description`, `holiday_date`, `holiday_type`, `holiday_recursive`, `created_at`, `created_by_user_idx`, `updated_at`, `updated_by_user_idx`) VALUES
	(13, 'New Year\'s Day', '2025-01-01', 'Regular Holiday', 1, NULL, NULL, NULL, NULL),
	(16, 'rewtresf546t', '2024-12-11', 'Special Non-Working Holiday', NULL, '2024-12-27 23:08:03', 1262, '2024-12-27 23:08:03', NULL),
	(18, 'yhgfhfh', '2024-12-27', 'Regular Holiday', NULL, '2024-12-27 23:24:28', 1262, '2024-12-27 23:24:28', NULL),
	(20, 'tret', '2024-12-30', 'Regular Holiday', NULL, '2024-12-27 23:37:13', 1262, '2024-12-27 23:37:13', NULL),
	(21, 'rer', '2024-12-13', 'Regular Holiday', NULL, '2024-12-27 23:37:58', 1262, '2024-12-27 23:37:58', NULL),
	(22, 'iytu', '2024-12-19', 'Regular Holiday', NULL, '2024-12-27 23:44:41', 1262, '2024-12-27 23:44:41', NULL),
	(24, 'rewtrewt43tfg', '2024-12-04', 'Regular Holiday', NULL, '2024-12-28 00:10:12', 1262, '2024-12-28 00:10:12', NULL),
	(25, 'treterg', '2024-12-06', 'Regular Holiday', NULL, '2024-12-28 00:12:40', 1262, '2024-12-28 00:12:40', NULL),
	(27, '879854965', '2024-12-26', 'Regular Holiday', NULL, '2024-12-28 00:32:39', 1262, '2024-12-28 00:32:39', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
