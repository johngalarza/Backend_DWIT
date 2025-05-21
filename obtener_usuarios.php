<?php
header("Content-Type: application/json");
require_once 'conexion.php';

try {
    $stmt = $conn->query("SELECT * FROM usuarios"); 
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($usuarios);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
