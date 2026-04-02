<?php
declare(strict_types=1);

$BASE = dirname(__DIR__, 2);
$CFG = is_file($BASE . '/config/db.php') ? ($BASE . '/config/db.php') : ($BASE . '/backend/config/db.php');
$UTL = is_file($BASE . '/utils/response.php') ? ($BASE . '/utils/response.php') : ($BASE . '/backend/utils/response.php');
require_once $CFG;
require_once $UTL;

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        json_error('Método no permitido', 405);
    }
    $body = json_decode(file_get_contents('php://input'), true) ?: [];

    $rfc = trim((string)($body['rfc'] ?? ''));
    $razon = trim((string)($body['razon_social'] ?? ''));
    if ($rfc === '' || $razon === '') {
        json_error('RFC y Razón social son requeridos', 422);
    }

    /** @noinspection PhpUndefinedClassInspection */
    $pdo = DB::get();
    $pdo->beginTransaction();

    $campos = [
        'razon_social' => $razon,
        'correo' => (string)($body['correo'] ?? ''),
        'telefono' => (string)($body['telefono'] ?? ''),
        'calle' => (string)($body['calle'] ?? ''),
        'numero_ext' => (string)($body['numero_ext'] ?? ''),
        'numero_int' => (string)($body['numero_int'] ?? ''),
        'colonia' => (string)($body['colonia'] ?? ''),
        'municipio' => (string)($body['municipio'] ?? ''),
        'estado' => (string)($body['estado'] ?? ''),
        'pais' => (string)($body['pais'] ?? 'México'),
        'cp' => (string)($body['cp'] ?? ''),
        'regimen' => (string)($body['regimen'] ?? ''),
        'uso_cfdi' => (string)($body['uso_cfdi'] ?? ''),
    ];

    // Buscar por RFC
    $sel = $pdo->prepare('SELECT id FROM clientes_facturacion WHERE rfc = ? LIMIT 1');
    $sel->execute([$rfc]);
    $id = (int)($sel->fetchColumn() ?: 0);

    if ($id > 0) {
        // Update
        $sql = 'UPDATE clientes_facturacion SET razon_social=?, correo=?, telefono=?, calle=?, numero_ext=?, numero_int=?, colonia=?, municipio=?, estado=?, pais=?, cp=?, regimen=?, uso_cfdi=?, updated_at=NOW() WHERE id=?';
        $pdo->prepare($sql)->execute([
            $campos['razon_social'], $campos['correo'], $campos['telefono'], $campos['calle'], $campos['numero_ext'],
            $campos['numero_int'], $campos['colonia'], $campos['municipio'], $campos['estado'], $campos['pais'],
            $campos['cp'], $campos['regimen'], $campos['uso_cfdi'], $id
        ]);
    } else {
        // Insert
        $sql = 'INSERT INTO clientes_facturacion (rfc, razon_social, correo, telefono, calle, numero_ext, numero_int, colonia, municipio, estado, pais, cp, regimen, uso_cfdi, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())';
        $pdo->prepare($sql)->execute([
            $rfc, $campos['razon_social'], $campos['correo'], $campos['telefono'], $campos['calle'], $campos['numero_ext'],
            $campos['numero_int'], $campos['colonia'], $campos['municipio'], $campos['estado'], $campos['pais'],
            $campos['cp'], $campos['regimen'], $campos['uso_cfdi']
        ]);
        $id = (int)$pdo->lastInsertId();
    }

    // Devolver el registro completo
    $out = $pdo->prepare('SELECT * FROM clientes_facturacion WHERE id = ? LIMIT 1');
    $out->execute([$id]);

    $pdo->commit();
    json_response(['cliente' => $out->fetch(PDO::FETCH_ASSOC)]);
} catch (Throwable $e) {
    /** @noinspection PhpUndefinedClassInspection */
    try { DB::get()->rollBack(); } catch (Throwable $e2) {}
    json_error('Error al registrar cliente', 500, $e->getMessage());
}
