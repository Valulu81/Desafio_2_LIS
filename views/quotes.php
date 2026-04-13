<?php
// Iniciamos sesión y llamamos al controlador
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/database.php';
require_once '../controllers/QuoteController.php';
require_once '../models/QuoteDetail.php';
$controller = new QuoteController();
$controller->index();

$usuario = [
    'nombre' => $_SESSION['user_name'] ?? 'Usuario',
    'rol'    => $_SESSION['user_role'] ?? 'user'
];
$quotes = $quotes ?? [];

// Contamos items del carrito para el badge del navbar
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Cotizaciones - UDB Academy SV</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/styles.css" rel="stylesheet" />
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#">UDB Academy sv</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link" href="../public/index.php?action=services">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/index.php?action=quotes">Cotizaciones</a></li>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="../public/index.php?action=admin">Administrar servicios</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-dark" href="../public/index.php?action=cart">
                        <i class="bi-cart-fill me-1"></i>
                        Cart
                        <span class="badge bg-dark text-white ms-1 rounded-pill" id="cart-count">
                            <?= $cartCount ?>
                        </span>
                    </a>
                    <a class="btn btn-outline-danger" href="../public/index.php?action=logout">
                        <i class="bi bi-x-circle-fill"></i> Salir
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="background py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Revisa tus cotizaciones</h1>
                <p class="lead fw-normal text-white-50 mb-0">Encuentra todas tus cotizaciones aquí.</p>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <?php if (empty($quotes)): ?>
            <!-- Si no hay cotizaciones mostramos este mensaje -->
            <div class="text-center py-5">
                <p class="text-muted">No tienes cotizaciones aún.</p>
                <a href="../public/index.php?action=services" class="btn btn-dark">Ver servicios</a>
            </div>
        <?php else: ?>

            <!-- Tabla para desktop -->
            <div class="d-none d-md-block">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Válido hasta</th>
                            <th>Total</th>
                            <th>Servicios</th>
                            <th>Ver más</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotes as $q):
                            // Obtenemos el detalle de servicios de esta cotización
                            // directamente desde PHP para no necesitar un endpoint extra
                            $detailModel = new QuoteDetail();
                            $detalle = $detailModel->getByQuoteId($q['id']);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($q['code']) ?></td>
                                <td><?= htmlspecialchars($q['cliente_nombre'] ?? $_SESSION['user_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($q['created_at'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($q['valid_until'])) ?></td>
                                <td>$<?= number_format($q['total'], 2) ?></td>
                                <td><?= $q['cantidad_servicios'] ?></td>
                                <td>
                                    <button class="btn btn-dark btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detalleModal"
                                        data-code="<?= htmlspecialchars($q['code']) ?>"
                                        data-total="<?= $q['total'] ?>"
                                        data-fecha="<?= date('d/m/Y', strtotime($q['created_at'])) ?>"
                                        data-validez="<?= date('d/m/Y', strtotime($q['valid_until'])) ?>"
                                        data-items='<?= htmlspecialchars(json_encode($detalle), ENT_QUOTES, 'UTF-8') ?>'>
                                        Ver detalle
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cards para móvil -->
            <div class="d-md-none">
                <?php foreach ($quotes as $q):
                    $detailModel = new QuoteDetail();
                    $detalle = $detailModel->getByQuoteId($q['id']);
                ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($q['code']) ?></h5>
                            <p><strong>Cliente:</strong> <?= htmlspecialchars($q['cliente_nombre'] ?? $_SESSION['user_name']) ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($q['created_at'])) ?></p>
                            <p><strong>Total:</strong> $<?= number_format($q['total'], 2) ?></p>
                            <p><strong>Servicios:</strong> <?= $q['cantidad_servicios'] ?></p>
                            <button class="btn btn-dark btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#detalleModal"
                                data-code="<?= htmlspecialchars($q['code']) ?>"
                                data-total="<?= $q['total'] ?>"
                                data-fecha="<?= date('d/m/Y', strtotime($q['created_at'])) ?>"
                                data-validez="<?= date('d/m/Y', strtotime($q['valid_until'])) ?>"
                                data-items='<?= htmlspecialchars(json_encode($detalle), ENT_QUOTES, 'UTF-8') ?>'>
                                Ver detalle
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>

    <!-- Modal de detalle -->
    <div class="modal fade" id="detalleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Código:</strong> <span id="detalleCodigo"></span></p>
                    <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                    <p><strong>Válido hasta:</strong> <span id="detalleValidez"></span></p>
                    <h6>Servicios:</h6>
                    <ul id="detalleServicios" class="list-group mb-3"></ul>
                    <div class="text-end">
                        <strong>Total: $<span id="detalleTotal"></span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Valeria Paredes &amp; Andre Preza</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cuando se abre el modal llenamos los datos con lo que
        // ya viene en los data-attributes del botón
        document.getElementById('detalleModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const code = button.getAttribute('data-code');
            const total = button.getAttribute('data-total');
            const fecha = button.getAttribute('data-fecha');
            const validez = button.getAttribute('data-validez');
            // Los items vienen como JSON desde PHP
            const items = JSON.parse(button.getAttribute('data-items'));

            document.getElementById('detalleCodigo').textContent = code;
            document.getElementById('detalleFecha').textContent = fecha;
            document.getElementById('detalleValidez').textContent = validez;
            document.getElementById('detalleTotal').textContent = parseFloat(total).toFixed(2);

            const lista = document.getElementById('detalleServicios');
            if (items && items.length > 0) {
                lista.innerHTML = items.map(function(item) {
                    const subtotal = (item.unit_price * item.quantity).toFixed(2);
                    return `
                        <li class="list-group-item">
                            <strong>${item.title}</strong><br>
                            Categoría: ${item.category}<br>
                            Cantidad: ${item.quantity}<br>
                            Precio unitario: $${parseFloat(item.unit_price).toFixed(2)}<br>
                            Subtotal: $${subtotal}
                        </li>
                    `;
                }).join('');
            } else {
                lista.innerHTML = '<li class="list-group-item text-muted">Sin servicios</li>';
            }
        });
    </script>
</body>

</html>