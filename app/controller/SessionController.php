<?php
require_once __DIR__ ."/../models/session.php";

// session_start();
// if(!isset($_SESSION['user_id'])){
    
//         header('Location: index.php');
//         exit();
//     }
class SessionsController {

    private $model;

    public function __construct($pdo) {
        $this->model = new SessionsModel($pdo);
    }

    public function index() {
        $sessions = $this->model->getAllSessions();
        return $sessions;
    }

    public function show($id) {
        $session = $this->model->getSessionsById($id);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        return $session;
    }

    public function store($game_id, $table_id, $user_id, $start_time, $end_time, $status) {
        if (empty($game_id) || empty($table_id) || empty($user_id) || empty($start_time) || empty($status)) {
            return ['error' => 'Missing required fields'];
        }
        $this->model->create($game_id, $table_id, $user_id, $start_time, $end_time, $status);
        return ['success' => 'Session created successfully'];
    }

    public function edit($id, $game_id, $table_id, $user_id, $start_time, $end_time, $status) {
        $session = $this->model->getSessionsById($id);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        $this->model->update($id, $game_id, $table_id, $user_id, $start_time, $end_time, $status);
        return ['success' => 'Session updated successfully'];
    }

    public function destroy($id) {
        $session = $this->model->getSessionsById($id);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        $this->model->delete($id);
        return ['success' => 'Session deleted successfully'];
    }

    public function start($gameId, $tableId, $userId) {
        if (empty($gameId) || empty($tableId) || empty($userId)) {
            return ['error' => 'Missing required fields'];
        }
        $sessionId = $this->model->startSession($gameId, $tableId, $userId);
        return ['success' => 'Session started', 'session_id' => $sessionId];
    }

    public function end($sessionId) {
        $session = $this->model->getSessionsById($sessionId);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        if ($session['status'] === 'finished') {
            return ['error' => 'Session already finished'];
        }
        $this->model->endSession($sessionId);
        return ['success' => 'Session ended successfully'];
    }

    public function active() {
        $sessions = $this->model->getActive();
        return $sessions;
    }

    public function byTable($tableId) {
        if (empty($tableId)) {
            return ['error' => 'Table ID is required'];
        }
        return $this->model->getByTable($tableId);
    }

    public function byUser($userId) {
        if (empty($userId)) {
            return ['error' => 'User ID is required'];
        }
        return $this->model->getByUser($userId);
    }

    public function byGame($gameId) {
        if (empty($gameId)) {
            return ['error' => 'Game ID is required'];
        }
        return $this->model->getByGame($gameId);
    }

    public function byDate($date) {
        if (empty($date)) {
            return ['error' => 'Date is required'];
        }
        return $this->model->getByDate($date);
    }

    public function duration($sessionId) {
        $session = $this->model->getSessionsById($sessionId);
        if (!$session) {
            return ['error' => 'Session not found'];
        }
        $duration = $this->model->getDuration($sessionId);
        if ($duration === null) {
            return ['error' => 'Session not finished yet'];
        }
        return ['session_id' => $sessionId, 'duration_minutes' => $duration];
    }

    public function countGame($gameId) {
        if (empty($gameId)) {
            return ['error' => 'Game ID is required'];
        }
        $total = $this->model->countByGame($gameId);
        return ['game_id' => $gameId, 'total_sessions' => $total];
    }

    public function countUser($userId) {
        if (empty($userId)) {
            return ['error' => 'User ID is required'];
        }
        $total = $this->model->countByUser($userId);
        return ['user_id' => $userId, 'total_sessions' => $total];
    }
}
?>