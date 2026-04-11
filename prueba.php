<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "si sirve";
} catch (Exception $e) {
    echo "no sirve";
    echo "Error: " . $e->getMessage();
}
?>