<?php
session_start();
//$usuario = $_SESSION['usuario']; 
$usuario = [
    'nombre' => 'Valeria Paredes',
    'email' => 'valeria.paredes@example.com',
    'rol' => 'admin'
];
//$quotes = $_SESSION['quotes'] ?? [];
$quotes = [
    [
        'codigo' => 'COT-001',
        'cliente' => [
            'nombre' => 'Empresa ABC',
            'empresa' => 'ABC S.A.',
            'email' => 'empresa.abc@example.com',
            'telefono' => '555-1234'
        ],
        'fecha' => '2024-06-01',
        'validez' => '2024-06-30',
        'total' => 1500.00,
        'cantidadServicios' => 3,
        'servicios' => [
            ['nombre' => 'Servicio 1', 'precio' => 500.00],
            ['nombre' => 'Servicio 2', 'precio' => 700.00],
            ['nombre' => 'Servicio 3', 'precio' => 300.00]
        ]
    ]

];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Carrito</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- styles.css -->
    <link href="../public/styles.css" rel="stylesheet" />
</head>

<body>
    <!-- navegador-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container px-4 px-lg-5">
            <a class="navbar-brand" href="#!">UDB Academy sv</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="../public/index.php?action=services">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../views/quotes.php">Cotizaciones</a></li>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link active" href="../public/index.php?action=admin">Administrar servicios</a></li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex">
                    <a class="btn btn-outline-dark me-2" href="../views/cart.php">
                        <i class="bi-cart-fill me-1"></i>
                        Cart
                    </a>
                    <a class="btn btn-outline-danger" href="../public/index.php?action=auth">
                        <i class="bi bi-x-circle-fill"></i>
                        salir
                    </a>
                </form>
            </div>
        </div>
    </nav>
    <!-- Header-->
    <header class="background py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Revisa tus cotizaciones</h1>
                <p class="lead fw-normal text-white-50 mb-0">Encuentra todas tus cotizaciones aquí.</p>
            </div>
        </div>
    </header>
    <!-- Section-->
    <!-- Tabla en desktop -->
    <div class="d-none d-md-block">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Servicios</th>
                    <th>Ver mas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $q): ?>
                    <tr>
                        <td><?= $q['codigo'] ?></td>
                        <td><?= $q['cliente']['nombre'] ?></td>
                        <td><?= $q['fecha'] ?></td>
                        <td>$<?= number_format($q['total'], 2) ?></td>
                        <td><?= $q['cantidadServicios'] ?></td>
                        <td><button class="btn btn-dark btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#detalleModal"
                                data-quote='<?= htmlspecialchars(json_encode($q), ENT_QUOTES, 'UTF-8') ?>'>
                                Ver detalle
                            </button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Cards en mobile -->
    <div class="d-md-none">
        <?php foreach ($quotes as $q): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= $q['codigo'] ?></h5>
                    <p><strong>Cliente:</strong> <?= $q['cliente']['nombre'] ?></p>
                    <p><strong>Fecha:</strong> <?= $q['fecha'] ?></p>
                    <p><strong>Total:</strong> $<?= number_format($q['total'], 2) ?></p>
                    <p><strong>Servicios:</strong> <?= $q['cantidadServicios'] ?></p>
                    <button class="btn btn-dark btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#detalleModal"
                        data-quote='<?= htmlspecialchars(json_encode($q), ENT_QUOTES, 'UTF-8') ?>'>
                        Ver detalle
                    </button>
                </div>
            </div>

        <?php endforeach; ?>

    </div>

    <!-- Modal para ver mas -->
    <div class="modal fade" id="detalleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de Cotización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Código:</strong> <span id="detalleCodigo"></span></p>
                    <p><strong>Cliente:</strong> <span id="detalleCliente"></span></p>
                    <p><strong>Email:</strong> <span id="detalleEmail"></span></p>
                    <p><strong>Teléfono:</strong> <span id="detalleTelefono"></span></p>
                    <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                    <p><strong>Validez:</strong> <span id="detalleValidez"></span></p>

                    <h6>Servicios:</h6>
                    <ul id="detalleServicios" class="list-group mb-3"></ul>

                    <div class="text-end">
                        <strong>Total: $<span id="detalleTotal"></span></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer-->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Valeria Paredes & Andre Preza</p>
        </div>
    </footer>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="../public/assets/scripts.js"></script>
    <script>
        document.getElementById('detalleModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const quote = JSON.parse(button.getAttribute('data-quote'));

            document.getElementById('detalleCodigo').textContent = quote.codigo;
            document.getElementById('detalleCliente').textContent = quote.cliente.nombre + " (" + quote.cliente.empresa + ")";
            document.getElementById('detalleEmail').textContent = quote.cliente.email;
            document.getElementById('detalleTelefono').textContent = quote.cliente.telefono;
            document.getElementById('detalleFecha').textContent = quote.fecha;
            document.getElementById('detalleValidez').textContent = quote.validez;
            document.getElementById('detalleTotal').textContent = quote.total.toFixed(2);

            const serviciosList = document.getElementById('detalleServicios');
            serviciosList.innerHTML = quote.items.map(p => {
                const cantidad = p.cantidad ?? 1;
                const subtotal = (p.precio * cantidad).toFixed(2);
                return `
                    <li class="list-group-item">
                        <strong>${p.nombre}</strong><br>
                        Cantidad: ${cantidad}<br>
                        Precio unitario: $${p.precio}<br>
                        Subtotal: $${subtotal}
                    </li>
                `;
            }).join('');
        });
    </script>
</body>

</html>