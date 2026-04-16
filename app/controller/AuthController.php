<?php
namespace App\Controller;

use App\Models\UserModel;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function showLoginForm() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $error = $_SESSION['auth_error'] ?? null;
        unset($_SESSION['auth_error']);
        require __DIR__ . '/../views/auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['auth_error'] = 'Veuillez remplir tous les champs.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $_SESSION['auth_error'] = 'Email ou mot de passe incorrect.';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function showRegisterForm() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        $error = $_SESSION['auth_error'] ?? null;
        unset($_SESSION['auth_error']);
        require __DIR__ . '/../views/auth/registre.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $username = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['auth_error'] = 'Veuillez remplir tous les champs obligatoires.';
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['auth_error'] = 'Les mots de passe ne correspondent pas.';
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['auth_error'] = 'Cet email est déjà utilisé.';
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $userId = $this->userModel->create([
            'username' => $username,
            'email'    => $email,
            'phone'    => $phone,
            'password' => $password,
            'role'     => 'user'
        ]);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $_SESSION['auth_error'] = 'Erreur lors de la création du compte.';
        header('Location: ' . BASE_URL . '/register');
        exit;
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}