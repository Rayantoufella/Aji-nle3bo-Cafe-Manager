<?php
namespace App\Models;

use PDO;
use PDOException;

class UserModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function create($data) {
        try {
            $sql = 'INSERT INTO users (username, email, password, phone, role) VALUES (:username, :email, :password, :phone, :role)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'username' => htmlspecialchars($data['username']),
                'email'    => htmlspecialchars($data['email']),
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'phone'    => htmlspecialchars($data['phone'] ?? ''),
                'role'     => $data['role'] ?? 'user'
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email) {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $sql = 'SELECT * FROM users ORDER BY created_at DESC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $sql = 'UPDATE users SET username = :username, email = :email, phone = :phone WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'username' => htmlspecialchars($data['username']),
            'email'    => htmlspecialchars($data['email']),
            'phone'    => htmlspecialchars($data['phone'] ?? ''),
            'id'       => (int)$id
        ]);
    }

    public function delete($id) {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => (int)$id]);
    }

    public function count() {
        return $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}