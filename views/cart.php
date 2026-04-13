<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../public/index.php?action=login');
    exit;
}

$usuario = [
    'nombre' => $_SESSION['user_name'] ?? 'Usuario',
    'rol'    => $_SESSION['user_role'] ?? 'user'
];

// Estas variables vienen del CartController
$cart      = $cart      ?? [];
$subtotal  = $subtotal  ?? 0;
$descuento = $descuento ?? 0;
$iva       = $iva       ?? 0;
$total     = $total     ?? 0;
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Carrito - UDB Academy SV</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="../public/index.php?action=services">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../public/index.php?action=quotes">Cotizaciones</a>
                    </li>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../public/index.php?action=admin">Administrar servicios</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-dark" href="../public/index.php?action=cart">
                        <i class="bi-cart-fill me-1"></i> Cart
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
                <h1 class="display-4 fw-bolder">Tu Carrito</h1>
                <p class="lead fw-normal text-white-50 mb-0">Revisa tus servicios antes de cotizar.</p>
            </div>
        </div>
    </header>

    <div class="container my-5">

        <!-- Mensaje de alerta para feedback del AJAX -->
        <div id="alert-msg" class="alert d-none mb-3" role="alert"></div>

        <?php if (empty($cart)): ?>
            <!-- Carrito vacío -->
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted"></i>
                <p class="text-muted mt-3">Tu carrito está vacío.</p>
                <a href="../public/index.php?action=services" class="btn btn-dark">Ver servicios</a>
            </div>

        <?php else: ?>
            <div class="row">
                <!-- Lista de servicios en el carrito -->
                <div class="col-md-8">
                    <h4 class="mb-3">Servicios seleccionados</h4>
                    <div id="cart-items">
                        <?php foreach ($cart as $item): ?>
                            <!-- Cada fila tiene un id único para poder eliminarla con JS -->
                            <div class="card mb-3" id="fila-<?= $item['id'] ?>">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <h6 class="fw-bold"><?= htmlspecialchars($item['title']) ?></h6>
                                            <small class="text-muted">$<?= number_format($item['price'], 2) ?> por unidad</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Botón menos: llama a actualizarCantidad() del cart.js -->
                                                <button class="btn btn-outline-dark btn-sm"
                                                    onclick="actualizarCantidad(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                                    -
                                                </button>
                                                <!-- Muestra la cantidad actual, se actualiza con JS -->
                                                <span id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                                                <!-- Botón más -->
                                                <button class="btn btn-outline-dark btn-sm"
                                                    onclick="actualizarCantidad(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <!-- Precio total de este item (precio * cantidad) -->
                                            <span id="precio-<?= $item['id'] ?>">
                                                $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                            </span>
                                        </div>
                                        <div class="col-md-1">
                                            <!-- Botón eliminar: llama a eliminarDelCarrito() del cart.js -->
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="eliminarDelCarrito(<?= $item['id'] ?>)">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Resumen de totales y formulario de cotización -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="fw-bold">Resumen</h5>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Descuento</span>
                                <span id="descuento">$<?= number_format($descuento, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>IVA (13%)</span>
                                <span id="iva">$<?= number_format($iva, 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span id="total">$<?= number_format($total, 2) ?></span>
                            </div>
                            <hr>
                            <!-- Botón que abre el formulario del cliente -->
                            <button class="btn btn-dark w-100 mb-2"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCotizar">
                                Cotizar
                            </button>
                            <!-- Botón vaciar carrito -->
                            <button class="btn btn-outline-danger w-100" onclick="vaciarCarrito()">Vaciar carrito</button>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal de cotización: formulario con datos del cliente -->
    <div class="modal fade" id="modalCotizar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Datos del cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Mensaje de error dentro del modal -->
                    <div id="modal-error" class="alert alert-danger d-none"></div>

                    <div class="mb-3">
                        <label class="form-label">Nombre completo *</label>
                        <input type="text" id="cotNombre" class="form-control" placeholder="Juan Pérez" required />
                        <div class="invalid-feedback">El nombre es obligatorio</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Empresa *</label>
                        <input type="text" id="cotEmpresa" class="form-control" placeholder="Mi Empresa S.A." required />
                        <div class="invalid-feedback">La empresa es obligatoria</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico *</label>
                        <input type="email" id="cotEmail" class="form-control" placeholder="correo@empresa.com" required />
                        <div class="invalid-feedback">Ingresa un correo válido</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono *</label>
                        <input type="text" id="cotTelefono" class="form-control" placeholder="7777-8888" required />
                        <div class="invalid-feedback">El teléfono es obligatorio</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" onclick="generarCotizacion()">
                        Generar cotización
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación: aparece cuando la cotización se generó exitosamente -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">¡Cotización generada!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Código:</strong> <span id="confCodigo"></span></p>
                    <p><strong>Subtotal:</strong> $<span id="confSubtotal"></span></p>
                    <p><strong>Descuento:</strong> $<span id="confDescuento"></span></p>
                    <p><strong>IVA:</strong> $<span id="confIva"></span></p>
                    <p><strong>Total:</strong> $<span id="confTotal"></span></p>
                </div>
                <div class="modal-footer">
                    <a href="../public/index.php?action=quotes" class="btn btn-dark">
                        Ver mis cotizaciones
                    </a>
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
    <script src="../public/cart.js"></script>
    <script>
        // ── VACIAR CARRITO ────────────────────────────────────────────────
        // Elimina todos los items del carrito de una vez
        function vaciarCarrito() {
            if (!confirm('¿Vaciar todo el carrito?')) return;

            const filas = document.querySelectorAll('[id^="fila-"]');
            const ids = [...filas].map(f => f.id.replace('fila-', ''));

            if (ids.length === 0) return;

            Promise.all(
                    ids.map(id =>
                        fetch('../public/remove-from-cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'id=' + id
                        }).then(r => r.json())
                    )
                ).then(() => location.reload())
                .catch(() => location.reload());
        }

        // ── GENERAR COTIZACIÓN ────────────────────────────────────────────
        // Valida los campos del formulario y manda los datos al endpoint
        function generarCotizacion() {
            const nombre = document.getElementById('cotNombre').value.trim();
            const empresa = document.getElementById('cotEmpresa').value.trim();
            const email = document.getElementById('cotEmail').value.trim();
            const telefono = document.getElementById('cotTelefono').value.trim();

            // Validación frontend: verificamos que no haya campos vacíos
            if (!nombre || !empresa || !email || !telefono) {
                document.getElementById('modal-error').textContent = 'Todos los campos son obligatorios';
                document.getElementById('modal-error').classList.remove('d-none');
                return; // Detenemos si hay error
            }

            // Validación del email con expresión regular
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('modal-error').textContent = 'El correo no tiene un formato válido';
                document.getElementById('modal-error').classList.remove('d-none');
                return;
            }

            // Ocultamos el error si todo está bien
            document.getElementById('modal-error').classList.add('d-none');

            // Mandamos los datos al endpoint AJAX
            fetch('../public/process-quote.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'nombre=' + encodeURIComponent(nombre) +
                        '&empresa=' + encodeURIComponent(empresa) +
                        '&email=' + encodeURIComponent(email) +
                        '&telefono=' + encodeURIComponent(telefono)
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        // Cerramos el modal del formulario
                        bootstrap.Modal.getInstance(
                            document.getElementById('modalCotizar')
                        ).hide();

                        // Llenamos el modal de confirmación con los datos
                        document.getElementById('confCodigo').textContent = data.codigo;
                        document.getElementById('confSubtotal').textContent = data.subtotal;
                        document.getElementById('confDescuento').textContent = data.descuento;
                        document.getElementById('confIva').textContent = data.iva;
                        document.getElementById('confTotal').textContent = data.total;

                        // Abrimos el modal de confirmación
                        new bootstrap.Modal(
                            document.getElementById('modalConfirmacion')
                        ).show();

                        // Actualizamos el badge del carrito a 0
                        document.getElementById('cart-count').textContent = '0';

                    } else {
                        // Si el backend devolvió error lo mostramos en el modal
                        document.getElementById('modal-error').textContent = data.message;
                        document.getElementById('modal-error').classList.remove('d-none');
                    }
                })
                .catch(function() {
                    document.getElementById('modal-error').textContent = 'Error al generar la cotización';
                    document.getElementById('modal-error').classList.remove('d-none');
                });
        }
    </script>
</body>

</html>