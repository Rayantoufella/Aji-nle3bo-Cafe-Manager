<?php

namespace App\Controller;

use App\Models\GameModel;
use App\Controllers\CategorieController;


// session_start();
// if(!isset($_SESSION['user_id'])){
    
//         header('Location: index.php');
//         exit();
//     }

class GamesController {

    private $gameModel;

    public function __construct() {
        $this->gameModel = new GameModel();
    }

    public function index() {
        $games = $this->gameModel->findAll();

        require __DIR__ . '/../views/games/index.php';
    }

    public function show($id) {
        $game = $this->gameModel->findById($id);

        require __DIR__ . '/../views/games/show.php';
    }

    public function filter() {
        $categoryId = $_GET['category'] ?? null;

        if ($categoryId) {
            $games = $this->gameModel->findByCategory($categoryId);
        } else {
            $games = $this->gameModel->findAll();
        }

        require __DIR__ . '/../Views/games/index.php';
    }

    public function store() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $nb_players = $_POST['nb_players'];
            $duration = $_POST['duration'];
            $difficulty = $_POST['difficulty'];
            $description = $_POST['description'];
            $status = $_POST['status'];

        $this->gameModel->create($name, $category_id, $nb_players, $duration, $difficulty, $description, $status);

        header("Location: /games");
        exit();
        }

            
    }
    public function update(){

        $catController = new CategorieController();
        $cats = $catController->getCategories($_GET['category_id']);
        
        $id = $_GET['id'];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $nb_players = $_POST['nb_players'];
            $duration = $_POST['duration'];
            $difficulty = $_POST['difficulty'];
            $description = $_POST['description'];
            $status = $_POST['status'];

            

            $this->gameModel->update($id, $name, $category_id, $nb_players, $duration, $difficulty, $description, $status);

            header('Location: /updategames');
            exit();
        }
        $game = $this->gameModel->findById($id);
        require_once __DIR__ .'/../views/games/edit.php';

    }

    public function delete() {
        $id = $_POST['id'];

        $this->gameModel->delete($id);

        header("Location: /games");
        exit();
    }
}
