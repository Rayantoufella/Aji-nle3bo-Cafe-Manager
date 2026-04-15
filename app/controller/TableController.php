<?php

require_once 'Model/TableModel.php';

// session_start();
// if(!isset($_SESSION['user_id'])){
    
//         header('Location: index.php');
//         exit();
//     }
class TableController {

    private $model;

    public function __construct($pdo) {
        $this->model = new TableModel($pdo);
    }

    public function index() {
        return $this->model->getAll();
    }

    public function show($id) {
        $table = $this->model->getById($id);
        if (!$table) {
            return ['error' => 'Table not found'];
        }
        return $table;
    }

    public function store($number, $capacity, $status = 'available') {
        if (empty($number) || empty($capacity)) {
            return ['error' => 'Missing required fields'];
        }
        $allowed_statuses = ['available', 'occupied'];
        if (!in_array($status, $allowed_statuses)) {
            return ['error' => 'Invalid status. Allowed: available, occupied'];
        }
        $this->model->create($number, $capacity, $status);
        return ['success' => 'Table created successfully'];
    }

    public function edit($id, $number, $capacity, $status) {
        $table = $this->model->getById($id);
        if (!$table) {
            return ['error' => 'Table not found'];
        }
        $allowed_statuses = ['available', 'occupied'];
        if (!in_array($status, $allowed_statuses)) {
            return ['error' => 'Invalid status. Allowed: available, occupied'];
        }
        $this->model->update($id, $number, $capacity, $status);
        return ['success' => 'Table updated successfully'];
    }

    public function destroy($id) {
        $table = $this->model->getById($id);
        if (!$table) {
            return ['error' => 'Table not found'];
        }
        $this->model->delete($id);
        return ['success' => 'Table deleted successfully'];
    }

    public function getAvailable() {
        return $this->model->getAvailable();
    }

    public function getOccupied() {
        return $this->model->getOccupied();
    }

    public function filterByCapacity($capacity) {
        if (!is_numeric($capacity) || $capacity < 1) {
            return ['error' => 'Invalid capacity'];
        }
        return $this->model->getByCapacity($capacity);
    }

    public function checkAvailability($date, $time_slot) {
        if (empty($date) || empty($time_slot)) {
            return ['error' => 'Date and time slot are required'];
        }
        return $this->model->getAvailableByDate($date, $time_slot);
    }

    public function makeAvailable($id) {
        $table = $this->model->getById($id);
        if (!$table) {
            return ['error' => 'Table not found'];
        }
        if ($table['status'] === 'available') {
            return ['error' => 'Table is already available'];
        }
        $this->model->setAvailable($id);
        return ['success' => 'Table is now available'];
    }

    public function makeOccupied($id) {
        $table = $this->model->getById($id);
        if (!$table) {
            return ['error' => 'Table not found'];
        }
        if ($table['status'] === 'occupied') {
            return ['error' => 'Table is already occupied'];
        }
        $this->model->setOccupied($id);
        return ['success' => 'Table is now occupied'];
    }

    public function stats() {
        return [
            'total'     => $this->model->countAll(),
            'available' => $this->model->countAvailable(),
            'occupied'  => $this->model->countOccupied(),
        ];
    }
}