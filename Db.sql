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
    status ENUM('pending', 'confirmed', 'cancelled') AS DEFAULT 'pending',
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

ALTER TABLE reservations
ADD user_id INT,
ADD FOREIGN KEY (user_id) REFERENCES users(id);

INSERT INTO categories (name) VALUES
('Stratégie'),
('Famille'),
('Ambiance'),
('Cartes'),
('Party');

INSERT INTO games (name, categories_id, nb_players, duration, difficulty, description, status) VALUES
('Chess', 1, 2, 60, 'hard', 'Jeu de stratégie classique', 'available'),
('Monopoly', 2, 4, 120, 'medium', 'Jeu de gestion et commerce', 'available'),
('UNO', 4, 6, 30, 'easy', 'Jeu de cartes fun', 'available'),
('Jenga', 3, 4, 20, 'easy', 'Jeu d équilibre', 'available'),
('Codenames', 5, 8, 30, 'medium', 'Jeu en équipe avec mots', 'available'),
('Scrabble', 1, 4, 90, 'medium', 'Jeu de lettres', 'available'),
('Risk', 1, 6, 180, 'hard', 'Jeu de conquête du monde', 'unavailable'),
('Dobble', 3, 6, 15, 'easy', 'Jeu rapide d observation', 'available'),
('Poker', 4, 6, 60, 'medium', 'Jeu de cartes stratégique', 'available'),
('Cluedo', 2, 6, 60, 'medium', 'Jeu d enquête', 'available');


INSERT INTO tables (number, capacity, status) VALUES
(1, 2, 'available'),
(2, 4, 'available'),
(3, 6, 'occupied'),
(4, 8, 'available'),
(5, 4, 'occupied');


INSERT INTO reservations (client_name, phone, number_of_people, reservation_date, reservation_time, table_id, status) VALUES
('Ali', '0612345678', 2, '2026-04-20', '14:00:00', 1, 'confirmed'),
('Sara', '0623456789', 4, '2026-04-20', '16:00:00', 2, 'pending'),
('Omar', '0634567890', 6, '2026-04-21', '18:00:00', 3, 'confirmed'),
('Lina', '0645678901', 3, '2026-04-21', '12:00:00', 2, 'cancelled'),
('Youssef', '0656789012', 5, '2026-04-22', '20:00:00', 5, 'pending');


INSERT INTO sessions (reservation_id, game_id, table_id, start_time, end_time, status) VALUES
(1, 1, 1, '2026-04-20 14:00:00', '2026-04-20 15:00:00', 'finished'),
(2, 3, 2, '2026-04-20 16:00:00', NULL, 'active'),
(3, 7, 3, '2026-04-21 18:00:00', NULL, 'active'),
(4, 4, 2, '2026-04-21 12:00:00', '2026-04-21 12:30:00', 'finished'),
(5, 5, 5, '2026-04-22 20:00:00', NULL, 'active');