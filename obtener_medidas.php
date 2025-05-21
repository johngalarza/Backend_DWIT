<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

require_once 'conexion.php';

try {
    // Validar que exista el parámetro user_id
    if (!isset($_GET['user_id'])) {
        echo json_encode(["error" => "Parámetro 'user_id' es requerido"]);
        exit;
    }

    $user_id = $_GET['user_id'];

    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM medidas WHERE user_id = ?");
    $stmt->execute([$user_id]);

    $medidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($medidas);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}