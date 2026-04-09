<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../services/facturas_service.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'id requerido']);
    exit;
}

$factura = showcase_get_factura($id);
if (!$factura) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Factura no encontrada']);
    exit;
}

echo json_encode(['success' => true, 'data' => $factura]);
