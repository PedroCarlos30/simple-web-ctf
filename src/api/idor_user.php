<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$userId = $_GET['id'] ?? null;

if (!$userId) {
    echo json_encode(['error' => 'Parâmetro ?id é obrigatório. Exemplo: /api/idor_user.php?id=1']);
    exit;
}

$pdo = getDb();
// Vulnerabilidade IDOR proposital: consulta direta sem checagem de autorização/sessão
$stmt = $pdo->prepare("SELECT id, full_name, email, role, salary, confidential_data FROM profiles WHERE id = :id");
$stmt->execute([':id' => $userId]);
$profile = $stmt->fetch();

if ($profile) {
    echo json_encode([
        'status' => 'success',
        'data' => $profile
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Perfil não encontrado.'], JSON_PRETTY_PRINT);
}

