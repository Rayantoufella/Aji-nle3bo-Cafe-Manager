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
        $this->tablesModel        = new TablesModel($pdo);
    }

    // ================================================================
    //  MODULE 1 — CATALOGUE DE JEUX (US3)
    // ================================================================

    /** List all games */
    public function listGames() {
        return $this->gamesModel->getAll();
    }

    /** Get one game detail */
    public function showGame($id) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        return $game;
    }

    /** Add a new game (US3) */
    public function addGame($name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        if (empty($name) || empty($category) || empty($status)) {
            return ['error' => 'Missing required fields'];
        }
        $this->gamesModel->create($name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status);
        return ['success' => 'Game added successfully'];
    }

    /** Edit an existing game (US3) */
    public function editGame($id, $name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->gamesModel->update($id, $name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status);
        return ['success' => 'Game updated successfully'];
    }

    /** Delete a game (US3) */
    public function deleteGame($id) {
        $game = $this->gamesModel->getById($id);
        if (!$game) {
            return ['error' => 'Game not found'];
        }
        $this->gamesModel->delete($id);
        return ['success' => 'Game deleted successfully'];
    }

    /** Filter games by category (US4) */
    public function filterGamesByCategory($category) {
        if (empty($category)) {
            return ['error' => 'Category is required'];
        }
        return $this->gamesModel->getByCategory($category);
    }

    // ================================================================
    //  MODULE 2 — RESERVATIONS (US8)
    // ================================================================

    /** Get all reservations for today (US8) */
    public function getTodayReservations() {
        $today = date('Y-m-d');
        return $this->reservationsModel->getByDate($today);
    }

    /** Get all reservations (full list) */
    public function listReservations() {
        return $this->reservationsModel->getAll();
    }

    /** Get one reservation detail */
    public function showReservation($id) {
        $reservation = $this->reservationsModel->getById($id);
        if (!$reservation) {
            return ['error' => 'Reservation not found'];
        }
        return $reservation;
    }

    /** Confirm a reservation (US8) */
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

    /** Cancel a reservation (US8) */
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

    /** Check available tables for a given date and time slot (US5) */
    public function checkAvailability($date, $time_slot) {
        if (empty($date) || empty($time_slot)) {
            return ['error' => 'Date and time slot are required'];
        }
        return $this->tablesModel->getAvailable($date, $time_slot);
    }

    // ================================================================
    //  MODULE 3 — SESSIONS (US9, US10, US11, US12)
    // ================================================================

    /** Start a session: link reservation + game + table (US9) */
    public function startSession($reservationId, $gameId, $tableId) {
        if (empty($reservationId) || empty($gameId) || empty($tableId)) {
            return ['error' => 'reservation_id, game_id and table_id are required'];
        }

        // Validate all three exist
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

        // Check no active session on that table already
        $activeSessions = $this->sessionsModel->getByTable($tableId);
        foreach ($activeSessions as $s) {
            if ($s['status'] === 'active') {
                return ['error' => 'Table already has an active session'];
            }
        }

        $sessionId = $this->sessionsModel->startSession($gameId, $tableId, $reservation['user_id']);

        // Mark table as occupied
        $this->tablesModel->updateStatus($tableId, 'occupied');

        return ['success' => 'Session started', 'session_id' => $sessionId];
    }

    /** Dashboard: all active sessions with game and elapsed time (US10) */
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

    /** End a session and free the table (US11) */
    public function endSession($sessionId) {
        $session = $this->sessionsModel->getById($sessionId);
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
        return $this->sessionsModel->getAll();
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
        $session = $this->sessionsModel->getById($sessionId);
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