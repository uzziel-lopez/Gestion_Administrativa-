<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../../services/facturas_service.php';

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'JSON invalido']);
    exit;
}

$facturaId = $payload['factura_id'] ?? null;
$tipo = $payload['tipo'] ?? null;
$status = $payload['status'] ?? null;
$motivo = $payload['motivo'] ?? '';

if (!$facturaId || !$tipo || !$status) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'factura_id, tipo y status son requeridos']);
    exit;
}

$allowed = ['pendiente', 'aprobado', 'rechazado', 'vacio'];
if (!in_array($status, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'status no permitido']);
    exit;
}

$ok = showcase_update_document_status($facturaId, $tipo, $status, $motivo);
if (!$ok) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Documento o factura no encontrado']);
    exit;
}

echo json_encode(['success' => true]);
