<?php
namespace App\Controller;

use App\Models\GameModel;
use App\Models\CategoryModel;

class GameController {
    private $gameModel;
    private $categoryModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $categoryFilter = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;

        if ($search) {
            $games = $this->gameModel->search($search);
        } elseif ($categoryFilter) {
            $games = $this->gameModel->findByCategory($categoryFilter);
        } else {
            $games = $this->gameModel->findAll();
        }

        $categories = $this->categoryModel->getAll();
        $totalGames = $this->gameModel->count();
        $availableCount = $this->gameModel->countAvailable();
        $inUseCount = $this->gameModel->countInUse();

        $pageTitle = 'Game Catalogue';
        require __DIR__ . '/../views/games/index.php';
    }

    public function show($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $game = $this->gameModel->findById($id);
        if (!$game) {
            header('Location: ' . BASE_URL . '/games');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $pageTitle = $game['name'];
        require __DIR__ . '/../views/games/show.php';
    }

    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $pageTitle = 'Add New Game';
        require __DIR__ . '/../views/games/create.php';
    }

    public function store() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->gameModel->create([
            'name'        => $_POST['name'],
            'category_id' => $_POST['category_id'],
            'nb_players'  => $_POST['nb_players'],
            'duration'    => $_POST['duration'],
            'difficulty'  => $_POST['difficulty'],
            'description' => $_POST['description'],
            'image_url'   => $_POST['image_url'] ?? null,
            'status'      => $_POST['status'] ?? 'available'
        ]);

        $_SESSION['flash_success'] = 'Jeu ajouté avec succès !';
        header('Location: ' . BASE_URL . '/games');
        exit;
    }

    public function edit($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $game = $this->gameModel->findById($id);
        if (!$game) {
            header('Location: ' . BASE_URL . '/games');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $pageTitle = 'Edit Game';
        require __DIR__ . '/../views/games/edit.php';
    }

    public function update($id) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->gameModel->update($id, [
            'name'        => $_POST['name'],
            'category_id' => $_POST['category_id'],
            'nb_players'  => $_POST['nb_players'],
            'duration'    => $_POST['duration'],
            'difficulty'  => $_POST['difficulty'],
            'description' => $_POST['description'],
            'image_url'   => $_POST['image_url'] ?? null,
            'status'      => $_POST['status']
        ]);

        $_SESSION['flash_success'] = 'Jeu modifié avec succès !';
        header('Location: ' . BASE_URL . '/games');
        exit;
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->gameModel->delete($id);
        $_SESSION['flash_success'] = 'Jeu supprimé avec succès !';
        header('Location: ' . BASE_URL . '/games');
        exit;
    }
}
