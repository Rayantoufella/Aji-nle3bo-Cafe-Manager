<?php
namespace App\Models;

use PDO;

class TableModel {
    private $db;

    public function __construct() {
        $this->db = DatabaseModel::getConnection();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM tables_cafe ORDER BY number")->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tables_cafe WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

    public function create($number, $capacity) {
        $stmt = $this->db->prepare("INSERT INTO tables_cafe (number, capacity) VALUES (:number, :capacity)");
        return $stmt->execute(['number' => (int)$number, 'capacity' => (int)$capacity]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE tables_cafe SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => (int)$id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tables_cafe WHERE id = :id");
        return $stmt->execute(['id' => (int)$id]);
    }

    public function getAvailable() {
        return $this->db->query("SELECT * FROM tables_cafe WHERE status = 'available' ORDER BY number")->fetchAll();
    }

    public function getAvailableByDateTime($date, $time) {
        $sql = "SELECT * FROM tables_cafe 
                WHERE id NOT IN (
                    SELECT table_id FROM reservations 
                    WHERE reservation_date = :date 
                    AND reservation_time = :time 
                    AND status IN ('pending', 'confirmed')
                ) AND status = 'available'
                ORDER BY number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date, 'time' => $time]);
        return $stmt->fetchAll();
    }

    public function getAvailableForCapacity($date, $time, $capacity) {
        $sql = "SELECT * FROM tables_cafe 
                WHERE capacity >= :capacity
                AND id NOT IN (
                    SELECT table_id FROM reservations 
                    WHERE reservation_date = :date 
                    AND reservation_time = :time 
                    AND status IN ('pending', 'confirmed')
                ) AND status = 'available'
                ORDER BY capacity ASC, number ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['capacity' => (int)$capacity, 'date' => $date, 'time' => $time]);
        return $stmt->fetchAll();
    }

    public function countAvailable() {
        return $this->db->query("SELECT COUNT(*) FROM tables_cafe WHERE status = 'available'")->fetchColumn();
    }

    public function countTotal() {
        return $this->db->query("SELECT COUNT(*) FROM tables_cafe")->fetchColumn();
    }
}
