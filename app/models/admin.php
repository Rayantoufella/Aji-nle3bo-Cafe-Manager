<?php

class AdminModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    //  MODULE 1 — GAMES
    // ================================================================

    public function getAllGames() {
        $stmt = $this->pdo->prepare("SELECT * FROM games");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createGame($name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO games (name, category, min_players, max_players, duration, description, difficulty, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status]);
    }

    public function updateGame($id, $name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status) {
        $stmt = $this->pdo->prepare("UPDATE games SET name = ?, category = ?, min_players = ?, max_players = ?, duration = ?, description = ?, difficulty = ?, status = ? WHERE id = ?");
        return $stmt->execute([$name, $category, $min_players, $max_players, $duration, $description, $difficulty, $status, $id]);
    }

    public function deleteGame($id) {
        $stmt = $this->pdo->prepare("DELETE FROM games WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getGamesByCategory($category) {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE category = ?");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countGames() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM games");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // ================================================================
    //  MODULE 2 — RESERVATIONS
    // ================================================================

    public function getAllReservations() {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations ORDER BY date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getReservationsByDate($date) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE DATE(date) = ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservationsByStatus($status) {
        $stmt = $this->pdo->prepare("SELECT * FROM reservations WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateReservationStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getAvailableTables($date, $time_slot) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM tables
            WHERE id NOT IN (
                SELECT table_id FROM reservations
                WHERE DATE(date) = ? AND time_slot = ? AND status != 'cancelled'
            )
        ");
        $stmt->execute([$date, $time_slot]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countReservationsByStatus($status) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM reservations WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // ================================================================
    //  MODULE 3 — SESSIONS
    // ================================================================

    public function getAllSessions() {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions ORDER BY start_time DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActiveSessions() {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function startSession($game_id, $table_id, $user_id) {
        $start_time = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("INSERT INTO sessions (game_id, table_id, user_id, start_time, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$game_id, $table_id, $user_id, $start_time]);
        return $this->pdo->lastInsertId();
    }

    public function endSession($id) {
        $end_time = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("UPDATE sessions SET end_time = ?, status = 'finished' WHERE id = ?");
        return $stmt->execute([$end_time, $id]);
    }

    public function getSessionsByGame($game_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE game_id = ?");
        $stmt->execute([$game_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionsByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionsByTable($table_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE table_id = ?");
        $stmt->execute([$table_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionsByDate($date) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE DATE(start_time) = ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionDuration($id) {
        $stmt = $this->pdo->prepare("SELECT TIMESTAMPDIFF(MINUTE, start_time, end_time) AS duration FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['duration'] : null;
    }

    public function countSessionsByGame($game_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM sessions WHERE game_id = ?");
        $stmt->execute([$game_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function countSessionsByUser($user_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // ================================================================
    //  TABLES
    // ================================================================

    public function getAllTables() {
        $stmt = $this->pdo->prepare("SELECT * FROM tables");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTableById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tables WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTableStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE tables SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}

?>