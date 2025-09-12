-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2025 at 08:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `core1_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) NOT NULL,
  `user_type` enum('customer','admin','system') NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_type`, `user_id`, `action`, `resource_type`, `resource_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'customer', 1, 'order_placed', 'order', 1, 'Customer placed order ORD-2024-001', '192.168.1.100', NULL, '2025-09-11 00:11:14'),
(2, 'customer', 2, 'order_placed', 'order', 2, 'Customer placed order ORD-2024-002', '192.168.1.101', NULL, '2025-09-11 00:11:14'),
(3, 'admin', 1, 'order_updated', 'order', 1, 'Order status updated to delivered', '192.168.1.200', NULL, '2025-09-11 00:11:14'),
(4, 'customer', 1, 'product_reviewed', 'product', 1, 'Customer reviewed iPhone 15 Pro', '192.168.1.100', NULL, '2025-09-11 00:11:14'),
(5, 'admin', 1, 'seller_approved', 'seller', 1, 'Seller TechGear Pro approved', '192.168.1.200', NULL, '2025-09-11 00:11:14'),
(6, 'admin', 2, 'seller_rejected', 'seller', 3, 'Seller rejected', '::1', NULL, '2025-09-11 00:20:59'),
(7, 'admin', 1, 'product_featured_updated', 'product', 1, 'Product featured status toggled', '::1', NULL, '2025-09-11 01:13:42'),
(8, 'admin', 1, 'product_featured_updated', 'product', 1, 'Product featured status toggled', '::1', NULL, '2025-09-11 01:13:45'),
(9, 'admin', 1, 'product_status_updated', 'product', 3, 'Product status toggled', '::1', NULL, '2025-09-11 01:13:54'),
(10, 'admin', 1, 'product_status_updated', 'product', 3, 'Product status toggled', '::1', NULL, '2025-09-11 01:14:00'),
(11, 'admin', 1, 'seller_approved', 'seller', 6, 'Seller approved', '::1', NULL, '2025-09-11 09:44:38');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','admin','support_agent','content_manager') NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `permissions`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Admin', 'super_admin', NULL, 1, '2025-09-11 10:14:35', '2025-09-11 00:11:14', '2025-09-11 10:14:35'),
(2, 'manager', 'manager@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Store', 'Manager', 'admin', NULL, 1, '2025-09-11 00:18:36', '2025-09-11 00:11:14', '2025-09-11 00:18:36'),
(3, 'support', 'support@shopzone.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Support', 'Agent', 'support_agent', NULL, 1, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `variant_id` bigint(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(18, 1, 1, NULL, 2, 0.00, '2025-09-12 01:55:29', '2025-09-12 01:57:32'),
(19, 1, 10, NULL, 2, 0.00, '2025-09-12 02:13:52', '2025-09-12 03:06:27'),
(27, 9, 11, NULL, 1, 0.00, '2025-09-12 05:42:00', '2025-09-12 05:42:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `parent_id`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', 'electronics', 'Electronic devices and gadgets', NULL, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 'Fashion', 'fashion', 'Clothing and accessories', NULL, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 'Home & Garden', 'home-garden', 'Home improvement and garden supplies', NULL, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(4, 'Sports', 'sports', 'Sports equipment and fitness gear', NULL, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(5, 'Books', 'books', 'Books and educational materials', NULL, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(6, 'Smartphones', 'smartphones', 'Mobile phones and accessories', 1, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(7, 'Laptops', 'laptops', 'Laptops and computer accessories', 1, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(8, 'Men\'s Clothing', 'mens-clothing', 'Clothing for men', 2, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(9, 'Women\'s Clothing', 'womens-clothing', 'Clothing for women', 2, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(10, 'Furniture', 'furniture', 'Home furniture', 3, NULL, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) NOT NULL,
  `session_id` bigint(20) NOT NULL,
  `sender_type` enum('customer','agent','bot') NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','image','file','system') DEFAULT 'text',
  `file_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `agent_id` bigint(20) DEFAULT NULL,
  `status` enum('waiting','active','ended') DEFAULT 'waiting',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_sessions`
--

CREATE TABLE `customer_sessions` (
  `id` varchar(64) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_sessions`
--

