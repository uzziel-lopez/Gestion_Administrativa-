<?php

require_once __DIR__ . '/store.php';

function showcase_get_facturas($filters)
{
    $state = showcase_load_state();
    $facturas = $state['facturas'];

    $unidad = isset($filters['unidad']) ? (int) $filters['unidad'] : null;
    $contrato = isset($filters['contrato']) ? (int) $filters['contrato'] : null;
    $mes = isset($filters['mes']) ? (int) $filters['mes'] : null;
    $anio = isset($filters['anio']) ? (int) $filters['anio'] : null;

    return array_values(array_filter($facturas, function ($factura) use ($unidad, $contrato, $mes, $anio) {
        if ($unidad && (int) $factura['unidad_id'] !== $unidad) {
            return false;
        }
        if ($contrato && (int) $factura['contrato_id'] !== $contrato) {
            return false;
        }
        if ($mes && (int) $factura['mes'] !== $mes) {
            return false;
        }
        if ($anio && (int) $factura['anio'] !== $anio) {
            return false;
        }
        return true;
    }));
}

function showcase_get_factura($facturaId)
{
    $state = showcase_load_state();
    foreach ($state['facturas'] as $factura) {
        if ((int) $factura['id'] === (int) $facturaId) {
            return $factura;
        }
    }
    return null;
}

function showcase_update_document_status($facturaId, $tipoDoc, $status, $motivo)
{
    $state = showcase_load_state();
    $updated = false;

    foreach ($state['facturas'] as &$factura) {
        if ((int) $factura['id'] !== (int) $facturaId) {
            continue;
        }

        foreach ($factura['documentos'] as &$doc) {
            if ($doc['tipo'] !== $tipoDoc) {
                continue;
            }

            $doc['status'] = $status;
            $doc['motivo'] = $motivo;
            $updated = true;
            break;
        }

        if ($updated) {
            $factura['historial'][] = [
                'fecha' => date('Y-m-d H:i:s'),
                'evento' => 'validacion_documento',
                'detalle' => sprintf('Documento %s cambiado a %s', $tipoDoc, $status),
                'motivo' => $motivo,
            ];

            $statuses = array_map(function ($doc) {
                return $doc['status'];
            }, $factura['documentos']);

            if (in_array('rechazado', $statuses, true)) {
                $factura['status'] = 'rechazada';
            } elseif (count(array_unique($statuses)) === 1 && $statuses[0] === 'aprobado') {
                $factura['status'] = 'aprobada';
            } else {
                $factura['status'] = 'en_revision';
            }

            break;
        }
    }

    if (!$updated) {
        return false;
    }

    showcase_save_state($state);
    return true;
}
