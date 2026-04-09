<?php

function showcase_storage_path($name)
{
    return __DIR__ . '/../storage/' . $name;
}

function showcase_seed_state()
{
    return [
        'facturas' => [
            [
                'id' => 501,
                'unidad_id' => 1,
                'contrato_id' => 101,
                'mes' => 3,
                'anio' => 2026,
                'numero' => 'FAC-40864',
                'proveedor' => 'Proveedor Demo Norte',
                'fecha' => '2026-03-18',
                'monto' => 248913.55,
                'status' => 'pendiente_revision',
                'lote' => 'L-2026-03-001',
                'documentos' => [
                    ['tipo' => 'pdf', 'label' => 'PDF Factura', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864.pdf'],
                    ['tipo' => 'xml', 'label' => 'XML Factura', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864.xml'],
                    ['tipo' => 'solicitud', 'label' => 'Solicitud Entrega', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864-solicitud.pdf'],
                    ['tipo' => 'layout', 'label' => 'Layout', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864-layout.xlsx'],
                    ['tipo' => 'anexos', 'label' => 'Anexos', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864-anexos.zip'],
                    ['tipo' => 'acuse', 'label' => 'Acuse CFDI', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40864-acuse.pdf']
                ],
                'historial' => []
            ],
            [
                'id' => 502,
                'unidad_id' => 1,
                'contrato_id' => 101,
                'mes' => 3,
                'anio' => 2026,
                'numero' => 'FAC-40871',
                'proveedor' => 'Proveedor Demo Centro',
                'fecha' => '2026-03-22',
                'monto' => 115004.20,
                'status' => 'en_revision',
                'lote' => 'L-2026-03-002',
                'documentos' => [
                    ['tipo' => 'pdf', 'label' => 'PDF Factura', 'status' => 'aprobado', 'ruta' => 'storage/demo/FAC-40871.pdf'],
                    ['tipo' => 'xml', 'label' => 'XML Factura', 'status' => 'aprobado', 'ruta' => 'storage/demo/FAC-40871.xml'],
                    ['tipo' => 'solicitud', 'label' => 'Solicitud Entrega', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40871-solicitud.pdf'],
                    ['tipo' => 'layout', 'label' => 'Layout', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40871-layout.xlsx'],
                    ['tipo' => 'anexos', 'label' => 'Anexos', 'status' => 'vacio', 'ruta' => null],
                    ['tipo' => 'acuse', 'label' => 'Acuse CFDI', 'status' => 'pendiente', 'ruta' => 'storage/demo/FAC-40871-acuse.pdf']
                ],
                'historial' => []
            ]
        ]
    ];
}

function showcase_load_state()
{
    $path = showcase_storage_path('state.json');
    if (!file_exists($path)) {
        $state = showcase_seed_state();
        showcase_save_state($state);
        return $state;
    }

    $raw = file_get_contents($path);
    $state = json_decode($raw, true);
    if (!is_array($state)) {
        $state = showcase_seed_state();
        showcase_save_state($state);
    }
    return $state;
}

function showcase_save_state($state)
{
    $path = showcase_storage_path('state.json');
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($path, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
