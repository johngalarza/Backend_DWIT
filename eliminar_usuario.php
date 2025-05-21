<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception("Método no permitido");
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        throw new Exception("ID de usuario no proporcionado");
    }

    // First delete related records (if any)
    $stmt = $conn->prepare("DELETE FROM medidas WHERE id_usuario = ?");
    $stmt->execute([$data['id']]);

    // Then delete the user
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $success = $stmt->execute([$data['id']]);

    if ($success) {
        http_response_code(200);
        echo json_encode(["message" => "Usuario eliminado correctamente"]);
    } else {
        throw new Exception("Error al eliminar usuario");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
?>