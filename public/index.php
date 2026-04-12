<?php
session_start();

require_once '../config/database.php';
require_once '../controllers/AuthController.php';
require_once '../controllers/ServiceController.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'services':
        $controller = new ServiceController();
        $controller->index();
        break;

    case 'admin':
        $controller = new ServiceController();
        $controller->adminIndex();
        break;

    default:
        $controller = new AuthController();
        $controller->login();
        break;
}
?>