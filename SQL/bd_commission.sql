-- Create table for BD monthly commissions
CREATE TABLE `bd_monthly_commission` (
  `commission_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_bd_id` int(11) NOT NULL,
  `commission_month` date NOT NULL,
  `total_sales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `payment_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`commission_id`),
  KEY `fk_bd_monthly_commission_bd_id` (`fk_bd_id`),
  CONSTRAINT `fk_bd_monthly_commission_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
