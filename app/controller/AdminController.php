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

    public function __construct($pdo) {
        $this->model = new AdminModel($pdo);
    }


    


    public function getAllUsers() {
        return $this->model->getAllUsers();
    }

    public function deleteUser($userId) {
        $user = $this->model->getUserById($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }
        $this->model->deleteUser($userId);
        $this->model->logAction('delete_user', 'Deleted user ID: ' . $userId);
        return ['success' => 'User deleted successfully'];
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
        $this->model->editGame($gameId, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status);
        $this->model->logAction('edit_game', 'Edited game ID: ' . $gameId);
        return ['success' => 'Game updated successfully'];
    }

    public function deleteGame($gameId) {
        $game = $this->model->getGameById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->model->deleteGame($gameId);
        $this->model->logAction('delete_game', 'Deleted game ID: ' . $gameId);
        return ['success' => 'Game deleted successfully'];
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
        $this->model->cancelReservation($resId);
        $this->model->logAction('cancel_reservation', 'Cancelled reservation ID: ' . $resId);
        return ['success' => 'Reservation cancelled successfully'];
    }



    public function viewLogs() {
        return $this->model->viewLogs();
    }

    public function logAction($action, $details) {
        if (empty($action) || empty($details)) {
            return ['error' => 'Action and details are required'];
        }
        $this->model->logAction($action, $details);
        return ['success' => 'Action logged successfully'];
    }

    public function getLogsByAction($action) {
        if (empty($action)) {
            return ['error' => 'Action is required'];
        }
        return $this->model->getLogsByAction($action);
    }

    public function getLogsByDate($date) {
        if (empty($date)) {
            return ['error' => 'Date is required'];
        }
        return $this->model->getLogsByDate($date);
    }
}