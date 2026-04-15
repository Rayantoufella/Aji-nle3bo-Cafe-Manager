<?php
namespace App\Models;

use App\Models\Database;
use PDO;
class CategoryModel {
    private $conn;
    private $name;
    private $id;

    public function getName() {
        return $this->name;
    }
    public function getId() {
        return $this->id;
    }

    public function __construct()
    {
        $database = new DatabaseModel();
        $this->conn = $database->connect();
    }

    public function getAllCategories(){
        $sql = 'SELECT * FROM categories';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCategory($name){
        $sql = 'INSERT INTO categories(name) VALUES(?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$name]);
    }

    public function deleteCategory($id){
        $sql = 'DELETE FROM categories WHERE id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
    }
}
?>