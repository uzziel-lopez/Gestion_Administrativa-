const fmtMoney = (n) =>
  new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
    maximumFractionDigits: 2,
  }).format(Number(n || 0));

const statusBadge = (status) => {
  if (status === "aprobado") return "approved";
  if (status === "rechazado") return "rejected";
  if (status === "vacio") return "empty";
  return "pending";
};

const statusText = (status) => {
  if (status === "aprobado") return "A";
  if (status === "rechazado") return "R";
  if (status === "vacio") return "-";
  return "P";
};

let currentFactura = null;
let currentDocType = null;

async function fetchJson(url, options = {}) {
  const res = await fetch(url, options);
  return res.json();
}

async function loadStats() {
  const data = await fetchJson("../api/dashboard/stats.php");
  if (!data.success) return;
  const s = data.data;

  document.getElementById("kpi-total-facturas").textContent = s.total_facturas;
  document.getElementById("kpi-monto-total").textContent = fmtMoney(s.monto_total);
  document.getElementById("kpi-pendientes").textContent = s.pendientes_revision;
  document.getElementById("kpi-aprobadas").textContent = s.aprobadas;
  document.getElementById("kpi-rechazadas").textContent = s.rechazadas;
  document.getElementById("kpi-batches").textContent = s.batchs_hoy;
}

async function loadFacturas() {
  const unidad = document.getElementById("f-unidad").value;
  const contrato = document.getElementById("f-contrato").value;
  const mes = document.getElementById("f-mes").value;
  const anio = document.getElementById("f-anio").value;

  const q = `?unidad=${unidad}&contrato=${contrato}&mes=${mes}&anio=${anio}`;
  const data = await fetchJson(`../api/facturas/list.php${q}`);
  const list = document.getElementById("factura-list");
  list.innerHTML = "";

  if (!data.success || !data.data.length) {
    list.innerHTML = '<div class="factura-item">No hay facturas con esos filtros.</div>';
    clearPreview();
    return;
  }

  data.data.forEach((f, idx) => {
    const el = document.createElement("div");
    el.className = "factura-item" + (idx === 0 ? " active" : "");
    el.innerHTML = `
      <strong>${f.numero}</strong>
      <div class="meta">${f.proveedor}</div>
      <div class="meta">${f.fecha} · ${fmtMoney(f.monto)} · ${f.status}</div>
    `;
    el.onclick = () => {
      document.querySelectorAll(".factura-item").forEach((i) => i.classList.remove("active"));
      el.classList.add("active");
      selectFactura(f);
    };
    list.appendChild(el);
  });

  selectFactura(data.data[0]);
}

function clearPreview() {
  currentFactura = null;
  currentDocType = null;
  document.getElementById("sidebar-documents").innerHTML = "";
  document.getElementById("preview-title").textContent = "Documento";
  document.getElementById("preview-body").innerHTML = '<div class="preview-card">Selecciona una factura para ver documentos.</div>';
}

function selectFactura(factura) {
  currentFactura = factura;
  renderDocumentsSidebar(factura.documentos);
  selectDocument(factura.documentos[0]?.tipo || null);
}

function renderDocumentsSidebar(documents) {
  const root = document.getElementById("sidebar-documents");
  root.innerHTML = "";

  documents.forEach((doc, idx) => {
    const btn = document.createElement("button");
    btn.className = "sidebar-doc-btn" + (idx === 0 ? " active" : "");
    btn.innerHTML = `
      <div class="doc-btn-content">
        <span class="doc-btn-label">${doc.label}</span>
        <span class="doc-status-badge ${statusBadge(doc.status)}">${statusText(doc.status)}</span>
      </div>
    `;
    btn.onclick = () => {
      document.querySelectorAll(".sidebar-doc-btn").forEach((x) => x.classList.remove("active"));
      btn.classList.add("active");
      selectDocument(doc.tipo);
    };
    root.appendChild(btn);
  });
}

function selectDocument(tipo) {
  currentDocType = tipo;
  const doc = currentFactura?.documentos?.find((d) => d.tipo === tipo);
  if (!doc) return;

  document.getElementById("preview-title").textContent = doc.label;
  document.getElementById("preview-body").innerHTML = `
    <div class="preview-card">
      <p><strong>Factura:</strong> ${currentFactura.numero}</p>
      <p><strong>Proveedor:</strong> ${currentFactura.proveedor}</p>
      <p><strong>Ruta:</strong> ${doc.ruta || "No disponible"}</p>
      <p><strong>Estatus:</strong> ${doc.status}</p>
      <p class="meta">Vista previa simulada para demo de portafolio.</p>
    </div>
  `;
  document.getElementById("reject-motivo").value = "";
}

async function updateStatus(status) {
  if (!currentFactura || !currentDocType) return;
  const motivo = document.getElementById("reject-motivo").value.trim();

  if (status === "rechazado" && !motivo) {
    alert("Agrega un motivo para rechazar.");
    return;
  }

  const payload = {
    factura_id: currentFactura.id,
    tipo: currentDocType,
    status,
    motivo,
  };

  const data = await fetchJson("../api/documentos/update_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });

  if (!data.success) {
    alert(data.message || "No se pudo actualizar estado");
    return;
  }

  await loadStats();
  await loadFacturas();
}

function appendBatchLog(line) {
  const log = document.getElementById("batch-log");
  log.textContent += `${line}\n`;
  log.scrollTop = log.scrollHeight;
}

async function runBatch() {
  document.getElementById("batch-log").textContent = "";
  appendBatchLog("Iniciando procesamiento batch demo...");

  const payload = {
    action: "process_batch",
    items: [
      { folio: "FAC_DEMO_001" },
      { folio: "FAC_DEMO_002" },
      { folio: "FAC_DEMO_003" },
    ],
  };

  const data = await fetchJson("../api/batch/process.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });

  if (!data.success) {
    appendBatchLog(`ERROR: ${data.message}`);
    return;
  }

  appendBatchLog(`Batch generado: ${data.data.batch_id}`);
  data.data.files.forEach((f) => {
    appendBatchLog(`- ${f.folio}: ${f.xml} / ${f.pdf}`);
  });
  appendBatchLog("Batch completado.");

  await loadStats();
  await loadBatchHistory();
}

async function loadBatchHistory() {
  const data = await fetchJson("../api/batch/process.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "list_history" }),
  });

  const root = document.getElementById("batch-history");
  root.innerHTML = "";
  if (!data.success || !data.data.length) {
    root.innerHTML = '<div class="meta">Sin ejecuciones previas.</div>';
    return;
  }

  data.data.slice(0, 8).forEach((item) => {
    const el = document.createElement("div");
    el.className = "factura-item";
    el.innerHTML = `<strong>${item.batch_id}</strong><div class="meta">${item.date} · XML ${item.xml_count} · PDF ${item.pdf_count}</div>`;
    root.appendChild(el);
  });
}

document.getElementById("btn-filtrar").addEventListener("click", loadFacturas);
document.getElementById("btn-aprobar").addEventListener("click", () => updateStatus("aprobado"));
document.getElementById("btn-rechazar").addEventListener("click", () => updateStatus("rechazado"));
document.getElementById("btn-batch").addEventListener("click", runBatch);
document.getElementById("btn-history").addEventListener("click", loadBatchHistory);

loadStats();
loadFacturas();
loadBatchHistory();
