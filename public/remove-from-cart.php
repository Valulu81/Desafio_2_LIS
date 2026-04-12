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
