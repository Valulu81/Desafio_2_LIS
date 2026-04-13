<?php
session_start();
$usuario = [
    'nombre' => $_SESSION['user_name'] ?? 'Usuario',
    'rol'    => $_SESSION['user_role'] ?? 'user'
];
$cart = [];
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
                <h1 class="display-4 fw-bolder">Confirma tu compra!</h1>
                <p class="lead fw-normal text-white-50 mb-0">Entre mas compres tendras mas recompensas.</p>
            </div>
        </div>
    </header>
    <!-- Section-->
    <div id="cart-panel">
        <div class="card m-0 auto">
            <div class="row">
                <div class="col-md-8 cart">
                    <div class="title">
                        <div class="row">
                            <div class="col">
                                <h4><b>Shopping Cart</b></h4>
                            </div>
                            <div class="col align-self-center text-right text-muted"><?php echo count($_SESSION['cart']); ?> items</div>
                        </div>
                    </div>
                    <!-- items -->
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="row border-top border-bottom">
                            <div class="row main align-items-center">
                                <div class="col-2">
                                    <img class="img-fluid" src="<?= $item['image_url'] ?? '' ?>" alt="<?= $item['title'] ?? '' ?>">
                                </div>
                                <div class="col">
                                    <div class="row text-muted"><?= $item['title'] ?? '' ?></div>
                                </div>
                                <div class="col">Cantidad: <?= $item['quantity'] ?? 1 ?></div>
                                <div class="col">&dollar;<?= $item['price'] ?? '0.00' ?><span class="close">&#10005;</span></div>
                            </div>
                        </div>
                    <?php endforeach; ?>


                    <!-- parte del pago -->
                </div>
                <div class="col-md-4 summary">
                    <div>
                        <h5><b>Summary</b></h5>
                    </div>
                    <hr>
                    <?php
                    require_once __DIR__ . '/../controllers/CartController.php';
                    $controller = new CartController();
                    $controller->summary();
                    ?>
                    <button class="btn fs-6">Cotizar</button>
                    <button class="btn mt-2 bg-danger fs-6 text">Vaciar Carrito</button>
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
    <script src="../public/main.js"></script>
</body>

</html>