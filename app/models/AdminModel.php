<?php
namespace App\Models;
use PDO;

class AdminModel {

    private $pdo;
    private $permissions = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    

    public function addPermission($perm) {
        if (!in_array($perm, $this->permissions)) {
            $this->permissions[] = $perm;
        }
    }

    public function hasPermission($perm) {
        return in_array($perm, $this->permissions);
    }

    public function getPermissions() {
        return $this->permissions;
    }



    public function getAllUsers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function changeUserRole($userId, $role) {
        $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $userId]);
    }

    public function createAdmin($username, $email, $pwd) {
        $hashed = password_hash($pwd, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, password, role, created_at)
            VALUES (?, ?, ?, 'admin', NOW())
        ");
        return $stmt->execute([$username, $email, $hashed]);
    }

    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function usernameExists($username) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function editGame($gameId, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE games SET name = ?, categories_id = ?, nb_players = ?, duration = ?, difficulty = ?, description = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $categories_id, $nb_players, $duration, $difficulty, $description, $status, $gameId]);
    }

    public function deleteGame($gameId) {
        $stmt = $this->pdo->prepare("DELETE FROM games WHERE id = ?");
        return $stmt->execute([$gameId]);
    }

    public function getGameById($gameId) {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$gameId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllReservations() {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations ORDER BY date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationById($resId) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$resId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelReservation($resId) {
        $stmt = $this->pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
        return $stmt->execute([$resId]);
    }


    public function logAction($action, $details) {
        $stmt = $this->pdo->prepare("
            INSERT INTO logs (action, details, created_at)
            VALUES (?, ?, NOW())
        ");
        return $stmt->execute([$action, $details]);
    }

    public function viewLogs() {
        $stmt = $this->pdo->prepare("SELECT * FROM logs ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogsByAction($action) {
        $stmt = $this->pdo->prepare("SELECT * FROM logs WHERE action = ? ORDER BY created_at DESC");
        $stmt->execute([$action]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogsByDate($date) {
        $stmt = $this->pdo->prepare("SELECT * FROM logs WHERE DATE(created_at) = ? ORDER BY created_at DESC");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>