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

$correo = trim($data['correo'] ?? '');
$contraseña = trim($data['contraseña'] ?? '');

if (empty($correo) || empty($contraseña)) {
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
    $stmt = $conn->prepare("SELECT * FROM usuarios where correo = :correo AND contraseña = :pass");
    $stmt->bindParam(":correo", $correo);
    $stmt->bindParam(":pass", $contraseña);

    if(!($stmt->execute())){
        http_response_code(500);
        echo json_encode(["error" => "No se pudo insertar el usuario"]);
        exit();
    }

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        http_response_code(200);
        echo json_encode([
            'id'=> $resultado["id"],
            "nombre" => $resultado["nombre"],
            "correo"=> $resultado["correo"],
        ]);
    }else{
        http_response_code(409);
        echo json_encode(["error"=> "Este correo no ha sido registrado"]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor", "detalle" => $e->getMessage()]);
}