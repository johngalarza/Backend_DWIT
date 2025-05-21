<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");
require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? '';
$correo = $data['correo'] ?? '';
$contraseña = $data['contraseña'] ?? '';

if ($nombre && $correo && $contraseña) {
    try {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (:nombre, :correo, :pass)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':pass', $contraseña);

        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Usuario insertado con éxito"]);
        } else {
            echo json_encode(["error" => "Error al insertar"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Faltan datos"]);
}
?>
