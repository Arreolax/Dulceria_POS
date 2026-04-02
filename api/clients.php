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
            id,
            razon_social,
            rfc,
            email,
            codigo_postal,
            uso_cfdi,
            regimen_fiscal
        FROM clientes_facturacion");

        $stmt->execute();
        $clients = $stmt->fetchAll();
        json_response($clients);
        break;

    case 'POST':

        $body = json_decode(file_get_contents("php://input"), true);

        $rfc = strtoupper(trim($body['rfc'] ?? ''));
        $razon = strtoupper(trim($body['razon_social'] ?? ''));
        $uso = trim($body['uso_cfdi'] ?? '');
        $regimen = trim($body['regimen_fiscal'] ?? '');
        $email = trim($body['email'] ?? '');
        $direccion = trim($body['direccion'] ?? '');
        $cp = trim($body['codigo_postal'] ?? '');

        // 🔥 Validaciones básicas
        if ($rfc === '' || $razon === '') {
            json_response([
                "success" => false,
                "mensaje" => "RFC y Razón Social son obligatorios"
            ], 422);
        }

        try {

            $stmt = $conn->prepare("
                INSERT INTO clientes_facturacion
                (rfc, razon_social, uso_cfdi, regimen_fiscal, email, direccion, codigo_postal)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $rfc,
                $razon,
                $uso,
                $regimen,
                $email,
                $direccion,
                $cp
            ]);

            $id = $conn->lastInsertId();

            json_response([
                "success" => true,
                "mensaje" => "Cliente creado correctamente",
                "data" => [
                    "id" => $id
                ]
            ]);

        } catch (PDOException $e) {

            json_response([
                "success" => false,
                "mensaje" => "Error al guardar",
                "error" => $e->getMessage()
            ], 500);
        }

        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Metodo Invalido']);
}
?>