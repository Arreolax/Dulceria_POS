<?php
header("Content-Type: application/json");
require_once(__DIR__ . '../../config/conexion.php');

session_start(); 

$conn = pdo();

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user["contrasena"])) {
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["name"]   = $user["nombre"];
    $_SESSION["email"]   = $email;
    $_SESSION["address"]   = $user["direccion"];
    $_SESSION["phone"]   = $user["telefono"];
    $_SESSION["role"]   = $user["rol"];
    $_SESSION["logged"]  = true;

    echo json_encode(["success" => true]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Credenciales inválidas"]);
}
