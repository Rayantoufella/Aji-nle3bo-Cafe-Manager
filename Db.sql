CREATE DATABASE IF NOT EXISTS games_db;
USE games_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role ENUM('admin', 'user'),
    created_at DATETIME default CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    categories_id INT,
    nb_players INT,
    duration INT,
    difficulty ENUM('easy', 'medium', 'hard'),
    description TEXT,
    status ENUM('available', 'unavailable'),
    created_at TIMESTAMP default CURRENT_TIMESTAMP,

    FOREIGN KEY (categories_id) REFERENCES categories(id)
);

CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    number INT,
    capacity INT,
    status ENUM('available', 'occupied')
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255),
    phone VARCHAR(50),
    number_of_people INT,
    reservation_date DATE,
    reservation_time TIME,
    table_id INT,
    status ENUM('pending', 'confirmed', 'cancelled')  DEFAULT 'pending',
    created_at TIMESTAMP default CURRENT_TIMESTAMP,

    FOREIGN KEY (table_id) REFERENCES tables(id)
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT,
    game_id INT,
    table_id INT,
    start_time DATETIME,
    end_time DATETIME,
    status ENUM('active', 'finished'),

    FOREIGN KEY (reservation_id) REFERENCES reservations(id),
    FOREIGN KEY (game_id) REFERENCES games(id),
    FOREIGN KEY (table_id) REFERENCES tables(id)
);



INSERT IGNORE INTO categories (id, name) VALUES
                                             (1, 'Stratégie'),
                                             (2, 'Ambiance'),
                                             (3, 'Famille'),
                                             (4, 'Experts'),
                                             (5, 'Coopératif'),
                                             (6, 'Cartes');

-- Games
INSERT IGNORE INTO games (id, name, categories_id, nb_players, duration, difficulty, description, status) VALUES
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
INSERT IGNORE INTO tables (id, number, capacity, status) VALUES
                                                                  (1, 1, 4, 'available'),
                                                                  (2, 2, 4, 'available'),
                                                                  (3, 3, 6, 'available'),
                                                                  (4, 4, 6, 'available'),
                                                                  (5, 5, 8, 'available'),
                                                                  (6, 6, 2, 'available');

