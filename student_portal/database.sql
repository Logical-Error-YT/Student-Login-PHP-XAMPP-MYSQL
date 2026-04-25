-- ============================================================
-- StudentPortal — Database Schema
-- ============================================================
-- Instructions:
--   1. Open phpMyAdmin (http://localhost/phpmyadmin)
--   2. Click "Import" → choose this file → click "Go"
--   OR run in terminal: mysql -u root -p < database.sql
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `student_portal`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `student_portal`;

-- ── Users Table ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,       -- bcrypt hash (60–72 chars)
  `course`     VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)           -- enforce unique email

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Optional: seed a demo user ───────────────────────────────
-- Password: demo123  (hashed via password_hash)
-- You can delete this row after testing.
INSERT INTO `users` (`name`, `email`, `password`, `course`) VALUES (
  'Demo Student',
  'demo@student.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'B.Tech Computer Science'
);
