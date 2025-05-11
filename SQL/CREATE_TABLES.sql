-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 11, 2025 at 10:30 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u650672385_PhoneMate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `admin_role` varchar(50) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `fk_wh_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification`
--

CREATE TABLE `admin_notification` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `is_read` varchar(50) NOT NULL DEFAULT 'unread',
  `admin_role` varchar(50) NOT NULL,
  `fk_wh_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bd_notification`
--

CREATE TABLE `bd_notification` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `is_read` varchar(50) NOT NULL DEFAULT 'unread',
  `fk_bd_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bd_payment_details`
--

CREATE TABLE `bd_payment_details` (
  `bdp_id` int(11) NOT NULL,
  `bdp_amount` int(11) NOT NULL,
  `bdp_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_bd_id` int(11) NOT NULL,
  `fk_delivery_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bd_payment_records`
--

CREATE TABLE `bd_payment_records` (
  `bdr_id` int(11) NOT NULL,
  `bdr_image` varchar(255) NOT NULL,
  `bdr_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_bd_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bd_pending_payments`
--

CREATE TABLE `bd_pending_payments` (
  `bdpp_id` int(11) NOT NULL,
  `bdpp_amount` int(11) NOT NULL,
  `fk_bd_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bd_referral_bonus`
--

CREATE TABLE `bd_referral_bonus` (
  `bonus_id` int(11) NOT NULL,
  `bonus_amount` int(11) NOT NULL DEFAULT 500,
  `date_earned` datetime NOT NULL DEFAULT current_timestamp(),
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `payment_date` datetime DEFAULT NULL,
  `fk_bd_id` int(11) NOT NULL,
  `fk_buyer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_developer`
--

CREATE TABLE `business_developer` (
  `bd_id` int(11) NOT NULL,
  `bd_name` varchar(100) NOT NULL,
  `bd_address` varchar(300) NOT NULL,
  `bd_contact` varchar(50) NOT NULL,
  `bd_cnic` varchar(50) NOT NULL,
  `bd_image` varchar(255) NOT NULL,
  `bd_email` varchar(255) NOT NULL,
  `bd_password` varchar(255) NOT NULL,
  `bd_status` varchar(50) NOT NULL,
  `bd_referal_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buyer`
--

CREATE TABLE `buyer` (
  `buyer_id` int(11) NOT NULL,
  `buyer_name` varchar(100) NOT NULL,
  `buyer_address` varchar(300) NOT NULL,
  `buyer_contact` varchar(50) NOT NULL,
  `buyer_cnic` varchar(50) NOT NULL,
  `buyer_image` varchar(255) NOT NULL,
  `shop_img` varchar(255) NOT NULL,
  `buyer_email` varchar(255) NOT NULL,
  `buyer_password` varchar(255) NOT NULL,
  `buyer_status` varchar(50) NOT NULL,
  `fk_bd_id` int(11) DEFAULT NULL,
  `fk_rider_id` int(11) NOT NULL,
  `location` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buyer_notification`
--

CREATE TABLE `buyer_notification` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `is_read` varchar(50) NOT NULL DEFAULT 'unread',
  `fk_buyer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_earnings`
--

CREATE TABLE `company_earnings` (
  `earning_id` int(11) NOT NULL,
  `earning_amount` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_delivery_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `delivery_id` int(11) NOT NULL,
  `delivery_quantity` int(11) NOT NULL,
  `total_cash` int(11) NOT NULL,
  `delivery_status` varchar(50) NOT NULL,
  `delivery_date` date NOT NULL,
  `fk_item_adj_id` int(11) NOT NULL,
  `fk_buyer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `item_brand` varchar(255) NOT NULL,
  `item_number` varchar(50) NOT NULL,
  `item_description` text NOT NULL,
  `item_image` varchar(255) NOT NULL,
  `item_price` int(11) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `more_quantity` int(10) NOT NULL DEFAULT 0,
  `item_sold` int(11) NOT NULL,
  `agreement_date` date NOT NULL,
  `item_profit` int(3) NOT NULL DEFAULT 0,
  `item_status` varchar(30) NOT NULL DEFAULT 'review',
  `fk_item_tracking_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items_sold`
--

CREATE TABLE `items_sold` (
  `sell_id` int(11) NOT NULL,
  `sell_price` int(11) NOT NULL,
  `sell_quantity` int(11) NOT NULL,
  `sell_date` date NOT NULL,
  `sell_status` varchar(20) NOT NULL,
  `tracking` varchar(256) NOT NULL,
  `fk_buyer_id` int(11) NOT NULL,
  `fk_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_adjustment`
--

CREATE TABLE `item_adjustment` (
  `item_adj_id` int(11) NOT NULL,
  `item_adj_price` int(11) NOT NULL,
  `pieces_pu` int(11) NOT NULL,
  `item_tag` varchar(20) NOT NULL DEFAULT 'normal_selling',
  `fk_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_rejection`
--

CREATE TABLE `item_rejection` (
  `rejection_id` int(11) NOT NULL,
  `rejection_reason` text NOT NULL,
  `fk_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item_tracking`
--

CREATE TABLE `item_tracking` (
  `item_tracking_id` int(11) NOT NULL,
  `fk_wh_id` int(11) NOT NULL,
  `fk_ws_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_delivery_status`
--

CREATE TABLE `order_delivery_status` (
  `status_id` int(11) NOT NULL,
  `tracking_id` varchar(50) NOT NULL,
  `status_type` enum('delivered_full','delivered_partial','rejected','payment_due') NOT NULL,
  `amount_collected` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `returned_items` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `fk_rider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item_status`
--

CREATE TABLE `order_item_status` (
  `item_status_id` int(11) NOT NULL,
  `fk_status_id` int(11) NOT NULL,
  `fk_sell_id` int(11) NOT NULL,
  `item_status` enum('delivered','returned','rejected') NOT NULL,
  `returned_quantity` int(11) NOT NULL DEFAULT 0,
  `delivered_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_rider_assignment`
--

CREATE TABLE `order_rider_assignment` (
  `assignment_id` int(11) NOT NULL,
  `tracking_id` varchar(50) NOT NULL,
  `fk_rider_id` int(11) NOT NULL,
  `assignment_date` date NOT NULL,
  `assignment_status` varchar(50) NOT NULL DEFAULT 'assigned',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE `rider` (
  `rider_id` int(11) NOT NULL,
  `rider_name` varchar(100) NOT NULL,
  `rider_address` varchar(300) NOT NULL,
  `rider_contact` varchar(50) NOT NULL,
  `rider_cnic` varchar(50) NOT NULL,
  `rider_image` varchar(255) NOT NULL,
  `rider_email` varchar(255) NOT NULL,
  `rider_password` varchar(255) NOT NULL,
  `rider_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_notification`
--

CREATE TABLE `rider_notification` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `is_read` varchar(50) NOT NULL DEFAULT 'unread',
  `fk_rider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_payment_details`
--

CREATE TABLE `rider_payment_details` (
  `rp_id` int(11) NOT NULL,
  `rp_amount` int(11) NOT NULL,
  `rp_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_rider_id` int(11) NOT NULL,
  `fk_delivery_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_payment_records`
--

CREATE TABLE `rider_payment_records` (
  `rr_id` int(11) NOT NULL,
  `rr_image` varchar(255) NOT NULL,
  `rr_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_rider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rider_pending_payments`
--

CREATE TABLE `rider_pending_payments` (
  `rpp_id` int(11) NOT NULL,
  `rpp_amount` int(11) NOT NULL,
  `fk_rider_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sh_profit`
--

CREATE TABLE `sh_profit` (
  `sh_profit_id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `profit_percent` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse`
--

CREATE TABLE `warehouse` (
  `wh_id` int(11) NOT NULL,
  `wh_name` varchar(255) NOT NULL,
  `wh_area` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wholesaler`
--

CREATE TABLE `wholesaler` (
  `ws_id` int(11) NOT NULL,
  `ws_name` varchar(255) NOT NULL,
  `ws_company_name` varchar(255) NOT NULL,
  `ws_home_address` varchar(500) NOT NULL,
  `ws_office_address` varchar(255) NOT NULL,
  `ws_personal_contact` varchar(255) NOT NULL,
  `ws_office_contact` varchar(255) NOT NULL,
  `ws_cnic` varchar(100) NOT NULL,
  `ws_image` varchar(255) NOT NULL,
  `ws_email` varchar(255) NOT NULL,
  `ws_status` varchar(30) NOT NULL DEFAULT 'pending',
  `ws_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_notification`
--

CREATE TABLE `ws_notification` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date` date NOT NULL,
  `is_read` varchar(50) NOT NULL DEFAULT 'unread',
  `fk_ws_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_payment_details`
--

CREATE TABLE `ws_payment_details` (
  `wsp_id` int(11) NOT NULL,
  `wsp_amount` int(11) NOT NULL,
  `wsp_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_ws_id` int(11) NOT NULL,
  `fk_delivery_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_payment_records`
--

CREATE TABLE `ws_payment_records` (
  `wsr_id` int(11) NOT NULL,
  `wsr_image` varchar(255) NOT NULL,
  `wsr_paid` int(11) NOT NULL,
  `date` date NOT NULL,
  `fk_ws_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_pending_payments`
--

CREATE TABLE `ws_pending_payments` (
  `wspp_id` int(11) NOT NULL,
  `wspp_amount` int(11) NOT NULL,
  `fk_ws_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ws_warnings`
--

CREATE TABLE `ws_warnings` (
  `warning_id` int(11) NOT NULL,
  `warning_reason` text NOT NULL,
  `fk_item_id` int(11) NOT NULL,
  `fk_ws_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `fk_admin_wh_id` (`fk_wh_id`);

--
-- Indexes for table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_notification_wh_id` (`fk_wh_id`);

--
-- Indexes for table `bd_notification`
--
ALTER TABLE `bd_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bd_notification_bd_id` (`fk_bd_id`);

--
-- Indexes for table `bd_payment_details`
--
ALTER TABLE `bd_payment_details`
  ADD PRIMARY KEY (`bdp_id`),
  ADD KEY `fk_bd_payment_details_bd_id` (`fk_bd_id`),
  ADD KEY `fk_bd_payment_details_delivery_id` (`fk_delivery_id`);

--
-- Indexes for table `bd_payment_records`
--
ALTER TABLE `bd_payment_records`
  ADD PRIMARY KEY (`bdr_id`),
  ADD KEY `fk_bd_payment_records_bd_id` (`fk_bd_id`);

--
-- Indexes for table `bd_pending_payments`
--
ALTER TABLE `bd_pending_payments`
  ADD PRIMARY KEY (`bdpp_id`),
  ADD KEY `fk_bd_pending_payments_bd_id` (`fk_bd_id`);

--
-- Indexes for table `bd_referral_bonus`
--
ALTER TABLE `bd_referral_bonus`
  ADD PRIMARY KEY (`bonus_id`),
  ADD KEY `fk_referral_bonus_bd_id` (`fk_bd_id`),
  ADD KEY `fk_referral_bonus_buyer_id` (`fk_buyer_id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `business_developer`
--
ALTER TABLE `business_developer`
  ADD PRIMARY KEY (`bd_id`);

--
-- Indexes for table `buyer`
--
ALTER TABLE `buyer`
  ADD PRIMARY KEY (`buyer_id`),
  ADD KEY `fk_buyer_bd_id` (`fk_bd_id`),
  ADD KEY `fk_buyer_rider_id` (`fk_rider_id`);

--
-- Indexes for table `buyer_notification`
--
ALTER TABLE `buyer_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_buyer_notification_buyer_id` (`fk_buyer_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `company_earnings`
--
ALTER TABLE `company_earnings`
  ADD PRIMARY KEY (`earning_id`),
  ADD KEY `fk_company_earnings_delivery_id` (`fk_delivery_id`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `fk_delivery_item_adj_id` (`fk_item_adj_id`),
  ADD KEY `fk_delivery_buyer_id` (`fk_buyer_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `fk_items_item_tracking_id` (`fk_item_tracking_id`);

--
-- Indexes for table `items_sold`
--
ALTER TABLE `items_sold`
  ADD PRIMARY KEY (`sell_id`),
  ADD KEY `fk_items_sold_item_id` (`fk_item_id`),
  ADD KEY `fk_buyer_id_for_items_sold` (`fk_buyer_id`);

--
-- Indexes for table `item_adjustment`
--
ALTER TABLE `item_adjustment`
  ADD PRIMARY KEY (`item_adj_id`),
  ADD KEY `fk_item_adjustment_item_id` (`fk_item_id`);

--
-- Indexes for table `item_rejection`
--
ALTER TABLE `item_rejection`
  ADD PRIMARY KEY (`rejection_id`),
  ADD KEY `fk_item_rejection_item_id` (`fk_item_id`);

--
-- Indexes for table `item_tracking`
--
ALTER TABLE `item_tracking`
  ADD PRIMARY KEY (`item_tracking_id`),
  ADD KEY `fk_item_tracking_wh_id` (`fk_wh_id`),
  ADD KEY `fk_item_tracking_ws_id` (`fk_ws_id`);

--
-- Indexes for table `order_delivery_status`
--
ALTER TABLE `order_delivery_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `fk_order_delivery_status_rider_id` (`fk_rider_id`),
  ADD KEY `idx_tracking_id` (`tracking_id`);

--
-- Indexes for table `order_item_status`
--
ALTER TABLE `order_item_status`
  ADD PRIMARY KEY (`item_status_id`),
  ADD KEY `fk_order_item_status_status_id` (`fk_status_id`),
  ADD KEY `fk_order_item_status_sell_id` (`fk_sell_id`);

--
-- Indexes for table `order_rider_assignment`
--
ALTER TABLE `order_rider_assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `fk_order_rider_assignment_rider_id` (`fk_rider_id`);

--
-- Indexes for table `rider`
--
ALTER TABLE `rider`
  ADD PRIMARY KEY (`rider_id`);

--
-- Indexes for table `rider_notification`
--
ALTER TABLE `rider_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rider_notification_rider_id` (`fk_rider_id`);

--
-- Indexes for table `rider_payment_details`
--
ALTER TABLE `rider_payment_details`
  ADD PRIMARY KEY (`rp_id`),
  ADD KEY `fk_rider_payment_details_rider_id` (`fk_rider_id`),
  ADD KEY `fk_rider_payment_details_delivery_id` (`fk_delivery_id`);

--
-- Indexes for table `rider_payment_records`
--
ALTER TABLE `rider_payment_records`
  ADD PRIMARY KEY (`rr_id`),
  ADD KEY `fk_rider_payment_records_rider_id` (`fk_rider_id`);

--
-- Indexes for table `rider_pending_payments`
--
ALTER TABLE `rider_pending_payments`
  ADD PRIMARY KEY (`rpp_id`),
  ADD KEY `fk_rider_pending_payments_rider_id` (`fk_rider_id`);

--
-- Indexes for table `sh_profit`
--
ALTER TABLE `sh_profit`
  ADD PRIMARY KEY (`sh_profit_id`);

--
-- Indexes for table `warehouse`
--
ALTER TABLE `warehouse`
  ADD PRIMARY KEY (`wh_id`);

--
-- Indexes for table `wholesaler`
--
ALTER TABLE `wholesaler`
  ADD PRIMARY KEY (`ws_id`);

--
-- Indexes for table `ws_notification`
--
ALTER TABLE `ws_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ws_notification_ws_id` (`fk_ws_id`);

--
-- Indexes for table `ws_payment_details`
--
ALTER TABLE `ws_payment_details`
  ADD PRIMARY KEY (`wsp_id`),
  ADD KEY `fk_ws_payment_details_ws_id` (`fk_ws_id`),
  ADD KEY `fk_ws_payment_details_delivery_id` (`fk_delivery_id`);

--
-- Indexes for table `ws_payment_records`
--
ALTER TABLE `ws_payment_records`
  ADD PRIMARY KEY (`wsr_id`),
  ADD KEY `fk_ws_payment_records_ws_id` (`fk_ws_id`);

--
-- Indexes for table `ws_pending_payments`
--
ALTER TABLE `ws_pending_payments`
  ADD PRIMARY KEY (`wspp_id`),
  ADD KEY `fk_ws_pending_payments_ws_id` (`fk_ws_id`);

--
-- Indexes for table `ws_warnings`
--
ALTER TABLE `ws_warnings`
  ADD PRIMARY KEY (`warning_id`),
  ADD KEY `fk_ws_warnings_item_id` (`fk_item_id`),
  ADD KEY `fk_ws_warnings_ws_id` (`fk_ws_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notification`
--
ALTER TABLE `admin_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bd_notification`
--
ALTER TABLE `bd_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bd_payment_details`
--
ALTER TABLE `bd_payment_details`
  MODIFY `bdp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bd_payment_records`
--
ALTER TABLE `bd_payment_records`
  MODIFY `bdr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bd_pending_payments`
--
ALTER TABLE `bd_pending_payments`
  MODIFY `bdpp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bd_referral_bonus`
--
ALTER TABLE `bd_referral_bonus`
  MODIFY `bonus_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_developer`
--
ALTER TABLE `business_developer`
  MODIFY `bd_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buyer`
--
ALTER TABLE `buyer`
  MODIFY `buyer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `buyer_notification`
--
ALTER TABLE `buyer_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_earnings`
--
ALTER TABLE `company_earnings`
  MODIFY `earning_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items_sold`
--
ALTER TABLE `items_sold`
  MODIFY `sell_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_adjustment`
--
ALTER TABLE `item_adjustment`
  MODIFY `item_adj_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_rejection`
--
ALTER TABLE `item_rejection`
  MODIFY `rejection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_tracking`
--
ALTER TABLE `item_tracking`
  MODIFY `item_tracking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_delivery_status`
--
ALTER TABLE `order_delivery_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_item_status`
--
ALTER TABLE `order_item_status`
  MODIFY `item_status_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_rider_assignment`
--
ALTER TABLE `order_rider_assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider`
--
ALTER TABLE `rider`
  MODIFY `rider_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider_notification`
--
ALTER TABLE `rider_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider_payment_details`
--
ALTER TABLE `rider_payment_details`
  MODIFY `rp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider_payment_records`
--
ALTER TABLE `rider_payment_records`
  MODIFY `rr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rider_pending_payments`
--
ALTER TABLE `rider_pending_payments`
  MODIFY `rpp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sh_profit`
--
ALTER TABLE `sh_profit`
  MODIFY `sh_profit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse`
--
ALTER TABLE `warehouse`
  MODIFY `wh_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wholesaler`
--
ALTER TABLE `wholesaler`
  MODIFY `ws_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ws_notification`
--
ALTER TABLE `ws_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ws_payment_details`
--
ALTER TABLE `ws_payment_details`
  MODIFY `wsp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ws_payment_records`
--
ALTER TABLE `ws_payment_records`
  MODIFY `wsr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ws_pending_payments`
--
ALTER TABLE `ws_pending_payments`
  MODIFY `wspp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ws_warnings`
--
ALTER TABLE `ws_warnings`
  MODIFY `warning_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_wh_id` FOREIGN KEY (`fk_wh_id`) REFERENCES `warehouse` (`wh_id`);

--
-- Constraints for table `admin_notification`
--
ALTER TABLE `admin_notification`
  ADD CONSTRAINT `fk_admin_notification_wh_id` FOREIGN KEY (`fk_wh_id`) REFERENCES `warehouse` (`wh_id`);

--
-- Constraints for table `bd_notification`
--
ALTER TABLE `bd_notification`
  ADD CONSTRAINT `fk_bd_notification_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`);

--
-- Constraints for table `bd_payment_details`
--
ALTER TABLE `bd_payment_details`
  ADD CONSTRAINT `fk_bd_payment_details_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`),
  ADD CONSTRAINT `fk_bd_payment_details_delivery_id` FOREIGN KEY (`fk_delivery_id`) REFERENCES `delivery` (`delivery_id`);

--
-- Constraints for table `bd_payment_records`
--
ALTER TABLE `bd_payment_records`
  ADD CONSTRAINT `fk_bd_payment_records_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`);

--
-- Constraints for table `bd_pending_payments`
--
ALTER TABLE `bd_pending_payments`
  ADD CONSTRAINT `fk_bd_pending_payments_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`);

--
-- Constraints for table `bd_referral_bonus`
--
ALTER TABLE `bd_referral_bonus`
  ADD CONSTRAINT `fk_referral_bonus_bd_id` FOREIGN KEY (`fk_bd_id`) REFERENCES `business_developer` (`bd_id`),
  ADD CONSTRAINT `fk_referral_bonus_buyer_id` FOREIGN KEY (`fk_buyer_id`) REFERENCES `buyer` (`buyer_id`);

--
-- Constraints for table `buyer_notification`
--
ALTER TABLE `buyer_notification`
  ADD CONSTRAINT `fk_buyer_notification_buyer_id` FOREIGN KEY (`fk_buyer_id`) REFERENCES `buyer` (`buyer_id`);

--
-- Constraints for table `company_earnings`
--
ALTER TABLE `company_earnings`
  ADD CONSTRAINT `fk_company_earnings_delivery_id` FOREIGN KEY (`fk_delivery_id`) REFERENCES `delivery` (`delivery_id`);

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `fk_delivery_buyer_id` FOREIGN KEY (`fk_buyer_id`) REFERENCES `buyer` (`buyer_id`),
  ADD CONSTRAINT `fk_delivery_item_adj_id` FOREIGN KEY (`fk_item_adj_id`) REFERENCES `item_adjustment` (`item_adj_id`);

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_item_tracking_id` FOREIGN KEY (`fk_item_tracking_id`) REFERENCES `item_tracking` (`item_tracking_id`);

--
-- Constraints for table `items_sold`
--
ALTER TABLE `items_sold`
  ADD CONSTRAINT `fk_buyer_id_for_items_sold` FOREIGN KEY (`fk_buyer_id`) REFERENCES `buyer` (`buyer_id`),
  ADD CONSTRAINT `fk_items_sold_item_id` FOREIGN KEY (`fk_item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `item_adjustment`
--
ALTER TABLE `item_adjustment`
  ADD CONSTRAINT `fk_item_adjustment_item_id` FOREIGN KEY (`fk_item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `item_rejection`
--
ALTER TABLE `item_rejection`
  ADD CONSTRAINT `fk_item_rejection_item_id` FOREIGN KEY (`fk_item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `item_tracking`
--
ALTER TABLE `item_tracking`
  ADD CONSTRAINT `fk_item_tracking_wh_id` FOREIGN KEY (`fk_wh_id`) REFERENCES `warehouse` (`wh_id`),
  ADD CONSTRAINT `fk_item_tracking_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);

--
-- Constraints for table `order_delivery_status`
--
ALTER TABLE `order_delivery_status`
  ADD CONSTRAINT `fk_order_delivery_status_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `order_item_status`
--
ALTER TABLE `order_item_status`
  ADD CONSTRAINT `fk_order_item_status_sell_id` FOREIGN KEY (`fk_sell_id`) REFERENCES `items_sold` (`sell_id`),
  ADD CONSTRAINT `fk_order_item_status_status_id` FOREIGN KEY (`fk_status_id`) REFERENCES `order_delivery_status` (`status_id`);

--
-- Constraints for table `order_rider_assignment`
--
ALTER TABLE `order_rider_assignment`
  ADD CONSTRAINT `fk_order_rider_assignment_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `rider_notification`
--
ALTER TABLE `rider_notification`
  ADD CONSTRAINT `fk_rider_notification_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `rider_payment_details`
--
ALTER TABLE `rider_payment_details`
  ADD CONSTRAINT `fk_rider_payment_details_delivery_id` FOREIGN KEY (`fk_delivery_id`) REFERENCES `delivery` (`delivery_id`),
  ADD CONSTRAINT `fk_rider_payment_details_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `rider_payment_records`
--
ALTER TABLE `rider_payment_records`
  ADD CONSTRAINT `fk_rider_payment_records_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `rider_pending_payments`
--
ALTER TABLE `rider_pending_payments`
  ADD CONSTRAINT `fk_rider_pending_payments_rider_id` FOREIGN KEY (`fk_rider_id`) REFERENCES `rider` (`rider_id`);

--
-- Constraints for table `ws_notification`
--
ALTER TABLE `ws_notification`
  ADD CONSTRAINT `fk_ws_notification_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);

--
-- Constraints for table `ws_payment_details`
--
ALTER TABLE `ws_payment_details`
  ADD CONSTRAINT `fk_ws_payment_details_delivery_id` FOREIGN KEY (`fk_delivery_id`) REFERENCES `delivery` (`delivery_id`),
  ADD CONSTRAINT `fk_ws_payment_details_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);

--
-- Constraints for table `ws_payment_records`
--
ALTER TABLE `ws_payment_records`
  ADD CONSTRAINT `fk_ws_payment_records_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);

--
-- Constraints for table `ws_pending_payments`
--
ALTER TABLE `ws_pending_payments`
  ADD CONSTRAINT `fk_ws_pending_payments_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);

--
-- Constraints for table `ws_warnings`
--
ALTER TABLE `ws_warnings`
  ADD CONSTRAINT `fk_ws_warnings_item_id` FOREIGN KEY (`fk_item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `fk_ws_warnings_ws_id` FOREIGN KEY (`fk_ws_id`) REFERENCES `wholesaler` (`ws_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
