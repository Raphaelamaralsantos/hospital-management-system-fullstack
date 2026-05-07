<?php
// Endpoint para receber agendamentos de exames e salvar no MySQL (XAMPP)
// Aponte o frontend para: server/api/exames.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

function respond($code, $payload) {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function logAction($userEmail, $action, $table, $recordId, $data = null) {
  global $pdo;
  try {
    // Tentar encontrar usuário pelo email
    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
    $stmt->execute([$userEmail]);
    $user = $stmt->fetch();

    $stmt = $pdo->prepare(
      'INSERT INTO logs (user_id, acao, tabela, registro_id, dados, ip_address)
       VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
      $user ? $user['id'] : null,
      $action,
      $table,
      $recordId,
      $data ? json_encode($data) : null,
      $_SERVER['REMOTE_ADDR'] ?? null
    ]);
  } catch (Exception $e) {
    // Log falhou, mas não interrompe o fluxo principal
  }
}

$raw = file_get_contents('php://input');
if (!$raw) {
  respond(400, ['ok' => false, 'error' => 'Body vazio']);
}

$data = json_decode($raw, true);
if (!is_array($data)) {
  respond(400, ['ok' => false, 'error' => 'JSON inválido', 'received' => $raw, 'json_error' => json_last_error_msg()]);
}

$nome = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$telefone = trim($data['telefone'] ?? '');
$tipo_exame = trim($data['tipo_exame'] ?? '');
$data_exame = trim($data['data_exame'] ?? '');
$hora_exame = trim($data['hora_exame'] ?? '');
$observacoes = trim($data['observacoes'] ?? '');

if ($nome === '' || $email === '' || $telefone === '' || $tipo_exame === '' || $data_exame === '' || $hora_exame === '') {
  respond(422, ['ok' => false, 'error' => 'Campos obrigatórios faltando', 'campos' => [
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone,
    'tipo_exame' => $tipo_exame,
    'data_exame' => $data_exame,
    'hora_exame' => $hora_exame
  ]]);
}

// Config do MySQL (ajuste se necessário)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'hospital'; // banco: hospital (ajuste se necessário)

// Diagnóstico simples para ajudar a identificar falhas
error_log('Exame agendado: ' . json_encode($data, JSON_UNESCAPED_UNICODE));

try {
  $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) {
  respond(500, ['ok' => false, 'error' => 'Falha na conexão com banco', 'details' => $e->getMessage()]);
}

try {
  $stmt = $pdo->prepare(
    'INSERT INTO exames (nome, email, telefone, tipo_exame, data_exame, hora_exame, observacoes)
     VALUES (:nome, :email, :telefone, :tipo_exame, :data_exame, :hora_exame, :observacoes)'
  );

  $stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':telefone' => $telefone,
    ':tipo_exame' => $tipo_exame,
    ':data_exame' => $data_exame,
    ':hora_exame' => $hora_exame,
    ':observacoes' => ($observacoes !== '' ? $observacoes : null),
  ]);

  $exameId = $pdo->lastInsertId();

  // Log da ação
  logAction($email, 'exame_agendado', 'exames', $exameId, [
    'tipo_exame' => $tipo_exame,
    'data_exame' => $data_exame,
    'hora_exame' => $hora_exame
  ]);

  respond(201, ['ok' => true, 'id' => $exameId]);
} catch (Exception $e) {
  respond(500, ['ok' => false, 'error' => 'Falha ao salvar no banco: ' . $e->getMessage()]);
}