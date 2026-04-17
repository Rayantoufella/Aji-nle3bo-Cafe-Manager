<?php

namespace App\Controller;
use App\Models\AdminModel;


// session_start();
// if(!isset($_SESSION['user_id'])){
    
//         header('Location: index.php');
//         exit();
//     }


class AdminController {

    private $model;

    public function __construct() {
        $database = new \App\Models\DatabaseModel();
        $this->model = new AdminModel($database->connect());
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/login');
            exit;
        }

        $users = $this->model->getAllUsers();
        $games = $this->model->getAllGames();
        $reservations = $this->model->getAllReservations();
        
        $tableModel = new \App\Models\TableModel();
        $tables = $tableModel->getAll();

        require_once __DIR__ . '/../views/admin/dashboard.php';
    }


    


    public function getAllUsers() {
        return $this->model->getAllUsers();
    }

    public function deleteUser($userId) {
        $user = $this->model->getUserById($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }
        $this->model->deleteUser($_POST['user_id'] ?? $userId);
        $_SESSION['flash_success'] = 'User deleted successfully';
        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin');
        exit;
    }


    public function editGame($gameId, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status) {
        $game = $this->model->getGameById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $allowed_difficulties = ['easy', 'medium', 'hard'];
        if (!in_array($difficulty, $allowed_difficulties)) {
            return ['error' => 'Invalid difficulty. Allowed: easy, medium, hard'];
        }
        $allowed_statuses = ['available', 'unavailable'];
        if (!in_array($status, $allowed_statuses)) {
            return ['error' => 'Invalid status. Allowed: available, unavailable'];
        }
        $this->model->editGame($gameId, $_POST['name'], $_POST['categories_id'], $_POST['nb_players'], $_POST['duration'], $_POST['difficulty'], $_POST['description'], $_POST['status']);
        $_SESSION['flash_success'] = 'Game updated successfully';
        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin');
        exit;
    }

    public function deleteGame($gameId) {
        $game = $this->model->getGameById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->model->deleteGame($_POST['game_id'] ?? $gameId);
        $_SESSION['flash_success'] = 'Game deleted successfully';
        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin');
        exit;
    }


    public function getAllReservations() {
        return $this->model->getAllReservations();
    }

    public function cancelReservation($resId) {
        $reservation = $this->model->getReservationById($resId);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }
        if ($reservation['status'] === 'cancelled') {
            return ['error' => 'Reservation is already cancelled'];
        }
        $this->model->cancelReservation($_POST['reservation_id'] ?? $resId);
        $_SESSION['flash_success'] = 'Reservation cancelled successfully';
        header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin');
        exit;
    }
}


?>