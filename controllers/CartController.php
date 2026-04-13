<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../models/Quote.php';
require_once __DIR__ . '/../models/QuoteDetail.php';
require_once __DIR__ . '/../config/database.php';

class CartController
{
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

    // nuevo método: imprime HTML directamente
    public function summary(): void
{
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        echo "<p>El carrito está vacío</p>";
        return;
    }

    $db = Database::getInstance()->getConnection();
    $subtotal = 0;
    // temporal para debug
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";

    foreach ($cart as $item) {
        if (!isset($item['id'])) {
            continue;
        }
        $stmt = $db->prepare("SELECT price FROM services WHERE id = ?");
        $stmt->execute([$item['id']]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($service) {
            $cantidad = $item['quantity'] ?? 1;
            $subtotal += $service['price'] * $cantidad;
            // $cantidad = $item['quantity'] ?? 1;
            // $subtotal += $service['price'] * $cantidad;
        }
    }

    $descuento = 0;
    if ($subtotal >= 2500) {
        $descuento = $subtotal * 0.15;
    } elseif ($subtotal >= 1000) {
        $descuento = $subtotal * 0.10;
    } elseif ($subtotal >= 500) {
        $descuento = $subtotal * 0.05;
    }

    $iva   = ($subtotal - $descuento) * 0.13;
    $total = $subtotal - $descuento + $iva;

    echo "
        <div class='row'>
            <div class='col'>Subtotal</div>
            <div class='col text-right'>\$" . number_format($subtotal, 2) . "</div>
        </div>
        <hr>
        <div class='row'>
            <div class='col'>Descuento</div>
            <div class='col text-right'>\$" . number_format($descuento, 2) . "</div>
        </div>
        <hr>
        <div class='row'>
            <div class='col'>Impuesto</div>
            <div class='col text-right'>\$" . number_format($iva, 2) . "</div>
        </div>
        <hr>
        <div class='row'>
            <div class='col'>TOTAL</div>
            <div class='col text-right'>\$" . number_format($total, 2) . "</div>
        </div>
    ";
}


    private function jsonError(string $msg): void
    {
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }
}

// router
$controller = new CartController();
$action = $_GET['action'] ?? '';

if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->checkout();
} elseif ($action === 'summary') {
    $controller->summary();
} else {
    // no cortar la ejecución aquí, solo mostrar mensaje
    echo "<!-- Acción no permitida -->";
}
