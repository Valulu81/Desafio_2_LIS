<?php
//se va encargar de buscar a los usuarios para el logiiing y 
// para insertar a los nuevos en el registro tambien 

require_once __DIR__ . '/../config/database.php';

class User{
    private $db;
    public function __construct(){
        $this->db = Database::getInstance()->getConnection();
    }

    // mi funcion para verificar usuarios
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user=$stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // la funcion para registrarlos 
    public function register($data) {
        if($this->emailExists($data['email'])){
            return ['success' => false, 'message' => 'El correo ya está registrado'];
        }

        $stmt = $this->db->prepare("INSERT INTO users (name, email, company, telephone ,password, role) VALUES (?, ?, ?, ?, ?, 'user')");

        $stmt->execute([
            htmlspecialchars(trim($data['name'])),
            htmlspecialchars(trim($data['email'])),
            htmlspecialchars(trim($data['company'] ??'')),
            htmlspecialchars(trim($data['telephone'] ??'')),
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        return ['success' => true];
    }

    //esta funcion es para obtener los datos del usuario logeado por su id
    public function getById($id){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    //verifica si el email ya esta registrado para evitar datos duplicados 
    private function emailExists($email){
        $stmt=$this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn()>0;
    }
}