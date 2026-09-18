-- Database Schema for Logistic Tracking System
-- Target Engine: MySQL 5.7+ / MariaDB 10.2+

CREATE DATABASE IF NOT EXISTS `logistic_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `logistic_db`;

-- 1. Table Hubs / Gudang / Transit Points
CREATE TABLE IF NOT EXISTS `hubs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `type` ENUM('CENTRAL_HUB', 'BRANCH_HUB', 'SORTING_CENTER', 'AGENT') DEFAULT 'BRANCH_HUB',
  `address` TEXT,
  `city` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table Users & Roles
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'operator_hub', 'courier', 'customer') NOT NULL DEFAULT 'operator_hub',
  `hub_id` INT NULL,
  `phone` VARCHAR(20),
  `avatar` VARCHAR(255) NULL,
  `status` ENUM('ACTIVE', 'INACTIVE') DEFAULT 'ACTIVE',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table Shipments / Data Pengiriman
CREATE TABLE IF NOT EXISTS `shipments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `resi_number` VARCHAR(50) NOT NULL UNIQUE,
  `sender_name` VARCHAR(100) NOT NULL,
  `sender_phone` VARCHAR(20) NOT NULL,
  `sender_address` TEXT NOT NULL,
  `receiver_name` VARCHAR(100) NOT NULL,
  `receiver_phone` VARCHAR(20) NOT NULL,
  `receiver_address` TEXT NOT NULL,
  `origin_hub_id` INT NULL,
  `current_hub_id` INT NULL,
  `destination_hub_id` INT NULL,
  `status` ENUM('DRAFT', 'RECEIVED_AT_HUB', 'SORTED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED') DEFAULT 'DRAFT',
  `weight_kg` DECIMAL(8,2) DEFAULT 1.00,
  `total_cost` DECIMAL(12,2) DEFAULT 0.00,
  `notes` TEXT NULL,
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`origin_hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`current_hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`destination_hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table Checkpoints / Log Tracking Perjalanan
CREATE TABLE IF NOT EXISTS `checkpoints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `shipment_id` INT NOT NULL,
  `hub_id` INT NULL,
  `user_id` INT NULL,
  `status` ENUM('DRAFT', 'RECEIVED_AT_HUB', 'SORTED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED') NOT NULL,
  `location_name` VARCHAR(150) NOT NULL,
  `notes` TEXT NULL,
  `photo_proof` VARCHAR(255) NULL,
  `scanned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`shipment_id`) REFERENCES `shipments`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table Manifests Perjalanan
CREATE TABLE IF NOT EXISTS `manifests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `manifest_number` VARCHAR(50) NOT NULL UNIQUE,
  `origin_hub_id` INT NULL,
  `destination_hub_id` INT NULL,
  `courier_id` INT NULL,
  `status` ENUM('OPEN', 'DEPARTED', 'ARRIVED', 'CLOSED') DEFAULT 'OPEN',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`origin_hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`destination_hub_id`) REFERENCES `hubs`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`courier_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table Manifest Items
CREATE TABLE IF NOT EXISTS `manifest_shipments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `manifest_id` INT NOT NULL,
  `shipment_id` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`manifest_id`) REFERENCES `manifests`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`shipment_id`) REFERENCES `shipments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Audit Logs Table
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initial Seed Data
INSERT INTO `hubs` (`id`, `code`, `name`, `type`, `address`, `city`) VALUES
(1, 'HUB-JKT', 'Jakarta Central Hub', 'CENTRAL_HUB', 'Jl. Gatot Subroto No. 88', 'Jakarta'),
(2, 'HUB-BDG', 'Bandung Sorting Hub', 'SORTING_CENTER', 'Jl. Soekarno Hatta No. 120', 'Bandung'),
(3, 'HUB-SUB', 'Surabaya Branch Hub', 'BRANCH_HUB', 'Jl. Basuki Rahmat No. 45', 'Surabaya')
ON DUPLICATE KEY UPDATE `code` = VALUES(`code`);

-- Password default for admin: admin123 (hashed with password_hash)
INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `hub_id`) VALUES
(1, 'admin', '$2y$10$wT2t2.HshO/o2FfK1kIyeuWzQ1bA1qS2Yn7P1w.x79Xo/v1c09mRO', 'Administrator', 'admin', 1),
(2, 'operator_jkt', '$2y$10$wT2t2.HshO/o2FfK1kIyeuWzQ1bA1qS2Yn7P1w.x79Xo/v1c09mRO', 'Budi Operator JKT', 'operator_hub', 1),
(3, 'kurir_jkt', '$2y$10$wT2t2.HshO/o2FfK1kIyeuWzQ1bA1qS2Yn7P1w.x79Xo/v1c09mRO', 'Doni Kurir JKT', 'courier', 1)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- Sample Shipment
INSERT INTO `shipments` (`id`, `resi_number`, `sender_name`, `sender_phone`, `sender_address`, `receiver_name`, `receiver_phone`, `receiver_address`, `origin_hub_id`, `current_hub_id`, `destination_hub_id`, `status`, `weight_kg`, `total_cost`, `created_by`) VALUES
(1, 'LOG-20260918-DEMO', 'PT Sukses Mandiri', '08123456789', 'Jl. Sudirman No. 10, Jakarta', 'Ahmad Rizal', '08987654321', 'Jl. Dago No. 45, Bandung', 1, 1, 2, 'RECEIVED_AT_HUB', 2.50, 35000.00, 1)
ON DUPLICATE KEY UPDATE `resi_number` = VALUES(`resi_number`);

INSERT INTO `checkpoints` (`id`, `shipment_id`, `hub_id`, `user_id`, `status`, `location_name`, `notes`) VALUES
(1, 1, 1, 1, 'DRAFT', 'Jakarta Central Hub', 'Paket berhasil dibuat dan diserahkan pengirim'),
(2, 1, 1, 2, 'RECEIVED_AT_HUB', 'Jakarta Central Hub', 'Paket telah diterima di Hub Jakarta')
ON DUPLICATE KEY UPDATE `id` = VALUES(`id`);
