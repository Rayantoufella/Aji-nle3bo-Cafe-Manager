<?php

namespace App\Controller;

use App\Models\GameModel;
use App\Models\CategoryModel;
use App\Models\UserModel;

class UserController {

    private $gameModel;
    private $categoryModel;
    private $userModel;

    public function __construct() {
        $this->gameModel = new GameModel();
        $this->categoryModel = new CategoryModel();
        $this->userModel = new UserModel();
    }

    public function dashboard() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/login');
            exit();
        }

        // Fetch user data
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);

        if (!$user) {
            session_destroy();
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/login');
            exit();
        }

        // Fetch games and categories
        $games = $this->gameModel->findAll();
        $categories = $this->categoryModel->getAllCategories();

        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        
        // Render the dashboard view
        require dirname(__DIR__) . '/views/user/Dashboard.php';
    }
}
