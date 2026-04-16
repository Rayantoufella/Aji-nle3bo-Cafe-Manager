<?php
namespace App\Controller;

use App\Models\CategoryModel;

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $pageTitle = 'Categories';
        require __DIR__ . '/../views/categories/index.php';
    }

    public function store() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        if (!empty($name)) {
            $this->categoryModel->create($name);
            $_SESSION['flash_success'] = 'Catégorie ajoutée !';
        }

        header('Location: ' . BASE_URL . '/categories');
        exit;
    }

    public function delete($id) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $this->categoryModel->delete($id);
        $_SESSION['flash_success'] = 'Catégorie supprimée !';
        header('Location: ' . BASE_URL . '/categories');
        exit;
    }
}
