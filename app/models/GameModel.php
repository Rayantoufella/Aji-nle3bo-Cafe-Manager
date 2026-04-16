<?php
namespace App\Models;

use PDO;

class GameModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function findAll() {
        $sql = "SELECT g.*, c.name as category_name 
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                ORDER BY g.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT g.*, c.name as category_name 
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                WHERE g.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function findByCategory($categoryId) {
        $sql = "SELECT g.*, c.name as category_name 
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                WHERE g.category_id = :category_id
                ORDER BY g.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['category_id' => (int)$categoryId]);
        return $stmt->fetchAll();
    }

    public function findAvailable() {
        $sql = "SELECT g.*, c.name as category_name 
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                WHERE g.status = 'available'
                ORDER BY g.name";
        return $this->db->query($sql)->fetchAll();
    }

    public function search($keyword) {
        $sql = "SELECT g.*, c.name as category_name 
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                WHERE g.name LIKE :keyword OR g.description LIKE :keyword2
                ORDER BY g.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%$keyword%", 'keyword2' => "%$keyword%"]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO games (name, category_id, nb_players, duration, difficulty, description, image_url, status)
                VALUES (:name, :category_id, :nb_players, :duration, :difficulty, :description, :image_url, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name'        => htmlspecialchars($data['name']),
            'category_id' => (int)$data['category_id'],
            'nb_players'  => (int)$data['nb_players'],
            'duration'    => (int)$data['duration'],
            'difficulty'  => $data['difficulty'],
            'description' => htmlspecialchars($data['description']),
            'image_url'   => $data['image_url'] ?? null,
            'status'      => $data['status'] ?? 'available'
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE games SET 
                name = :name, category_id = :category_id, nb_players = :nb_players,
                duration = :duration, difficulty = :difficulty, description = :description,
                image_url = :image_url, status = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name'        => htmlspecialchars($data['name']),
            'category_id' => (int)$data['category_id'],
            'nb_players'  => (int)$data['nb_players'],
            'duration'    => (int)$data['duration'],
            'difficulty'  => $data['difficulty'],
            'description' => htmlspecialchars($data['description']),
            'image_url'   => $data['image_url'] ?? null,
            'status'      => $data['status'],
            'id'          => (int)$id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM games WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM games")->fetchColumn();
    }

    public function countAvailable() {
        return $this->db->query("SELECT COUNT(*) FROM games WHERE status = 'available'")->fetchColumn();
    }

    public function countInUse() {
        return $this->db->query("SELECT COUNT(DISTINCT game_id) FROM sessions WHERE status = 'active'")->fetchColumn();
    }

    public function getMostPlayed($limit = 5) {
        $sql = "SELECT g.*, c.name as category_name, COUNT(s.id) as play_count
                FROM games g
                LEFT JOIN categories c ON g.category_id = c.id
                LEFT JOIN sessions s ON g.id = s.game_id
                GROUP BY g.id
                ORDER BY play_count DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
