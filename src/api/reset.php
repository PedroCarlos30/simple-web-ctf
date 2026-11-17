<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido.']);
    exit;
}

try {
    $pdo = getDb();
    
    // Dropar tabelas para reset limpo
    $pdo->exec("DROP TABLE IF EXISTS users");
    $pdo->exec("DROP TABLE IF EXISTS guestbook");
    $pdo->exec("DROP TABLE IF EXISTS profiles");
    
    // Recriar tabelas e dados
    initDatabase();
    
    // Limpar progresso da sessão
    $_SESSION['solved_flags'] = [];
    
    echo json_encode([
        'success' => true,
        'message' => 'Laboratório CTF resetado com sucesso! Banco de dados e progresso restaurados ao padrão.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao resetar: ' . $e->getMessage()
    ]);
}

