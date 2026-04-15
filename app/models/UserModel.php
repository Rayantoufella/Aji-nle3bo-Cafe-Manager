<?php 

namespace App\Models;

use App\Models\DatabaseModel;
use PDO ; 
use PDOException;

class UserModel {
    protected $db ;
    protected $id ; 
    protected $username ;

    protected $email ; 
    protected $password ;
    protected $role ; 
    protected $created_at ; 

    public function __construct(){
        $this->db = (new DatabaseModel())->connect();

    }

    public function login($email,$password){
        try{
                    $this->email = $email;
        $this->password = $password;
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email") ; 
        $stmt->execute(["email" => $this->email]) ; 

        $user = $stmt->fetch(PDO::FETCH_ASSOC) ;
        
        if($user && password_verify($this->password,$user['password'])){
            return $user ;
        }
        }catch(PDOException $e){
            return false ;
        }
    }

    public function register($username ,$email ,$password){
        try{
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;

        $query = "INSERT INTO users (username,email,password) VALUES (:username,:email,:password)" ;
        $stmt = $this->db->prepare($query) ;
        $stmt->execute(["username" => $this->username,"email" => $this->email,"password" => $this->password]) ;
        return true ;
        }catch(PDOException $e){
            return false ;
        }
    }

    public function logout(){
        session_destroy();
        header("Location: /Aji-nle3bo-Cafe-Manager/public/index.php");
    }

    

}