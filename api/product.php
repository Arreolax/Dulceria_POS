<?php
require_once(__DIR__ . '../../config/conexion.php');

$conn = pdo();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $conn->prepare("SELECT p.id, p.sku, p.nombre, p.descripcion, p.precio, p.stock, p.id_categoria, c.nombre AS categoria, p.activo AS estado, p.imagen
       FROM productos p
       JOIN categorias c ON p.id_categoria = c.id
       WHERE activo = 1");
        $stmt->execute();
        $productos = $stmt->fetchAll();

        json_response([
            'success' => true,
            'resultado' => [
                'products' => $productos
            ]
        ]);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $sku = 'SKU-' . strtoupper(bin2hex(random_bytes(4))); 
        $nombre = trim($data["nombre"] ?? "");
        $descripcion = trim($data["descripcion"] ?? "");
        $precio = $data["precio"] ?? 0;
        $stock = $data["stock"] ?? 0;
        $id_categoria = $data["id_categoria"] ?? null;
        $activo = $data["activo"] ?? 1;

        // Validación básica
        if ( !$nombre || !$precio || !$id_categoria) {
            http_response_code(400);
            echo json_encode(["error" => "Datos incompletos"]);
            exit;
        }

        try {
            $stmt = $conn->prepare("
            INSERT INTO productos 
            (sku, nombre, descripcion, precio, stock, id_categoria, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

            $stmt->execute([
                $sku,
                $nombre,
                $descripcion,
                $precio,
                $stock,
                $id_categoria,
                $activo
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Producto agregado correctamente"
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "error" => "Error al insertar producto",
                "detalle" => $e->getMessage()
            ]);
        }
        break;

    default:
        http_response_code(405);    // Maper el codigo si no se escuentra el metodo
        echo json_encode(['error' => 'Metodo Invalido']);
}
