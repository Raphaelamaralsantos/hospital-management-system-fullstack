<?php
// Endpoint para receber agendamentos e salvar no MySQL (XAMPP)
// Aponte o frontend para: server/api/agendamentos.php

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
  respond(400, ['ok' => false, 'error' => 'JSON inválido']);
}

$nome = trim($data['nome'] ?? '');
$email = trim($data['email'] ?? '');
$especialidade = trim($data['especialidade'] ?? '');
$dataDate = trim($data['data'] ?? '');
$hora = trim($data['hora'] ?? '');
$observacoes = trim($data['observacoes'] ?? '');

if ($nome === '' || $email === '' || $especialidade === '' || $dataDate === '' || $hora === '') {
  respond(422, ['ok' => false, 'error' => 'Campos obrigatórios faltando']);
}

// Config do MySQL (ajuste se necessário)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'hospital'; // banco: hospital (ajuste se necessário)

// Diagnóstico simples para ajudar a identificar falhas
error_log('Agendamento recebido: ' . json_encode($data, JSON_UNESCAPED_UNICODE));

try {
  $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) {
  respond(500, ['ok' => false, 'error' => 'Falha na conexão com banco']);
}

try {
  $stmt = $pdo->prepare(
    'INSERT INTO agendamentos (nome, email, especialidade, data, hora, observacoes)
     VALUES (:nome, :email, :especialidade, :data, :hora, :observacoes)'
  );

$stmt->execute([
    ':nome' => $nome,
    ':email' => $email,
    ':especialidade' => $especialidade,
    ':data' => $dataDate,
    ':hora' => $hora,
    ':observacoes' => ($observacoes !== '' ? $observacoes : null),
  ]);

  $agendamentoId = $pdo->lastInsertId();

  // Log da ação
  logAction($email, 'agendamento_criado', 'agendamentos', $agendamentoId, [
    'especialidade' => $especialidade,
    'data' => $dataDate,
    'hora' => $hora
  ]);

  respond(201, ['ok' => true, 'id' => $agendamentoId]);
} catch (Exception $e) {
  respond(500, ['ok' => false, 'error' => 'Falha ao salvar no banco: ' . $e->getMessage()]);
}

