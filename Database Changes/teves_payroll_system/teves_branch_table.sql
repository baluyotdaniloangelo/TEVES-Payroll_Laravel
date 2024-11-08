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
  `updated_by_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`branch_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_branch_table: ~1 rows (approximately)
INSERT INTO `teves_branch_table` (`branch_id`, `branch_code`, `branch_initial`, `branch_name`, `branch_tin`, `branch_address`, `branch_contact_number`, `branch_owner`, `branch_owner_title`, `branch_logo`, `image_reference`, `created_at`, `created_by_user_idx`, `updated_at`, `updated_by_user_id`) VALUES
	(1, '111', '111', '111', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
