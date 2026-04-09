<?php

function showcase_batch_root()
{
    return __DIR__ . '/../storage/batches';
}

function showcase_batch_ensure_dir()
{
    $root = showcase_batch_root();
    if (!is_dir($root)) {
        mkdir($root, 0775, true);
    }
    return $root;
}

function showcase_batch_process($items)
{
    if (empty($items)) {
        throw new RuntimeException('No hay elementos para procesar');
    }

    $batchId = 'batch_' . date('Ymd_His') . '_' . rand(100, 999);
    $batchDir = showcase_batch_ensure_dir() . '/' . $batchId;
    mkdir($batchDir, 0775, true);

    $results = [];

    foreach ($items as $idx => $item) {
        $folio = isset($item['folio']) ? preg_replace('/[^A-Za-z0-9_-]/', '_', $item['folio']) : ('FAC_DEMO_' . ($idx + 1));
        $xml = $batchDir . '/' . $folio . '.xml';
        $pdf = $batchDir . '/' . $folio . '.pdf';

        file_put_contents($xml, '<comprobante><folio>' . $folio . '</folio><modo>demo</modo></comprobante>');
        file_put_contents($pdf, "%PDF-1.4\n% Demo PDF\n");

        $results[] = [
            'folio' => $folio,
            'xml' => basename($xml),
            'pdf' => basename($pdf),
            'status' => 'generado',
        ];
    }

    return [
        'batch_id' => $batchId,
        'files' => $results,
        'total' => count($results),
        'path' => 'storage/batches/' . $batchId,
    ];
}

function showcase_batch_history()
{
    $root = showcase_batch_ensure_dir();
    $folders = glob($root . '/batch_*', GLOB_ONLYDIR) ?: [];
    usort($folders, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    $history = [];
    foreach ($folders as $folder) {
        $xmlCount = count(glob($folder . '/*.xml') ?: []);
        $pdfCount = count(glob($folder . '/*.pdf') ?: []);
        $history[] = [
            'batch_id' => basename($folder),
            'timestamp' => filemtime($folder),
            'date' => date('Y-m-d H:i:s', filemtime($folder)),
            'xml_count' => $xmlCount,
            'pdf_count' => $pdfCount,
            'total' => $xmlCount + $pdfCount,
        ];
    }

    return $history;
}
