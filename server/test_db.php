<?php
// Testa conexão com MySQL e existência das tabelas agendamentos e internacao
header('Content-Type: application/json; charset=utf-8');

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'hospital';

try {
  $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $stmt = $pdo->query("SHOW TABLES LIKE 'agendamentos'");
  $table_agendamentos = $stmt->fetch();

  $stmt = $pdo->query("SHOW TABLES LIKE 'internacao'");
  $table_internacao = $stmt->fetch();

  echo json_encode([
    'ok' => true,
    'db' => $dbName,
    'table_agendamentos_exists' => $table_agendamentos ? true : false,
    'table_internacao_exists' => $table_internacao ? true : false
  ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => $e->getMessage(),
  ], JSON_UNESCAPED_UNICODE);
}

