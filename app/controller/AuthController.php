<?php

namespace App\Controller;
use App\Models\UserModel;
use PDOException;


class AuthController
{

    private $userModel ;

    public function __construct(){
        $this->userModel = new UserModel();
    }
    public function showRegisterForm()
    {
        require dirname(__DIR__) . '/views/auth/registre.php';
    }

    public function Register()
    {
       try{
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $username = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['password_confirm'] ?? '';
            $terms = isset($_POST['terms']);

            // Validate empty fields
            if(empty($username) || empty($email) || empty($password) || empty($confirm_password)){
                $error = "All fields are required";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            if(!$terms){
                $error = "You must accept the Terms of Service.";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            // Validate email format
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error = "Please enter a valid email";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            if($password !== $confirm_password){
                $error = "Passwords do not match.";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            if(strlen($password) < 6){
                $error = "Password must be at least 6 characters";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            if($this->userModel->emailExists($email)){
                $error = "This email is already registered.";
                require dirname(__DIR__) . '/views/auth/registre.php';
                exit();
            }

            $this->userModel->create([
                'username' => $username, 
                'email' => $email, 
                'password' => $password, 
                'phone' => $phone
            ]);
            
            $baseUrl = defined('BASE_URL') ? BASE_URL : '';
            header('Location: ' . $baseUrl . '/login?success=Registration successful! Please login.');
            exit();
           }
       }catch(PDOException $e){
            exit('Error registering user: ' . $e->getMessage());
       }
    }

    public function showLoginForm()
    {
        require dirname(__DIR__) . '/views/auth/login.php';
    }

    public function Login()
    {
        try{
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                $email = $_POST['email'];
                $password = $_POST['password'];
                $user = $this->userModel->findByEmail($email);
                if($user && password_verify($password, $user['password'])){
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    $baseUrl = defined('BASE_URL') ? BASE_URL : '';
                    header('Location: ' . $baseUrl . '/dashboard');
                    exit();
                }
                else{
                    $error = "Invalid email or password.";
                    require dirname(__DIR__) . '/views/auth/login.php';
                    exit();
                }
            }
        }catch(PDOException $e){
            exit('Error logging in: ' . $e->getMessage());
        }
    }

    public function Logout()
    {
        session_destroy();
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        header('Location: ' . $baseUrl . '/login');
        exit();
    }
}
?>