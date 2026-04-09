<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../services/batch_service.php';

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalido']);
    exit;
}

$action = $payload['action'] ?? '';

try {
    if ($action === 'process_batch') {
        $items = $payload['items'] ?? [];
        $data = showcase_batch_process($items);
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    if ($action === 'list_history') {
        echo json_encode(['success' => true, 'data' => showcase_batch_history()]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'action invalida']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
