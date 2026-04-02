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
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
            json_response(["ok" => false, "error" => "Usuario no autenticado"], 401);
        }
        $id_usuario = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT
        c.id AS id_carrito, 
        c.id_usuario AS id_usuario, 
        c.cantidad AS cantidad,

        u.nombre AS nombre_usuario,

        p.id AS id_producto,
        p.sku AS folio_producto,
        p.nombre AS nombre_producto, 
        p.id_categoria AS id_categoria_producto,
        p.descripcion AS descripcion_producto,
        p.precio AS precio_unitario, 
        p.imagen AS imagen,

        cat.nombre AS nombre_categoria,

        (p.precio * c.cantidad) AS subtotal
        FROM carrito c 
        JOIN productos p ON c.id_producto = p.id
        JOIN usuarios u ON c.id_usuario = u.id
        JOIN categorias cat ON p.id_categoria = cat.id

        WHERE c.id_usuario = :uid
        ORDER BY c.id DESC");

        $stmt->execute([':uid' => $id_usuario]);
        $items = $stmt->fetchAll();

        $total = 0.0;
        foreach ($items as $it) $total += (float)$it['subtotal'];

        json_response([
            "success" => true,
            "resultado" => [
                "products" => $items,
                "total" => $total
            ]
        ]);

        break;
    case 'POST':
        $body = get_json_body();

        $id_usuario = $_SESSION['user_id'];
        $id_producto = isset($body['id_producto']) ? (int)$body['id_producto'] : 0;
        $cantidad = isset($body['cantidad']) ? (int)$body['cantidad'] : 1;

        if ($id_usuario <= 0 || $id_producto <= 0 || $cantidad <= 0) {
            json_response(["ok" => false, "error" => "Parametros invalidos"], 400);
        }

        try {
            $conn->beginTransaction();

            $product = $conn->prepare("SELECT id, stock, activo FROM productos WHERE id = :pid FOR UPDATE");
            $product->execute([':pid' => $id_producto]);
            $prod_data = $product->fetch();

            if (!$prod_data || (int)$prod_data['activo'] !== 1) {
                throw new Exception("Producto no disponible");
            }

            if ((int)$prod_data['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente");
            }

            // Actualizar carrito
            $sql = "INSERT INTO carrito (id_producto, id_usuario, cantidad)
                VALUES (:pid, :uid, :cant_insert)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + :cant_update";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':pid'          => $id_producto,
                ':uid'          => $id_usuario,
                ':cant_insert'  => $cantidad,
                ':cant_update'  => $cantidad
            ]);
            /*
            // Restar stock
            $updateStock = $conn->prepare(
                "UPDATE productos SET stock = stock - :cant WHERE id = :pid"
            );

            $updateStock->execute([
                ':cant' => $cantidad,
                ':pid'  => $id_producto
            ]);
            */
            $conn->commit();

            json_response(["ok" => true, "message" => "Carrito actualizado"]);
        } catch (Exception $e) {
            $conn->rollBack();
            json_response(["ok" => false, "error" => $e->getMessage()], 400);
        }

        break;
    case 'DELETE':

        $body = get_json_body();

        $id_usuario = $_SESSION['user_id'];
        $id_producto = isset($body['id_producto']) ? (int)$body['id_producto'] : 0;

        if ($id_usuario <= 0 || $id_producto <= 0) {
            json_response(["ok" => false, "error" => "Parametros invalidos"], 400);
        }

        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("
            SELECT cantidad 
            FROM carrito 
            WHERE id_usuario = :uid 
            AND id_producto = :pid
            FOR UPDATE
        ");

            $stmt->execute([
                ':uid' => $id_usuario,
                ':pid' => $id_producto
            ]);

            $item = $stmt->fetch();

            if (!$item) {
                throw new Exception("Producto no encontrado en carrito");
            }

            $cantidad_actual = (int)$item['cantidad'];

            if ($cantidad_actual > 1) {

                // Restar
                $update = $conn->prepare("
                UPDATE carrito 
                SET cantidad = cantidad - 1 
                WHERE id_usuario = :uid 
                AND id_producto = :pid
            ");

                $update->execute([
                    ':uid' => $id_usuario,
                    ':pid' => $id_producto
                ]);
            } else {

                // 1 → eliminar registro
                $delete = $conn->prepare("
                DELETE FROM carrito 
                WHERE id_usuario = :uid 
                AND id_producto = :pid
            ");

                $delete->execute([
                    ':uid' => $id_usuario,
                    ':pid' => $id_producto
                ]);
            }
            /*
            // Devolver 1 unidad al stock
            $updateStock = $conn->prepare("
            UPDATE productos 
            SET stock = stock + 1 
            WHERE id = :pid
        ");

            $updateStock->execute([
                ':pid' => $id_producto
            ]);
            */

            $conn->commit();

            json_response([
                "ok" => true,
                "message" => "Cantidad actualizada correctamente"
            ]);
        } catch (Exception $e) {

            $conn->rollBack();

            json_response([
                "ok" => false,
                "error" => $e->getMessage()
            ], 400);
        }

        break;
    default:
        json_response(["ok" => false, "error" => "Metodo no permitido"], 405);
        break;
}
