<?php
require_once 'config/database.php';
require_once 'models/User.php';

$user = new User();

$resultado = $user->login('admin@academy_sv.com', 'password1234');

echo "<pre>";
print_r($resultado);
echo "</pre>";
?>