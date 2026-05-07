<?php
// API do dashboard do paciente
// server/api/dashboard.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

function respond($code, $payload) {
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

function validateSession($sessionId) {
  global $pdo;
  $stmt = $pdo->prepare(
    'SELECT s.*, u.id as user_id, u.nome, u.email, u.tipo
     FROM sessions s
     JOIN usuarios u ON s.user_id = u.id
     WHERE s.id = ? AND s.expires_at > NOW() AND u.ativo = TRUE'
  );
  $stmt->execute([$sessionId]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Conexão com banco
try {
  $pdo = new PDO('mysql:host=localhost;dbname=hospital;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (Exception $e) {
  respond(500, ['ok' => false, 'error' => 'Erro de conexão com banco']);
}

$method = $_SERVER['REQUEST_METHOD'];
$sessionId = $_GET['session_id'] ?? '';

if (!$sessionId) {
  respond(401, ['ok' => false, 'error' => 'Sessão não fornecida']);
}

$session = validateSession($sessionId);
if (!$session) {
  respond(401, ['ok' => false, 'error' => 'Sessão inválida']);
}

$userId = $session['user_id'];

switch ($method) {
  case 'GET':
    try {
      // Buscar agendamentos do usuário
      $stmt = $pdo->prepare(
        'SELECT id, especialidade, data, hora, observacoes, created_at
         FROM agendamentos
         WHERE email = ?
         ORDER BY created_at DESC'
      );
      $stmt->execute([$session['email']]);
      $agendamentos = $stmt->fetchAll();

      // Buscar exames do usuário
      $stmt = $pdo->prepare(
        'SELECT id, tipo_exame, data_exame, hora_exame, observacoes, created_at
         FROM exames
         WHERE email = ?
         ORDER BY created_at DESC'
      );
      $stmt->execute([$session['email']]);
      $exames = $stmt->fetchAll();

      // Buscar internações do usuário
      $stmt = $pdo->prepare(
        'SELECT id, tipo_internacao, data_entrada, data_saida, observacoes, created_at
         FROM internacao
         WHERE email = ?
         ORDER BY created_at DESC'
      );
      $stmt->execute([$session['email']]);
      $internacoes = $stmt->fetchAll();

      respond(200, [
        'ok' => true,
        'user' => [
          'nome' => $session['nome'],
          'email' => $session['email'],
          'tipo' => $session['tipo']
        ],
        'agendamentos' => $agendamentos,
        'exames' => $exames,
        'internacoes' => $internacoes
      ]);

    } catch (Exception $e) {
      respond(500, ['ok' => false, 'error' => 'Erro ao buscar dados']);
    }

    break;

  default:
    respond(405, ['ok' => false, 'error' => 'Método não permitido']);
}