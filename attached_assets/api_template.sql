-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 07:52 PM
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
-- Database: `web2`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_aggregators`
--

CREATE TABLE `api_aggregators` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `api_base_url` text NOT NULL,
  `agent_code` varchar(255) NOT NULL,
  `agent_token` varchar(255) NOT NULL,
  `api_version` varchar(50) DEFAULT 'v1',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_aggregators`
--

INSERT INTO `api_aggregators` (`id`, `name`, `api_base_url`, `agent_code`, `agent_token`, `api_version`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nexus', 'https://api.nexusggr.com/', 'firman', '82a084e79888b482034f2d4599e0251e', NULL, 1, '2025-04-18 17:39:39', '2025-04-18 17:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `api_endpoints`
--

CREATE TABLE `api_endpoints` (
  `id` int(11) NOT NULL,
  `aggregator_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `endpoint_url` text NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `request_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_body`)),
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`headers`)),
  `query_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`query_parameters`)),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_endpoints`
--

INSERT INTO `api_endpoints` (`id`, `aggregator_id`, `name`, `endpoint_url`, `method`, `request_body`, `headers`, `query_parameters`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Create New User', '/', 'POST', '{\n    \"method\": \"user_create\",\n    \"agent_code\": \"{{agent_code}}\",\n    \"agent_token\": \"{{agent_token}}\",\n    \"user_code\": \"{{user_code}}\"\n  }', NULL, NULL, 1, '2025-04-18 17:45:08', '2025-04-18 17:46:26'),
(4, 1, 'Deposit User Balance', '/', 'POST', '{\r\n    \"method\": \"user_deposit\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\",\r\n    \"amount\": \"{{amount}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:46:13', '2025-04-18 17:46:13'),
(5, 1, 'Withdraw User Balance', '/', 'POST', '{\r\n    \"method\": \"user_withdraw\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\",\r\n    \"amount\": \"{{amount}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:47:33', '2025-04-18 17:47:33'),
(6, 1, 'Reset User Balance (Single)', '/', 'POST', '{\r\n    \"method\": \"user_withdraw_reset\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:48:19', '2025-04-18 17:48:19'),
(7, 1, 'Reset User Balance (All)', '/', 'POST', '{\r\n    \"method\": \"user_withdraw_reset\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"all_users\": true\r\n  }', NULL, NULL, 1, '2025-04-18 17:48:28', '2025-04-18 17:48:28'),
(8, 1, 'Launch Game', '/', 'POST', '{\r\n    \"method\": \"game_launch\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\",\r\n    \"provider_code\": \"{{provider_code}}\",\r\n    \"game_code\": \"{{game_code}}\",\r\n    \"lang\": \"{{lang}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:48:56', '2025-04-18 17:48:56'),
(9, 1, 'Launch Lobby (No Game Code)', '/', 'POST', '{\r\n    \"method\": \"game_launch\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\",\r\n    \"provider_code\": \"{{provider_code}}\",\r\n    \"lang\": \"{{lang}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:49:15', '2025-04-18 17:49:15'),
(10, 1, 'Get Agent Balance', '/', 'POST', '{\r\n    \"method\": \"money_info\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:50:06', '2025-04-18 17:50:06'),
(11, 1, 'Get User Balance', '/', 'POST', '{\r\n    \"method\": \"money_info\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"user_code\": \"{{user_code}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:50:18', '2025-04-18 17:50:18'),
(12, 1, 'Get All Users Balance', '/', 'POST', '{\r\n    \"method\": \"money_info\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"all_users\": true\r\n  }', NULL, NULL, 1, '2025-04-18 17:50:18', '2025-04-18 17:50:18'),
(13, 1, 'Get Provider List', '/', 'POST', '{\r\n    \"method\": \"provider_list\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:51:35', '2025-04-18 17:51:35'),
(14, 1, 'Get Game List', '/', 'POST', '{\r\n    \"method\": \"game_list\",\r\n    \"agent_code\": \"{{agent_code}}\",\r\n    \"agent_token\": \"{{agent_token}}\",\r\n    \"provider_code\": \"{{provider_code}}\"\r\n  }', NULL, NULL, 1, '2025-04-18 17:51:36', '2025-04-18 17:51:36');

-- --------------------------------------------------------

--
-- Table structure for table `api_requests_log`
--

CREATE TABLE `api_requests_log` (
  `id` int(11) NOT NULL,
  `aggregator_id` int(11) NOT NULL,
  `endpoint_id` int(11) NOT NULL,
  `request_method` varchar(10) NOT NULL,
  `request_url` text NOT NULL,
  `request_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_body`)),
  `response_code` int(11) DEFAULT NULL,
  `response_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_body`)),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_request_templates`
--

CREATE TABLE `api_request_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'GET',
  `url_pattern` text NOT NULL,
  `default_headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_headers`)),
  `query_parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`query_parameters`)),
  `request_body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_body`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `name` varchar(150) NOT NULL,
  `code` varchar(100) NOT NULL,
  `provider_id` char(36) NOT NULL,
  `type` varchar(30) NOT NULL,
  `image_url` text DEFAULT NULL,
  `status` enum('active','maintenance','disabled') NOT NULL DEFAULT 'active',
  `has_demo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_categories`
--

CREATE TABLE `game_categories` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_category_map`
--

CREATE TABLE `game_category_map` (
  `game_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_providers`
--

CREATE TABLE `game_providers` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `aggregator_id` char(36) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `logo_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `banner_url` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `turnover_multiplier` decimal(5,2) NOT NULL DEFAULT 0.00,
  `turnover_period_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion_claims`
--

CREATE TABLE `promotion_claims` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `promotion_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_category_map`
--

