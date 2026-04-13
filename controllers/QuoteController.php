<?php

require_once __DIR__ . '/../models/Quote.php';

class QuoteController {

    private $quoteModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->quoteModel = new Quote();
    }


    public function index() {
        $this->requireLogin();

        // Dependiendo del rol cargamos cotizaciones diferentes
        if ($_SESSION['user_role'] === 'admin') {
            $quotes = $this->quoteModel->getAll();
        } else {
            $quotes = $this->quoteModel->getByUserId($_SESSION['user_id']);
        }

        require_once __DIR__ . '/../views/quotes.php';
    }


    public function procesarCotizacion() {
        $this->requireLogin();
        header('Content-Type: application/json');

        // Validamos que vengan todos los datos del cliente
        $clienteData = [
            'id'        => $_SESSION['user_id'],
            'nombre'    => trim($_POST['nombre']  ?? ''),
            'empresa'   => trim($_POST['empresa']  ?? ''),
            'email'     => trim($_POST['email']    ?? ''),
            'telefono'  => trim($_POST['telefono'] ?? '')
        ];

        // Verificamos que ningún campo del cliente esté vacío
        if (empty($clienteData['nombre'])  ||
            empty($clienteData['empresa']) ||
            empty($clienteData['email'])   ||
            empty($clienteData['telefono'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los datos del cliente son obligatorios'
            ]);
            exit;
        }

        // Validamos que el email tenga formato correcto
        if (!filter_var($clienteData['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo electrónico no es válido'
            ]);
            exit;
        }

        // Obtenemos el carrito de la sesión
        $cart = $_SESSION['cart'] ?? [];

        // Le pedimos al modelo que genere la cotización
        $resultado = $this->quoteModel->generar($clienteData, $cart);

        if ($resultado['success']) {
            // Si todo salió bien vaciamos el carrito
            $_SESSION['cart'] = [];
        }

        echo json_encode($resultado);
        exit;
    }

    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }
}
?>