INSERT INTO `customer_sessions` (`id`, `customer_id`, `ip_address`, `user_agent`, `payload`, `last_activity`, `created_at`) VALUES
('5e0776170c7c1641eb339a6dcf2c947be0cad32d40d42b448e4592a4d6f08c49', 9, '::1', 'Mozilla/5.0 (X11; Linux aarch64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 CrKey/1.54.250320', '{\"customer_id\":9,\"email\":\"jejaycoco@gmail.com\",\"name\":\"Jejay coco\",\"login_time\":1757649998}', '2025-09-12 04:06:38', '2025-09-12 04:06:38'),
('d5fccfe777f13aa02f6f5d641e6674190f0c417549867108b19f3241d5f542ca', 8, '::1', 'curl/8.15.0', '{\"customer_id\":8,\"email\":\"testuser2@example.com\",\"name\":\"Test User2\",\"login_time\":1757635992}', '2025-09-12 00:13:12', '2025-09-12 00:13:12'),
('e37a29be18f0528d020a882a0936eba20b3ab2a0d472838164851d5fa4f73ddb', 7, '::1', 'curl/8.15.0', '{\"customer_id\":7,\"email\":\"test@example.com\",\"name\":\"Test User\",\"login_time\":1757635678}', '2025-09-12 00:07:58', '2025-09-12 00:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint(20) NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `category_id`, `question`, `answer`, `sort_order`, `view_count`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'How do I place an order?', 'Simply browse our products, add items to your cart, and proceed to checkout. You can pay using various methods including credit card, GCash, PayMaya, or Cash on Delivery.', 0, 0, 1, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 2, 'How long does shipping take?', 'Standard shipping takes 2-5 business days within Metro Manila and 5-7 business days for provincial areas. Express shipping is available for faster delivery.', 0, 0, 1, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 3, 'What is your return policy?', 'We accept returns within 7 days of delivery for unused items in original packaging. Some restrictions apply for certain product categories.', 0, 0, 1, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(4, 4, 'How do I track my order?', 'You can track your order status in your account dashboard or use the tracking number provided in your confirmation email.', 0, 0, 1, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(5, 2, 'Do you offer free shipping?', 'Yes! We offer free shipping on orders over ₱2,000 within Metro Manila. Use promo code FREESHIP at checkout.', 0, 0, 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `faq_categories`
--

CREATE TABLE `faq_categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq_categories`
--

INSERT INTO `faq_categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Ordering', 'ordering', 'Questions about placing orders', 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 'Shipping', 'shipping', 'Shipping and delivery information', 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 'Returns', 'returns', 'Return and refund policies', 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(4, 'Account', 'account', 'Account management questions', 0, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` enum('order_update','promotion','product_back_in_stock','support_reply','system') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `created_at`) VALUES
(1, 1, 'order_update', 'Order Delivered', 'Your order ORD-2024-001 has been delivered successfully!', '{\"order_id\": 1, \"order_number\": \"ORD-2024-001\"}', 0, '2025-09-11 00:11:14'),
(2, 2, 'order_update', 'Order Shipped', 'Your order ORD-2024-002 is on the way!', '{\"order_id\": 2, \"order_number\": \"ORD-2024-002\"}', 0, '2025-09-11 00:11:14'),
(3, 3, 'promotion', 'Welcome Offer', 'Get 10% off your first order with code WELCOME10!', '{\"promo_code\": \"WELCOME10\"}', 0, '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) DEFAULT 0.00,
  `shipping_cost` decimal(12,2) DEFAULT 0.00,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `total_amount` decimal(12,2) NOT NULL,
  `shipping_first_name` varchar(100) DEFAULT NULL,
  `shipping_last_name` varchar(100) DEFAULT NULL,
  `shipping_company` varchar(255) DEFAULT NULL,
  `shipping_address_1` varchar(255) DEFAULT NULL,
  `shipping_address_2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_state` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `billing_first_name` varchar(100) DEFAULT NULL,
  `billing_last_name` varchar(100) DEFAULT NULL,
  `billing_company` varchar(255) DEFAULT NULL,
  `billing_address_1` varchar(255) DEFAULT NULL,
  `billing_address_2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_postal_code` varchar(20) DEFAULT NULL,
  `billing_country` varchar(100) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `courier_company` varchar(100) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `estimated_delivery_date` date DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `payment_method` enum('cod','card','gcash','paymaya','wallet') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `status`, `subtotal`, `tax_amount`, `shipping_cost`, `discount_amount`, `total_amount`, `shipping_first_name`, `shipping_last_name`, `shipping_company`, `shipping_address_1`, `shipping_address_2`, `shipping_city`, `shipping_state`, `shipping_postal_code`, `shipping_country`, `shipping_phone`, `billing_first_name`, `billing_last_name`, `billing_company`, `billing_address_1`, `billing_address_2`, `billing_city`, `billing_state`, `billing_postal_code`, `billing_country`, `billing_phone`, `courier_company`, `tracking_number`, `estimated_delivery_date`, `delivered_at`, `payment_method`, `payment_status`, `payment_reference`, `created_at`, `updated_at`) VALUES
