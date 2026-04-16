<?php
namespace App\Models;

use PDO;

class SessionModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function getAll() {
        $sql = "SELECT s.*, g.name as game_name, t.number as table_number, r.client_name
                FROM sessions s
                LEFT JOIN games g ON s.game_id = g.id
                LEFT JOIN tables_cafe t ON s.table_id = t.id
                LEFT JOIN reservations r ON s.reservation_id = r.id
                ORDER BY s.start_time DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT s.*, g.name as game_name, t.number as table_number, r.client_name
                FROM sessions s
                LEFT JOIN games g ON s.game_id = g.id
                LEFT JOIN tables_cafe t ON s.table_id = t.id
                LEFT JOIN reservations r ON s.reservation_id = r.id
                WHERE s.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function getActive() {
        $sql = "SELECT s.*, g.name as game_name, g.image_url as game_image, 
                       t.number as table_number, t.capacity as table_capacity,
                       r.client_name, r.number_of_people,
                       TIMESTAMPDIFF(MINUTE, s.start_time, NOW()) as elapsed_minutes
                FROM sessions s
                LEFT JOIN games g ON s.game_id = g.id
                LEFT JOIN tables_cafe t ON s.table_id = t.id
                LEFT JOIN reservations r ON s.reservation_id = r.id
                WHERE s.status = 'active'
                ORDER BY s.start_time ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getFinished() {
        $sql = "SELECT s.*, g.name as game_name, t.number as table_number, r.client_name,
                       TIMESTAMPDIFF(MINUTE, s.start_time, s.end_time) as duration_minutes
                FROM sessions s
                LEFT JOIN games g ON s.game_id = g.id
                LEFT JOIN tables_cafe t ON s.table_id = t.id
                LEFT JOIN reservations r ON s.reservation_id = r.id
                WHERE s.status = 'finished'
                ORDER BY s.end_time DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getRecent($limit = 5) {
        $sql = "SELECT s.*, g.name as game_name, t.number as table_number, r.client_name,
                       TIMESTAMPDIFF(MINUTE, s.start_time, COALESCE(s.end_time, NOW())) as duration_minutes
                FROM sessions s
                LEFT JOIN games g ON s.game_id = g.id
                LEFT JOIN tables_cafe t ON s.table_id = t.id
                LEFT JOIN reservations r ON s.reservation_id = r.id
                ORDER BY s.start_time DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function start($data) {
        $sql = "INSERT INTO sessions (reservation_id, game_id, table_id, start_time, status) 
                VALUES (:reservation_id, :game_id, :table_id, NOW(), 'active')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'reservation_id' => $data['reservation_id'] ?: null,
            'game_id'        => (int)$data['game_id'],
            'table_id'       => (int)$data['table_id']
        ]);

        // Mark table as occupied
        $tableModel = new TableModel();
        $tableModel->updateStatus($data['table_id'], 'occupied');

        return $this->db->lastInsertId();
    }

    public function end($id) {
        $session = $this->getById($id);
        if (!$session) return false;

        $sql = "UPDATE sessions SET end_time = NOW(), status = 'finished' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);

        // Free the table
        $tableModel = new TableModel();
        $tableModel->updateStatus($session['table_id'], 'available');

        return true;
    }

    public function countActive() {
        return $this->db->query("SELECT COUNT(*) FROM sessions WHERE status = 'active'")->fetchColumn();
    }

    public function countTotal() {
        return $this->db->query("SELECT COUNT(*) FROM sessions")->fetchColumn();
    }

    public function countToday() {
        return $this->db->query("SELECT COUNT(*) FROM sessions WHERE DATE(start_time) = CURDATE()")->fetchColumn();
    }

    public function getMostPlayedGame() {
        $sql = "SELECT g.name, COUNT(s.id) as play_count
                FROM sessions s
                JOIN games g ON s.game_id = g.id
                GROUP BY s.game_id
                ORDER BY play_count DESC
                LIMIT 1";
        return $this->db->query($sql)->fetch();
    }
}
