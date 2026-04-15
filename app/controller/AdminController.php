<?php

namespace App\Controller;
use App\Models\AdminModel;
use App\Models\Database;
use PDO;
use PDOException;

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


    public function addPermission($perm) {
        if (empty($perm)) {
            return ['error' => 'Permission cannot be empty'];
        }
        $this->model->addPermission($perm);
        return ['success' => 'Permission added: ' . $perm];
    }

    public function hasPermission($perm) {
        return ['has_permission' => $this->model->hasPermission($perm)];
    }

    public function getPermissions() {
        $perms = $this->model->getPermissions();
        return ['permissions' => $perms, 'total' => count($perms)];
    }


    public function getAllUsers() {
        if (!$this->model->hasPermission('manage_users')) {
            return ['error' => 'Access denied: missing manage_users permission'];
        }
        return $this->model->getAllUsers();
    }

    public function deleteUser($userId) {
        if (!$this->model->hasPermission('manage_users')) {
            return ['error' => 'Access denied: missing manage_users permission'];
        }
        $user = $this->model->getUserById($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }
        $this->model->deleteUser($userId);
        $this->model->logAction('delete_user', 'Deleted user ID: ' . $userId);
        return ['success' => 'User deleted successfully'];
    }

    public function changeUserRole($userId, $role) {
        if (!$this->model->hasPermission('manage_users')) {
            return ['error' => 'Access denied: missing manage_users permission'];
        }
        $allowed_roles = ['admin', 'client'];
        if (!in_array($role, $allowed_roles)) {
            return ['error' => 'Invalid role. Allowed: admin, client'];
        }
        $user = $this->model->getUserById($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }
        $this->model->changeUserRole($userId, $role);
        $this->model->logAction('change_role', 'Changed role of user ID: ' . $userId . ' to ' . $role);
        return ['success' => 'User role updated to ' . $role];
    }

    public function createAdmin($username, $email, $pwd) {
        if (!$this->model->hasPermission('manage_users')) {
            return ['error' => 'Access denied: missing manage_users permission'];
        }
        if (empty($username) || empty($email) || empty($pwd)) {
            return ['error' => 'Missing required fields'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format'];
        }
        if (strlen($pwd) < 8) {
            return ['error' => 'Password must be at least 8 characters'];
        }
        if ($this->model->emailExists($email)) {
            return ['error' => 'Email already in use'];
        }
        if ($this->model->usernameExists($username)) {
            return ['error' => 'Username already taken'];
        }
        $this->model->createAdmin($username, $email, $pwd);
        $this->model->logAction('create_admin', 'Created new admin: ' . $username);
        return ['success' => 'Admin account created successfully'];
    }


    public function editGame($gameId, $name, $categories_id, $nb_players, $duration, $difficulty, $description, $status) {
        if (!$this->model->hasPermission('manage_games')) {
            return ['error' => 'Access denied: missing manage_games permission'];
        }
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
        if (!$this->model->hasPermission('manage_games')) {
            return ['error' => 'Access denied: missing manage_games permission'];
        }
        $game = $this->model->getGameById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->model->deleteGame($gameId);
        $this->model->logAction('delete_game', 'Deleted game ID: ' . $gameId);
        return ['success' => 'Game deleted successfully'];
    }


    public function getAllReservations() {
        if (!$this->model->hasPermission('manage_reservations')) {
            return ['error' => 'Access denied: missing manage_reservations permission'];
        }
        return $this->model->getAllReservations();
    }

    public function cancelReservation($resId) {
        if (!$this->model->hasPermission('manage_reservations')) {
            return ['error' => 'Access denied: missing manage_reservations permission'];
        }
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
        if (!$this->model->hasPermission('view_logs')) {
            return ['error' => 'Access denied: missing view_logs permission'];
        }
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
        if (!$this->model->hasPermission('view_logs')) {
            return ['error' => 'Access denied: missing view_logs permission'];
        }
        if (empty($action)) {
            return ['error' => 'Action is required'];
        }
        return $this->model->getLogsByAction($action);
    }

    public function getLogsByDate($date) {
        if (!$this->model->hasPermission('view_logs')) {
            return ['error' => 'Access denied: missing view_logs permission'];
        }
        if (empty($date)) {
            return ['error' => 'Date is required'];
        }
        return $this->model->getLogsByDate($date);
    }
}