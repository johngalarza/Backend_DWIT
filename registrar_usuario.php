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

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "JSON inválido"]);
    exit();
}

$nombre = trim($data['nombre'] ?? '');
$correo = trim($data['correo'] ?? '');
$contraseña = trim($data['contraseña'] ?? '');
$fecha_nacimiento = trim($data['fecha_nacimiento'] ??'');
$años_entrenando = trim($data['años_entrenando'] ??'');

if (empty($nombre) || empty($correo) || empty($contraseña) || empty($fecha_nacimiento)) {
    http_response_code(422); 
    echo json_encode(["error" => "Faltan datos requeridos"]);
    exit();
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); 
    echo json_encode(["error" => "Correo no válido"]);
    exit();
}

try {
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM usuarios where correo = :correo");
    $checkStmt->bindParam(":correo", $correo);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        http_response_code(409);
        echo json_encode(["error"=> "Este correo ya fue registrado"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña, fecha_nacimiento, años_entrenando) VALUES (:nombre, :correo, :pass, :fecha_nacimiento, :years_training)");
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':pass', $contraseña); 
    $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento); 
    $stmt->bindParam(':years_training', $años_entrenando); 

    if ($stmt->execute()) {
        $id = $conn->lastInsertId();
        http_response_code(201);
        echo json_encode([
            'id'=> $id,
            "nombre" => $nombre,
            "correo"=> $correo,
            'fecha_nacimiento' => $fecha_nacimiento,
            'años_entrenando' => $años_entrenando
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo insertar el usuario"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor", "detalle" => $e->getMessage()]);
}
?>
