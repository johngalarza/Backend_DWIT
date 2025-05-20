<?php
header("Content-Type: application/json");
require_once 'conexion.php';

$result = $conn->query("SELECT * FROM user");

$usuarios = [];

while ($fila = $result->fetch_assoc()) {
    $usuarios[] = $fila;
}

echo json_encode($usuarios);

$conn->close();
?>
