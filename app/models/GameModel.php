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
                JOIN categories ON games.category_id = categories.id";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT * FROM games WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCategory($categoryId) {
        $sql = "SELECT * FROM games WHERE category_id = :category_id";

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
        $sql = "INSERT INTO games (name, category_id, nb_players, duration, difficulty, description, status)
                VALUES (:name, :category_id, :nb_players, :duration, :difficulty, :description, :status)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$name, $categories_id, $nb_players, $duration, $difficulty, $description, $status]);
    }

    public function update($id, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status){
            $sql = 'UPDATE games
                    SET name = ?, category_id = ?, nb_players = ?, duration = ?, difficulty = ?, description = ?, status = ?
                    WHERE id = ?';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $categories_id, $nb_players, $duration, $difficulty, $description, $status, $id]);        
        }
    

    public function delete($id) {
        $sql = "DELETE FROM games WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
