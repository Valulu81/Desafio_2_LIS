<?php

class CartController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function index() {
        $this->requireLogin();

        // Obtenemos el carrito de la sesión
        $cart = $_SESSION['cart'] ?? [];

        // Calculamos subtotal sumando precio * cantidad de cada item
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Descuento por monto - Opción A del proyecto
        if ($subtotal >= 2500) {
            $descuento = $subtotal * 0.15;
        } elseif ($subtotal >= 1000) {
            $descuento = $subtotal * 0.10;
        } elseif ($subtotal >= 500) {
            $descuento = $subtotal * 0.05;
        } else {
            $descuento = 0;
        }

        // IVA del 13% sobre (subtotal - descuento)
        $iva   = ($subtotal - $descuento) * 0.13;
        $total = $subtotal - $descuento + $iva;

        // Total de items para el badge del navbar
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartCount += $item['quantity'];
        }

        // Cargamos la vista pasándole todas las variables calculadas
        require_once __DIR__ . '/../views/cart.php';
    }

// segurida
    private function requireLogin() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../public/index.php?action=login');
            exit;
        }
    }
}
?>