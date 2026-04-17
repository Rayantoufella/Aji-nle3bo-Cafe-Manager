<?php
namespace App\Controller;

use App\Models\ReservationModel;
use App\Models\TableModel;

class ReservationController {
    private $reservationModel;
    private $tableModel;

    public function __construct() {
        $this->reservationModel = new ReservationModel();
        $this->tableModel = new TableModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $reservations = $this->reservationModel->getByUser($_SESSION['user_id']);

        $pageTitle = 'Reservations';
        require __DIR__ . '/../views/reservation/index.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $gameId = $_GET['game_id'] ?? null;
        $game = null;
        if ($gameId) {
            $gameModel = new \App\Models\GameModel();
            $game = $gameModel->findById($gameId);
        }

        $tables = $this->tableModel->getAvailableTables();
        $pageTitle = 'New Reservation';
        require __DIR__ . '/../views/reservation/create.php';
    }

    public function store() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $tableId = $_POST['table_id'];
        $date = $_POST['reservation_date'];
        
        // Convert '06:00 PM' etc to MySQL TIME format (HH:MM:SS)
        $rawTime = $_POST['reservation_time'];
        $time = date('H:i:s', strtotime($rawTime));

        // Check availability
        if (!$this->reservationModel->checkAvailability($tableId, $date, $time)) {
            $_SESSION['flash_error'] = 'Cette table est déjà réservée pour ce créneau.';
            header('Location: ' . BASE_URL . '/reservations/create');
            exit;
        }

        $this->reservationModel->create([
            'client_name'      => $_POST['client_name'],
            'phone'            => $_POST['phone'] ?? '',
            'user_id'          => $_SESSION['user_id'],
            'table_id'         => $tableId,
            'reservation_date' => $date,
            'reservation_time' => $time,
            'number_of_people' => $_POST['number_of_people'],
            'status'           => 'pending'
        ]);

        $_SESSION['flash_success'] = 'Réservation créée avec succès !';
        header('Location: ' . BASE_URL . '/reservations');
        exit;
    }

    public function show($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $reservation = $this->reservationModel->getById($id);
        if (!$reservation) {
            header('Location: ' . BASE_URL . '/reservations');
            exit;
        }

        $pageTitle = 'Reservation Details';
        require __DIR__ . '/../views/reservation/show.php';
    }

    public function updateStatus($id) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $status = $_POST['status'];
        $this->reservationModel->updateStatus($id, $status);

        $_SESSION['flash_success'] = 'Statut mis à jour !';
        header('Location: ' . BASE_URL . '/reservations');
        exit;
    }

    public function cancel($id) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->reservationModel->cancel($id);
        $_SESSION['flash_success'] = 'Réservation annulée.';
        header('Location: ' . BASE_URL . '/reservations');
        exit;
    }

    public function checkAvailability() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $date = $_GET['date'] ?? date('Y-m-d');
        $time = $_GET['time'] ?? '18:00';
        $capacity = $_GET['capacity'] ?? 2;

        $tables = $this->tableModel->getAvailableByDateTime($date, $time);
        // Filter by capacity in memory
        $tables = array_filter($tables, function($t) use ($capacity) {
            return $t['capacity'] >= $capacity;
        });

        header('Content-Type: application/json');
        echo json_encode(array_values($tables));
        exit;
    }
}
