<?php
// API de autenticação - registro e login
// server/api/auth.php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

function logAction($userId, $action, $table = null, $recordId = null, $data = null) {
  global $pdo;
  try {
    $stmt = $pdo->prepare(
      'INSERT INTO logs (user_id, acao, tabela, registro_id, dados, ip_address)
       VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
      $userId,
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

function generateSessionId() {
  return bin2hex(random_bytes(32));
}

function createSession($userId) {
  global $pdo;
  $sessionId = generateSessionId();
  $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

  $stmt = $pdo->prepare(
    'INSERT INTO sessions (id, user_id, ip_address, user_agent, expires_at)
     VALUES (?, ?, ?, ?, ?)'
  );
  $stmt->execute([
    $sessionId,
    $userId,
    $_SERVER['REMOTE_ADDR'] ?? null,
    $_SERVER['HTTP_USER_AGENT'] ?? null,
    $expiresAt
  ]);

  return $sessionId;
}

function validateSession($sessionId) {
  global $pdo;
  $stmt = $pdo->prepare(
    'SELECT s.*, u.nome, u.email, u.tipo
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
$action = $_GET['action'] ?? '';

switch ($method) {
  case 'POST':
    $raw = file_get_contents('php://input');
    if (!$raw) {
      respond(400, ['ok' => false, 'error' => 'Body vazio']);
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
      respond(400, ['ok' => false, 'error' => 'JSON inválido']);
    }

    if ($action === 'register') {
      // Registro de usuário
      $nome = trim($data['nome'] ?? '');
      $email = trim($data['email'] ?? '');
      $telefone = trim($data['telefone'] ?? '');
      $senha = $data['senha'] ?? '';

      if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {
        respond(422, ['ok' => false, 'error' => 'Dados inválidos']);
      }

      // Verificar se email já existe
      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
        respond(409, ['ok' => false, 'error' => 'Email já cadastrado']);
      }

      // Hash da senha
      $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

      // Inserir usuário
      $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nome, email, telefone, senha_hash)
         VALUES (?, ?, ?, ?)'
      );
      $stmt->execute([$nome, $email, $telefone, $senhaHash]);
      $userId = $pdo->lastInsertId();

      logAction($userId, 'registro', 'usuarios', $userId);

      respond(201, ['ok' => true, 'message' => 'Usuário cadastrado com sucesso']);

    } elseif ($action === 'login') {
      // Login
      $email = trim($data['email'] ?? '');
      $senha = $data['senha'] ?? '';

      if ($email === '' || $senha === '') {
        respond(422, ['ok' => false, 'error' => 'Email e senha obrigatórios']);
      }

      // Buscar usuário
      $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ? AND ativo = TRUE');
      $stmt->execute([$email]);
      $user = $stmt->fetch();

      if (!$user || !password_verify($senha, $user['senha_hash'])) {
        respond(401, ['ok' => false, 'error' => 'Credenciais inválidas']);
      }

      // Criar sessão
      $sessionId = createSession($user['id']);
      logAction($user['id'], 'login');

      respond(200, [
        'ok' => true,
        'session_id' => $sessionId,
        'user' => [
          'id' => $user['id'],
          'nome' => $user['nome'],
          'email' => $user['email'],
          'tipo' => $user['tipo']
        ]
      ]);

    } elseif ($action === 'logout') {
      // Logout
      $sessionId = $data['session_id'] ?? '';
      if ($sessionId) {
        $stmt = $pdo->prepare('DELETE FROM sessions WHERE id = ?');
        $stmt->execute([$sessionId]);
      }
      respond(200, ['ok' => true, 'message' => 'Logout realizado']);

    } elseif ($action === 'check') {
      // Verificar sessão
      $sessionId = $data['session_id'] ?? '';
      $session = validateSession($sessionId);

      if (!$session) {
        respond(401, ['ok' => false, 'error' => 'Sessão inválida']);
      }

      respond(200, [
        'ok' => true,
        'user' => [
          'id' => $session['user_id'],
          'nome' => $session['nome'],
          'email' => $session['email'],
          'tipo' => $session['tipo']
        ]
      ]);
    }

    break;

  default:
    respond(405, ['ok' => false, 'error' => 'Método não permitido']);
}