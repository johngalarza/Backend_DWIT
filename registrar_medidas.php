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

$user_id      = $data['user_id'] ?? null;
$altura       = $data['altura'] ?? null;
$peso         = $data['peso'] ?? null;
$brazo_izq    = $data['brazo_izq'] ?? null;
$brazo_der    = $data['brazo_der'] ?? null;
$pierna_izq   = $data['pierna_izq'] ?? null;
$pierna_der   = $data['pierna_der'] ?? null;
$cadera       = $data['cadera'] ?? null;
$pecho        = $data['pecho'] ?? null;

if (
    isset($user_id, $altura, $peso, $brazo_izq, $brazo_der, $pierna_izq, $pierna_der, $cadera, $pecho)
) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO medidas (
                user_id, altura, peso, brazo_izq, brazo_der,
                pierna_izq, pierna_der, cadera, pecho
            ) VALUES (
                :user_id, :altura, :peso, :brazo_izq, :brazo_der,
                :pierna_izq, :pierna_der, :cadera, :pecho
            )
        ");

        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':altura', $altura);
        $stmt->bindParam(':peso', $peso);
        $stmt->bindParam(':brazo_izq', $brazo_izq);
        $stmt->bindParam(':brazo_der', $brazo_der);
        $stmt->bindParam(':pierna_izq', $pierna_izq);
        $stmt->bindParam(':pierna_der', $pierna_der);
        $stmt->bindParam(':cadera', $cadera);
        $stmt->bindParam(':pecho', $pecho);

        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Medidas registradas correctamente."]);
        } else {
            echo json_encode(["error" => "No se pudieron registrar las medidas."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Faltan datos para registrar."]);
}
?>