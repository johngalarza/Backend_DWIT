<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'conexion.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data) || !isset($data['id'])) {
        throw new Exception("Datos inválidos");
    }

    // Validate required fields
    $required = ['nombre', 'correo', 'fecha_nacimiento'];
    foreach ($required as $field) {
        if (empty(trim($data[$field] ?? ''))) {
            throw new Exception("Campo requerido: $field");
        }
    }

    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Correo no válido");
    }

    // Check if email exists for other users
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
    $stmt->execute([$data['correo'], $data['id']]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(["error" => "Este correo ya está registrado por otro usuario"]);
        exit;
    }

    // Build update query
    $updates = [];
    $params = [];
    
    // Add basic fields
    $fields = ['nombre', 'correo', 'fecha_nacimiento', 'años_entrenando'];
    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }

    // Add password if provided
    if (!empty($data['contraseña'])) {
        $updates[] = "contraseña = ?";
        $params[] = password_hash($data['contraseña'], PASSWORD_DEFAULT);
    }

    // Add user ID at the end of params
    $params[] = $data['id'];

    // Execute update
    $sql = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute($params);

    if ($success) {
        // Get updated user data
        $stmt = $conn->prepare("SELECT id, nombre, correo, fecha_nacimiento, años_entrenando FROM usuarios WHERE id = ?");
        $stmt->execute([$data['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($user);
    } else {
        throw new Exception("Error al actualizar usuario");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}