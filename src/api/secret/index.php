<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Verificar se a requisição vem do próprio servidor (localhost)
// Isso é o alvo do desafio SSRF: apenas requisições internas recebem a flag
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
$isLocalhost = in_array($remoteAddr, ['127.0.0.1', '::1', '0:0:0:0:0:0:0:1'], true);

// Verificar token de acesso interno
$token = $_GET['token'] ?? '';

if ($isLocalhost && $token === 'internal') {
    // Acesso via SSRF confirmado — retornar flag
    echo json_encode([
        'status'  => 'success',
        'service' => 'CyberPulse Internal Metadata Service v1.0',
        'access'  => 'GRANTED (internal network only)',
        'message' => 'Parabéns! Você acessou um serviço interno via SSRF.',
        'flag'    => 'FLAG{ssrf_1nt3rn4l_s3rv1c3_pwn3d}',
        'note'    => 'Este endpoint retorna 403 para qualquer acesso externo direto.',
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Acesso externo direto — negar
http_response_code(403);
echo json_encode([
    'status'  => 'forbidden',
    'code'    => 403,
    'message' => 'Acesso restrito a serviços internos da rede corporativa.',
    'hint'    => 'Este endpoint só responde a requisições originadas pelo próprio servidor.',
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
