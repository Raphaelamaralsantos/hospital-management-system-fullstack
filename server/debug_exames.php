<?php
// Debug para API de exames

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

$raw = file_get_contents('php://input');
$method = $_SERVER['REQUEST_METHOD'];
$content_type = $_SERVER['CONTENT_TYPE'] ?? 'not set';

$data = json_decode($raw, true);
$json_error = json_last_error_msg();

echo json_encode([
    'received_raw' => $raw,
    'method' => $method,
    'content_type' => $content_type,
    'json_decode_test' => $data,
    'json_last_error' => $json_error
], JSON_UNESCAPED_UNICODE);
?>