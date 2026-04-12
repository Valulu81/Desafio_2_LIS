<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {

    private $userModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->userModel = new User();
    }

    // Muestra el login o lo procesa dependiendo si vino un POST
    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Por favor completa todos los campos';
            } else {
                $user = $this->userModel->login($email, $password);

                if ($user) {
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role']  = $user['role'];
                    header('Location: index.php?action=services');
                    exit;
                } else {
                    $error = 'Correo o contraseña incorrectos';
                }
            }
        }

        require_once __DIR__ . '/../views/auth.php';
    }

    // Muestra el registro o lo procesa dependiendo si vino un POST
    public function register() {
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'      => trim($_POST['name']      ?? ''),
                'email'     => trim($_POST['email']      ?? ''),
                'company'   => trim($_POST['company']    ?? ''),
                'telephone' => trim($_POST['telephone']  ?? ''),
                'password'  => $_POST['password']        ?? ''
            ];
            $confirmar = $_POST['confirm_password'] ?? '';

            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                $error = 'Por favor completa todos los campos obligatorios';
            } elseif ($data['password'] !== $confirmar) {
                $error = 'Las contraseñas no coinciden';
            } else {
                $resultado = $this->userModel->register($data);
                if ($resultado['success']) {
                    $success = 'Cuenta creada. Ya puedes iniciar sesión.';
                } else {
                    $error = $resultado['message'];
                }
            }
        }

        require_once __DIR__ . '/../views/register.php';
    }

    // Destruye la sesión y manda al login
    public function logout() {
        $_SESSION = [];
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
?>