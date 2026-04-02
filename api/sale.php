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
        DATE_FORMAT(v.created_at, '%d/%m/%Y %H:%i:%s') AS fecha_venta,
        v.estatus AS estatus_venta,
        v.folio AS folio_venta,
        v.metodo_pago AS metodo_pago,
        
        u.nombre AS nombre_cliente,

        SUM(vd.subtotal) AS total_venta,

        GROUP_CONCAT(
            CONCAT('» ', p.nombre, ' (', vd.cantidad, ' pz. - $', vd.subtotal, ')')
            SEPARATOR '<br>'
        ) AS productos

        FROM venta v 
        JOIN usuarios u ON v.id_usuario = u.id
        JOIN venta_detalle vd ON vd.id_venta = v.id
        JOIN productos p ON p.id = vd.id_producto

        GROUP BY 
            v.id,
            v.created_at,
            v.estatus,
            u.nombre

        ORDER BY v.created_at DESC;");

        $stmt->execute();
        $sales = $stmt->fetchAll();

        json_response([
            'success' => true,
            'resultado' => [
                'ventas' => $sales
            ]
        ]);
        break;

    case 'POST':

        $body = get_json_body();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
            json_response(["ok" => false, "error" => "Usuario no autenticado"], 401);
        }

        $id_usuario = $_SESSION['user_id'];

        try {

            $conn->beginTransaction();

            //Obtener productos del carrito
            $stmt = $conn->prepare("SELECT 
            c.id_producto,
            c.cantidad,
            p.precio,
            p.stock,
            p.activo
            FROM carrito c
            INNER JOIN productos p ON p.id = c.id_producto
            WHERE c.id_usuario = :uid
            FOR UPDATE
        ");

            $stmt->execute([":uid" => $id_usuario]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$items || count($items) === 0) {
                $conn->rollBack();
                json_response(["ok" => false, "error" => "Carrito vacío"], 409);
            }

            //Validar activo y stock
            foreach ($items as $it) {

                if ((int)$it["activo"] !== 1) {
                    $conn->rollBack();
                    json_response(["ok" => false, "error" => "Producto no disponible"], 409);
                }

                if ((int)$it["stock"] < (int)$it["cantidad"]) {
                    $conn->rollBack();
                    json_response(["ok" => false, "error" => "Stock insuficiente"], 409);
                }
            }

            // Crear venta
            $instVenta = $conn->prepare("
            INSERT INTO venta (id_usuario, total, estatus)
            VALUES (:uid, 0.00, 'CREADA')
        ");

            $instVenta->execute([":uid" => $id_usuario]);

            $id_venta = (int)$conn->lastInsertId();

            // inserts y update stock
            $insDet = $conn->prepare("
            INSERT INTO venta_detalle
            (id_venta, id_producto, cantidad, precio_unitario, subtotal)
            VALUES (:vid, :pid, :cant, :precio, :sub)
        ");

            $updStock = $conn->prepare("
            UPDATE productos
            SET stock = stock - :cant
            WHERE id = :pid
        ");

            $insTicket = $conn->prepare("
            INSERT INTO ticket (id_venta, id_usuario, total)
            VALUES (:vid, :uid, :total)
        ");

            $insTicketDet = $conn->prepare("
            INSERT INTO ticket_detalle
            (id_ticket, id_producto, cantidad, precio_unitario, subtotal)
            VALUES (:tid, :pid, :cant, :precio, :sub)
        ");

            $total = 0.00;

            foreach ($items as $it) {

                $pid = (int)$it["id_producto"];
                $cant = (int)$it["cantidad"];
                $precio = (float)$it["precio"];
                $sub = $precio * $cant;

                // Insertar detalle
                $insDet->execute([
                    ':vid' => $id_venta,
                    ':pid' => $pid,
                    ':cant' => $cant,
                    ':precio' => $precio,
                    ':sub' => $sub
                ]);

                // Descontar stock
                $updStock->execute([
                    ':cant' => $cant,
                    ':pid' => $pid
                ]);

                $total += $sub;
            }

            // Actualizar total de la venta
            $updVenta = $conn->prepare("
            UPDATE venta
            SET total = :total
            WHERE id = :vid
        ");

            // Crear ticket
            $insTicket->execute([
                ':vid' => $id_venta,
                ':uid' => $id_usuario,
                ':total' => $total
            ]);

            $id_ticket = (int)$conn->lastInsertId();

            foreach ($items as $it) {

                $pid = (int)$it["id_producto"];
                $cant = (int)$it["cantidad"];
                $precio = (float)$it["precio"];
                $sub = $precio * $cant;

                $insTicketDet->execute([
                    ':tid' => $id_ticket,
                    ':pid' => $pid,
                    ':cant' => $cant,
                    ':precio' => $precio,
                    ':sub' => $sub
                ]);
            }

            $updVenta->execute([
                ':total' => $total,
                ':vid' => $id_venta
            ]);

            //Vaciar carrito
            $delCarrito = $conn->prepare("
            DELETE FROM carrito
            WHERE id_usuario = :uid
        ");

            $delCarrito->execute([':uid' => $id_usuario]);

            $conn->commit();

            json_response([
                "ok" => true,
                "message" => "Venta creada correctamente",
                "data" => [
                    "id_venta" => $id_venta,
                    "total" => round($total, 2)
                ]
            ], 200);
        } catch (Throwable $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            json_response([
                "ok" => false,
                "error" => "Error al crear venta",
                "detail" => $e->getMessage()
            ], 500);
        }

        break;
    case 'DELETE':

        if (!isset($_GET['id'])) {
            json_response([
                'success' => false,
                'message' => 'ID de venta requerido'
            ], 400);
        }

        $id = intval($_GET['id']);

        try {

            $conn->beginTransaction();

            // Obtener productos para restaurar stock
            $stmtGet = $conn->prepare("
            SELECT id_producto, cantidad
            FROM venta_detalle
            WHERE id_venta = :id
            FOR UPDATE
        ");
            $stmtGet->execute([':id' => $id]);
            $items = $stmtGet->fetchAll(PDO::FETCH_ASSOC);

            if (!$items) {
                $conn->rollBack();
                json_response([
                    'success' => false,
                    'message' => 'La venta no existe o no tiene productos'
                ], 404);
            }

            // Restaurar stock
            $updStock = $conn->prepare("
            UPDATE productos
            SET stock = stock + :cant
            WHERE id = :pid
        ");

            foreach ($items as $it) {
                $updStock->execute([
                    ':cant' => (int)$it['cantidad'],
                    ':pid' => (int)$it['id_producto']
                ]);
            }

            // Eliminar detalle venta
            $stmtDetalle = $conn->prepare("
            DELETE FROM venta_detalle WHERE id_venta = :id
        ");
            $stmtDetalle->execute([':id' => $id]);

            // Eliminar venta
            $stmtVenta = $conn->prepare("
            DELETE FROM venta WHERE id = :id
        ");
            $stmtVenta->execute([':id' => $id]);

            if ($stmtVenta->rowCount() === 0) {
                $conn->rollBack();
                json_response([
                    'success' => false,
                    'message' => 'La venta no existe'
                ], 404);
            }

            $conn->commit();

            json_response([
                'success' => true,
                'message' => 'Venta y ticket eliminados correctamente'
            ]);
        } catch (Exception $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            json_response([
                'success' => false,
                'message' => 'Error al eliminar la venta',
                'error' => $e->getMessage()
            ], 500);
        }

        break;
    default:
        json_response(["ok" => false, "error" => "Metodo no permitido"], 405);
        break;
}
