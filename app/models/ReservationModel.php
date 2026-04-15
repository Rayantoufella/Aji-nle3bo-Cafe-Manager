<?php

namespace App\Models;

require_once "dbModel.php";

use PDO;
use PDOException;

class Reservation {
    protected $id;
    protected $client_name;
    protected $phone;
    protected $user_id; 
    protected $table_id;
    protected $reservation_date;
    protected $reservation_time;
    protected $number_of_people;
    protected $status;
    protected $created_at;

    private $db;

    public function __construct() {
        $this->db = \app\models\Database::connect();
    }

   

   
 
    public function create($data) {
        $sql = "INSERT INTO reservations (client_name, phone, user_id, table_id, reservation_date, reservation_time, number_of_people) 
                VALUES (:client_name, :phone, :user_id, :table_id, :reservation_date, :reservation_time, :number_of_people)";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            $stmt->bindValue(':client_name', htmlspecialchars($data['client_name']));
            $stmt->bindValue(':phone', htmlspecialchars($data['phone']));
            $stmt->bindValue(':user_id', $data['user_id'] ? (int)$data['user_id'] : null, PDO::PARAM_INT);
            $stmt->bindValue(':table_id', (int)$data['table_id'], PDO::PARAM_INT);
            $stmt->bindValue(':reservation_date', $data['reservation_date']);
            $stmt->bindValue(':reservation_time', $data['reservation_time']);
            $stmt->bindValue(':number_of_people', (int)$data['number_of_people'], PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

   
    public function getById($id) {
        $sql = "SELECT * FROM reservations WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => (int)$id]);
        return $stmt->fetch();
    }

 
    public function update($id, $data) {
        $sql = "UPDATE reservations SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'status' => $data['status'],
            'id'     => (int)$id
        ]);
    }

  
    public function cancel($id) {
        $sql = "UPDATE reservations SET status = 'cancelled' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => (int)$id]);
    }

   
    public function getByUser($userId) {
        $sql = "SELECT * FROM reservations WHERE user_id = :user_id ORDER BY reservation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => (int)$userId]);
        return $stmt->fetchAll();
    }

    public function getByTable($tableId) {
        $sql = "SELECT * FROM reservations WHERE table_id = :table_id ORDER BY reservation_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['table_id' => (int)$tableId]);
        return $stmt->fetchAll();
    }

    public function getByDate($date) {
        $sql = "SELECT * FROM reservations WHERE reservation_date = :date ORDER BY reservation_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }

   
    public function getUpcoming() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM reservations 
                WHERE CONCAT(reservation_date, ' ', reservation_time) >= :now 
                AND status = 'confirmed' 
                ORDER BY reservation_date ASC, reservation_time ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['now' => $now]);
        return $stmt->fetchAll();
    }

  
    public function getPast() {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM reservations 
                WHERE CONCAT(reservation_date, ' ', reservation_time) < :now 
                ORDER BY reservation_date DESC, reservation_time DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    public function checkAvailability($tableId, $date, $time) {
        $sql = "SELECT COUNT(*) FROM reservations 
                WHERE table_id = :table_id 
                AND reservation_date = :date 
                AND reservation_time = :time 
                AND status = 'confirmed'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'table_id' => (int)$tableId,
            'date'     => $date,
            'time'     => $time
        ]);
        
        return $stmt->fetchColumn() == 0;
    }

   
    public function getAvailableTables($date, $time, $capacity) {
        $sql = "SELECT * FROM tables_cafe 
                WHERE capacity >= :capacity 
                AND id NOT IN (
                    SELECT table_id FROM reservations 
                    WHERE reservation_date = :date 
                    AND reservation_time = :time 
                    AND status = 'confirmed'
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'capacity' => (int)$capacity,
            'date'     => $date,
            'time'     => $time
        ]);
        return $stmt->fetchAll();
    }

   
    public function isConflict($tableId, $date, $time) {
        return !$this->checkAvailability($tableId, $date, $time);
    }
}
