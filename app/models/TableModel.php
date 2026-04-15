<?php

class TableModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM tables");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($number, $capacity, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO tables (number, capacity, status) VALUES (?, ?, ?)");
        return $stmt->execute([$number, $capacity, $status]);
    }

    public function update($id, $number, $capacity, $status) {
        $stmt = $this->pdo->prepare("UPDATE tables SET number = ?, capacity = ?, status = ? WHERE id = ?");
        return $stmt->execute([$number, $capacity, $status, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM tables WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAvailable() {
        $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOccupied() {
        $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE status = 'occupied'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCapacity($capacity) {
        $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE capacity >= ?");
        $stmt->execute([$capacity]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableByDate($date, $time_slot) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tables
            WHERE id NOT IN (
                SELECT table_id FROM reservations
                WHERE DATE(date) = ? AND time_slot = ? AND status != 'cancelled'
            )
        ");
        $stmt->execute([$date, $time_slot]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE tables SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function setAvailable($id) {
        return $this->updateStatus($id, 'available');
    }

    public function setOccupied($id) {
        return $this->updateStatus($id, 'occupied');
    }

    public function countAll() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM tables");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countAvailable() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM tables WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countOccupied() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM tables WHERE status = 'occupied'");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}