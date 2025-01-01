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

-- Dumping data for table teves_payroll_system.teves_user_table: ~0 rows (approximately)
INSERT INTO `teves_user_table` (`user_id`, `user_name`, `user_real_name`, `user_job_title`, `user_password`, `user_type`, `user_email_address`, `user_branch_access_type`, `created_at`, `created_by_user_id`, `updated_at`, `updated_by_user_id`) VALUES
	(1262, 'admin', 'aaa', 'a', '$2y$10$3vi5KR06E1/NSLSpix9a1OsUcQ/7t4v9pejwBLuOa9jWEkXlGMS1i', 'Admin', NULL, '1', '00:00:00 00:00:00', 1, '00:00:00 00:00:00', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
