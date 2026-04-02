<?php
require_once(__DIR__ . '../../config/conexion.php');

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "mensaje" => "Usuario no autenticado"
    ]);
    exit;
}

$conn = pdo();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $conn->prepare("SELECT
        v.id AS id_venta,
        t.id AS id_ticket,
        DATE_FORMAT(v.created_at, '%d/%m/%Y %H:%i:%s') AS fecha_venta,
        v.folio,
        v.metodo_pago,
        u.nombre AS nombre_cliente,

        p.nombre AS producto,
        td.cantidad,
        td.subtotal,

        v.total AS total_venta

        FROM ticket t 
        LEFT JOIN facturas f ON t.id = f.id_ticket
        JOIN venta v ON t.id_venta = v.id
        JOIN usuarios u ON v.id_usuario = u.id
        JOIN ticket_detalle td ON td.id_ticket = t.id
        JOIN productos p ON p.id = td.id_producto

        WHERE f.id_ticket IS NULL

        ORDER BY t.id DESC;");

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


        $tickets = [];

        foreach ($rows as $row) {

            $id = $row['id_ticket'];

            if (!isset($tickets[$id])) {
                $tickets[$id] = [
                    'id_ticket' => $id,
                    'id_venta' => $row['id_venta'],
                    'fecha_venta' => $row['fecha_venta'],
                    'folio' => $row['folio'],
                    'metodo_pago' => $row['metodo_pago'],
                    'nombre_cliente' => $row['nombre_cliente'],
                    'total_venta' => $row['total_venta'],
                    'productos' => []
                ];
            }

            $tickets[$id]['productos'][] = [
                'producto' => $row['producto'],
                'cantidad' => (int)$row['cantidad'],
                'subtotal' => (float)$row['subtotal']
            ];
        }

        $tickets = array_values($tickets);

        json_response([
            'success' => true,
            'resultado' => [
                'tickets' => $tickets
            ]
        ]);
        break;

    default:
        json_response(["ok" => false, "error" => "Metodo no permitido"], 405);
        break;
}
