<?php

namespace App\controller;

use App\models\Table;

class TableController {
    private $tableModel;

    public function __construct() {
        $this->tableModel = new Table();
    }

    public function index() {
        $tables = $this->tableModel->getAll();
        require_once '../app/views/games/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $number = $_POST['number'];
            $capacity = $_POST['capacity'];
            $this->tableModel->create($number, $capacity);
            header('Location: /index.php?action=tables');
            exit();
        }
    }

    public function checkStatus() {
        $date = $_GET['date'] ?? date('Y-m-d');
        $time = $_GET['time'] ?? '18:00';
        $tables = $this->tableModel->getAvailableByDateTime($date, $time);
        require_once '../app/views/games/create.php';
    }

    public function updateStatus($id) {
        $status = $_GET['status'];
        $this->tableModel->updateStatus($id, $status);
        header('Location: /index.php?action=tables');
        exit();
    }
}