CREATE TABLE `provider_category_map` (
  `provider_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_links`
--

CREATE TABLE `referral_links` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `program_id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `code` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_programs`
--

CREATE TABLE `referral_programs` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `name` varchar(150) NOT NULL,
  `code` varchar(50) NOT NULL,
  `reward_type` varchar(30) NOT NULL,
  `reward_value` decimal(16,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_rewards`
--

CREATE TABLE `referral_rewards` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `link_id` char(36) NOT NULL,
  `referred_user_id` char(36) NOT NULL,
  `reward_amount` decimal(16,2) NOT NULL,
  `rewarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `turnover_rules`
--

CREATE TABLE `turnover_rules` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `promotion_id` char(36) NOT NULL,
  `multiplier` decimal(5,2) NOT NULL,
  `period_days` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `referred_by_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `user_id` char(36) NOT NULL,
  `type` enum('main','bonus','aggregator') NOT NULL,
  `aggregator_id` char(36) DEFAULT NULL,
  `balance` decimal(16,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(5) NOT NULL DEFAULT 'IDR',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transfers`
--

CREATE TABLE `wallet_transfers` (
  `id` char(36) NOT NULL DEFAULT uuid(),
  `user_id` char(36) NOT NULL,
  `from_wallet_id` char(36) NOT NULL,
  `to_wallet_id` char(36) NOT NULL,
  `amount` decimal(16,2) NOT NULL,
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `transfer_type` enum('to_game','from_game','internal') NOT NULL,
  `aggregator_id` char(36) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_aggregators`
--
ALTER TABLE `api_aggregators`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_endpoints`
--
ALTER TABLE `api_endpoints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aggregator_id` (`aggregator_id`);

--
-- Indexes for table `api_requests_log`
--
ALTER TABLE `api_requests_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aggregator_id` (`aggregator_id`),
  ADD KEY `endpoint_id` (`endpoint_id`);

--
-- Indexes for table `api_request_templates`
--
ALTER TABLE `api_request_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Indexes for table `game_categories`
--
ALTER TABLE `game_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `game_category_map`
--
ALTER TABLE `game_category_map`
  ADD PRIMARY KEY (`game_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `game_providers`
--
ALTER TABLE `game_providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `aggregator_id` (`aggregator_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `promotion_claims`
--
ALTER TABLE `promotion_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `provider_category_map`
--
ALTER TABLE `provider_category_map`
  ADD PRIMARY KEY (`provider_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `referral_links`
--
ALTER TABLE `referral_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `referral_programs`
--
ALTER TABLE `referral_programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `referral_rewards`
--
ALTER TABLE `referral_rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_id` (`link_id`),
  ADD KEY `referred_user_id` (`referred_user_id`);

--
-- Indexes for table `turnover_rules`
--
ALTER TABLE `turnover_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotion_id` (`promotion_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`type`,`aggregator_id`),
  ADD KEY `aggregator_id` (`aggregator_id`);

--
-- Indexes for table `wallet_transfers`
--
ALTER TABLE `wallet_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `from_wallet_id` (`from_wallet_id`),
  ADD KEY `to_wallet_id` (`to_wallet_id`),
  ADD KEY `aggregator_id` (`aggregator_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_aggregators`
--
ALTER TABLE `api_aggregators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `api_endpoints`
--
ALTER TABLE `api_endpoints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `api_requests_log`
--
ALTER TABLE `api_requests_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_request_templates`
--
ALTER TABLE `api_request_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_endpoints`
--
ALTER TABLE `api_endpoints`
  ADD CONSTRAINT `api_endpoints_ibfk_1` FOREIGN KEY (`aggregator_id`) REFERENCES `api_aggregators` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `api_requests_log`
--
ALTER TABLE `api_requests_log`
  ADD CONSTRAINT `api_requests_log_ibfk_1` FOREIGN KEY (`aggregator_id`) REFERENCES `api_aggregators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `api_requests_log_ibfk_2` FOREIGN KEY (`endpoint_id`) REFERENCES `api_endpoints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `game_providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_category_map`
--
ALTER TABLE `game_category_map`
  ADD CONSTRAINT `game_category_map_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `game_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_providers`
--
ALTER TABLE `game_providers`
  ADD CONSTRAINT `game_providers_ibfk_1` FOREIGN KEY (`aggregator_id`) REFERENCES `aggregators` (`id`);

--
-- Constraints for table `promotion_claims`
--
ALTER TABLE `promotion_claims`
  ADD CONSTRAINT `promotion_claims_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_category_map`
--
ALTER TABLE `provider_category_map`
  ADD CONSTRAINT `provider_category_map_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `game_providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provider_category_map_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `game_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_links`
--
ALTER TABLE `referral_links`
  ADD CONSTRAINT `referral_links_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `referral_programs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_links_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_rewards`
--
ALTER TABLE `referral_rewards`
  ADD CONSTRAINT `referral_rewards_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `referral_links` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_rewards_ibfk_2` FOREIGN KEY (`referred_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `turnover_rules`
--
ALTER TABLE `turnover_rules`
  ADD CONSTRAINT `turnover_rules_ibfk_1` FOREIGN KEY (`promotion_id`) REFERENCES `promotions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallets_ibfk_2` FOREIGN KEY (`aggregator_id`) REFERENCES `aggregators` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallet_transfers`
--
ALTER TABLE `wallet_transfers`
  ADD CONSTRAINT `wallet_transfers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_transfers_ibfk_2` FOREIGN KEY (`from_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_transfers_ibfk_3` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_transfers_ibfk_4` FOREIGN KEY (`aggregator_id`) REFERENCES `aggregators` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
