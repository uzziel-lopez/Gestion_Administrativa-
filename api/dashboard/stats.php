<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../services/dashboard_service.php';

try {
    echo json_encode([
        'success' => true,
        'data' => showcase_dashboard_stats(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudieron obtener las metricas',
    ]);
}
