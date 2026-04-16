<?php
namespace App\Models;

use PDO;
use PDOException;

class ReservationModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function getAll() {
        $sql = "SELECT r.*, t.number as table_number, t.capacity as table_capacity
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                ORDER BY r.reservation_date DESC, r.reservation_time DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT r.*, t.number as table_number, t.capacity as table_capacity
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function getByUser($userId) {
        $sql = "SELECT r.*, t.number as table_number
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                WHERE r.user_id = :user_id 
                ORDER BY r.reservation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => (int)$userId]);
        return $stmt->fetchAll();
    }

    public function getToday() {
        $sql = "SELECT r.*, t.number as table_number, t.capacity as table_capacity
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                WHERE r.reservation_date = CURDATE()
                ORDER BY r.reservation_time ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getUpcoming() {
        $sql = "SELECT r.*, t.number as table_number
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                WHERE r.reservation_date >= CURDATE()
                AND r.status IN ('pending', 'confirmed')
                ORDER BY r.reservation_date ASC, r.reservation_time ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getRecent($limit = 5) {
        $sql = "SELECT r.*, t.number as table_number
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                ORDER BY r.created_at DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO reservations (client_name, phone, user_id, table_id, reservation_date, reservation_time, number_of_people, status) 
                VALUES (:client_name, :phone, :user_id, :table_id, :reservation_date, :reservation_time, :number_of_people, :status)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'client_name'      => htmlspecialchars($data['client_name']),
                'phone'            => htmlspecialchars($data['phone'] ?? ''),
                'user_id'          => $data['user_id'] ? (int)$data['user_id'] : null,
                'table_id'         => (int)$data['table_id'],
                'reservation_date' => $data['reservation_date'],
                'reservation_time' => $data['reservation_time'],
                'number_of_people' => (int)$data['number_of_people'],
                'status'           => $data['status'] ?? 'pending'
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE reservations SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => (int)$id]);
    }

    public function cancel($id) {
        return $this->updateStatus($id, 'cancelled');
    }

    public function confirm($id) {
        return $this->updateStatus($id, 'confirmed');
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function checkAvailability($tableId, $date, $time) {
        $sql = "SELECT COUNT(*) FROM reservations 
                WHERE table_id = :table_id 
                AND reservation_date = :date 
                AND reservation_time = :time 
                AND status IN ('pending', 'confirmed')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['table_id' => (int)$tableId, 'date' => $date, 'time' => $time]);
        return $stmt->fetchColumn() == 0;
    }

    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
    }

    public function countToday() {
        return $this->db->query("SELECT COUNT(*) FROM reservations WHERE reservation_date = CURDATE()")->fetchColumn();
    }

    public function countByStatus($status) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reservations WHERE status = :status");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchColumn();
    }

    public function getConfirmedForSession() {
        $sql = "SELECT r.*, t.number as table_number
                FROM reservations r
                LEFT JOIN tables_cafe t ON r.table_id = t.id
                WHERE r.status = 'confirmed'
                AND r.reservation_date = CURDATE()
                AND r.id NOT IN (SELECT reservation_id FROM sessions WHERE reservation_id IS NOT NULL)
                ORDER BY r.reservation_time ASC";
        return $this->db->query($sql)->fetchAll();
    }
}
