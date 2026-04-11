<?php
require_once 'config/database.php';
require_once 'models/Service.php';

$service = new Service();
$todos = $service->getAll();

echo "<pre>";
print_r($todos);
echo "</pre>";
?>