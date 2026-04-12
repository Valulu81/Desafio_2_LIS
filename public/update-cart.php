<?php
if(session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}
//validaciones del carrito

// Valida que la cantidad esten entre 1 y 10
$id       = (int)($_POST['id']       ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($quantity < 1 || $quantity > 10) {
    echo json_encode(['success' => false, 'message' => 'Cantidad debe estar entre 1 y 10']);
    exit;
}

//valida si esta vacio
if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['success' => false, 'message' => 'Servicio no encontrado en el carrito']);
    exit;
}
// fin

// Actualizamos la cantidad de los items
$_SESSION['cart'][$id]['quantity'] = $quantity;
//inicializa las variables para calclar los totales
$subtotal = 0;
$totalItems = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal   += $item['price'] * $item['quantity'];
    $totalItems += $item['quantity'];
}

/* ACa pone la regla del negocio no se cual vas a ocupar porque la 
vez pasada me dijiste que daba error xd*/
$descuento =0; //mi aportacion 