<?php
require_once(__DIR__ . '../../config/conexion.php');

$conn = pdo();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $conn->prepare("SELECT c.id, c.nombre, c.descripcion, c.imagen,
        COUNT(p.id) AS cantidad
        FROM categorias  c
        LEFT JOIN productos p ON c.id = p.id_categoria
        GROUP BY c.id, c.nombre, c.descripcion
        ORDER BY cantidad DESC");
        $stmt->execute();
        $categorias = $stmt->fetchAll();

        json_response([
            'success' => true,
            'resultado' => [
                'categorias' => $categorias
            ]
        ]);
        break;

    default:
        http_response_code(405);    // Maper el codigo si no se escuentra el metodo
        echo json_encode(['error' => 'Metodo Invalido']);
}
