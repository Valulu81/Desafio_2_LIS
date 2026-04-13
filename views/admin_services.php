<?php
$usuario = [
    'nombre' => $_SESSION['user_name'] ?? 'Usuario',
    'rol'    => $_SESSION['user_role'] ?? 'user'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Administrar Servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link" href="../public/index.php?action=services">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/index.php?action=quotes">Cotizaciones</a></li>
                    <?php if ($usuario['rol'] === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="../public/index.php?action=admin">Administrar servicios</a></li>
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

    <!-- header -->
    <header class="background  py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Administrar Servicios</h1>
                <p class="lead fw-normal text-white-50 mb-0">Crea, edita o elimina servicios.</p>
            </div>
        </div>
    </header>
    

    <!-- botón crear -->
    <div class="text-center my-4">
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalCrearServicio">
            <i class="bi bi-plus-circle me-1"></i> Crear nuevo servicio
        </button>
    </div>

    <!-- listado -->
    <section class="pb-5">
        <div class="container px-4 px-lg-5 mt-3">
            <div id="mensajeExito" class="alert alert-success d-none"></div>
            <div id="mensajeError" class="alert alert-danger  d-none"></div>

            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php foreach ($servicios as $servicio): ?>
                    <div class="col mb-5" id="card-<?= $servicio['id'] ?>">
                        <div class="card h-100">
                            <img class="card-img-top" src="<?= htmlspecialchars($servicio['image_url']) ?>" alt="<?= htmlspecialchars($servicio['title']) ?>" />
                            <div class="card-body p-4 text-center">
                                <h5 class="fw-bolder"><?= htmlspecialchars($servicio['title']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($servicio['category']) ?></p>
                                <p>$<?= number_format($servicio['price'], 2) ?></p>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-outline-dark btn-editar"
                                        data-id="<?= $servicio['id'] ?>"
                                        data-title="<?= htmlspecialchars($servicio['title']) ?>"
                                        data-category="<?= htmlspecialchars($servicio['category']) ?>"
                                        data-price="<?= $servicio['price'] ?>"
                                        data-image="<?= htmlspecialchars($servicio['image_url']) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarServicio">
                                        Editar
                                    </button>
                                    <button class="btn btn-outline-danger btn-borrar"
                                        data-id="<?= $servicio['id'] ?>">
                                        Borrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- modal crear -->
    <div class="modal fade" id="modalCrearServicio" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear nuevo servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="crearTitle" class="form-control" placeholder="Nombre del servicio">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select id="crearCategory" class="form-select">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" id="crearPrice" class="form-control" placeholder="0.00" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL de imagen</label>
                        <input type="url" id="crearImage" class="form-control" placeholder="https://...">
                    </div>
                    <div id="crearError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" id="btnCrear">Crear servicio</button>
                </div>
            </div>
        </div>
    </div>

    <!-- modal editar -->
    <div class="modal fade" id="modalEditarServicio" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="editarTitle" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select id="editarCategory" class="form-select">
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" id="editarPrice" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL de imagen</label>
                        <input type="url" id="editarImage" class="form-control">
                    </div>
                    <div id="editarError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" id="btnEditar">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <footer class="py-5 bg-dark">
        <div class="container">
            <p class="m-0 text-center text-white">Valeria Paredes & Andre Preza</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src = "../public/main.js"></script>
</body>

</html>