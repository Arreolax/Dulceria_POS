<?php

declare(strict_types=1);

$BASE = dirname(__DIR__, 2);
// Rutas robustas para config y utils
$CFG = is_file($BASE . '/config/conexion.php') ? ($BASE . '/config/conexion.php') : ($BASE . '/backend/config/db.php');
$FCT = is_file($BASE . '/config/facturama.php') ? ($BASE . '/config/facturama.php') : ($BASE . '/backend/config/facturama.php');

require_once $CFG;
if (is_file($FCT)) require_once $FCT;

/**
 * Funciones Auxiliares
 */
function getTicketId(PDO $conn, int $ticketId, int $folioIn): int
{
    if ($ticketId > 0) {
        $q = $conn->prepare('SELECT id FROM ticket WHERE id = ? LIMIT 1');
        $q->execute([$ticketId]);
        if ($q->fetchColumn()) return $ticketId;

        $ticketFromFolio = getTicketIdByFolio($conn, $ticketId);
        if ($ticketFromFolio > 0) return $ticketFromFolio;
    }

    if ($folioIn > 0) {
        $ticketFromFolio = getTicketIdByFolio($conn, $folioIn);
        if ($ticketFromFolio > 0) return $ticketFromFolio;
    }
    return 0;
}

function getTicketIdByFolio(PDO $conn, int $folio): int
{
    $q = $conn->prepare('
        SELECT t.id 
        FROM ticket t
        JOIN venta v ON t.id_venta = v.id 
        WHERE v.folio = ? 
        LIMIT 1
    ');
    $q->execute([$folio]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : 0;
}

/**
 * Proceso Principal
 */
try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        json_error('Método no permitido', 405);
    }

    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $ticketIdRaw = (int)($body['id_ticket'] ?? 0);
    $folioIn     = (int)($body['folio'] ?? 0);
    $clienteId   = (int)($body['id_cliente'] ?? 0);

    if (($ticketIdRaw <= 0 && $folioIn <= 0) || $clienteId <= 0) {
        json_error('Se requiere id_ticket o folio, y id_cliente', 422, ['body' => $body]);
    }

    $conn = DB::get();
    $conn->beginTransaction();

    // 1. Resolver Ticket
    $ticketId = getTicketId($conn, $ticketIdRaw, $folioIn);
    if ($ticketId <= 0) {
        $conn->rollBack();
        json_error('Ticket no encontrado', 404, ['id_ticket' => $ticketIdRaw, 'folio' => $folioIn]);
    }

    // 2. Idempotencia (Evitar duplicados)
    $q = $conn->prepare('SELECT id FROM facturas WHERE id_ticket = ? LIMIT 1');
    $q->execute([$ticketId]);
    $existingFacturaId = (int)($q->fetchColumn() ?: 0);

    if ($existingFacturaId > 0) {
        $f  = $conn->prepare('SELECT * FROM vista_facturas WHERE id_factura = ?');
        $fd = $conn->prepare('SELECT * FROM vista_factura_detalles WHERE id_factura = ?');
        $f->execute([$existingFacturaId]);
        $fd->execute([$existingFacturaId]);
        $conn->commit();
        json_response([
            'factura' => $f->fetch(PDO::FETCH_ASSOC),
            'detalles' => $fd->fetchAll(PDO::FETCH_ASSOC),
            'idempotent' => true
        ]);
    }

    // 3. Obtener Datos del Ticket y Detalles
    $t = $conn->prepare('SELECT t.id, v.folio, t.total FROM ticket t JOIN venta v ON t.id_venta = v.id WHERE t.id = ?');
    $t->execute([$ticketId]);
    $ticket = $t->fetch(PDO::FETCH_ASSOC);

    $d = $conn->prepare('SELECT td.id, td.id_producto, td.cantidad, td.precio_unitario, p.nombre AS producto 
                         FROM ticket_detalle td LEFT JOIN productos p ON p.id = td.id_producto WHERE td.id_ticket = ?');
    $d->execute([$ticketId]);
    $detalles = $d->fetchAll(PDO::FETCH_ASSOC);

    // --- DESGLOSE DE IVA 16% (PRECIO YA INCLUYE IVA) ---
    $totalFactura = 0.0;
    $subtotalFactura = 0.0;
    $ivaRate = 0.16;

    foreach ($detalles as $row) {
        $precioConIva = (float)$row['precio_unitario'];
        $cantidad = (int)$row['cantidad'];
        $importeConIva = $precioConIva * $cantidad;

        $totalFactura += $importeConIva;
        $subtotalFactura += $importeConIva / (1 + $ivaRate);
    }
    $impuestosFactura = $totalFactura - $subtotalFactura;

    // 4. Pre-insertar Factura Local
    $folioFactura = 'F-' . $ticket['folio'];
    $uuidProvisional = bin2hex(random_bytes(8));
    $insF = $conn->prepare('INSERT INTO facturas (id_ticket, id_cliente, folio, uuid, subtotal, impuestos, total, fecha_emision, estado) VALUES (?,?,?,?,?,?,?,NOW(),"procesando")');
    $insF->execute([(int)$ticket['id'], $clienteId, $folioFactura, $uuidProvisional, $subtotalFactura, $impuestosFactura, $totalFactura]);
    $facturaId = (int)$conn->lastInsertId();

    $insD = $conn->prepare('INSERT INTO factura_detalles (id_factura, id_ticket_detalle, id_producto, descripcion, cantidad, precio_unitario, importe) VALUES (?,?,?,?,?,?,?)');
    foreach ($detalles as $row) {
        $cant = (int)$row['cantidad'];
        $puConIva = (float)$row['precio_unitario'];
        $puSinIva = $puConIva / (1 + $ivaRate);

        $insD->execute([$facturaId, (int)$row['id'], (int)$row['id_producto'], $row['producto'], $cant, $puSinIva, $puSinIva * $cant]);
    }

    // 5. Integración con Facturama
    $debugFacturama = null;
    if (function_exists('facturama_create_cfdi')) {
        $clQ = $conn->prepare('SELECT * FROM clientes_facturacion WHERE id = ? LIMIT 1');
        $clQ->execute([$clienteId]);
        $cli = $clQ->fetch(PDO::FETCH_ASSOC) ?: [];

        $formaPago = '01';
        $metodoPago = 'PUE';
        $sucursal = facturama_get_default_branch();
        $lugarExpedicion = $sucursal['Address']['ZipCode'] ?? ($sucursal['ZipCode'] ?? '34217');

        $rfcReceptor = strtoupper(trim((string)($cli['rfc'] ?? '')));
        $nombreReceptor = strtoupper(trim((string)($cli['razon_social'] ?? '')));

        if ($rfcReceptor === '' || $rfcReceptor === 'XAXX010101000') {
            $rfcReceptor = 'XAXX010101000';
            $nombreReceptor = 'PUBLICO EN GENERAL';
            $usoCfdi = 'S01';
            $cpReceptor = $lugarExpedicion;
            $regimenReceptor = '616';
        } else {
            $usoCfdi = (string)($cli['uso_cfdi'] ?? 'G03');
            $cpReceptor = (string)($cli['codigo_postal'] ?? $lugarExpedicion);
            $regimenReceptor = (string)($cli['regimen_fiscal'] ?? '616');
        }

        $fields = [
            'CfdiType' => 'I',
            'ExpeditionPlace' => $lugarExpedicion,
            'PaymentForm' => $formaPago,
            'PaymentMethod' => $metodoPago,
            'Currency' => 'MXN',
            'Receiver' => [
                'Rfc' => $rfcReceptor,
                'Name' => $nombreReceptor,
                'CfdiUse' => $usoCfdi,
                'FiscalRegime' => $regimenReceptor,
                'TaxZipCode' => $cpReceptor
            ],
            'Items' => []
        ];

        foreach ($detalles as $row) {
            $cant = (int)$row['cantidad'];
            $precioConIva = (float)$row['precio_unitario'];
            $unitPriceSinIva = $precioConIva / (1 + $ivaRate);
            $itemSubtotal = $unitPriceSinIva * $cant;
            $itemIva = $itemSubtotal * $ivaRate;

            $fields['Items'][] = [
                'ProductCode' => '01010101',
                'Description' => (string)$row['producto'],
                'Unit' => 'Pieza',
                'UnitCode' => 'H87',
                'UnitPrice' => number_format($unitPriceSinIva, 4, '.', ''), // 4 decimales para evitar redondeos prematuros
                'Quantity' => $cant,
                'Subtotal' => number_format($itemSubtotal, 2, '.', ''),
                'TaxObject' => '02',
                'Taxes' => [
                    [
                        'Total' => number_format($itemIva, 2, '.', ''),
                        'Name' => 'IVA',
                        'Base' => number_format($itemSubtotal, 2, '.', ''),
                        'Rate' => '0.160000',
                        'IsRetention' => false,
                        'IsQuota' => false
                    ]
                ],
                'Total' => number_format($itemSubtotal + $itemIva, 2, '.', '')
            ];
        }

        try {
            $resp = facturama_create_cfdi($fields);
            $debugFacturama = $resp;

            $uuidResp = (string)($resp['Complement']['TaxStamp']['Uuid'] ?? '');

            if (empty($uuidResp)) {
                $uuidResp = (string)($resp['Uuid'] ?? $resp['uuid'] ?? '');
            }
            
            $facturamaId = (string)($resp['Id'] ?? ($resp['id'] ?? ''));
            $pdfUrl = (string)($resp['Links']['Pdf'] ?? '');
            $xmlUrl = (string)($resp['Links']['Xml'] ?? '');

            $up = $conn->prepare('UPDATE facturas SET facturama_id=?, uuid=?, serie=?, folio=?, metodo_pago=?, forma_pago=?, uso_cfdi=?, xml_path=?, pdf_path=?, estado="completa" WHERE id=?');
            $up->execute([
                $facturamaId,
                $uuidResp,
                $resp['Serie'] ?? null,
                $resp['Folio'] ?? $folioFactura,
                $metodoPago,
                $formaPago,
                $usoCfdi,
                $xmlUrl,
                $pdfUrl,
                $facturaId
            ]);
        } catch (Throwable $fe) {
            if ($conn->inTransaction()) $conn->rollBack();
            json_error('Error de Facturama', 502, ['mensaje' => $fe->getMessage(), 'campos_enviados' => $fields]);
        }
    }

    $f  = $conn->prepare('SELECT * FROM vista_facturas WHERE id_factura = ?');
    $fd = $conn->prepare('SELECT * FROM vista_factura_detalles WHERE id_factura = ?');
    $f->execute([$facturaId]);
    $fd->execute([$facturaId]);

    $conn->commit();
    json_response([
        'factura' => $f->fetch(PDO::FETCH_ASSOC),
        'detalles' => $fd->fetchAll(PDO::FETCH_ASSOC),
        'debug_facturama' => $debugFacturama
    ]);
} catch (Throwable $e) {
    if (isset($conn) && $conn->inTransaction()) $conn->rollBack();
    json_error('Error general al generar factura', 500, $e->getMessage());
}
