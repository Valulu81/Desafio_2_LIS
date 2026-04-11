<?php
session_start();
//$usuario = $_SESSION['usuario']; 
$usuario = [
    'nombre' => 'Valeria Paredes',
    'email' => 'valeria.paredes@example.com',
    'rol' => 'admin'
]
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Shop Homepage - Start Bootstrap Template</title>
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="services.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="quotes.php">Cotizaciones</a></li>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="admin_services.php">Administrar servicios</a></li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex">
                    <a class="btn btn-outline-dark me-2" href="cart.php">
                        <i class="bi-cart-fill me-1"></i>
                        Cart
                        <span class="badge bg-dark text-white ms-1 rounded-pill">0</span>
                    </a>
                    <a class="btn btn-outline-danger" href="auth.php">
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
                <h1 class="display-4 fw-bolder">Compra lo que quieras!</h1>
                <p class="lead fw-normal text-white-50 mb-0">Encuentra los mejores servicios, justo lo que necesitas.</p>
            </div>
        </div>
    </header>
    <!-- Section-->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Product image-->
                        <img class="card-img-top" src="https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775883765/diseno-web-corporativa-medida_khpqk8.webp" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">Nombre Servicio</h5>
                                <!-- product category -->
                                <p class="text-muted">Categoría del servicio</p>
                                <!-- Product price-->
                                $40.00
                            </div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                        </div>
                    </div>
                </div>
                <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Product image-->
                        <img class="card-img-top" src="https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884544/mantenimiento-de-servidores_evfmrz.jpg" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">Nombre Servicio</h5>
                                <!-- product category -->
                                <p class="text-muted">Categoría del servicio</p>
                                <!-- Product price-->
                                $40.00
                            </div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                        </div>
                    </div>
                </div>
                <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Product image-->
                        <img class="card-img-top" src="https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775884595/mejorar-una-red-wifi-empresarial-1-1200x675_oilx3e.webp" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">Nombre Servicio</h5>
                                <!-- product category -->
                                <p class="text-muted">Categoría del servicio</p>
                                <!-- Product price-->
                                $40.00
                            </div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                        </div>
                    </div>
                </div>
                <div class="col mb-5">
                    <div class="card h-100">
                        <!-- Product image-->
                        <img class="card-img-top" src="https://res.cloudinary.com/dhotqeo6c/image/upload/q_auto/f_auto/v1775885449/software-engineers-working-on-project-and-programm-bducjhl_redimensionar_vmle2t.jpg" alt="..." />
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name-->
                                <h5 class="fw-bolder">Nombre Servicio</h5>
                                <!-- product category -->
                                <p class="text-muted">Categoría del servicio</p>
                                <!-- Product price-->
                                $40.00
                            </div>
                        </div>
                        <!-- Product actions-->
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center"><a class="btn btn-outline-dark mt-auto" href="#">View options</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
</body>

</html>