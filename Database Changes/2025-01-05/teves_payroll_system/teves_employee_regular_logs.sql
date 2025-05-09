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

-- Dumping structure for table teves_payroll_system.teves_employee_regular_logs
CREATE TABLE IF NOT EXISTS `teves_employee_regular_logs` (
  `regular_logs_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_idx` int(11) DEFAULT NULL,
  `log_in` datetime DEFAULT NULL,
  `breaktime_start` datetime DEFAULT NULL,
  `breaktime_end` datetime DEFAULT NULL,
  `log_out` datetime DEFAULT NULL,
  `total_hours` int(11) DEFAULT NULL,
  `total_regular_hours` int(11) DEFAULT NULL,
  `total_breaktime_hours` int(11) DEFAULT NULL,
  `total_tardiness_hours` int(11) DEFAULT NULL,
  `total_undertime_hours` int(11) DEFAULT NULL,
  `total_night_differential_hours` int(11) DEFAULT NULL,
  `holiday_type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by_user_idx` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by_user_idx` int(11) DEFAULT NULL,
  PRIMARY KEY (`regular_logs_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

-- Dumping data for table teves_payroll_system.teves_employee_regular_logs: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
