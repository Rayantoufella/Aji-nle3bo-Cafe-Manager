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
            $stmt = $this->db->prepare($query) ;
            $stmt->bindValue(':username', htmlspecialchars($data['username']), PDO::PARAM_STR) ;
            $stmt->bindValue(':email', htmlspecialchars($data['email']), PDO::PARAM_STR) ;
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_BCRYPT), PDO::PARAM_STR) ;
            $stmt->bindValue(':role', 'user', PDO::PARAM_STR) ;
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR) ;
            $stmt->execute() ;
        }catch(PDOException $e){
            exit ("Erreur Connexion " . $e->getMessage()) ;
        }
    }
    public function finByEmail($email){
        try{
            $query = 'SELECT * FROM users WHERE email = :email' ;
            $stmt = $this->db->prepare($query) ;
            $stmt->bindValue(':email', $email, PDO::PARAM_STR) ;
            $stmt->execute() ;
            return $stmt->fetch(PDO::FETCH_ASSOC) ;
        }catch(PDOException $e){
            exit('Error fetching user by email: ' . $e->getMessage()) ;
        }
    }

    public function findById(){
        try{
            $query = 'SELECT * FROM users WHERE id = :id' ;
            $stmt = $this->db->prepare($query) ;
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT) ;
            $stmt->execute() ;
            return $stmt->fetch(PDO::FETCH_ASSOC) ;
        }catch(PDOException $e){
            exit('Error fetching user by id: ' . $e->getMessage()) ;
        }
    }

    

}