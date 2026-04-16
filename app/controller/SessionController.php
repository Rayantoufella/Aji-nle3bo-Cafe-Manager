<?php
namespace App\Controller;

use App\Models\SessionModel;
use App\Models\GameModel;
use App\Models\TableModel;
use App\Models\ReservationModel;

class SessionController {
    private $sessionModel;

    public function __construct() {
        $this->sessionModel = new SessionModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $activeSessions = $this->sessionModel->getActive();
        $activeCount = $this->sessionModel->countActive();
        $todayCount = $this->sessionModel->countToday();
        $totalCount = $this->sessionModel->countTotal();

        $pageTitle = 'Sessions';
        require __DIR__ . '/../views/sessions/index.php';
    }

    public function active() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $activeSessions = $this->sessionModel->getActive();
        $pageTitle = 'Active Sessions';
        require __DIR__ . '/../views/sessions/active.php';
    }

    public function history() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $sessions = $this->sessionModel->getFinished();
        $pageTitle = 'Session History';
        require __DIR__ . '/../views/sessions/history.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $gameModel = new GameModel();
        $tableModel = new TableModel();
        $reservationModel = new ReservationModel();

        $games = $gameModel->findAvailable();
        $tables = $tableModel->getAvailable();
        $reservations = $reservationModel->getConfirmedForSession();

        $pageTitle = 'Start Session';
        require __DIR__ . '/../views/sessions/create.php';
    }

    public function start() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->sessionModel->start([
            'reservation_id' => $_POST['reservation_id'] ?? null,
            'game_id'        => $_POST['game_id'],
            'table_id'       => $_POST['table_id']
        ]);

        $_SESSION['flash_success'] = 'Session démarrée !';
        header('Location: ' . BASE_URL . '/sessions');
        exit;
    }

    public function end($id) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->sessionModel->end($id);
        $_SESSION['flash_success'] = 'Session terminée, table libérée !';
        header('Location: ' . BASE_URL . '/sessions');
        exit;
    }
}