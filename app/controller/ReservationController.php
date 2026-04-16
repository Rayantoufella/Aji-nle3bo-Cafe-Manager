<?php

namespace App\Controllers;

use App\Models\ReservationModel; 

class ReservationController {
    private $reservationModel;

    public function __construct() {
        $this->reservationModel = new ReservationModel();
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'all';

        // Get all reservations based on filter
        if($filter === 'today'){
            $reservations = $this->reservationModel->getToday();
        } elseif($filter === 'upcoming'){
            $reservations = $this->reservationModel->getUpcoming();
        } elseif($filter === 'mine'){
            $userId = $_SESSION['user_id'] ?? null;
            $reservations = $userId ? $this->reservationModel->getByUserId($userId) : [];
        } else {
            $reservations = $this->reservationModel->getAll();
        }

        // Get statistics
        $totalCount = $this->reservationModel->getTotalCount();
        $todayCount = $this->reservationModel->getTodayCount();
        $pendingCount = $this->reservationModel->getPendingCount();
        $confirmedCount = $this->reservationModel->getConfirmedCount();

        require_once dirname(__DIR__) . '/views/reservations/index.php';
    }

    public function create() {
        require_once dirname(__DIR__) . '/views/reservations/create.php';
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
        require_once dirname(__DIR__) . '/views/reservations/show.php';
    }

    public function cancel($id) {
        $this->reservationModel->cancel($id);
        header('Location: /index.php?action=cancelled');
        exit();
    }
}
