<?php
require_once(__DIR__ . '../../config/conexion.php');
$conn = pdo();

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"];

try {

    $stmt = $conn->prepare("CALL sp_cancelar_venta(:id)");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Venta cancelada correctamente"
    ]);

} catch (PDOException $e) {

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}