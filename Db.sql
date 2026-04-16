-- ═══════════════════════════════════════════════════
--  AJI L3BO CAFÉ — Complete Database Schema
--  DB Name: games_db
--  Run this in phpMyAdmin or MySQL CLI
-- ═══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS games_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE games_db;

-- ── CATEGORIES ────────────────────────────────────
CREATE TABLE IF NOT EXISTS categories (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── GAMES ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS games (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(150) NOT NULL,
  category_id INT UNSIGNED,
  nb_players  INT NOT NULL DEFAULT 2,
  duration    INT NOT NULL DEFAULT 30 COMMENT 'Minutes',
  difficulty  ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
  description TEXT,
  image_url   VARCHAR(500),
  status      ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── USERS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(80)  NOT NULL,
  email      VARCHAR(180) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLES_CAFE ───────────────────────────────────
CREATE TABLE IF NOT EXISTS tables_cafe (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  number   INT NOT NULL UNIQUE,
  capacity INT NOT NULL DEFAULT 4,
  status   ENUM('available','occupied') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── RESERVATIONS ──────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_name      VARCHAR(120) NOT NULL,
  phone            VARCHAR(30),
  user_id          INT UNSIGNED,
  table_id         INT UNSIGNED NOT NULL,
  reservation_date DATE NOT NULL,
  reservation_time TIME NOT NULL,
  number_of_people INT NOT NULL DEFAULT 2,
  status           ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (table_id) REFERENCES tables_cafe(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── SESSIONS ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS sessions (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  game_id    INT UNSIGNED,
  table_id   INT UNSIGNED,
  start_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_time   DATETIME,
  status     ENUM('active','finished') NOT NULL DEFAULT 'active',
  FOREIGN KEY (game_id)  REFERENCES games(id) ON DELETE SET NULL,
  FOREIGN KEY (table_id) REFERENCES tables_cafe(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ═══════════════════════════════════════════════════
--  SEED DATA
-- ═══════════════════════════════════════════════════

-- Categories
INSERT IGNORE INTO categories (id, name) VALUES
(1, 'Stratégie'),
(2, 'Ambiance'),
(3, 'Famille'),
(4, 'Experts'),
(5, 'Coopératif'),
(6, 'Cartes');

-- Games
INSERT IGNORE INTO games (id, name, category_id, nb_players, duration, difficulty, description, status) VALUES
(1, 'Catan', 1, 4, 90, 'medium', 'Build settlements, trade resources, and expand your empire across the island of Catan.', 'available'),
(2, 'Dixit', 2, 6, 30, 'easy', 'A dreamy storytelling game using beautiful illustrated cards. Find the card that matches the clue.', 'available'),
(3, 'Pandemic', 5, 4, 60, 'medium', 'Work together to stop four diseases from spreading across the world before it is too late.', 'available'),
(4, 'Ticket to Ride', 3, 5, 75, 'easy', 'Collect train cards to claim railway routes and connect cities across the map.', 'available'),
(5, 'Chess', 1, 2, 60, 'hard', 'The classic strategy game of kings and queens. Outsmart your opponent in this timeless duel.', 'available'),
(6, 'Codenames', 2, 8, 30, 'easy', 'Two rival spymasters know the secret identities of 25 agents. Their teammates must identify them using one-word clues.', 'available'),
(7, '7 Wonders', 1, 7, 45, 'medium', 'Lead an ancient civilization to glory over three ages. Draft cards to build your wonder.', 'available'),
(8, 'Splendor', 6, 4, 30, 'easy', 'Collect gems, buy development cards, and attract nobles to become the most prestigious merchant.', 'available');

-- Admin user (password: admin123)
INSERT IGNORE INTO users (id, username, email, password, role) VALUES
(1, 'Admin', 'admin@ajil3bo.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Tables
INSERT IGNORE INTO tables_cafe (id, number, capacity, status) VALUES
(1, 1, 4, 'available'),
(2, 2, 4, 'available'),
(3, 3, 6, 'available'),
(4, 4, 6, 'available'),
(5, 5, 8, 'available'),
(6, 6, 2, 'available');