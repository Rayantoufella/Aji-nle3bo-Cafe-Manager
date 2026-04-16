CREATE DATABASE IF NOT EXISTS games_db;
USE games_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    nb_players INT DEFAULT 2,
    duration INT DEFAULT 30,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    description TEXT,
    image_url VARCHAR(500) DEFAULT NULL,
    status ENUM('available', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tables_cafe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number INT NOT NULL,
    capacity INT NOT NULL DEFAULT 4,
    status ENUM('available', 'occupied') DEFAULT 'available'
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    user_id INT DEFAULT NULL,
    number_of_people INT DEFAULT 1,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    table_id INT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (table_id) REFERENCES tables_cafe(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT DEFAULT NULL,
    game_id INT,
    table_id INT,
    start_time DATETIME NOT NULL,
    end_time DATETIME DEFAULT NULL,
    status ENUM('active', 'finished') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables_cafe(id) ON DELETE CASCADE
);

-- Seed categories
INSERT INTO categories (name) VALUES 
('Stratégie'), ('Ambiance'), ('Famille'), ('Experts');

-- Seed tables
INSERT INTO tables_cafe (number, capacity, status) VALUES
(1, 4, 'available'),
(2, 6, 'available'),
(3, 2, 'available'),
(4, 8, 'available'),
(5, 4, 'available'),
(6, 4, 'available'),
(7, 6, 'available'),
(8, 10, 'available'),
(9, 4, 'available'),
(10, 2, 'available'),
(11, 6, 'available'),
(12, 4, 'available');

-- Seed admin user (password: admin123)
INSERT INTO users (username, email, password, role) VALUES
('Admin', 'admin@ajil3bo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Seed games
INSERT INTO games (name, category_id, nb_players, duration, difficulty, description, image_url, status) VALUES
('Catan', 1, 4, 90, 'medium', 'Collectez et échangez des ressources pour construire des routes, des colonies et des villes.', NULL, 'available'),
('Dixit', 2, 6, 30, 'easy', 'Un jeu poétique et créatif où l''imagination guide vos choix.', NULL, 'available'),
('Ticket to Ride', 3, 5, 60, 'easy', 'Construisez des lignes de chemin de fer à travers le continent.', NULL, 'available'),
('Terraforming Mars', 4, 5, 120, 'hard', 'Transformez Mars en planète habitable dans ce jeu de stratégie complexe.', NULL, 'available'),
('Codenames', 2, 8, 20, 'easy', 'Un jeu de mots et de déduction en équipe passionnant.', NULL, 'available'),
('7 Wonders', 1, 7, 45, 'medium', 'Développez votre civilisation à travers trois âges.', NULL, 'available'),
('Azul', 3, 4, 45, 'medium', 'Créez les plus belles mosaïques dans ce jeu de placement de tuiles.', NULL, 'available'),
('Pandemic', 1, 4, 60, 'medium', 'Coopérez pour sauver l''humanité de maladies mortelles.', NULL, 'available'),
('Splendor', 3, 4, 30, 'easy', 'Devenez un marchand de pierres précieuses de la Renaissance.', NULL, 'available'),
('Scythe', 4, 5, 115, 'hard', 'Dirigez votre faction dans une Europe uchronique des années 1920.', NULL, 'available');