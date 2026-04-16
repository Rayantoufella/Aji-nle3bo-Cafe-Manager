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

    public function create($data){
try{
    $query = 'INSERT INTO users (username, email, password, role, created_at) VALUES (:username, :email, :password, :role, :created_at)' ;
    $this->db->prepare($query) ;
    $this->db->bindValue(':username', htmlspecialchars($data['username']), PDO::PARAM_STR) ;
    $this->db->bindValue(':email', htmlspecialchars($data['email']), PDO::PARAM_STR) ;
    $this->db->bindValue(':password', password_hash($data['password'], PASSWORD_BCRYPT), PDO::PARAM_STR) ;
    $this->db->bindValue(':role', 'user', PDO::PARAM_STR) ;
    $this->db->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR) ;
}catch(PDOException $e){
    exit ("Erreur  Connectionn " . $e->getMessage()) ;

}

    }
    public function finByEmail($email){
        try{
            $query = 'SELECT email FROM users WHERE email = :email ' ;
            $this->db->prepare($query) ;
            $this->db->bindValue(':email', $email, PDO::PARAM_STR) ;
            $this->db->execute() ;
            return $this->db->fetch(PDO::FETCH_ASSOC) ;
        }catch(PDOException $e){
            exit('Error fetching user by email' . $e->getMessage()) ;
        }
    }

    public function findById(){
        try{
            $query = 'SELECT * FROM users WHERE id = :id ' ;
            $this->db->prepare($query) ;
            $this->db->bindValue(':id', $this->id, PDO::PARAM_INT) ;
            $this->db->execute() ;
            return $this->db->fetch(PDO::FETCH_ASSOC) ;
        }catch(PDOException $e){
            exit('Error fetching user by id' . $e->getMessage()) ;
        }
    }

    

}