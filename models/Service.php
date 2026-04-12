<?php
require_once __DIR__ . '/../config/database.php';

class Service {
    const CATEGORIAS_VALIDAS = [
        'Servicios Tecnológicos',
        'Marketing y Diseño', // Corregido: decía "Maketing" en tu código
        'Servicios Generales'
    ];
    const PRECIO_MIN = 10;
    const PRECIO_MAX = 1000;

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function validarDatos($data) {
        $errores = [];
        if (empty($data['title'])) {
            $errores[] = 'El nombre es obligatorio';
        }
        if (empty($data['category']) || !in_array($data['category'], self::CATEGORIAS_VALIDAS)) {
            $errores[] = 'La categoría no es válida';
        }
        if (!isset($data['price']) || !is_numeric($data['price'])) {
            $errores[] = 'El precio debe ser de tipo numérico';
        } elseif ($data['price'] < self::PRECIO_MIN || $data['price'] > self::PRECIO_MAX) {
            $errores[] = 'El precio debe estar entre $' . self::PRECIO_MIN . ' y $' . self::PRECIO_MAX;
        }
        if (empty($data['image_url'])) {
            $errores[] = 'La URL de la imagen es obligatoria';
        }
        return $errores;
    }

    public function getAll() {
        // CORRECCIÓN: La tabla se llama 'services'
        $stmt = $this->db->prepare("SELECT * FROM services ORDER BY category, title");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        // CORRECCIÓN: La tabla se llama 'services'
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            return ['success' => false, 'errors' => $errores];
        }
        $sql = "INSERT INTO services (title, category, price, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
            $stmt->execute([
                htmlspecialchars(trim($data['title'])),
                $data['category'],
                (float)$data['price'],
                htmlspecialchars(trim($data['image_url']))
            ]);
            return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    public function update($id, $data) {
        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            return ['success' => false, 'errors' => $errores];
        }

        $sql = "UPDATE services SET title=?, category=?, price=?, image_url=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            htmlspecialchars(trim($data['title'])),
            $data['category'],
            (float)$data['price'],
            htmlspecialchars(trim($data['image_url'])),
            $id
        ]);
        return ['success' => true];
    }
    
    public function delete($id) {
        // Verificar si está en uso en quote_services (llave foránea)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM quote_services WHERE service_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar: el servicio está en cotizaciones existentes'];
        }

        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }
}