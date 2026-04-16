<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../router/router.php';

use Router\Router;
use App\Controller\AuthController;
use App\Controller\GameController;
use App\Controller\ReservationController;
use App\Controller\SessionController;
use App\Controller\CategoryController;
use App\Controller\DashboardController;

$router = new Router();
$url = $_GET['url'] ?? '';

// ─── Auth Routes ───
$router->get('', function() { 
    if (isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
    header('Location: ' . BASE_URL . '/login');
    exit;
});
$router->get('login', function() { (new AuthController())->showLoginForm(); });
$router->post('login', function() { (new AuthController())->login(); });
$router->get('register', function() { (new AuthController())->showRegisterForm(); });
$router->post('register', function() { (new AuthController())->register(); });
$router->get('logout', function() { (new AuthController())->logout(); });

// ─── Dashboard ───
$router->get('dashboard', function() { (new DashboardController())->index(); });

// ─── Games Routes ───
$router->get('games', function() { (new GameController())->index(); });
$router->get('games/create', function() { (new GameController())->create(); });
$router->post('games/store', function() { (new GameController())->store(); });
$router->get('games/{id}', function($id) { (new GameController())->show($id); });
$router->get('games/{id}/edit', function($id) { (new GameController())->edit($id); });
$router->post('games/{id}/update', function($id) { (new GameController())->update($id); });
$router->post('games/{id}/delete', function($id) { (new GameController())->delete($id); });

// ─── Categories Routes ───
$router->get('categories', function() { (new CategoryController())->index(); });
$router->post('categories/store', function() { (new CategoryController())->store(); });
$router->get('categories/{id}/delete', function($id) { (new CategoryController())->delete($id); });

// ─── Reservations Routes ───
$router->get('reservations', function() { (new ReservationController())->index(); });
$router->get('reservations/create', function() { (new ReservationController())->create(); });
$router->post('reservations/store', function() { (new ReservationController())->store(); });
$router->get('reservations/{id}', function($id) { (new ReservationController())->show($id); });
$router->post('reservations/{id}/update', function($id) { (new ReservationController())->updateStatus($id); });
$router->post('reservations/{id}/cancel', function($id) { (new ReservationController())->cancel($id); });
$router->get('reservations/check-availability', function() { (new ReservationController())->checkAvailability(); });

// ─── Sessions Routes ───
$router->get('sessions', function() { (new SessionController())->index(); });
$router->get('sessions/active', function() { (new SessionController())->active(); });
$router->get('sessions/history', function() { (new SessionController())->history(); });
$router->get('sessions/create', function() { (new SessionController())->create(); });
$router->post('sessions/start', function() { (new SessionController())->start(); });
$router->post('sessions/{id}/end', function($id) { (new SessionController())->end($id); });

// Dispatch
$router->dispatch($url, $_SERVER['REQUEST_METHOD']);
