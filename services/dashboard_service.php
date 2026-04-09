<?php

require_once __DIR__ . '/store.php';

function showcase_dashboard_stats()
{
    $state = showcase_load_state();
    $facturas = $state['facturas'];

    $totalFacturas = count($facturas);
    $montoTotal = 0;
    $pendientes = 0;
    $aprobadas = 0;
    $rechazadas = 0;

    foreach ($facturas as $factura) {
        $montoTotal += (float) $factura['monto'];

        $docStatuses = array_map(function ($doc) {
            return $doc['status'];
        }, $factura['documentos']);

        if (in_array('rechazado', $docStatuses, true)) {
            $rechazadas++;
            continue;
        }

        if (count(array_unique($docStatuses)) === 1 && $docStatuses[0] === 'aprobado') {
            $aprobadas++;
        } else {
            $pendientes++;
        }
    }

    return [
        'total_facturas' => $totalFacturas,
        'monto_total' => round($montoTotal, 2),
        'pendientes_revision' => $pendientes,
        'aprobadas' => $aprobadas,
        'rechazadas' => $rechazadas,
        'batchs_hoy' => count(glob(__DIR__ . '/../storage/batches/batch_*')),
    ];
}
