-- CareDesk Database Schema & Seed Data
CREATE DATABASE IF NOT EXISTS `caredesk_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `caredesk_db`;

DROP TABLE IF EXISTS `appointments`;
DROP TABLE IF EXISTS `doctors`;
DROP TABLE IF EXISTS `users`;

-- Users Table
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('patient', 'admin') DEFAULT 'patient',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Doctors Table
CREATE TABLE `doctors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `specialty` VARCHAR(100) NOT NULL,
    `room_no` VARCHAR(20) NOT NULL,
    `fee` DECIMAL(10,2) NOT NULL,
    `available_days` VARCHAR(100) NOT NULL DEFAULT 'Monday - Friday'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointments Table
CREATE TABLE `appointments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `doctor_id` INT NOT NULL,
    `appointment_date` DATE NOT NULL,
    `appointment_time` TIME NOT NULL,
    `symptoms` TEXT NOT NULL,
    `status` ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_appointment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_appointment_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Doctors
INSERT INTO `doctors` (`name`, `specialty`, `room_no`, `fee`, `available_days`) VALUES
('Dr. Aisha Sharma', 'Cardiologist', 'Room 101', 800.00, 'Mon, Wed, Fri'),
('Dr. Vikram Verma', 'Dermatologist', 'Room 205', 500.00, 'Tue, Thu, Sat'),
('Dr. Neha Patel', 'General Physician', 'Room 102', 400.00, 'Mon to Sat'),
('Dr. Rajesh Rao', 'Neurologist', 'Room 304', 1000.00, 'Wed, Sat');

-- Seed Users: Admin (Admin@123) and Patient (Patient@123)
INSERT INTO `users` (`full_name`, `email`, `phone`, `password`, `role`) VALUES
('Hospital Administrator', 'admin@caredesk.com', '9876543210', '$2y$10$i2qT8g4bZ76k8hD9m6qN..7N0RSmFw4eXpB7v.u8nKq8K8nU8m0Kq', 'admin'),
('John Doe', 'patient@caredesk.com', '9123456780', '$2y$10$wO7vF1tB4fK9mP8rD2bS..5X9K4kQ5vB8sR9mK6rL4kP2vX6mP8rD', 'patient');