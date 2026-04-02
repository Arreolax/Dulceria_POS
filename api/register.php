<?php
header("Content-Type: application/json");
require_once(__DIR__ . "../../config/conexion.php");

session_start();

$conn = pdo();

$data = json_decode(file_get_contents("php://input"), true);

$name     = trim($data["nombre"] ?? "");
$email    = trim($data["correo"] ?? "");
$password = $data["contrasena"] ?? "";
$phone    = $data["telefono"] ?? "";

if (!$name || !$email || !$password || !$phone) {
    http_response_code(400);
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO usuarios(nombre, email, contrasena, telefono)
     VALUES (?, ?, ?, ?)"
);
$stmt->execute([$name, $email, $hash, $phone]); // $password

session_regenerate_id(true);

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$conn->lastInsertId()]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(500);
    echo json_encode(["error" => "Error al crear sesión"]);
    exit;
}

$_SESSION["user_id"] = $user["id"];
$_SESSION["name"]    = $user["nombre"];
$_SESSION["email"]   = $user["email"];
$_SESSION["phone"]   = $user["telefono"];
$_SESSION["role"]    = $user["rol"] ?? "user";
$_SESSION["logged"]  = true;

echo json_encode(["success" => true]);
