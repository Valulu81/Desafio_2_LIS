<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}
//verificar si esta logeado 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
    exit;
}
// se recibe el id del servicio que manda el JavaScript
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// logica detras de que bsuca el base para obtener la info del servicio
require_once '../config/database.php';
require_once '../models/Service.php';


$serviceModel = new Service();
$servicio = $serviceModel->getById($id);
if (!$servicio) {
    echo json_encode(['success' => false, 'message' => 'Servicio no encontrado']);
    exit;
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* Accion en caso de  que el servicio este o no agregado */
if (isset($_SESSION['cart'][$id])) {
    // Verificamos que no pase del máximo de 10 por servicio
    if ($_SESSION['cart'][$id]['quantity'] >= 10) {
        echo json_encode(['success' => false, 'message' => 'Máximo 10 unidades por servicio']);
        exit;
    }
    $_SESSION['cart'][$id]['quantity']++;
} else {
    $_SESSION['cart'][$id] = [
        'id'        => $servicio['id'],
        'title'     => $servicio['title'],
        'price'     => $servicio['price'],
        'quantity'  => 1,
        'image_url' => $servicio['image_url'] ?? ''
    ];
}

// eseto solo es para aumentar el contador del menu
$totalItems = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalItems += $item['quantity'];
}

// Devolvemos éxito y el nuevo total de items al JavaScript
echo json_encode([
    'success'    => true,
    'message'    => 'Servicio agregado al carrito',
    'totalItems' => $totalItems
]);
exit;
