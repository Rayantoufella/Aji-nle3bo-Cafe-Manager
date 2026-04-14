CREATE DATABASE IF NOT EXISTS games-db;
USE games-db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role ENUM('admin', 'user'),
    created_at DATETIME
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
    created_at TIMESTAMP,

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
    created_at TIMESTAMP,

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