<?php

namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\GameModel;
use App\Models\TableModel;

class ReservationController {
    private $reservationModel;

    public function __construct() {
        $this->reservationModel = new ReservationModel();
    }

    public function index() {
        $reservations = $this->reservationModel->getUpcoming();
        require_once __DIR__ . '/../views/reservation/index.php';
    }

    public function create() {
        $gameId = $_GET['game_id'] ?? null;
        $game = null;

        if ($gameId) {
            $gameModel = new GameModel();
            $game = $gameModel->findById($gameId);
        }

        $tableModel = new TableModel();
        $tables = $tableModel->getAll();

        require_once __DIR__ . '/../views/reservation/create.php';
    }

    public function store() {
        $baseUrl = defined('BASE_URL') ? BASE_URL : '/Aji-nle3bo-Cafe-Manager';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_name'      => $_POST['client_name'],
                'phone'            => $_POST['phone'],
                'user_id'          => $_POST['user_id'] ?? null,
                'table_id'         => $_POST['table_id'],
                'reservation_date' => $_POST['reservation_date'],
                'reservation_time' => $_POST['reservation_time'],
                'number_of_people' => $_POST['number_of_people']
            ];

            if ($this->reservationModel->checkAvailability($data['table_id'], $data['reservation_date'], $data['reservation_time'])) {
                $this->reservationModel->create($data);
                header("Location: {$baseUrl}/reservations");
            } else {
                header("Location: {$baseUrl}/reservations/create?error=conflict");
            }
            exit();
        }
    }

    public function show($id) {
        $reservation = $this->reservationModel->getById($id);
        require_once __DIR__ . '/../views/reservation/show.php';
    }
}
