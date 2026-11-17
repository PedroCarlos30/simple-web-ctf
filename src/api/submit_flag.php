<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método inválido. Use POST.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$submittedFlag = trim($input['flag'] ?? '');
$challengeId = trim($input['challenge_id'] ?? '');

if (empty($submittedFlag)) {
    echo json_encode(['success' => false, 'message' => 'Por favor, informe uma flag. Formato: FLAG{...}']);
    exit;
}

$matchedChallenge = null;

// Se challenge_id foi informado, validar nele
if (!empty($challengeId) && isset($CHALLENGES[$challengeId])) {
    if ($CHALLENGES[$challengeId]['flag'] === $submittedFlag) {
        $matchedChallenge = $CHALLENGES[$challengeId];
    }
} else {
    // Busca global entre todos os desafios
    foreach ($CHALLENGES as $id => $ch) {
        if ($ch['flag'] === $submittedFlag) {
            $matchedChallenge = $ch;
            $challengeId = $id;
            break;
        }
    }
}

if ($matchedChallenge) {
    $alreadySolved = in_array($matchedChallenge['id'], $_SESSION['solved_flags']);
    if (!$alreadySolved) {
        $_SESSION['solved_flags'][] = $matchedChallenge['id'];
    }

    echo json_encode([
        'success' => true,
        'message' => $alreadySolved 
            ? 'Você já submeteu esta flag anteriormente!' 
            : 'PARABÉNS! Flag correta! Desafio "' . $matchedChallenge['title'] . '" concluído!',
        'already_solved' => $alreadySolved,
        'challenge_id' => $matchedChallenge['id'],
        'challenge_title' => $matchedChallenge['title'],
        'points_awarded' => $alreadySolved ? 0 : $matchedChallenge['points'],
        'total_score' => getTotalScore(),
        'solved_count' => count($_SESSION['solved_flags']),
        'total_challenges' => count($CHALLENGES)
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Flag incorreta ou inválida. Verifique a sintaxe e tente novamente!'
    ]);
}

