<?php
header("Content-Type: application/json; charset=utf-8");

function json_response($data, int $status = 200): void {
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function json_error(string $message, int $status = 400, $extra = null): void {
  http_response_code($status);

  $response = [
    "success" => false,
    "error" => $message
  ];

  if ($extra !== null) {
    $response["details"] = $extra;
  }

  echo json_encode($response, JSON_UNESCAPED_UNICODE);
  exit;
}

function get_json_body(): array {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $decoded = json_decode($raw, true);
  return is_array($decoded) ? $decoded : [];
}

function pdo(): PDO {
  $host = "127.0.0.1";
  $db   = "dulceria";
  $user = "root";
  $pass = "";
  $charset = "utf8mb4";

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  return new PDO($dsn, $user, $pass, $options);
}

class DB {
  private static ?PDO $pdo = null;

  public static function get(): PDO {
    if (self::$pdo === null) {
      self::$pdo = pdo();
    }
    return self::$pdo;
  }
}
?>