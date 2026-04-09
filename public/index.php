<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Hub Demo - Showcase</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="page">
        <section class="hero">
            <h1>Compliance Hub Demo</h1>
            <p>Dashboard ejecutivo, validacion documental en tiempo real y procesamiento batch ZIP/XML/PDF.</p>
        </section>

        <section class="kpis">
            <article class="kpi"><div class="label">Total Facturas</div><div class="value" id="kpi-total-facturas">-</div></article>
            <article class="kpi"><div class="label">Monto Total</div><div class="value" id="kpi-monto-total">-</div></article>
            <article class="kpi"><div class="label">Pendientes Revision</div><div class="value" id="kpi-pendientes">-</div></article>
            <article class="kpi"><div class="label">Aprobadas</div><div class="value" id="kpi-aprobadas">-</div></article>
            <article class="kpi"><div class="label">Rechazadas</div><div class="value" id="kpi-rechazadas">-</div></article>
            <article class="kpi"><div class="label">Batches Hoy</div><div class="value" id="kpi-batches">-</div></article>
        </section>

        <section class="layout">
            <div class="panel">
                <h2>Facturas</h2>
                <div class="filters">
                    <input id="f-unidad" type="number" value="1" placeholder="Unidad">
                    <input id="f-contrato" type="number" value="101" placeholder="Contrato">
                    <input id="f-mes" type="number" value="3" placeholder="Mes">
                    <input id="f-anio" type="number" value="2026" placeholder="Anio">
                </div>
                <div class="row" style="display:flex;gap:8px;margin-bottom:10px;">
                    <button id="btn-filtrar">Filtrar</button>
                </div>
                <div id="factura-list" class="factura-list"></div>

                <h2 style="margin-top:16px;">Historial Batch</h2>
                <div id="batch-history" class="factura-list"></div>
            </div>

            <div class="panel">
                <h2>Validacion Documental</h2>
                <div class="documents-layout">
                    <div class="documents-sidebar">
                        <div class="sidebar-header">Documentos de la factura</div>
                        <div id="sidebar-documents" class="sidebar-documents"></div>
                    </div>

                    <div class="preview">
                        <div class="preview-header">
                            <strong id="preview-title">Documento</strong>
                            <span class="meta">Preview de demo</span>
                        </div>
                        <div id="preview-body" class="preview-body">
                            <div class="preview-card">Selecciona una factura para iniciar.</div>
                        </div>
                        <div class="preview-actions">
                            <textarea id="reject-motivo" placeholder="Motivo (solo para rechazo)"></textarea>
                            <button class="btn-ok" id="btn-aprobar">Aprobar</button>
                            <button class="btn-danger" id="btn-rechazar">Rechazar</button>
                        </div>
                    </div>
                </div>

                <h2 style="margin-top:16px;">Validacion ZIP</h2>
                <div class="row" style="display:flex;gap:8px;margin-bottom:8px;">
                    <button id="btn-batch">Procesar Batch Demo</button>
                    <button class="btn-secondary" id="btn-history">Actualizar Historial</button>
                </div>
                <div id="batch-log" class="batch-log"></div>
            </div>
        </section>
    </div>

    <script src="assets/app.js"></script>
</body>
</html>
