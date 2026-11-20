<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

// Validar token CSRF (header ou body)
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN']
    ?? (json_decode(file_get_contents('php://input'), true)['csrf_token'] ?? '')
    ?? ($_POST['csrf_token'] ?? '');

if (!validateCsrfToken((string)$csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token CSRF inválido. Acesso negado.']);
    exit;
}

try {
    $pdo = getDb();

    // Dropar tabelas para reset limpo
    $pdo->exec('DROP TABLE IF EXISTS users');
    $pdo->exec('DROP TABLE IF EXISTS guestbook');
    $pdo->exec('DROP TABLE IF EXISTS profiles');

    // Recriar tabelas e dados
    initDatabase();

    // Limpar progresso via cookie
    saveProgress([]);

    echo json_encode([
        'success' => true,
        'message' => 'Laboratório CTF resetado com sucesso! Banco de dados e progresso restaurados ao padrão.',
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao resetar: ' . $e->getMessage(),
    ]);
}
