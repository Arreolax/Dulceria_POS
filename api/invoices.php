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
        f.fecha_emision AS fecha_emision,
        f.id AS id_factura,
        f.folio AS folio_factura,
        f.uuid AS uuid,
        f.estado AS estado,
        f.metodo_pago AS metodo_pago,
        f.forma_pago AS forma_pago,
        f.uso_cfdi AS uso_cfdi,
        f.subtotal AS subtotal,
        f.impuestos AS impuestos,
        f.total AS total,

        t.id AS id_ticket,

        cf.razon_social AS razon_social,
        cf.regimen_fiscal AS regimen_fiscal,

        v.folio AS folio_venta

        FROM facturas f 
        JOIN clientes_facturacion cf ON f.id_cliente = cf.id
        JOIN ticket t ON t.id = f.id_ticket
        JOIN venta v ON t.id_venta = v.id

        ORDER BY f.id DESC;");

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $invoices = [];

        foreach ($rows as $row) {

            $id = $row['id_factura'];

            if (!isset($invoices[$id])) {
                $invoices[$id] = [
                    'fecha_emision' => $row['fecha_emision'],
                    'id_factura' => $id,
                    'folio_factura' => $row['folio_factura'],
                    'uuid' => $row['uuid'],
                    'estado' => $row['estado'],
                    'metodo_pago' => $row['metodo_pago'],
                    'forma_pago' => $row['forma_pago'],
                    'uso_cfdi' => $row['uso_cfdi'],
                    'subtotal' => $row['subtotal'],
                    'impuestos' => $row['impuestos'],
                    'total' => $row['total'],
                    'id_ticket' => $row['id_ticket'],
                    'razon_social' => $row['razon_social'],
                    'regimen_fiscal' => $row['regimen_fiscal'],
                    'folio_venta' => $row['folio_venta']
                ];
            }
        }

        $invoices = array_values($invoices);

        json_response([
            'success' => true,
            'resultado' => [
                'invoices' => $invoices
            ]
        ]);
        break;

    default:
        json_response(["ok" => false, "error" => "Metodo no permitido"], 405);
        break;
}
