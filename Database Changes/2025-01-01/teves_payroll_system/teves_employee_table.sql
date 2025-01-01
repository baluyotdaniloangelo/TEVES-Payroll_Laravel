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

-- Dumping structure for table teves_payroll_system.teves_employee_table
CREATE TABLE IF NOT EXISTS `teves_employee_table` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_number` varchar(100) NOT NULL,
  `employee_last_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `employee_first_name` varchar(255) NOT NULL,
  `employee_middle_name` varchar(255) DEFAULT NULL,
  `employee_extension_name` varchar(255) DEFAULT NULL,
  `employee_birthday` varchar(50) NOT NULL,
  `employee_position` varchar(50) NOT NULL DEFAULT '',
  `employee_picture` longtext DEFAULT NULL,
  `employee_phone` varchar(50) DEFAULT NULL,
  `employee_email` varchar(255) DEFAULT NULL,
  `branch_idx` int(11) NOT NULL DEFAULT 0,
  `department_idx` int(11) NOT NULL DEFAULT 0,
  `employee_status` varchar(100) NOT NULL DEFAULT 'Active',
  `sss_contribution` double NOT NULL DEFAULT 0,
  `philhealth_contribution` double NOT NULL DEFAULT 0,
  `pagibig_contribution` double NOT NULL DEFAULT 0,
  `time_in` time NOT NULL,
  `break_time_in` time NOT NULL,
  `break_time_out` time NOT NULL,
  `time_out` time NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Dumping data for table teves_payroll_system.teves_employee_table: ~5 rows (approximately)
INSERT INTO `teves_employee_table` (`employee_id`, `employee_number`, `employee_last_name`, `employee_first_name`, `employee_middle_name`, `employee_extension_name`, `employee_birthday`, `employee_position`, `employee_picture`, `employee_phone`, `employee_email`, `branch_idx`, `department_idx`, `employee_status`, `sss_contribution`, `philhealth_contribution`, `pagibig_contribution`, `time_in`, `break_time_in`, `break_time_out`, `time_out`, `restday_monday`, `restday_tuesday`, `restday_wednesday`, `restday_thursday`, `restday_friday`, `restday_saturday`, `restday_sunday`, `created_at`, `created_by_user_idx`, `updated_at`, `updated_by_user_idx`) VALUES
	(126, 'DANY-0001', 'Baluyot', 'Danilo Angelo', 'Relingado', 'NA', '03-08-1992', 'SA', NULL, '09266213790', 'dsadsa@gmail.com', 1, 1, 'Active', 0, 0, 0, '08:00:00', '12:00:00', '00:00:00', '05:00:00', 0, 0, 0, 0, 0, 1, 1, '2024-12-15 08:08:25', 1262, '2024-12-16 03:23:08', 1262),
	(128, '546456', 'DANY-0001', 'Danilo Angelo', 'Relingado', NULL, '16-12-2024', '5464564564', NULL, NULL, NULL, 1, 1, 'Active', 0, 0, 0, '08:00:00', '12:00:00', '00:00:00', '17:00:00', 0, 0, 0, 0, 0, 0, 0, '2024-12-16 09:28:20', 1262, '2024-12-16 09:28:20', NULL),
	(129, '435trsgd', 'rewrew', 'rewrew', 'rewra', 'esares', '27-12-2024', 'fdgers', NULL, '46456546', 'fesafds', 1, 12, 'Active', 0, 0, 0, '08:00:00', '12:00:00', '00:00:00', '17:00:00', 0, 0, 0, 0, 0, 0, 0, '2024-12-27 15:04:42', 1262, '2024-12-27 15:04:42', NULL),
	(130, '5435435', 'Baluyot', 'Danilo Angelo', 'Relingado', NULL, '2025-01-01', '3243243r', NULL, '543543253767443', 'dsadsad@gmail.com', 3, 12, 'Active', 0, 0, 0, '09:00:00', '01:00:00', '02:00:00', '06:00:00', 0, 0, 0, 0, 0, 1, 1, '2025-01-01 13:10:45', 1262, '2025-01-01 13:13:47', 1262),
	(131, '43253254', '545343', '5435', '54354343', '5345', '2025-01-02', '32423432', NULL, '345', '5435345@fdsfeya.com', 4, 12, 'Active', 0, 0, 0, '08:00:00', '13:00:00', '14:00:00', '18:00:00', 0, 0, 0, 0, 0, 1, 1, '2025-01-01 13:12:41', 1262, '2025-01-01 13:13:34', 1262);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
