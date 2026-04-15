<?php

namespace App\models;
use App\Models\Database;


use PDO;

class Table {
    protected $id;
    protected $number;
    protected $capacity;
    protected $status;

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM tables");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tables WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function create($number, $capacity) {
        $sql = "INSERT INTO tables (number, capacity) VALUES (:num, :cap)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':num' => (int)$number,
            ':cap' => (int)$capacity
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE tables SET number = :num, capacity = :cap, status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':num'    => (int)$data['number'],
            ':cap'    => (int)$data['capacity'],
            ':status' => $data['status'],
            ':id'     => (int)$id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tables WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function getAvailableTables() {
        $stmt = $this->db->prepare("SELECT * FROM tables WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAvailableByCapacity($capacity) {
        $stmt = $this->db->prepare("SELECT * FROM tables WHERE status = 'available' AND capacity >= :cap");
        $stmt->execute(['cap' => (int)$capacity]);
        return $stmt->fetchAll();
    }

    public function getAvailableByDateTime($date, $time) {
        $sql = "SELECT * FROM tables 
                WHERE id NOT IN (
                    SELECT table_id FROM reservations 
                    WHERE reservation_date = :date AND reservation_time = :time AND status != 'cancelled'
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date, 'time' => $time]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE tables SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => (int)$id]);
    }

    public function getSessions($tableId) {
        $stmt = $this->db->prepare("SELECT * FROM sessions WHERE table_id = :id");
        $stmt->execute(['id' => (int)$tableId]);
        return $stmt->fetchAll();
    }

    public function getReservations($tableId) {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE table_id = :id");
        $stmt->execute(['id' => (int)$tableId]);
        return $stmt->fetchAll();
    }

    public function isAvailable($tableId, $date, $time) {
        $sql = "SELECT COUNT(*) FROM reservations 
                WHERE table_id = :id AND reservation_date = :date AND reservation_time = :time AND status != 'cancelled'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$tableId, 'date' => $date, 'time' => $time]);
        return $stmt->fetchColumn() == 0;
    }
}
