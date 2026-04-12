<?php
// verificaciones de sesion
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}

// Eliminamos el servicio del carrito usando unset
$id = (int)($_POST['id'] ?? 0);
if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// lo mismo que el update se copila los datos para reclaular
$subtotal   = 0;
$totalItems = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal   += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

/*Aca vas a pegar los mismo que pusiste en la regla de
negocios del update */
$descuento=0;
