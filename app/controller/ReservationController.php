<?php

namespace App\Controllers;

use App\Models\Reservation;

class ReservationController {
    private $reservationModel;

    public function __construct() {
        $this->reservationModel = new Reservation();
    }

    public function index() {
        $reservations = $this->reservationModel->getUpcoming();
        require_once '../app/views/games/index.php';
    }

    public function create() {
        require_once '../app/views/games/create.php';
    }

    public function store() {
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
                header('Location: /index.php?action=success');
            } else {
                header('Location: /index.php?action=conflict');
            }
            exit();
        }
    }

    public function show($id) {
        $reservation = $this->reservationModel->getById($id);
        require_once '../app/views/games/show.php';
    }

    public function cancel($id) {
        $this->reservationModel->cancel($id);
        header('Location: /index.php?action=cancelled');
        exit();
    }
}
