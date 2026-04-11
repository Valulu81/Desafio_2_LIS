<?php
session_start();

// Simulamos sesión activa para que no nos bote al login
$_SESSION['user_id']   = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_name'] = 'Test';

require_once 'controllers/ServiceController.php';

$controller = new ServiceController();
$controller->index();
?>