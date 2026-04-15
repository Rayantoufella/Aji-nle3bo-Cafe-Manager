<?php

require_once 'Model/SessionsModel.php';
require_once 'Model/GamesModel.php';
require_once 'Model/ReservationsModel.php';
require_once 'Model/TablesModel.php';

class AdminController {

    private $sessionsModel;
    private $gamesModel;
    private $reservationsModel;
    private $tablesModel;

    public function __construct($pdo) {
        $this->sessionsModel      = new SessionsModel($pdo);
        $this->gamesModel         = new GamesModel($pdo);
        $this->reservationsModel  = new ReservationsModel($pdo);
        $this->tablesModel        = new TableModel($pdo);
    }


    public function listGames() {
        return $this->gamesModel->getAll();
    }
    public function showGame($id) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        return $game;
    }

    public function addGame($name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        if (empty($name) || empty($category) || empty($status)) {
            return ['error' => 'Missing required fields'];
        }
        $this->gamesModel->create($name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status);
        return ['success' => 'Game added successfully'];
    }

    public function editGame($id, $name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->gamesModel->update($id, $name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status);
        return ['success' => 'Game updated successfully'];
    }

    public function deleteGame($id) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->gamesModel->delete($id);
        return ['success' => 'Game deleted successfully'];
    }

    public function filterGamesByCategory($category) {
        if (empty($category)) {
            return ['error' => 'Category is required'];
        }
        return $this->gamesModel->getByCategory($category);
    }


    public function getTodayReservations() {
        $today = date('Y-m-d');
        return $this->reservationsModel->getByDate($today);
    }

    public function listReservations() {
        return $this->reservationsModel->getAll();
    }

    public function showReservation($id) {
        $reservation = $this->reservationsModel->getById($id);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }
        return $reservation;
    }

    public function confirmReservation($id) {
        $reservation = $this->reservationsModel->getById($id);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }
        if ($reservation['status'] === 'confirmed') {
            return ['error' => 'Reservation already confirmed'];
        }
        $this->reservationsModel->updateStatus($id, 'confirmed');
        return ['success' => 'Reservation confirmed'];
    }

    public function cancelReservation($id) {
        $reservation = $this->reservationsModel->getById($id);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }
        if ($reservation['status'] === 'cancelled') {
            return ['error' => 'Reservation already cancelled'];
        }
        $this->reservationsModel->updateStatus($id, 'cancelled');
        return ['success' => 'Reservation cancelled'];
    }

    public function checkAvailability($date, $time_slot) {
        if (empty($date) || empty($time_slot)) {
            return ['error' => 'Date and time slot are required'];
        }
        return $this->tablesModel->getAvailable($date, $time_slot);
    }

    public function startSession($reservationId, $gameId, $tableId) {
        if (empty($reservationId) || empty($gameId) || empty($tableId)) {
            return ['error' => 'reservation_id, game_id and table_id are required'];
        }

        $reservation = $this->reservationsModel->getById($reservationId);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }

        $game = $this->gamesModel->getById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }

        $table = $this->tablesModel->getById($tableId);
        if (!$table) {
            return ['error' => 'Table not found'];
        }

        $activeSessions = $this->sessionsModel->getByTable($tableId);
        foreach ($activeSessions as $s) {
            if ($s['status'] === 'active') {
                return ['error' => 'Table already has an active session'];
            }
        }

        $sessionId = $this->sessionsModel->startSession($gameId, $tableId, $reservation['user_id']);

        $this->tablesModel->updateStatus($tableId, 'occupied');

        return ['success' => 'Session started', 'session_id' => $sessionId];
    }
    public function getActiveSessions() {
        $sessions = $this->sessionsModel->getActive();
        $dashboard = [];

        foreach ($sessions as $session) {
            $start     = new DateTime($session['start_time']);
            $now       = new DateTime();
            $elapsed   = $start->diff($now);

            $dashboard[] = [
                'session_id'      => $session['id'],
                'table_id'        => $session['table_id'],
                'game_id'         => $session['game_id'],
                'user_id'         => $session['user_id'],
                'start_time'      => $session['start_time'],
                'elapsed_minutes' => ($elapsed->h * 60) + $elapsed->i,
                'elapsed_display' => $elapsed->format('%H:%I:%S'),
            ];
        }

        return $dashboard;
    }

    public function endSession($sessionId) {
        $session = $this->sessionsModel->getSessionsById($sessionId);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        if ($session['status'] === 'finished') {
            return ['error' => 'Session already finished'];
        }

        $this->sessionsModel->endSession($sessionId);

        // Free the table
        $this->tablesModel->updateStatus($session['table_id'], 'available');

        // Get duration for response
        $duration = $this->sessionsModel->getDuration($sessionId);

        return [
            'success'          => 'Session ended successfully',
            'session_id'       => $sessionId,
            'duration_minutes' => $duration,
        ];
    }

    /** Full session history with details (US12) */
    public function getSessionHistory() {
        return $this->sessionsModel->getAllSessions();
    }

    /** Filter history by game (US12) */
    public function getSessionHistoryByGame($gameId) {
        $game = $this->gamesModel->getById($gameId);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $sessions = $this->sessionsModel->getByGame($gameId);
        return [
            'game'     => $game,
            'sessions' => $sessions,
            'total'    => count($sessions),
        ];
    }

    /** Filter history by user (US12) */
    public function getSessionHistoryByUser($userId) {
        $sessions = $this->sessionsModel->getByUser($userId);
        return [
            'user_id'  => $userId,
            'sessions' => $sessions,
            'total'    => count($sessions),
        ];
    }

    /** Filter history by date (US12) */
    public function getSessionHistoryByDate($date) {
        if (empty($date)) {
            return ['error' => 'Date is required'];
        }
        return $this->sessionsModel->getByDate($date);
    }

    /** Get duration of a finished session */
    public function getSessionDuration($sessionId) {
        $session = $this->sessionsModel->getSessionsById($sessionId);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        $duration = $this->sessionsModel->getDuration($sessionId);
        if ($duration === null) {
            return ['error' => 'Session not finished yet'];
        }
        return ['session_id' => $sessionId, 'duration_minutes' => $duration];
    }
}

?>
