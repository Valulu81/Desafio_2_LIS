<?php
$error   = $error   ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Registro</title>
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

    <section class="h-100 gradient-form" style="background-color: #853232;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                   <form method="POST" action="../public/index.php?action=register">
                                        <?php if (!empty($error)): ?>
                                           <div class="alert alert-danger"><?= $error ?></div>
                                        <?php endif; ?>    
                                        <p class="fw-bolder">Por favor registrate aqui: </p>
                                        <p>Nombre</p>
                                        <div data-mdb-input-init class="form-outline mb-1">
                                            <input type="text" name="name" class="form-control" placeholder="Camila Sanchez" required />
                                        </div>
                                        <p>Correo</p>
                                        <div data-mdb-input-init class="form-outline mb-1">
                                            <input type="email" name="email" class="form-control" placeholder="example@example.com" required />
                                        </div>
                                        <p>Empresa</p>
                                        <div data-mdb-input-init class="form-outline mb-1">
                                           <input type="text" name="company" class="form-control" placeholder="Nombre de la empresa" />
                                        </div>
                                        <p>Telefono</p>
                                        <div data-mdb-input-init class="form-outline mb-1">
                                            <input type="text" name="telephone" class="form-control" placeholder="123-456-7890" />
                                        </div>
                                        <p>Contraseña</p>
                                        <div data-mdb-input-init class="form-outline mb-3">
                                            <input type="password" name="password" class="form-control" placeholder="minimo 8 caracteres" minlength="8" required />
                                        </div>
                                        <p>Confirmar Contraseña</p>
                                        <div class="form-outline mb-3">
                                            <input type="password" name="confirm_password" class="form-control" placeholder="misma contra"></div>
                                        <div class="text-center pt-1  pb-1">
                                            <button class="btn btn-danger btn-block fa-lg mb-3 border-0" type="submit">Registrarse</button>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Ya tienes una cuenta?</p>
                                            <a href="auth.php" data-mdb-button-init data-mdb-ripple-init class="btn btn-outline-danger">Inicia sesion</a>
                                        </div>

                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2 rounded-3">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <div class="text-center">
                                        <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/lotus.webp" style="width: 185px;" alt="logo">
                                        <h4 class="mb-4">Somos mas que un servicio</h4>
                                        <p class="small mb-0">Registrate en nuestra pagina web para poder gozar de adquirir nuestros productos y servicios a un precio espectacular! </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="../public/assets/scripts.js"></script>
</body>

</html>