(1, 'ORD-2024-001', 1, 'delivered', 52999.00, 6359.88, 0.00, 0.00, 59358.88, 'John', 'Doe', NULL, '123 Rizal Street', NULL, 'Makati', 'Metro Manila', '1200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'card', 'paid', NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 'ORD-2024-002', 2, 'shipped', 1899.00, 227.88, 150.00, 0.00, 2276.88, 'Jane', 'Smith', NULL, '456 Bonifacio Avenue', NULL, 'Quezon City', 'Metro Manila', '1100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'paid', NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 'ORD-2024-003', 1, 'processing', 12999.00, 1559.88, 0.00, 0.00, 14558.88, 'John', 'Doe', NULL, '123 Rizal Street', NULL, 'Makati', 'Metro Manila', '1200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cod', 'pending', NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(4, 'ORD-2024-004', 3, 'pending', 899.00, 107.88, 150.00, 0.00, 1156.88, 'Mike', 'Wilson', NULL, '321 EDSA Extension', NULL, 'Pasay', 'Metro Manila', '1300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'card', 'paid', NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(5, 'ORD-2024-005', 4, 'cancelled', 65999.00, 7919.88, 0.00, 0.00, 73918.88, 'Sarah', 'Brown', NULL, '654 Ortigas Center', NULL, 'Pasig', 'Metro Manila', '1605', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'card', 'refunded', NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(6, 'ORD-20250912-3197', 1, 'cancelled', 104196.00, 0.00, 50.00, 0.00, 104246.00, 'John', 'Doe', NULL, '123 Test Street', NULL, 'Manila', 'Metro Manila', '1000', NULL, '+639123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cod', 'pending', NULL, '2025-09-12 00:30:19', '2025-09-12 04:38:12'),
(7, 'ORD-20250912-8616', 9, 'pending', 158997.00, 0.00, 50.00, 0.00, 159047.00, 'Test', 'Customer', NULL, '123 Test Street', NULL, 'Test City', 'Test Province', '12345', NULL, '09123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'cod', 'pending', NULL, '2025-09-12 04:16:45', '2025-09-12 04:16:45'),
(8, 'ORD-20250912-4515', 9, 'pending', 52999.00, 0.00, 50.00, 0.00, 53049.00, 'Ronald', 'Gerida', NULL, 'Surigao-Davao Coastal Road', NULL, 'Tago', 'Surigao del Sur', '8302', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'pending', NULL, '2025-09-12 04:18:29', '2025-09-12 04:18:29'),
(9, 'ORD-20250912-8285', 9, 'pending', 8000.00, 0.00, 50.00, 0.00, 8050.00, 'Jejay', 'COco', NULL, 'acacia', NULL, 'tago', 'Surigao del Sur', '8302', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'pending', NULL, '2025-09-12 04:23:58', '2025-09-12 04:23:58'),
(10, 'ORD-20250912-6170', 9, 'cancelled', 200.00, 0.00, 50.00, 0.00, 250.00, 'Ronald', 'Gerida', NULL, 'acacia', NULL, 'tago', 'Surigao del Sur', '8302', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'paid', 'pi_dg4trVZLdQN1pkpUPUJbAZRZ', '2025-09-12 04:27:00', '2025-09-12 04:38:23'),
(11, 'ORD-20250912-7317', 9, 'shipped', 16000.00, 0.00, 50.00, 0.00, 16050.00, 'Ronald', 'Gerida', NULL, 'acacia', NULL, 'tago', 'Surigao del Sur', '8302', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'paid', 'pi_kxEhf9wkUCbnSrzXtf5hCgyp', '2025-09-12 04:28:22', '2025-09-12 04:52:24'),
(12, 'ORD-20250912-6997', 9, 'processing', 16000.00, 0.00, 50.00, 0.00, 16050.00, 'Ronald', 'Gerida', NULL, '1', NULL, 'Santa Cruz', 'Misamis Occidental', '7209', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'paid', 'pi_YE7Z9L5aKTZmV8hzmVrBVsLh', '2025-09-12 05:34:31', '2025-09-12 05:34:36'),
(13, 'ORD-20250912-1138', 9, 'processing', 52999.00, 0.00, 50.00, 0.00, 53049.00, 'Ronald', 'Gerida', NULL, '2', NULL, 'Santa Cruz', 'Misamis Occidental', '7209', NULL, '09076694171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'gcash', 'paid', 'pi_YrSHdDwznna4tiuK7fXRLYYM', '2025-09-12 05:38:34', '2025-09-12 05:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `variant_id` bigint(20) DEFAULT NULL,
  `seller_id` bigint(20) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `variant_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variant_details`)),
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `seller_id`, `product_name`, `product_sku`, `variant_details`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 1, 1, NULL, 1, 'iPhone 15 Pro', 'IP15PRO128', NULL, 1, 52999.00, 52999.00, '2025-09-11 00:11:14'),
(2, 2, 5, NULL, 2, 'Summer Dress Collection', 'SCD001S', NULL, 1, 1899.00, 1899.00, '2025-09-11 00:11:14'),
(3, 3, 3, NULL, 1, 'AirPods Pro 2nd Gen', 'APP2ND256', NULL, 1, 12999.00, 12999.00, '2025-09-11 00:11:14'),
(4, 4, 4, NULL, 2, 'Premium Cotton T-Shirt', 'TCT001M', NULL, 1, 899.00, 899.00, '2025-09-11 00:11:14'),
(5, 5, 2, NULL, 1, 'MacBook Air M2', 'MBA13M2256', NULL, 1, 65999.00, 65999.00, '2025-09-11 00:11:14'),
(6, 6, 11, NULL, 6, 'Perfume bottle', NULL, NULL, 3, 100.00, 300.00, '2025-09-12 00:30:19'),
(7, 6, 10, NULL, 6, 'Air Jordan 1 Retro', NULL, NULL, 3, 8000.00, 24000.00, '2025-09-12 00:30:19'),
(8, 6, 4, NULL, 2, 'Premium Cotton T-Shirt', NULL, NULL, 1, 899.00, 899.00, '2025-09-12 00:30:19'),
(9, 6, 1, NULL, 1, 'iPhone 15 Pro', NULL, NULL, 1, 52999.00, 52999.00, '2025-09-12 00:30:19'),
(10, 6, 3, NULL, 1, 'AirPods Pro 2nd Gen', NULL, NULL, 2, 12999.00, 25998.00, '2025-09-12 00:30:19'),
(11, 7, 1, NULL, 1, 'iPhone 15 Pro', NULL, NULL, 3, 52999.00, 158997.00, '2025-09-12 04:16:45'),
(12, 8, 1, NULL, 1, 'iPhone 15 Pro', NULL, NULL, 1, 52999.00, 52999.00, '2025-09-12 04:18:29'),
(13, 9, 10, NULL, 6, 'Air Jordan 1 Retro', NULL, NULL, 1, 8000.00, 8000.00, '2025-09-12 04:23:58'),
(14, 10, 11, NULL, 6, 'Perfume bottle', NULL, NULL, 2, 100.00, 200.00, '2025-09-12 04:27:00'),
(15, 11, 10, NULL, 6, 'Air Jordan 1 Retro', NULL, NULL, 2, 8000.00, 16000.00, '2025-09-12 04:28:22'),
(16, 12, 10, NULL, 6, 'Air Jordan 1 Retro', NULL, NULL, 2, 8000.00, 16000.00, '2025-09-12 05:34:31'),
(17, 13, 1, NULL, 1, 'iPhone 15 Pro', NULL, NULL, 1, 52999.00, 52999.00, '2025-09-12 05:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','refunded') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `notes`, `created_by`, `created_at`) VALUES
(1, 1, 'pending', 'Order placed successfully', NULL, '2025-09-11 00:11:14'),
(2, 1, 'processing', 'Payment confirmed, preparing for shipment', NULL, '2025-09-11 00:11:14'),
(3, 1, 'shipped', 'Order shipped via LBC Express', NULL, '2025-09-11 00:11:14'),
(4, 1, 'delivered', 'Order delivered successfully', NULL, '2025-09-11 00:11:14'),
(5, 2, 'pending', 'Order placed successfully', NULL, '2025-09-11 00:11:14'),
(6, 2, 'processing', 'Payment confirmed via GCash', NULL, '2025-09-11 00:11:14'),
(7, 2, 'shipped', 'Order shipped via J&T Express', NULL, '2025-09-11 00:11:14'),
(8, 3, 'pending', 'Order placed successfully', NULL, '2025-09-11 00:11:14'),
(9, 3, 'processing', 'COD order being prepared', NULL, '2025-09-11 00:11:14'),
(10, 4, 'pending', 'Order placed successfully', NULL, '2025-09-11 00:11:14'),
(11, 5, 'pending', 'Order placed successfully', NULL, '2025-09-11 00:11:14'),
(12, 5, 'cancelled', 'Customer requested cancellation', NULL, '2025-09-11 00:11:14'),
(13, 6, 'pending', 'Order placed', NULL, '2025-09-12 00:30:19'),
(14, 7, 'pending', 'Order placed', NULL, '2025-09-12 04:16:45'),
(15, 8, 'pending', 'Order placed', NULL, '2025-09-12 04:18:29'),
(16, 9, 'pending', 'Order placed', NULL, '2025-09-12 04:23:58'),
(17, 10, 'pending', 'Order placed', NULL, '2025-09-12 04:27:00'),
(18, 10, 'processing', 'Payment confirmed via GCash', NULL, '2025-09-12 04:27:06'),
(19, 11, 'pending', 'Order placed', NULL, '2025-09-12 04:28:22'),
(20, 11, 'processing', 'Payment confirmed via GCash', NULL, '2025-09-12 04:28:38'),
(21, 6, 'cancelled', '', 6, '2025-09-12 04:38:12'),
(22, 10, 'cancelled', '', 6, '2025-09-12 04:38:23'),
(23, 11, 'shipped', '', 6, '2025-09-12 04:52:24'),
(24, 12, 'pending', 'Order placed', NULL, '2025-09-12 05:34:31'),
(25, 12, 'processing', 'Payment confirmed via GCash', NULL, '2025-09-12 05:34:36'),
(26, 13, 'pending', 'Order placed', NULL, '2025-09-12 05:38:34'),
(27, 13, 'processing', 'Payment confirmed via GCash', NULL, '2025-09-12 05:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `payment_method` enum('gcash','card','paymaya','wallet') NOT NULL,
  `payment_intent_id` varchar(255) DEFAULT NULL,
  `payment_method_id` varchar(255) DEFAULT NULL,
  `paymongo_payment_id` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'PHP',
  `status` enum('pending','processing','succeeded','failed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `order_id`, `payment_method`, `payment_intent_id`, `payment_method_id`, `paymongo_payment_id`, `amount`, `currency`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, 'gcash', 'pi_2aqSXDP6YdV499yumBCYp2Yv', 'pm_HKtFXGKvFdEhQK4z7wRLhmcW', NULL, 53049.00, 'PHP', 'pending', '2025-09-12 04:18:30', '2025-09-12 04:18:30'),
(2, 9, 'gcash', 'pi_Ezqvnd3GKpHQryE3hUTe76e7', 'pm_6Ntd6MXd44ubx4E4RGL3syXv', NULL, 8050.00, 'PHP', 'pending', '2025-09-12 04:23:59', '2025-09-12 04:23:59'),
(3, 10, 'gcash', 'pi_dg4trVZLdQN1pkpUPUJbAZRZ', 'pm_RR5MEXCv36PCTRLa1ZxBDcsn', 'pay_mcNMncSv92FVKqbWi4ubCAXk', 250.00, 'PHP', 'succeeded', '2025-09-12 04:27:01', '2025-09-12 04:27:06'),
(4, 11, 'gcash', 'pi_kxEhf9wkUCbnSrzXtf5hCgyp', 'pm_XsJ6cEHs7yhtnzin63YHZSa5', 'pay_ebdZvbrShdYJe3ybHUVpWVnU', 16050.00, 'PHP', 'succeeded', '2025-09-12 04:28:23', '2025-09-12 04:28:38'),
(5, 12, 'gcash', 'pi_YE7Z9L5aKTZmV8hzmVrBVsLh', 'pm_oZToYq9n6xGVEzd5EkHY5s89', 'pay_1RyjNerZbKnqPRcCHr17rNYx', 16050.00, 'PHP', 'succeeded', '2025-09-12 05:34:32', '2025-09-12 05:34:36'),
(6, 13, 'gcash', 'pi_YrSHdDwznna4tiuK7fXRLYYM', 'pm_9ex6qcrSZEW1G7saLegpYdrs', 'pay_2XqEEci8zvwoPNLg9xFNVwTz', 53049.00, 'PHP', 'succeeded', '2025-09-12 05:38:35', '2025-09-12 05:38:39');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) NOT NULL,
  `seller_id` bigint(20) NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `compare_price` decimal(12,2) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT NULL,
  `weight` decimal(8,3) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `low_stock_threshold` int(11) DEFAULT 5,
  `manage_stock` tinyint(1) DEFAULT 1,
  `stock_status` enum('in_stock','out_of_stock','on_backorder') DEFAULT 'in_stock',
  `visibility` enum('visible','catalog','search','hidden') DEFAULT 'visible',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `featured` tinyint(1) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `name`, `slug`, `description`, `short_description`, `sku`, `price`, `compare_price`, `cost_price`, `weight`, `dimensions`, `stock_quantity`, `low_stock_threshold`, `manage_stock`, `stock_status`, `visibility`, `status`, `featured`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'iPhone 15 Pro', 'iphone-15-pro', 'Latest iPhone with advanced camera system and A17 Pro chip', 'Premium smartphone with cutting-edge technology', 'IP15PRO128', 52999.00, 55999.00, NULL, NULL, NULL, 19, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 00:11:14', '2025-09-12 05:38:34'),
(2, 1, 7, 'MacBook Air M2', 'macbook-air-m2', 'Thin and light laptop with M2 chip and all-day battery life', 'Ultra-portable laptop for professionals', 'MBA13M2256', 65999.00, 69999.00, NULL, NULL, NULL, 15, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 1, 1, 'AirPods Pro 2nd Gen', 'airpods-pro-2', 'Active noise cancellation with spatial audio', 'Premium wireless earbuds', 'APP2ND256', 12999.00, 14999.00, NULL, NULL, NULL, 48, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-11 00:11:14', '2025-09-12 00:30:19'),
(4, 2, 8, 'Premium Cotton T-Shirt', 'premium-cotton-tshirt', 'High-quality cotton t-shirt with modern fit', 'Comfortable everyday wear', 'TCT001M', 899.00, 1199.00, NULL, NULL, NULL, 99, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-11 00:11:14', '2025-09-12 00:30:19'),
(5, 2, 9, 'Summer Dress Collection', 'summer-dress', 'Elegant summer dress perfect for any occasion', 'Stylish and comfortable dress', 'SCD001S', 1899.00, 2299.00, NULL, NULL, NULL, 30, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(6, 5, 5, 'The Psychology of Money', 'psychology-of-money', 'Bestselling book about financial psychology and decision making', 'Must-read finance book', 'BK001POM', 599.00, 699.00, NULL, NULL, NULL, 75, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(7, 3, 10, 'Modern Office Chair', 'modern-office-chair', 'Ergonomic office chair with lumbar support', 'Comfortable work-from-home chair', 'MOC001BLK', 4999.00, 5999.00, NULL, NULL, NULL, 20, 5, 1, 'in_stock', 'visible', 'draft', 0, NULL, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(8, 4, 4, 'Professional Basketball', 'pro-basketball', 'Official size basketball for serious players', 'High-quality sports equipment', 'BB001PRO', 1299.00, 1599.00, NULL, NULL, NULL, 40, 5, 1, 'in_stock', 'visible', 'draft', 0, NULL, NULL, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(10, 6, 4, 'Air Jordan 1 Retro', 'air-jordan-1-retro', 'Jordan is the Goat!', 'Jordan\'s Shoes', 'GS3342D1', 8000.00, 500.00, NULL, NULL, '', 22, 5, 1, 'in_stock', 'visible', 'published', 0, '', '', '2025-09-11 11:06:30', '2025-09-12 05:34:31'),
(11, 6, 2, 'Perfume bottle', 'perfume', 'Magic bottle', 'Empty bottle', 'GS3342D6', 100.00, 25.00, NULL, NULL, '', 995, 5, 1, 'in_stock', 'visible', 'published', 0, '', '', '2025-09-11 14:21:15', '2025-09-12 04:27:00'),
(12, 7, NULL, 'iPhone 15 Pro Max', NULL, 'Latest Apple iPhone with titanium design, 48MP camera system, and A17 Pro chip', 'Premium flagship smartphone with advanced features', NULL, 79990.00, 89990.00, NULL, NULL, NULL, 25, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 23:59:48', '2025-09-11 23:59:48'),
(13, 7, NULL, 'Samsung Galaxy S24 Ultra', NULL, 'Flagship Android phone with S Pen, 200MP camera, and Galaxy AI features', 'Ultimate Android flagship with productivity features', NULL, 69990.00, 79990.00, NULL, NULL, NULL, 30, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 23:59:48', '2025-09-11 23:59:48'),
(14, 7, NULL, 'MacBook Air M3', NULL, 'Ultra-thin laptop with Apple M3 chip, 13-inch Liquid Retina display', 'Lightweight laptop for everyday computing', NULL, 69990.00, 74990.00, NULL, NULL, NULL, 15, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-11 23:59:48', '2025-09-11 23:59:48'),
(15, 7, NULL, 'Sony WH-1000XM5', NULL, 'Premium noise-canceling wireless headphones with 30-hour battery', 'Industry-leading noise cancellation headphones', NULL, 18990.00, 21990.00, NULL, NULL, NULL, 40, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-11 23:59:48', '2025-09-11 23:59:48'),
(16, 7, NULL, 'iPad Pro 11-inch', NULL, 'Powerful tablet with M4 chip and Apple Pencil Pro compatibility', 'Professional tablet for creative work', NULL, 54990.00, 59990.00, NULL, NULL, NULL, 20, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-11 23:59:48', '2025-09-11 23:59:48'),
(17, 8, NULL, 'Designer Denim Jacket', NULL, 'Premium denim jacket with vintage wash and modern fit', 'Stylish denim jacket for casual wear', NULL, 3499.00, 4299.00, NULL, NULL, NULL, 50, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:00', '2025-09-12 00:00:00'),
(18, 8, NULL, 'Silk Blouse - Elegant', NULL, 'Luxurious silk blouse perfect for office or evening wear', 'Premium silk blouse in multiple colors', NULL, 2799.00, 3499.00, NULL, NULL, NULL, 35, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:00', '2025-09-12 00:00:00'),
(19, 8, NULL, 'Summer Maxi Dress', NULL, 'Flowy maxi dress perfect for summer occasions and beach trips', 'Comfortable and stylish summer dress', NULL, 1999.00, 2499.00, NULL, NULL, NULL, 60, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:00', '2025-09-12 00:00:00'),
(20, 8, NULL, 'Leather Handbag', NULL, 'Genuine leather handbag with multiple compartments', 'Premium leather handbag for everyday use', NULL, 4999.00, 6299.00, NULL, NULL, NULL, 25, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:00', '2025-09-12 00:00:00'),
(21, 8, NULL, 'Casual Sneakers', NULL, 'Comfortable sneakers perfect for daily wear and light exercise', 'Trendy sneakers with superior comfort', NULL, 3299.00, 3999.00, NULL, NULL, NULL, 80, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:00', '2025-09-12 00:00:00'),
(22, 10, NULL, 'Nike Air Max 270', NULL, 'Iconic Nike sneakers with Max Air unit and breathable mesh upper', 'Comfortable running shoes with premium cushioning', NULL, 7299.00, 8499.00, NULL, NULL, NULL, 45, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(23, 10, NULL, 'Adidas Ultraboost 23', NULL, 'High-performance running shoes with Boost midsole technology', 'Premium running shoes for serious athletes', NULL, 9999.00, 11499.00, NULL, NULL, NULL, 30, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(24, 10, NULL, 'Yoga Mat Premium', NULL, 'Non-slip yoga mat with extra thickness for comfort', 'Professional yoga mat for all skill levels', NULL, 1899.00, 2299.00, NULL, NULL, NULL, 70, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(25, 10, NULL, 'Resistance Bands Set', NULL, 'Complete set of resistance bands for home workouts', 'Versatile exercise equipment for strength training', NULL, 1299.00, 1599.00, NULL, NULL, NULL, 100, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(26, 10, NULL, 'Basketball Official Size', NULL, 'Official size and weight basketball for indoor and outdoor play', 'Professional basketball for training and games', NULL, 2499.00, 2999.00, NULL, NULL, NULL, 55, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:11', '2025-09-12 00:00:11'),
(27, 11, NULL, 'Korean Skincare Set', NULL, 'Complete 10-step Korean skincare routine with premium ingredients', '10-step skincare routine for glowing skin', NULL, 4999.00, 6499.00, NULL, NULL, NULL, 40, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:22', '2025-09-12 00:00:22'),
(28, 11, NULL, 'Luxury Lipstick Collection', NULL, 'Set of 5 premium lipsticks in trending colors', 'Long-lasting matte and glossy lipsticks', NULL, 2799.00, 3499.00, NULL, NULL, NULL, 60, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:22', '2025-09-12 00:00:22'),
(29, 11, NULL, 'Anti-Aging Serum', NULL, 'Powerful anti-aging serum with vitamin C and hyaluronic acid', 'Professional-grade anti-aging treatment', NULL, 3299.00, 3999.00, NULL, NULL, NULL, 35, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:22', '2025-09-12 00:00:22'),
(30, 11, NULL, 'Makeup Brush Set', NULL, 'Professional 12-piece makeup brush set with storage case', 'Complete brush set for flawless makeup application', NULL, 1999.00, 2499.00, NULL, NULL, NULL, 80, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:22', '2025-09-12 00:00:22'),
(31, 12, NULL, 'Programming Books Bundle', NULL, 'Collection of 5 essential programming books for developers', 'Must-have books for software development', NULL, 3999.00, 4999.00, NULL, NULL, NULL, 30, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:39', '2025-09-12 00:00:39'),
(32, 12, NULL, 'Fiction Novel Bestsellers', NULL, 'Set of 3 current bestselling fiction novels', 'Popular fiction books for leisure reading', NULL, 1799.00, 2199.00, NULL, NULL, NULL, 50, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:39', '2025-09-12 00:00:39'),
(33, 12, NULL, 'Study Planner 2024', NULL, 'Comprehensive study planner with goal tracking and schedules', 'Academic planner for students', NULL, 899.00, 1199.00, NULL, NULL, NULL, 100, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:39', '2025-09-12 00:00:39'),
(34, 12, NULL, 'Premium Notebook Set', NULL, 'Set of 3 high-quality notebooks with different ruling', 'Professional notebooks for work and study', NULL, 1299.00, 1599.00, NULL, NULL, NULL, 75, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:39', '2025-09-12 00:00:39'),
(35, 13, NULL, 'Premium Dog Food 15kg', NULL, 'High-quality dry dog food with real chicken and vegetables', 'Nutritious dog food for healthy pets', NULL, 2899.00, 3299.00, NULL, NULL, NULL, 40, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:00:50', '2025-09-12 00:00:50'),
(36, 13, NULL, 'Cat Scratching Post', NULL, 'Tall scratching post with multiple levels and hanging toys', 'Entertainment and exercise for indoor cats', NULL, 1899.00, 2299.00, NULL, NULL, NULL, 25, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:50', '2025-09-12 00:00:50'),
(37, 13, NULL, 'Pet Carrier Bag', NULL, 'Comfortable and secure pet carrier for travel', 'Safe transportation for small to medium pets', NULL, 2299.00, 2699.00, NULL, NULL, NULL, 30, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:50', '2025-09-12 00:00:50'),
(38, 13, NULL, 'Dog Leash & Collar Set', NULL, 'Durable leash and collar set in various colors and sizes', 'Essential walking accessories for dogs', NULL, 799.00, 999.00, NULL, NULL, NULL, 60, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:00:50', '2025-09-12 00:00:50'),
(39, 14, NULL, 'Monstera Deliciosa Plant', NULL, 'Beautiful large-leaf houseplant perfect for indoor decoration', 'Popular houseplant for modern homes', NULL, 1299.00, 1599.00, NULL, NULL, NULL, 20, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:01:00', '2025-09-12 00:01:00'),
(40, 14, NULL, 'Garden Tool Set', NULL, 'Complete set of essential gardening tools with storage bag', 'Professional gardening tools for home gardens', NULL, 2499.00, 2999.00, NULL, NULL, NULL, 35, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:01:00', '2025-09-12 00:01:00'),
(41, 14, NULL, 'Succulent Collection', NULL, 'Set of 6 different succulent plants in decorative pots', 'Low-maintenance plants perfect for beginners', NULL, 1899.00, 2299.00, NULL, NULL, NULL, 45, 5, 1, 'in_stock', 'visible', 'published', 1, NULL, NULL, '2025-09-12 00:01:00', '2025-09-12 00:01:00'),
(42, 14, NULL, 'Outdoor Planter Set', NULL, 'Set of 3 weather-resistant planters in different sizes', 'Durable planters for outdoor gardening', NULL, 3299.00, 3999.00, NULL, NULL, NULL, 25, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:01:00', '2025-09-12 00:01:00'),
(43, 14, NULL, 'Fertilizer Organic Mix', NULL, 'Organic fertilizer perfect for all types of plants', 'Eco-friendly plant nutrition solution', NULL, 699.00, 899.00, NULL, NULL, NULL, 80, 5, 1, 'in_stock', 'visible', 'published', 0, NULL, NULL, '2025-09-12 00:01:00', '2025-09-12 00:01:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `alt_text`, `sort_order`, `is_primary`, `created_at`) VALUES
(1, 1, '/Core1_ecommerce/uploads/products/iphone.jpg', 'iPhone 15 Pro front view', 0, 1, '2025-09-11 00:11:14'),
(2, 1, '/Core1_ecommerce/uploads/products/apple-iphone-15-pro-6-1.avif', 'iPhone 15 Pro back view', 1, 0, '2025-09-11 00:11:14'),
(3, 2, 'https://via.placeholder.com/300x200?text=Product+Image', 'MacBook Air open view', 0, 1, '2025-09-11 00:11:14'),
(4, 2, 'https://via.placeholder.com/300x200?text=Product+Image', 'MacBook Air closed view', 1, 0, '2025-09-11 00:11:14'),
(5, 3, 'https://via.placeholder.com/300x200?text=Product+Image', 'AirPods Pro with case', 0, 1, '2025-09-11 00:11:14'),
(6, 4, 'https://via.placeholder.com/300x200?text=Product+Image', 'Cotton T-Shirt front', 0, 1, '2025-09-11 00:11:14'),
(7, 5, 'https://via.placeholder.com/300x200?text=Product+Image', 'Summer dress model', 0, 1, '2025-09-11 00:11:14'),
(8, 6, 'https://via.placeholder.com/300x200?text=Product+Image', 'Psychology of Money cover', 0, 1, '2025-09-11 00:11:14'),
(10, 10, '/Core1_ecommerce/uploads/products/68c2ad34ecc7e_1757588788.jpeg', '', 0, 1, '2025-09-11 11:06:30'),
(11, 11, '/Core1_ecommerce/uploads/products/68c2dada22fa7_1757600474.webp', '', 0, 1, '2025-09-11 14:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(255) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `is_verified_purchase` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `helpful_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `order_id`, `rating`, `title`, `review_text`, `images`, `is_verified_purchase`, `is_approved`, `helpful_count`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 5, 'Amazing phone!', 'The iPhone 15 Pro exceeded my expectations. Camera quality is outstanding and battery life is great.', NULL, 1, 1, 0, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 5, 2, 2, 4, 'Beautiful dress', 'Love the design and fabric quality. Fits perfectly and very comfortable to wear.', NULL, 1, 1, 0, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 4, 4, 4, 5, 'Great quality t-shirt', 'Soft cotton material and the fit is perfect. Will definitely order more colors.', NULL, 1, 1, 0, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `variant_name` varchar(100) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` bigint(20) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `type` enum('percentage','fixed_amount','free_shipping') NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `minimum_order_amount` decimal(12,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_count` int(11) DEFAULT 0,
  `user_limit` int(11) DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promo_codes`
--

INSERT INTO `promo_codes` (`id`, `code`, `description`, `type`, `value`, `minimum_order_amount`, `usage_limit`, `usage_count`, `user_limit`, `starts_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Welcome discount for new customers', 'percentage', 10.00, 1000.00, 100, 0, 1, '2025-09-11 00:11:14', '2025-10-11 00:11:14', 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 'FREESHIP', 'Free shipping on orders over 2000', 'free_shipping', 0.00, 2000.00, NULL, 0, 1, '2025-09-11 00:11:14', '2025-11-10 00:11:14', 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 'SAVE500', 'Save 500 pesos on orders over 5000', 'fixed_amount', 500.00, 5000.00, 50, 0, 1, '2025-09-11 00:11:14', '2025-09-26 00:11:14', 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `promo_code_usage`
--

CREATE TABLE `promo_code_usage` (
  `id` bigint(20) NOT NULL,
  `promo_code_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `discount_amount` decimal(12,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_requests`
--

CREATE TABLE `return_requests` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL,
  `order_item_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` enum('return','exchange','refund') NOT NULL,
  `reason` enum('defective','wrong_item','not_as_described','changed_mind','damaged_shipping','other') NOT NULL,
  `reason_details` text DEFAULT NULL,
  `status` enum('requested','approved','rejected','processing','completed') DEFAULT 'requested',
  `refund_amount` decimal(12,2) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_slug` varchar(255) DEFAULT NULL,
  `store_description` text DEFAULT NULL,
  `store_logo` varchar(500) DEFAULT NULL,
  `store_banner` varchar(500) DEFAULT NULL,
  `business_type` enum('individual','business') DEFAULT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','suspended','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `store_name`, `store_slug`, `store_description`, `store_logo`, `store_banner`, `business_type`, `tax_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'TechGear Pro', 'techgear-pro', 'Premium electronics and gadgets for tech enthusiasts', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 2, 'Fashion Hub', 'fashion-hub', 'Trendy clothing and accessories for all ages', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 3, 'Home Essentials', 'home-essentials', 'Quality home goods and furniture', NULL, NULL, 'individual', NULL, 'rejected', '2025-09-11 00:11:14', '2025-09-11 00:20:59'),
(4, 4, 'Sports Arena', 'sports-arena', 'Sports equipment and athletic wear', NULL, NULL, 'business', NULL, 'pending', '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(5, 5, 'Book Corner', 'book-corner', 'Books, magazines, and educational materials', NULL, NULL, 'individual', NULL, 'approved', '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(6, 6, 'ClothoGo', 'clothogo', '', NULL, NULL, 'individual', '', 'approved', '2025-09-11 09:43:49', '2025-09-11 09:44:38'),
(7, NULL, 'TechHub Electronics', NULL, 'Leading provider of consumer electronics, smartphones, laptops, and tech gadgets', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(8, NULL, 'Fashion Forward', NULL, 'Trendy clothing and accessories for modern lifestyle. Latest fashion from casual to formal wear', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(9, NULL, 'Home Essentials', NULL, 'Everything you need for a comfortable home - furniture, decor, kitchen appliances', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(10, NULL, 'Sports World', NULL, 'Athletic gear and sporting goods for all sports - fitness equipment, sportswear, accessories', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(11, NULL, 'Beauty Zone', NULL, 'Premium beauty and skincare products, cosmetics, and wellness items', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(12, NULL, 'Book Haven', NULL, 'Your one-stop shop for books, educational materials, and stationery supplies', NULL, NULL, 'individual', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(13, NULL, 'Pet Paradise', NULL, 'Complete pet care solutions - food, toys, accessories for all your furry friends', NULL, NULL, 'business', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20'),
(14, NULL, 'Garden Glory', NULL, 'Plants, gardening tools, and outdoor decoration items for beautiful spaces', NULL, NULL, 'individual', NULL, 'approved', '2025-09-11 23:59:20', '2025-09-11 23:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `seller_preferences`
--

CREATE TABLE `seller_preferences` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `email_new_orders` tinyint(1) DEFAULT 1,
  `email_order_updates` tinyint(1) DEFAULT 1,
  `email_customer_messages` tinyint(1) DEFAULT 0,
  `email_marketing_updates` tinyint(1) DEFAULT 1,
  `sms_urgent_orders` tinyint(1) DEFAULT 1,
  `sms_payment_issues` tinyint(1) DEFAULT 0,
  `timezone` varchar(50) DEFAULT 'UTC-5',
  `currency` varchar(10) DEFAULT 'USD',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seller_preferences`
--

INSERT INTO `seller_preferences` (`id`, `seller_id`, `email_new_orders`, `email_order_updates`, `email_customer_messages`, `email_marketing_updates`, `sms_urgent_orders`, `sms_payment_issues`, `timezone`, `currency`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 1, 0, 1, 1, 0, 'UTC-5', 'USD', '2025-09-11 10:26:59', '2025-09-11 10:26:59');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'ShopZone E-commerce', 'string', 'Website name', 1, '2025-09-10 23:32:43', '2025-09-11 00:11:14'),
(2, 'default_currency', 'PHP', 'string', 'Default currency code', 1, '2025-09-10 23:32:43', '2025-09-10 23:32:43'),
(3, 'tax_rate', '12.00', 'string', 'Default tax rate percentage', 0, '2025-09-10 23:32:43', '2025-09-10 23:32:43'),
(4, 'free_shipping_threshold', '1000.00', 'string', 'Minimum order for free shipping', 1, '2025-09-10 23:32:43', '2025-09-10 23:32:43'),
(5, 'order_processing_days', '1-2', 'string', 'Order processing time', 1, '2025-09-10 23:32:43', '2025-09-10 23:32:43'),
(6, 'support_phone', '+63-917-123-4567', 'string', 'Customer support phone', 1, '2025-09-10 23:32:43', '2025-09-11 00:11:14'),
(7, 'support_email', 'support@shopzone.com', 'string', 'Customer support email', 1, '2025-09-10 23:32:43', '2025-09-11 00:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint(20) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` enum('order','product','payment','shipping','technical','other') NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('open','in_progress','waiting_customer','resolved','closed') DEFAULT 'open',
  `order_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket_messages`
--

CREATE TABLE `support_ticket_messages` (
  `id` bigint(20) NOT NULL,
  `ticket_id` bigint(20) NOT NULL,
  `sender_type` enum('customer','agent','system') NOT NULL,
  `sender_id` bigint(20) DEFAULT NULL,
  `message` text NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `is_internal` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `phone_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','suspended','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `phone`, `profile_image`, `first_name`, `last_name`, `date_of_birth`, `gender`, `email_verified`, `phone_verified`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456789', NULL, 'John', 'Doe', NULL, NULL, 1, 1, 'suspended', '2025-09-11 00:11:14', '2025-09-11 04:16:24', NULL),
(2, 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456790', NULL, 'Jane', 'Smith', NULL, NULL, 1, 1, 'active', '2025-09-11 00:11:14', '2025-09-11 00:11:14', NULL),
(3, 'mike.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456791', NULL, 'Mike', 'Wilson', NULL, NULL, 1, 0, 'active', '2025-09-11 00:11:14', '2025-09-11 00:11:14', NULL),
(4, 'sarah.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456792', NULL, 'Sarah', 'Brown', NULL, NULL, 1, 1, 'active', '2025-09-11 00:11:14', '2025-09-11 00:11:14', NULL),
(5, 'david.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+639123456793', NULL, 'David', 'Lee', NULL, NULL, 1, 1, 'active', '2025-09-11 00:11:14', '2025-09-11 00:11:14', NULL),
(6, 'ronaldgereda@gmail.com', '$2y$10$yKpJjv6WY6E95vXFnC0WROI2DZuDfhHu7m0DjBqqVH/GjS3KLD446', '09076694171', NULL, 'Ronald', 'Gerida', NULL, NULL, 0, 0, 'active', '2025-09-11 09:43:49', '2025-09-11 10:29:39', NULL),
(7, 'test@example.com', '$2y$10$Qmg9yoJYdB.mlrc91k59y.nv5gC1wGwDMQGwVweWdFkj8pdWpPTGu', NULL, NULL, 'Test', 'User', NULL, NULL, 0, 0, 'active', '2025-09-12 00:07:51', '2025-09-12 00:07:51', NULL),
(8, 'testuser2@example.com', '$2y$10$OEgZJ2Q8YRScTqY8AABul.JNpQdIOdo7PLMKI9bCOjKQRLXA4534K', NULL, NULL, 'Test', 'User2', NULL, NULL, 0, 0, 'active', '2025-09-12 00:13:06', '2025-09-12 00:13:06', NULL),
(9, 'jejaycoco@gmail.com', '$2y$10$YVB6qk8N1WtpOJtluAGAQe.zWLbVv28m9B1hovl/I/PNHhvLYgzka', '09076694321', '/Core1_ecommerce/uploads/profiles/profile_9_1757656974_725a2692b62c16d3.jpg', 'Jejay', 'coco', NULL, NULL, 0, 0, 'active', '2025-09-12 00:19:17', '2025-09-12 06:02:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` enum('home','office','other') DEFAULT 'home',
  `label` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Philippines',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `type`, `label`, `first_name`, `last_name`, `company`, `address_line_1`, `address_line_2`, `city`, `state`, `postal_code`, `country`, `latitude`, `longitude`, `phone`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'home', 'Home Address', 'John', 'Doe', NULL, '123 Rizal Street', NULL, 'Makati', 'Metro Manila', '1200', 'Philippines', NULL, NULL, NULL, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(2, 2, 'home', 'Home Address', 'Jane', 'Smith', NULL, '456 Bonifacio Avenue', NULL, 'Quezon City', 'Metro Manila', '1100', 'Philippines', NULL, NULL, NULL, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(3, 2, 'office', 'Office Address', 'Jane', 'Smith', NULL, '789 BGC Central', NULL, 'Taguig', 'Metro Manila', '1630', 'Philippines', NULL, NULL, NULL, 0, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(4, 3, 'home', 'Home Address', 'Mike', 'Wilson', NULL, '321 EDSA Extension', NULL, 'Pasay', 'Metro Manila', '1300', 'Philippines', NULL, NULL, NULL, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(5, 4, 'home', 'Home Address', 'Sarah', 'Brown', NULL, '654 Ortigas Center', NULL, 'Pasig', 'Metro Manila', '1605', 'Philippines', NULL, NULL, NULL, 1, '2025-09-11 00:11:14', '2025-09-11 00:11:14'),
(6, 9, 'home', NULL, 'Ronald', 'Gerida', NULL, '2', NULL, 'Santa Cruz', 'Misamis Occidental', '7209', 'Philippines', 8.62202210, 123.68468770, '09076694171', 1, '2025-09-12 05:38:34', '2025-09-12 05:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist_items`
--

CREATE TABLE `wishlist_items` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_resource` (`resource_type`,`resource_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`,`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `customer_sessions`
--
ALTER TABLE `customer_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_customer_id` (`customer_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`);
ALTER TABLE `faqs` ADD FULLTEXT KEY `idx_search` (`question`,`answer`);

--
-- Indexes for table `faq_categories`
--
ALTER TABLE `faq_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_unread` (`user_id`,`is_read`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_tracking` (`tracking_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_seller` (`seller_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_payment_intent` (`payment_intent_id`),
  ADD KEY `idx_order_payment` (`order_id`,`payment_method`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_seller` (`seller_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product_order` (`user_id`,`product_id`,`order_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_approved` (`is_approved`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_sku` (`sku`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_dates` (`starts_at`,`expires_at`);

--
-- Indexes for table `promo_code_usage`
--
ALTER TABLE `promo_code_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_promo_code` (`promo_code_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `idx_order` (`order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `store_slug` (`store_slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_store_slug` (`store_slug`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `seller_preferences`
--
ALTER TABLE `seller_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seller_id` (`seller_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_public` (`is_public`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_ticket_number` (`ticket_number`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ticket` (`ticket_id`),
  ADD KEY `idx_sender` (`sender_type`,`sender_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_default` (`is_default`),
  ADD KEY `idx_location` (`latitude`,`longitude`);

--
-- Indexes for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faq_categories`
--
ALTER TABLE `faq_categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `promo_code_usage`
--
ALTER TABLE `promo_code_usage`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `seller_preferences`
--
ALTER TABLE `seller_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_sessions`
--
ALTER TABLE `customer_sessions`
  ADD CONSTRAINT `customer_sessions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `faq_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `order_items_ibfk_4` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promo_code_usage`
--
ALTER TABLE `promo_code_usage`
  ADD CONSTRAINT `promo_code_usage_ibfk_1` FOREIGN KEY (`promo_code_id`) REFERENCES `promo_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promo_code_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promo_code_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `return_requests`
--
ALTER TABLE `return_requests`
  ADD CONSTRAINT `return_requests_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `return_requests_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `return_requests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `support_ticket_messages`
--
ALTER TABLE `support_ticket_messages`
  ADD CONSTRAINT `support_ticket_messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist_items`
--
ALTER TABLE `wishlist_items`
  ADD CONSTRAINT `wishlist_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
