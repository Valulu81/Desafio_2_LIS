<?php
// controlador para el carrito de compras
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../models/Quote.php';
require_once __DIR__ . '/../models/QuoteDetail.php';

class CartController
{
    // convierte el carrito en una cotización
    public function checkout(): void
    {
        header('Content-Type: application/json');

        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->jsonError('El carrito está vacío');
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonError('Debes iniciar sesión');
        }

        $client_id   = $_SESSION['user_id'];
        $subtotal    = 0;
        $descuento   = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if ($subtotal >= 2500) {
            $descuento = $subtotal * 0.15;
        } elseif ($subtotal >= 1000) {
            $descuento = $subtotal * 0.10;
        } elseif ($subtotal >= 500) {
            $descuento = $subtotal * 0.05;
        }

        $iva   = ($subtotal - $descuento) * 0.13;
        $total = $subtotal - $descuento + $iva;

        $quoteModel  = new Quote();
        $detailModel = new QuoteDetail();

        $code    = $quoteModel->generateUniqueCode();
        $created = $quoteModel->createQuote([
            'code'      => $code,
            'client_id' => $client_id,
            'total'     => $total
        ]);

        if (!$created) {
            $this->jsonError('Error al crear la cotización');
        }

        $quote = $quoteModel->getQuoteByCode($code);

        foreach ($cart as $item) {
            $detailModel->insertService(
                $quote['id'],
                $item['id'],
                $item['price'],
                $item['quantity']
            );
        }

        // limpiar carrito
        $_SESSION['cart'] = [];

        echo json_encode([
            'success'   => true,
            'code'      => $code,
            'subtotal'  => number_format($subtotal, 2),
            'descuento' => number_format($descuento, 2),
            'iva'       => number_format($iva, 2),
            'total'     => number_format($total, 2)
        ]);
        exit;
    }

    private function jsonError(string $msg): void
    {
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }
}

// router
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$controller = new CartController();
$controller->checkout();