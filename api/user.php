<?php
require_once(__DIR__ . '../../config/conexion.php');

$conn = pdo();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $conn->prepare("SELECT * FROM usuarios");
        $stmt->execute();
        $users = $stmt->fetchAll();
        json_response($users);
        break;

    default:
        http_response_code(405);    // Maper el codigo si no se escuentra el metodo
        echo json_encode(['error' => 'Metodo Invalido']);
}
?>