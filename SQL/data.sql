-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 11:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u650672385_phonemate`
--

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `admin_role`, `admin_email`, `admin_password`, `fk_wh_id`) VALUES
(1, 'CodesMine', 'super', 'admin@gmail.com', '1a1dc91c907325c69271ddf0c944bc72', 1);

--
-- Dumping data for table `admin_notification`
--

INSERT INTO `admin_notification` (`id`, `message`, `date`, `is_read`, `admin_role`, `fk_wh_id`) VALUES
(1, 'New buyer registration: Nabeel', '2025-02-26', 'unread', 'admin', 1),
(2, 'New buyer registration: Nabeel Shehzad', '2025-02-26', 'unread', 'admin', 1),
(3, 'New buyer registration: Shakeel', '2025-02-27', 'unread', 'admin', 1),
(4, 'New buyer registration: codsmine ', '2025-02-28', 'unread', 'admin', 1),
(5, 'New buyer registration: Test ', '2025-02-28', 'unread', 'admin', 1),
(6, 'New buyer registration: Ali', '2025-02-28', 'unread', 'admin', 1),
(7, 'New buyer registration: Orders', '2025-02-28', 'unread', 'admin', 1),
(8, 'New buyer registration: Hamza', '2025-02-28', 'unread', 'admin', 1),
(9, 'New buyer registration: Hamza', '2025-03-01', 'unread', 'admin', 1),
(10, 'New buyer registration: Hamza', '2025-03-01', 'unread', 'admin', 1),
(11, 'New buyer registration: Hamza', '2025-03-01', 'unread', 'admin', 1),
(12, 'New buyer registration: Hamza', '2025-03-01', 'unread', 'admin', 1),
(13, 'New buyer registration: Umar iqbal', '2025-03-01', 'unread', 'admin', 1),
(14, 'New buyer registration: mustansar Hussain ', '2025-03-01', 'unread', 'admin', 1),
(15, 'New buyer registration: ghulam murtza ', '2025-03-01', 'unread', 'admin', 1),
(16, 'New buyer registration: Husnain', '2025-03-04', 'unread', 'admin', 1),
(17, 'New buyer registration: Husnain', '2025-03-04', 'unread', 'admin', 1),
(18, 'New buyer registration: test 2', '2025-03-04', 'unread', 'admin', 1),
(19, 'New buyer registration: saqib Ali', '2025-03-09', 'unread', 'admin', 1),
(20, 'New buyer registration: Test Google', '2025-03-29', 'unread', 'admin', 1),
(21, 'New rider registration: ahmad', '2025-04-10', 'unread', 'admin', 1),
(22, 'Rider updated: ahmad', '2025-04-10', 'unread', 'admin', 1);

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`) VALUES
(1, 'Samsung'),
(2, 'Audionic'),
(3, 'Huawei');

--
-- Dumping data for table `buyer`
--

