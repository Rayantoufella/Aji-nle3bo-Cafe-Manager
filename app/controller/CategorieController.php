<?php
namespace App\Controllers;


use App\Models\CategoryModel;

// session_start();
// if(!isset($_SESSION['user_id'])){
    
//         header('Location: index.php');
//         exit();
//     }



class CategorieController {
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index(){
    $cats = $this->categoryModel->getAllCategories();
    require_once __DIR__ . '/../Views/user/categories.php';
}

    public function addCategory(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $cat_name = trim($_POST['cat_name']); 
            if(!empty($cat_name)){
                $this->categoryModel->addCategory($cat_name); 
            }
            header('Location: /category'); 
            exit();
        }
    }

    public function deleteCategory(){
        if(isset($_GET['delete_cat'])){
            $this->categoryModel->deleteCategory($_GET['delete_cat']); 
            header('Location: /category');
            exit();
        }
    }

   public function getCategories($id) {
    return $this->categoryModel->findById($id);
    }
}
?>
