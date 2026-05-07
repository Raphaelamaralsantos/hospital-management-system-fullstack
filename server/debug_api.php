<?php
// Debug script para testar a API de internações
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
echo json_encode([
    'received_raw' => $raw,
    'method' => $_SERVER['REQUEST_METHOD'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
    'json_decode_test' => json_decode($raw, true),
    'json_last_error' => json_last_error_msg()
], JSON_PRETTY_PRINT);