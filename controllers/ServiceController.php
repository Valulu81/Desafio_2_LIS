<?php
require_once __DIR__ . '/../models/Service.php';
if (session_status() === PHP_SESSION_NONE) session_start();

class ServiceController
{
    private $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
    }
    /*Metodo por el cual vamos a cargar la pagina principal
    con los servicios pero antes verificamos el log */
    public function index()
    {
        $this->requireLogin();
        $servicios = $this->serviceModel->getAll();
        $categorias = Service::CATEGORIAS_VALIDAS;

        //cargas las vistas de servicios y categorias 
        require_once __DIR__ . '/../views/services.php';
    }
    /* Carga la vista del admin */
    public function adminIndex()
    {
        $this->requireLogin();
        $servicios = $this->serviceModel->getAll();
        $categorias = Service::CATEGORIAS_VALIDAS;
        require_once __DIR__ . '/../views/admin_services.php';
    }

    /*Este es el AJA de crear un sericio */
    public function ajaxCreate()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        //Se recogen los datos que envio el formulario por metodo post y luego los devulve json
        $data = [
            'title' => $_POST['title'] ?? '',
            'category' => $_POST['category'] ?? '',
            'price' => $_POST['price'] ?? '',
            'image_url' => $_POST['image_url'] ?? ''
        ];
        //se guardan aca y luego se envian
        $resultado = $this->serviceModel->create($data);
        echo json_encode($resultado);
        exit;
    }

    /*Ajax para actaulizar un servicio */
    public function ajaxUpdate()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        //aqui se usa para saber cual servicio apartir del id
        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'title' => $_POST['title'] ?? '',
            'category' => $_POST['category'] ?? '',
            'price' => $_POST['price'] ?? '',
            'image_url' => $_POST['image_url'] ?? ''
        ];

        $resultado = $this->serviceModel->update($id, $data);
        echo json_encode($resultado);
        exit;
    }

    /*para eliminar un servicio */
    public function ajaxDelete()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $id = (int)($_POST['id'] ?? 0);
        $resultado = $this->serviceModel->delete($id);
        echo json_encode($resultado);
        exit;
    }

    public function ajaxGetById()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);
        $servicio = $this->serviceModel->getById($id);
        if ($servicio) {
            echo json_encode(['success' => true, 'data' => $servicio]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se encontro el servicio']);
        }
        exit;
    }

    /*Esto es lo que verifica el logeo */
    private function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    private function requireAdmin()
    {
        $this->requireLogin();
        if ($_SESSION['user_role'] != 'admin') {
            http_response_code(403);
            die(json_encode(['success' => false, 'message' => 'Acceso denegado']));
        }
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $controller = new ServiceController();
    $action = $_GET['action'] ?? '';

    match ($action) {
        'create'  => $controller->ajaxCreate(),
        'update'  => $controller->ajaxUpdate(),
        'delete'  => $controller->ajaxDelete(),
        'getById' => $controller->ajaxGetById(),
        default   => http_response_code(404)
    };
}
