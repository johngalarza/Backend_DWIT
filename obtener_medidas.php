<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");
require_once 'conexion.php';

try {
    $stmt = $conn->query("SELECT * FROM medidas");
    $medidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($medidas);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
