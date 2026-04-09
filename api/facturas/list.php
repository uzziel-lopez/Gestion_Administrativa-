<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../services/facturas_service.php';

try {
    $filters = [
        'unidad' => $_GET['unidad'] ?? null,
        'contrato' => $_GET['contrato'] ?? null,
        'mes' => $_GET['mes'] ?? null,
        'anio' => $_GET['anio'] ?? null,
    ];

    echo json_encode([
        'success' => true,
        'data' => showcase_get_facturas($filters),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'No se pudieron obtener facturas',
    ]);
}
