<?php
namespace App\Models;

use PDO;

class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function getAll() {
        $sql = 'SELECT c.*, COUNT(g.id) as game_count 
                FROM categories c 
                LEFT JOIN games g ON c.id = g.category_id 
                GROUP BY c.id 
                ORDER BY c.name';
        return $this->db->query($sql)->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => htmlspecialchars($name)]);
        return $this->db->lastInsertId();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }
}