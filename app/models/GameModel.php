<?php

namespace App\Models;

use App\Models\Database;
use PDO;

class GameModel {

    private $conn;
    private $name;
    private $category_id;
    private $nb_players;
    private $duration;
    private $difficulty;
    private $description;
    private $status;

    public function getName() {
        return $this->name;
    }

    public function getCategory_id() {
        return $this->category_id;
    }

    public function getNb_players() {
        return $this->nb_players;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function getDifficulty() {
        return $this->difficulty;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getStatus() {
        return $this->status;
    }

    public function __construct()
    {
        $database = new DatabaseModel();
        $this->conn = $database->connect();
    }

    public function findAll() {
        $sql = "SELECT games.*, categories.name as category_name 
                FROM games
                LEFT JOIN categories ON games.categories_id = categories.id
                ORDER BY games.created_at DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    public function findById($id) {
        $sql = "SELECT games.*, categories.name as category_name 
                FROM games
                LEFT JOIN categories ON games.categories_id = categories.id
                WHERE games.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCategory($categoryId) {
        $sql = "SELECT * FROM games WHERE categories_id = :category_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['category_id' => $categoryId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPlayers($nb_players) {
        $sql = "SELECT * FROM games 
                WHERE nb_players = :nb_players";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['nb_players' => $nb_players]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name, $categories_id, $nb_players, $duration, $difficulty, $description, $status) {
        $sql = "INSERT INTO games (name, categories_id, nb_players, duration, difficulty, description, status)
                VALUES (:name, :categories_id, :nb_players, :duration, :difficulty, :description, :status)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':categories_id' => $categories_id,
            ':nb_players' => $nb_players,
            ':duration' => $duration,
            ':difficulty' => $difficulty,
            ':description' => $description,
            ':status' => $status
        ]);
    }

    public function update($id, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status){
        $sql = 'UPDATE games
                SET name = ?, categories_id = ?, nb_players = ?, duration = ?, difficulty = ?, description = ?, status = ?
                WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name, $categories_id, $nb_players, $duration, $difficulty, $description, $status, $id]);
    }


    public function delete($id) {
        $sql = "DELETE FROM games WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Récupérer les jeux similaires par catégorie
    public function findRelatedGames($categoryId, $excludeId = null, $limit = 3) {
        $sql = "SELECT games.*, categories.name as category_name 
                FROM games
                LEFT JOIN categories ON games.categories_id = categories.id
                WHERE games.categories_id = :category_id";

        if ($excludeId) {
            $sql .= " AND games.id != :exclude_id";
        }

        $sql .= " ORDER BY games.created_at DESC LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);

        if ($excludeId) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }

    // Récupérer les statistiques des avis
    public function getGameStats($gameId) {
        try {
            $sql = "SELECT COUNT(*) as total_reviews, AVG(rating) as average_rating,
                    MAX(rating) as max_rating, MIN(rating) as min_rating
                    FROM reviews WHERE game_id = :game_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['game_id' => $gameId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? ['total_reviews' => 0, 'average_rating' => 0, 'max_rating' => 0, 'min_rating' => 0];
        } catch (\Exception $e) {
            return ['total_reviews' => 0, 'average_rating' => 0, 'max_rating' => 0, 'min_rating' => 0];
        }
    }

    // Récupérer les avis pour un jeu
    public function getGameReviews($gameId, $limit = 5) {
        try {
            $sql = "SELECT r.id, r.user_id, r.game_id, r.rating, r.comment, r.created_at,
                    u.username as user_name
                    FROM reviews r
                    LEFT JOIN users u ON r.user_id = u.id
                    WHERE r.game_id = :game_id
                    ORDER BY r.created_at DESC LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':game_id', $gameId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    // Chercher les jeux populaires
    public function getPopularGames($limit = 6) {
        try {
            $sql = "SELECT games.*, categories.name as category_name
                    FROM games
                    LEFT JOIN categories ON games.categories_id = categories.id
                    WHERE games.status = 'active'
                    ORDER BY games.created_at DESC
                    LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
