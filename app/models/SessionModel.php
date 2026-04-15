<?php

class SessionsModel {

    protected $id;
    protected $game_id;
    protected $table_id;
    protected $user_id;
    protected $start_time;
    protected $end_time;
    protected $status;

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllSessions() {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSessionsById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($game_id, $table_id, $user_id, $start_time, $end_time, $status) {
        $stmt = $this->pdo->prepare("INSERT INTO sessions (game_id, table_id, user_id, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$game_id, $table_id, $user_id, $start_time, $end_time, $status]);
    }

    public function update($id, $game_id, $table_id, $user_id, $start_time, $end_time, $status) {
        $stmt = $this->pdo->prepare("UPDATE sessions SET game_id = ?, table_id = ?, user_id = ?, start_time = ?, end_time = ?, status = ? WHERE id = ?");
        return $stmt->execute([$game_id, $table_id, $user_id, $start_time, $end_time, $status, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function startSession($gameId, $tableId, $userId) {
        $start_time = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("INSERT INTO sessions (game_id, table_id, user_id, start_time, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$gameId, $tableId, $userId, $start_time]);
        return $this->pdo->lastInsertId();
    }

    public function endSession($sessionId) {
        $end_time = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("UPDATE sessions SET end_time = ?, status = 'finished' WHERE id = ?");
        return $stmt->execute([$end_time, $sessionId]);
    }

    public function getActive() {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE status = 'active'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getByTable($tableId) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE table_id = ?");
        $stmt->execute([$tableId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByGame($gameId) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE game_id = ?");
        $stmt->execute([$gameId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDate($date) {
        $stmt = $this->pdo->prepare("SELECT * FROM sessions WHERE DATE(start_time) = ?");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDuration($sessionId) {
        $stmt = $this->pdo->prepare("SELECT TIMESTAMPDIFF(MINUTE, start_time, end_time) AS duration FROM sessions WHERE id = ?");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['duration'] : null;
    }

    public function countByGame($gameId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM sessions WHERE game_id = ?");
        $stmt->execute([$gameId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function countByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS total FROM sessions WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