INSERT INTO `buyer` (`buyer_id`, `buyer_name`, `buyer_address`, `buyer_contact`, `buyer_cnic`, `buyer_image`, `shop_img`, `buyer_email`, `buyer_password`, `buyer_status`, `fk_bd_id`, `fk_rider_id`, `location`) VALUES
(2, 'Nabeel', 'Daska kalan', '03097367969', '1234543234456', 'default.jpg', '', 'nabeelshahzad88@gmail.com', '$2y$10$etCWYNNwGPXg0C9IAWqraejiwL2BcvdLa.i0QLXOUZFb66fysiUva', 'approved', 1, 1, ''),
(3, 'Shakeel', 'Gujranwala ', '03126485243', '', 'default.jpg', '', '16140037@gift.edu.pk', '$2y$10$EQLESB73NLP6vD0VmKryjOut3M63p25dqgAFf/sQYnpQ0NleSNbi2', 'approved', 1, 1, ''),
(4, 'codsmine ', 'gujranwala ', '03451122360', '', 'default.jpg', '', 'codsmine@gmail.com', '$2y$10$NVdw2.negIzKLjNVnpC4w.e8NTNX..f4bUgKtqNHaBjQy6SKjQ9MO', 'approved', 1, 1, ''),
(5, 'Test ', 'gujranwala ', '03121234567', '', '', '', 'test@gmail.com', '$2y$10$nVyd26Car0tL.z33U0DykOAMYAfqX7RiSr1iYiluNAnYLYv.MYTsC', 'approved', 1, 1, ''),
(6, 'Ali', 'Sialkot', '03124569784', '3265412397854', 'default.jpg', '', 'apptreo.official@gmail.com', '$2y$10$JEI8k/FPA7vKncoeohvnWuoX.sSM/ZWIDcpnkwurn/B3qyzNyJHEO', 'approved', 1, 1, ''),
(7, 'Orders', 'Gujranwala', '03096532258', '', 'default.jpg', '', 'orders@gmail.com', '$2y$10$Lip37LVSWJXRMnzFVPZrOuh/9AmIV5g/sGYKAdTeuvi.JhCnmf3om', 'approved', 1, 1, ''),
(12, 'Hamza', 'Daska', '03214569854', '3265487946132', 'shop_1740821265.jpg', 'shop_1740821266.jpg', '16140034@gift.edu.pk', '$2y$10$SEEgaJOzZgKameR0A6w8XuzZPtz9sSVDvgOnz0kun2VhMQWwSDRju', 'approved', 1, 1, '32.3235179,74.3553209'),
(13, 'Umar iqbal', 'Daska', '03214569785', '3569864758861', 'shop_1740835079.jpg', 'shop_1740835081.jpg', '16140029@gift.edu.pk', '$2y$10$ReQUa5aarXqg9c9oTpXs.u8Vm7TnhRzhpcbvydp4eblDwWJiTkbcm', 'approved', 1, 1, '32.3235238,74.3553244'),
(14, 'mustansar Hussain ', 'skp', '03230966755', '', 'default.jpg', '', 'mustansar3755hussain@gmail.com', '$2y$10$h/hLWOO25Q/5ixVMY/ZYNOxJF2hI6hSQbyJ0xetZHUZDxmEJZ0xNW', 'approved', 1, 1, ''),
(15, 'ghulam murtza ', 'gujranwala ', '03016889980', '3410180923785', 'default.jpg', 'shop_1740857169.jpg', 'murtzag1986@gmail.com', '$2y$10$EPcIUmLbwhO6VKQVvKGuWutnHU43RoGTYEtBhI4AuXDlVdI8Ud88.', 'approved', 1, 1, '32.3235238,74.3553244'),
(18, 'test 2', 'talvandi', '03006268334', '3410180923785', 'default.jpg', 'shop_1741083404.jpg', 'test2@gmail.com', '$2y$10$lt0gM0DyVlYCodQ/ioZaSuy6OHRQTZJ85EKdmin6ywJPEQ0HLUia.', 'approved', 1, 1, '32.2853335,74.2147461'),
(19, 'saqib Ali', 'Rahwali cant', '03061009955', '3410183068023', 'default.jpg', 'shop_1741511071.jpg', 'cguj1205@gmail.com', '$2y$10$f7Yigrp9oIxr0F3sRtE3Y.ena0DpHLUinHrZwP6We6uiXVQpxYgzi', 'approved', 1, 1, '32.2014661,74.2071655'),
(20, 'Test Google', 'Google Store', '12345678964', '3268495346654', 'default.jpg', 'shop_1743236035.jpg', 'google@gmail.com', '$2y$10$C/fFWTRXGld18/HiPhc5t.j7dVjV2E8CsPFdu8PyYKj28pQTtE8/2', 'approved', 1, 1, '32.3192254,74.3517092');

--
-- Dumping data for table `buyer_notification`
--

