<?php
namespace App\Controller;

use App\Models\ReservationModel;
use App\Models\SessionModel;
use App\Models\GameModel;
use App\Models\TableModel;

class DashboardController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $reservationModel = new ReservationModel();
        $sessionModel = new SessionModel();
        $gameModel = new GameModel();
        $tableModel = new TableModel();

        $totalReservations = $reservationModel->count();
        $activeSessions = $sessionModel->countActive();
        $availableTables = $tableModel->countAvailable();
        $totalTables = $tableModel->countTotal();
        $mostPlayed = $sessionModel->getMostPlayedGame();
        $recentReservations = $reservationModel->getRecent(5);
        $activeSessionsList = $sessionModel->getActive();
        $popularGames = $gameModel->getMostPlayed(5);

        $pageTitle = 'Dashboard';
        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
