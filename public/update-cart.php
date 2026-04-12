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

/* Descuento del tipo A*/
if ($subtotal >= 2500) {
    $descuento = $subtotal * 0.15;
} elseif ($subtotal >= 1000) {
    $descuento = $subtotal * 0.10;
} elseif ($subtotal >= 500) {
    $descuento = $subtotal * 0.05;
}
$iva   = ($subtotal - $descuento) * 0.13;
$total = $subtotal - $descuento + $iva;

echo json_encode([
    'success'    => true,
    'totalItems' => $totalItems,
    'subtotal'   => number_format($subtotal, 2),
    'descuento'  => number_format($descuento, 2),
    'iva'        => number_format($iva, 2),
    'total'      => number_format($total, 2)
]);
exit;