INSERT INTO `buyer_notification` (`id`, `message`, `date`, `is_read`, `fk_buyer_id`) VALUES
(1, 'Your order has been approved and is in delivery!', '2025-03-05', 'unread', 14),
(2, 'Your order has been delivered!', '2025-03-05', 'unread', 2),
(3, 'Your order has been approved and is in delivery!', '2025-03-05', 'unread', 14),
(4, 'Your order has been approved and is in delivery!', '2025-03-05', 'unread', 13),
(5, 'Your order has been approved and is in delivery!', '2025-03-05', 'unread', 12),
(6, 'Your order has been delivered!', '2025-03-05', 'unread', 14),
(7, 'Your order has been delivered!', '2025-03-05', 'unread', 2),
(8, 'Your order has been approved and is in delivery!', '2025-03-05', 'unread', 15),
(9, 'Your order has been approved and is in delivery!', '2025-03-15', 'unread', 15),
(10, 'Your order has been approved and is in delivery!', '2025-03-15', 'unread', 15),
(11, 'Your order has been approved and is in delivery!', '2025-03-15', 'unread', 15),
(12, 'Your order has been delivered!', '2025-03-15', 'unread', 15),
(13, 'Your order has been delivered!', '2025-03-15', 'unread', 15),
(14, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(15, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 2),
(16, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 13),
(17, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(18, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(19, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(20, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(21, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(22, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(23, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(24, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(25, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(26, 'Your order has been approved and is in delivery!', '2025-03-23', 'unread', 15),
(27, 'Your order has been delivered!', '2025-03-25', 'unread', 15),
(28, 'Your order has been delivered!', '2025-03-25', 'unread', 15),
(29, 'Your order has been approved and is in delivery!', '2025-04-08', 'unread', 20),
(30, 'Your order has been approved and is in delivery!', '2025-04-17', 'unread', 15),
(31, 'Your order has been delivered!', '2025-04-17', 'unread', 15);

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Hand-free'),
(2, 'Data Cable'),
(3, 'Power Bank'),
(4, 'Head Phone'),
(5, 'Smart Watch');

--
-- Dumping data for table `company_earnings`
--

INSERT INTO `company_earnings` (`earning_id`, `earning_amount`, `date`, `fk_delivery_id`) VALUES
(1, 2300, '2025-03-05', 1),
(2, 2300, '2025-03-05', 2),
(3, 3000, '2025-03-05', 4),
(4, -240540, '2025-03-15', 6),
(5, -4560, '2025-03-15', 5),
(6, -240540, '2025-03-25', 13),
(7, -240540, '2025-03-25', 13),
(8, 125, '2025-04-17', 23);

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`delivery_id`, `delivery_quantity`, `total_cash`, `delivery_status`, `delivery_date`, `fk_item_adj_id`, `fk_buyer_id`) VALUES
(1, 100, 10000, 'delivered', '2025-03-05', 6, 14),
(2, 100, 10000, 'delivered', '2025-03-05', 6, 14),
(3, 200, 14000, 'in_delivery', '2025-03-05', 5, 13),
(4, 200, 14000, 'delivered', '2025-03-05', 5, 12),
(5, 6, 2700, 'delivered', '2025-03-15', 17, 15),
(6, 760, 85500, 'delivered', '2025-03-15', 3, 15),
(7, 400, 25000, 'in_delivery', '2025-03-15', 4, 15),
(8, 60, 4800, 'in_delivery', '2025-03-15', 1, 15),
(9, 400, 14000, 'in_delivery', '2025-03-23', 5, 15),
(10, 300, 10000, 'in_delivery', '2025-03-23', 6, 2),
(11, 200, 14000, 'in_delivery', '2025-03-23', 5, 13),
(12, 100, 25000, 'in_delivery', '2025-03-23', 4, 15),
(13, 190, 85500, 'delivered', '2025-03-25', 3, 15),
(14, 50, 12500, 'in_delivery', '2025-03-23', 2, 15),
(15, 30, 4800, 'in_delivery', '2025-03-23', 1, 15),
(16, 5, 3850, 'in_delivery', '2025-03-23', 16, 15),
(17, 60, 4800, 'in_delivery', '2025-03-23', 1, 15),
(18, 5, 3850, 'in_delivery', '2025-03-23', 16, 15),
(19, 3, 3850, 'in_delivery', '2025-03-23', 16, 15),
(20, 100, 12500, 'in_delivery', '2025-03-23', 2, 15),
(21, 90, 4800, 'in_delivery', '2025-03-23', 1, 15),
(22, 10, 1400, 'in_delivery', '2025-04-08', 18, 20),
(23, 10, 1700, 'delivered', '2025-04-17', 23, 15);

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `item_category`, `item_brand`, `item_number`, `item_description`, `item_image`, `item_price`, `item_quantity`, `more_quantity`, `item_sold`, `agreement_date`, `item_profit`, `item_status`, `fk_item_tracking_id`) VALUES
(1, 'Hand-free', 'Samsung', 'm8 95 c-pro', 'Genuine Handfrees, good sound level, no distortion, integrated mice, overall longer life span', 'item_67e8422d89f62.jpg', 131, 0, 0, 0, '2026-02-19', 5, 'processed', 1),
(2, 'Hand-free', 'Audionic', 'Audionic Special z-100', 'Experience crystal-clear sound with the Audionic Special Z-100 Handsfree. Designed for comfort and performance, this handsfree delivers rich audio quality with deep bass and crisp highs, making it perfect for music, calls, and gaming. The ergonomic design ensures a secure fit, while the in-line mic and control buttons let you manage calls and media effortlessly. Built with durable materials, the Z-100 is a reliable companion for everyday use.', '2elevated-view-black-earphone-coffee-cup-white-background.jpg', 200, 499600, 0, 0, '2026-02-28', 10, 'processed', 1),
(3, 'Hand-free', 'Samsung', 'yu-09 9876', 'Unlock immersive sound with the Audionic YU-09 9876 Handsfree, designed to complement your Samsung devices. This handsfree delivers dynamic audio with rich bass, sharp mids, and clear highs for an outstanding listening experience. Its ergonomic design ensures a snug, comfortable fit, while the built-in mic and control buttons make managing calls and music a breeze. Crafted with durable materials, it\\\'s built to last — perfect for everyday use.', '3988.jpg', 390, 17290, 0, 1520, '2026-02-28', 10, 'processed', 1),
(4, 'Hand-free', 'Huawei', 'mini g-pro', 'Experience powerful sound in a compact design with the Audionic Mini G-Pro Handsfree, perfectly optimized for Huawei devices. Whether you\\\'re enjoying music, taking calls, or gaming, this handsfree delivers crystal-clear audio with deep bass and balanced tones. Its lightweight, ergonomic fit ensures all-day comfort, while the in-line mic and controls make switching between tracks and calls effortless.', '4sound-wave-icon-near-earphone-blackboard.jpg', 200, 0, 0, 0, '2026-02-01', 10, 'processed', 1),
(5, 'Data Cable', 'Samsung', 'c-11', 'Stay connected and powered up with the Audionic Data Cable, designed for fast and reliable charging and data transfer for Samsung devices. Built with high-quality materials, this cable ensures durability and efficient performance, whether you’re syncing files or charging on the go. The sleek, tangle-free design makes it perfect for everyday use.', '5pexels-pixabay-159304.jpg', 50, 600, 0, 200, '2026-02-28', 10, 'processed', 1),
(6, 'Data Cable', 'Audionic', 'l-11', '**Audionic Data Cable — Fast & Reliable**  \\r\\nCharge and sync your devices with confidence using the **Audionic Data Cable**. Designed for durability and speed, this cable ensures fast charging and stable data transfer, making it a must-have for everyday use. Its sturdy build and flexible design prevent wear and tear, while universal compatibility makes it perfect for a wide range of devices.', '6pexels-karolina-grabowska-4219862.jpg', 70, 6303, 0, 97, '2026-02-28', 10, 'processed', 1),
(7, 'Data Cable', 'Huawei', 'iphone-smart 111', 'Experience seamless charging and data transfer with the Audionic iPhone-Smart 111 DC, designed specifically for Huawei devices. This high-performance cable ensures fast, reliable power delivery and smooth syncing, all in a durable, tangle-free design. Perfect for everyday use, it combines strength and efficiency to keep your devices powered up and connected.', '7pexels-matthiaszomer-914912.jpg', 90, 50000, 0, 0, '2026-02-28', 10, 'processed', 1),
(8, 'Power Bank', 'Samsung', 'pb-098', 'Stay charged on the go with the Audionic Samsung Power Bank. Designed for convenience and reliability, this compact power bank delivers fast, efficient charging for your Samsung devices. With a sleek, lightweight design and high-capacity battery, it’s perfect for travel, work, or daily use, ensuring your devices stay powered whenever you need them.', '8pexels-zion-10104281.jpg', 230, 80000, 0, 0, '2026-02-28', 10, 'processed', 1),
(9, 'Power Bank', 'Audionic', 'pb-z-long-67', 'Stay powered anytime, anywhere with the Audionic Power Bank. Designed for fast and efficient charging, this portable powerhouse keeps your devices running throughout the day. Its sleek, lightweight design makes it easy to carry, while the high-capacity battery ensures multiple charges for your smartphone, tablet, or other gadgets. Perfect for travel, work, or emergencies!\\r\\n', '9pexels-zion-10104320.jpg', 150, 600, 0, 0, '2026-02-28', 10, 'processed', 1),
(10, 'Head Phone', 'Samsung', 'c99', 'Enjoy rich, high-quality audio with Audionic Headphones for Samsung. Designed for crystal-clear sound and deep bass, these headphones deliver an exceptional listening experience for music, calls, and gaming. The cushioned ear cups and adjustable headband provide all-day comfort, while the durable build ensures long-lasting performance. Perfect for Samsung devices, they offer seamless connectivity and powerful sound on the go.', '10pexels-moose-photos-170195-1037992.jpg', 200, 3999, 0, 0, '2026-02-28', 10, 'processed', 1),
(11, 'Head Phone', 'Audionic', 'g88', 'Experience powerful, crystal-clear audio with Audionic Headphones. Designed for music lovers and gamers alike, these headphones deliver deep bass, balanced mids, and crisp highs for an immersive sound experience. With soft, cushioned ear cups and an adjustable headband, they provide lasting comfort for extended listening sessions. Built with durable materials, they’re perfect for everyday use at home, work, or on the go.', '11pexels-parag-deshmukh-180046-577769.jpg', 150, 27600, 0, 0, '2026-02-28', 10, 'processed', 1),
(12, 'Head Phone', 'Huawei', 'h01', 'Enjoy high-definition audio with Audionic Headphones for Huawei. Engineered for rich sound quality, these headphones deliver deep bass and clear vocals, enhancing your music, calls, and gaming experience. The lightweight design, cushioned ear cups, and adjustable headband ensure all-day comfort, while the sturdy build guarantees lasting durability. Perfect for Huawei devices, they offer seamless connectivity and exceptional performance.', '12pexels-garrettmorrow-1649771.jpg', 400, 3000, 0, 0, '2026-02-28', 10, 'processed', 1),
(13, 'Smart Watch', 'Samsung', 'new-gen 99', 'Stay connected and in control with the Audionic Smartwatch for Samsung. Designed for seamless compatibility, this sleek smartwatch tracks your fitness, monitors health metrics, and keeps you updated with notifications — all from your wrist. With a vibrant display, long battery life, and customizable features, it\\\'s the perfect companion for your Samsung device, balancing style and practicality effortlessly.', '13pexels-luckysam-51011.jpg', 2000, 2000, 0, 0, '2026-02-28', 10, 'processed', 1),
(14, 'Smart Watch', 'Audionic', 'new SmartW 100', 'Stay connected and track your health with the Audionic Smartwatch. Designed for style and functionality, this sleek smartwatch offers fitness tracking, heart rate monitoring, and smart notifications right on your wrist. With a vibrant display, long battery life, and customizable features, it’s the perfect companion for your active lifestyle — whether at work, the gym, or on the go.', '14pexels-brett-sayles-1080745.jpg', 3000, 500, 0, 0, '2026-02-28', 10, 'processed', 1),
(15, 'Smart Watch', 'Huawei', 'yen 788', 'Stay ahead with the Huawei Smartwatch, designed to blend elegance with advanced technology. Track your fitness, monitor your heart rate, and receive smart notifications, all on a vibrant, easy-to-read display. With a sleek design, long battery life, and customizable watch faces, it’s the perfect companion for your daily routine, workouts, and everything in between.', '15pexels-vishven-solanki-1441477-2861929.jpg', 5000, 5000, 0, 0, '2026-02-28', 10, 'processed', 1),
(16, 'Hand-free', 'Huawei', 'pro plus', 'its a good quality handfree ', '16hawai.webp', 3200, 37, 0, 0, '2025-03-30', 5, 'processed', 1),
(17, 'Smart Watch', 'Huawei', '101', 'it is newly lanched', '16hawai.webp', 1100, 70, 0, 6, '2025-10-04', 10, 'processed', 1),
(18, 'Data Cable', 'Samsung', 'pro 2', 'top quality and dont drop voltage', '18Untitled-1.png', 120, 990, 0, 0, '2025-03-06', 7, 'processed', 1),
(19, 'Hand-free', 'Samsung', 'pro r 2', 'top quality 0 % complain', '19handfree.png', 180, 485, 0, 0, '2025-03-06', 10, 'processed', 2),
(20, 'Hand-free', 'Huawei', 'i phone 11', 'latest in i phone technology', '20i phone hand free.png', 300, 190, 0, 0, '2025-03-06', 10, 'processed', 2),
(21, 'Smart Watch', 'Huawei', '2025 hawi', 'Boss Pro Smart watch\\r\\n', '21smart watch.png', 9000, 20, 0, 0, '2025-03-08', 10, 'review', 2),
(22, 'Data Cable', 'Samsung', 'i phone cable 27w ', '27 w pd magnet\\r\\n27 w pd n0n magnet', '22WhatsApp Image 2025-03-28 at 01.01.26_22a783bf.jpg', 220, 2000, 0, 0, '2025-10-05', 5, 'approved', 2),
(23, 'Hand-free', 'Samsung', 'advace ANC', 'adance three pin and ANC technology', '23handfree.png', 150, 990, 0, 10, '2025-06-30', 5, 'processed', 1);

--
-- Dumping data for table `items_sold`
--

INSERT INTO `items_sold` (`sell_id`, `sell_price`, `sell_quantity`, `sell_date`, `sell_status`, `tracking`, `fk_buyer_id`, `fk_item_id`) VALUES
(19, 4800, 2, '2025-03-01', 'completed', 'zyyxi4', 2, 1),
(20, 14000, 1, '2025-03-01', 'completed', 'zyyxi4', 2, 5),
(21, 10000, 1, '2025-03-01', 'completed', 'zyyxi4', 2, 6),
(23, 4000, 1, '2025-03-01', 'completed', 'zyyxi4', 2, 9),
(24, 14000, 1, '2025-03-01', 'approved', 'wrh42r', 12, 5),
(27, 12500, 1, '2025-03-01', 'approved', 'wrh42r', 12, 2),
(28, 25000, 1, '2025-03-01', 'approved', 'wrh42r', 12, 13),
(29, 60000, 1, '2025-03-01', 'approved', 'wrh42r', 12, 15),
(38, 4000, 1, '2025-03-01', 'approved', 'wrh42r', 12, 9),
(39, 25000, 1, '2025-03-01', 'pending', '3f98d0', 13, 4),
(40, 14000, 1, '2025-03-01', 'approved', '3f98d0', 13, 5),
(41, 60000, 1, '2025-03-01', 'pending', '3f98d0', 13, 11),
(42, 3850, 5, '2025-03-01', 'shipped', 'vsi8iw', 2, 16),
(43, 54000, 2, '2025-03-01', 'shipped', 'vsi8iw', 2, 8),
(44, 160, 1, '2025-03-04', 'pending', 'zc1m9g', 14, 1),
(46, 200, 1, '2025-03-04', 'pending', 'zc1m9g', 14, 11),
(47, 250, 1, '2025-03-04', 'pending', 'zc1m9g', 14, 2),
(48, 4800, 1, '2025-03-04', 'pending', '8nnrqc', 2, 1),
(55, 10000, 1, '2025-03-04', 'completed', 'ydzy8l', 14, 6),
(56, 2700, 3, '2025-03-15', 'shipped', 'ilyqgj', 15, 17),
(57, 4800, 2, '2025-03-15', 'shipped', 'ilyqgj', 15, 1),
(58, 25000, 4, '2025-03-15', 'shipped', 'ilyqgj', 15, 4),
(59, 85500, 4, '2025-03-15', 'completed', 'ilyqgj', 15, 3),
(63, 4800, 1, '2025-03-16', 'pending', '5ba0bz', 2, 1),
(64, 12500, 1, '2025-03-16', 'pending', '5ba0bz', 2, 2),
(65, 85500, 1, '2025-03-16', 'pending', '5ba0bz', 2, 3),
(66, 60000, 1, '2025-03-16', 'partially_delivered', 'pr5fvk', 2, 11),
(67, 30000, 3, '2025-03-16', 'partially_delivered', 'pr5fvk', 2, 6),
(68, 4800, 3, '2025-03-17', 'partially_delivered', '1j3gi7', 15, 1),
(69, 12500, 2, '2025-03-17', 'partially_delivered', '1j3gi7', 15, 2),
(70, 3850, 3, '2025-03-17', 'partially_delivered', '1j3gi7', 15, 16),
(72, 3850, 5, '2025-03-18', 'delivered_with_dues', 'ibd9zh', 15, 16),
(73, 4800, 2, '2025-03-18', 'delivered_with_dues', 'ibd9zh', 15, 1),
(74, 50000, 2, '2025-03-18', 'pending', '6bt8lb', 2, 4),
(75, 50000, 2, '2025-03-18', 'pending', '1nx7b0', 2, 4),
(76, 3850, 5, '2025-03-18', 'approved', 'og5jpi', 15, 16),
(77, 12500, 1, '2025-03-20', 'pending', '6wejhw', 19, 2),
(78, 12500, 1, '2025-03-20', 'pending', '8yzdzp', 19, 2),
(79, 4800, 1, '2025-03-22', 'rejected', 'wtjdxq', 15, 1),
(80, 12500, 1, '2025-03-22', 'rejected', 'wtjdxq', 15, 2),
(81, 85500, 1, '2025-03-22', 'rejected', 'wtjdxq', 15, 3),
(82, 25000, 1, '2025-03-22', 'rejected', 'wtjdxq', 15, 4),
(89, 8100, 3, '2025-03-24', 'pending', 'lzcdbg', 2, 17),
(90, 85500, 1, '2025-03-24', 'pending', '03nvs7', 2, 3),
(91, 12500, 1, '2025-03-24', 'pending', 'g9njhi', 2, 2),
(92, 85500, 1, '2025-03-24', 'pending', 'g9njhi', 2, 3),
(93, 10000, 1, '2025-03-24', 'pending', 'ng065d', 2, 6),
(94, 60000, 1, '2025-03-24', 'pending', 'ng065d', 2, 11),
(95, 25000, 1, '2025-03-25', 'pending', 'g090uk', 15, 4),
(96, 300000, 5, '2025-03-25', 'pending', 'b991gn', 15, 11),
(97, 3300, 3, '2025-03-25', 'pending', 'eza711', 15, 19),
(98, 3800, 5, '2025-03-25', 'pending', 'b3iu5p', 15, 20),
(99, 10000, 1, '2025-03-29', 'pending', 'eawjyr', 20, 6),
(100, 1400, 1, '2025-03-29', 'approved', 'z3c1c0', 20, 18),
(101, 10000, 1, '2025-03-31', 'pending', 'ges6gv', 20, 6),
(102, 1700, 2, '2025-04-17', 'completed', '5dropj', 15, 23);

--
-- Dumping data for table `item_adjustment`
--

INSERT INTO `item_adjustment` (`item_adj_id`, `item_adj_price`, `pieces_pu`, `item_tag`, `fk_item_id`) VALUES
(1, 160, 30, 'top_selling', 1),
(2, 250, 50, 'hot_selling', 2),
(3, 450, 190, 'normal_selling', 3),
(4, 250, 100, 'normal_selling', 4),
(5, 70, 200, 'normal_selling', 5),
(6, 100, 100, 'top_selling', 6),
(7, 120, 200, 'normal_selling', 7),
(8, 270, 200, 'normal_selling', 8),
(9, 200, 20, 'normal_selling', 9),
(10, 300, 50, 'normal_selling', 10),
(11, 200, 300, 'hot_selling', 11),
(12, 600, 100, 'normal_selling', 12),
(13, 2500, 10, 'top_selling', 13),
(14, 3500, 10, 'normal_selling', 14),
(15, 6000, 10, 'top_selling', 15),
(16, 3850, 1, 'hot_selling', 16),
(17, 1350, 2, 'top_selling', 17),
(18, 140, 10, 'normal_selling', 18),
(19, 380, 2, 'normal_selling', 20),
(20, 220, 5, 'normal_selling', 19),
(21, 11000, 1, 'hot_selling', 21),
(22, 251, 10, 'top_selling', 22),
(23, 170, 5, 'hot_selling', 23);

--
-- Dumping data for table `item_tracking`
--

INSERT INTO `item_tracking` (`item_tracking_id`, `fk_wh_id`, `fk_ws_id`) VALUES
(1, 1, 1),
(2, 1, 3);

--
-- Dumping data for table `order_delivery_status`
--

INSERT INTO `order_delivery_status` (`status_id`, `tracking_id`, `status_type`, `amount_collected`, `amount_due`, `returned_items`, `rejection_reason`, `notes`, `update_date`, `fk_rider_id`) VALUES
(1, 'ibd9zh', 'payment_due', 2000.00, 8850.00, NULL, NULL, NULL, '2025-04-17 10:47:41', 1),
(2, '5dropj', 'delivered_full', 3400.00, 0.00, NULL, NULL, NULL, '2025-04-17 17:11:29', 1),
(3, '1j3gi7', 'delivered_partial', 29800.00, 0.00, NULL, NULL, NULL, '2025-04-17 17:12:27', 1),
(4, 'wtjdxq', 'rejected', 0.00, 0.00, NULL, 'He didn\'t accept the order', NULL, '2025-04-17 17:12:46', 1),
(5, 'pr5fvk', 'delivered_partial', 60000.00, 0.00, NULL, NULL, NULL, '2025-04-19 07:54:17', 1);

--
-- Dumping data for table `order_item_status`
--

INSERT INTO `order_item_status` (`item_status_id`, `fk_status_id`, `fk_sell_id`, `item_status`, `returned_quantity`, `delivered_quantity`) VALUES
(1, 1, 73, 'delivered', 0, 2),
(2, 1, 72, 'delivered', 0, 5),
(3, 2, 102, 'delivered', 0, 2),
(4, 3, 68, 'delivered', 1, 2),
(5, 3, 69, 'delivered', 1, 1),
(6, 3, 70, 'delivered', 1, 2),
(7, 4, 79, 'delivered', 0, 1),
(8, 4, 80, 'delivered', 0, 1),
(9, 4, 81, 'delivered', 0, 1),
(10, 4, 82, 'delivered', 0, 1),
(11, 5, 67, 'returned', 3, 0),
(12, 5, 66, 'delivered', 0, 1);

--
-- Dumping data for table `order_rider_assignment`
--

INSERT INTO `order_rider_assignment` (`assignment_id`, `tracking_id`, `fk_rider_id`, `assignment_date`, `assignment_status`, `notes`) VALUES
(1, '1j3gi7', 1, '2025-04-13', 'completed', 'Deliver on time'),
(2, 'ibd9zh', 1, '2025-04-13', 'completed', 'make sure these items should be safe and on time. don\\\'t get late'),
(3, '5dropj', 1, '2025-04-17', 'completed', ''),
(4, 'pr5fvk', 1, '2025-04-17', 'completed', 'Take care of it'),
(5, 'wtjdxq', 1, '2025-04-17', 'completed', 'deliver on time'),
(6, 'wrh42r', 1, '2025-04-17', 'assigned', '');

--
-- Dumping data for table `rider`
--

INSERT INTO `rider` (`rider_id`, `rider_name`, `rider_address`, `rider_contact`, `rider_cnic`, `rider_image`, `rider_email`, `rider_password`, `rider_status`) VALUES
(1, 'ahmad', 'Gujranwala', '+923091234567', '347827564773', 'rider_1744314186.jpg', 'ahmad@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'approved');

--
-- Dumping data for table `rider_notification`
--

INSERT INTO `rider_notification` (`id`, `message`, `date`, `is_read`, `fk_rider_id`) VALUES
(1, 'Rider ahmad logged in at 2025-04-13 09:05:34', '2025-04-13', 'unread', 1),
(2, 'New order #1j3gi7 has been assigned to you', '2025-04-13', 'unread', 1),
(3, 'Rider ahmad logged in at 2025-04-13 09:56:15', '2025-04-13', 'unread', 1),
(4, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(5, 'Rider ahmad logged in at 2025-04-13 09:59:25', '2025-04-13', 'unread', 1),
(6, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(7, 'Rider ahmad logged in at 2025-04-13 10:04:28', '2025-04-13', 'unread', 1),
(8, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(9, 'Rider ahmad logged in at 2025-04-13 11:45:05', '2025-04-13', 'unread', 1),
(10, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(11, 'Rider ahmad logged in at 2025-04-13 11:55:19', '2025-04-13', 'unread', 1),
(12, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(13, 'Rider ahmad logged in at 2025-04-13 12:05:59', '2025-04-13', 'unread', 1),
(14, 'You have 1 pending deliveries', '2025-04-13', 'unread', 1),
(15, 'New order #ibd9zh has been assigned to you', '2025-04-13', 'unread', 1),
(16, 'New order #5dropj has been assigned to you', '2025-04-17', 'unread', 1),
(17, 'New order #pr5fvk has been assigned to you', '2025-04-17', 'unread', 1),
(18, 'New order #wtjdxq has been assigned to you', '2025-04-17', 'unread', 1),
(19, 'New order #wrh42r has been assigned to you', '2025-04-17', 'unread', 1),
(20, 'Rider ahmad logged in at 2025-04-17 17:10:24', '2025-04-17', 'unread', 1),
(21, 'You have 5 pending deliveries', '2025-04-17', 'unread', 1),
(22, 'Order #wrh42r has been assigned to you', '2025-04-17', 'unread', 1),
(23, 'Rider ahmad logged in at 2025-04-17 20:28:17', '2025-04-17', 'unread', 1),
(24, 'You have 2 pending deliveries', '2025-04-17', 'unread', 1),
(25, 'Rider ahmad logged in at 2025-04-18 05:18:40', '2025-04-18', 'unread', 1),
(26, 'You have 2 pending deliveries', '2025-04-18', 'unread', 1),
(27, 'Rider ahmad logged in at 2025-04-19 07:50:45', '2025-04-19', 'unread', 1),
(28, 'You have 2 pending deliveries', '2025-04-19', 'unread', 1);

--
-- Dumping data for table `warehouse`
--

INSERT INTO `warehouse` (`wh_id`, `wh_name`, `wh_area`) VALUES
(1, 'Rahwali Main', 'Gujranwala');

--
-- Dumping data for table `wholesaler`
--

INSERT INTO `wholesaler` (`ws_id`, `ws_name`, `ws_company_name`, `ws_home_address`, `ws_office_address`, `ws_personal_contact`, `ws_office_contact`, `ws_cnic`, `ws_image`, `ws_email`, `ws_status`, `ws_password`) VALUES
(1, 'Tayyab Saleh', 'Motic Slaers', 'Rahwali, gali#10, h#898-c', 'GT road, Rahwlai Chowk', '+923167763456', '+923268934563', '34509-9872329-9', '1wallpaperflare.com_wallpaper (8).jpg', 'sales@gmail.com', 'approved', '1a1dc91c907325c69271ddf0c944bc72'),
(2, 'Murtza', '99 Stores', 'rahwali gujranwala', 'rahwali Gujranwala', '+923451122360', '+923016889980', '11111-8092378-5', '2KB.exe', 'murtza@codsmin.com', 'approved', '984d8144fa08bfc637d2825463e184fa'),
(3, 'murtza', 'codsmine', 'bally wala', 'rahwali', '+923016889980', '+923451122360', '34101-8092378-5', '3Untitled-1.png', 'murtza@codsmin.com', 'approved', 'd0970714757783e6cf17b26fb8e2298f'),
(4, 'Sellwe', 'Sellwe', 'lskjflsakfjd', 'klsjflsdjf', '+923457678765', '+923426765432', '34565-0987654-0', '4wallpaperflare.com_wallpaper (2).jpg', 'sales1@gmail.com', 'pending', '1a1dc91c907325c69271ddf0c944bc72');

--
-- Dumping data for table `ws_notification`
--

INSERT INTO `ws_notification` (`id`, `message`, `date`, `is_read`, `fk_ws_id`) VALUES
(1, 'Your listed items have been sold, check Sold Items!', '2025-03-05', 'unread', 1),
(2, 'Your listed items have been sold, check Sold Items!', '2025-03-05', 'unread', 1),
(3, 'Your listed items have been sold, check Sold Items!', '2025-03-05', 'unread', 1),
(4, 'Your listed items have been sold, check Sold Items!', '2025-03-15', 'unread', 1),
(5, 'Your listed items have been sold, check Sold Items!', '2025-03-15', 'unread', 1),
(6, 'Your listed items have been sold, check Sold Items!', '2025-03-25', 'unread', 1),
(7, 'Your listed items have been sold, check Sold Items!', '2025-03-25', 'unread', 1),
(8, 'Your listed items have been sold, check Sold Items!', '2025-04-17', 'unread', 1);

--
-- Dumping data for table `ws_payment_records`
--

INSERT INTO `ws_payment_records` (`wsr_id`, `wsr_image`, `wsr_paid`, `date`, `fk_ws_id`) VALUES
(1, 'rec_pay1news.jpg', 6400, '2025-03-15', 1),
(2, 'rec_pay2#codsmine.jpg', 53300, '2025-03-23', 1),
(3, 'rec_pay3KB.exe', 17500, '2025-03-23', 1),
(4, 'rec_pay4New Text Document.txt', 50000, '2025-03-23', 1),
(5, 'rec_pay5payment.png', 50000, '2025-03-24', 1);

--
-- Dumping data for table `ws_pending_payments`
--

INSERT INTO `ws_pending_payments` (`wspp_id`, `wspp_amount`, `fk_ws_id`) VALUES
(1, 0, 1),
(2, 0, 1),
(3, 0, 1),
(4, 175240, 1),
(5, 7260, 1),
(6, 326040, 1),
(7, 326040, 1),
(8, 1575, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
