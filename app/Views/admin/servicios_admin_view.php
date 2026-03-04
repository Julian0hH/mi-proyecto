<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Servicios</h2>
        <p class="text-muted small mb-0">Gestiona los servicios de tu portafolio</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-primary fs-6" id="total-badge">–</span>
        <button class="btn btn-primary" id="btn-nuevo-srv">
            <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
        </button>
    </div>
</div>

<!-- FILTROS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2 px-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar por título o descripción...">
                    <button class="btn btn-outline-secondary" id="btn-clear" type="button"><i class="bi bi-x"></i></button>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select id="f-estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="f-color" class="form-select form-select-sm">
                    <option value="">Todos los colores</option>
                    <option value="primary">Azul</option>
                    <option value="info">Celeste</option>
                    <option value="success">Verde</option>
                    <option value="warning">Amarillo</option>
                    <option value="danger">Rojo</option>
                    <option value="secondary">Gris</option>
                    <option value="dark">Oscuro</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-secondary btn-sm w-100" id="btn-reset">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TABLA DE SERVICIOS (JS-driven) -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3" style="width:56px">Icono</th>
                        <th class="py-3">Título</th>
                        <th class="py-3">Descripción</th>
                        <th class="py-3" style="width:80px">Orden</th>
                        <th class="py-3" style="width:90px">Estado</th>
                        <th class="text-end pe-4 py-3" style="width:90px">Acciones</th>
                    </tr>
                </thead>
                <tbody id="servicios-grid">
                    <tr><td colspan="6" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted mb-0">Cargando servicios...</p>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer border-0 bg-transparent d-flex align-items-center justify-content-between py-2 px-4">
        <small class="text-muted" id="pag-info"></small>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacion"></ul></nav>
    </div>
</div>

<!-- MODAL CREAR/EDITAR -->
<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-srv"><i class="bi bi-gear me-2"></i>Nuevo Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-servicio" novalidate>
                <input type="hidden" id="srv-id" name="id" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" id="srv-titulo" class="form-control" required maxlength="200" placeholder="Ej: Gestión de Bases de Datos" data-vt="nohtml">
                            <div class="form-error text-danger small" id="err-srv-titulo"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción corta <span class="text-danger">*</span></label>
                            <textarea name="descripcion" id="srv-descripcion" class="form-control" rows="2" required placeholder="Descripción breve para la tarjeta" maxlength="500" data-vt="nohtml"></textarea>
                            <div class="form-error text-danger small" id="err-srv-descripcion"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción larga</label>
                            <textarea name="descripcion_larga" id="srv-descripcion-larga" class="form-control" rows="4" placeholder="Descripción detallada del servicio" maxlength="3000" data-vt="nohtml"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Icono Bootstrap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="icon-preview" class="bi bi-gear"></i></span>
                                <input type="text" name="icono" id="srv-icono" class="form-control" value="bi-gear" placeholder="bi-gear" maxlength="50" data-vt="icon">
                            </div>
                            <div class="form-text">Ver <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Color</label>
                            <select name="color" id="srv-color" class="form-select">
                                <option value="primary">Azul (primary)</option>
                                <option value="info">Celeste (info)</option>
                                <option value="success">Verde (success)</option>
                                <option value="warning">Amarillo (warning)</option>
                                <option value="danger">Rojo (danger)</option>
                                <option value="secondary">Gris (secondary)</option>
                                <option value="dark">Oscuro (dark)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" name="orden" id="srv-orden" class="form-control" min="0" max="9999" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="activo" id="srv-activo" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <!-- PREVIEW -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted">Vista previa</label>
                            <div class="card border p-3" id="srv-preview">
                                <div class="d-flex gap-3 align-items-start">
                                    <div id="prev-icon" class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                                        <i class="bi bi-gear fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 id="prev-titulo" class="fw-bold mb-1">Título del Servicio</h6>
                                        <p id="prev-desc" class="text-muted small mb-0">Descripción del servicio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-srv">
                        <i class="bi bi-check-circle me-2"></i>Guardar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modal   = new bootstrap.Modal(document.getElementById('modalServicio'));
const form    = document.getElementById('form-servicio');
const PER_PAGE = 5;
let editingId  = null;
let todosServs = []; // cache local
let currentPage = 1;

// Preview live
const srvTitulo = document.getElementById('srv-titulo');
const srvDesc   = document.getElementById('srv-descripcion');
const srvIcono  = document.getElementById('srv-icono');
const srvColor  = document.getElementById('srv-color');

function updatePreview() {
    document.getElementById('prev-titulo').textContent = srvTitulo.value || 'Título del Servicio';
    document.getElementById('prev-desc').textContent   = srvDesc.value  || 'Descripción del servicio';
    const ic = srvIcono.value || 'bi-gear';
    const cl = srvColor.value || 'primary';
    const pi = document.getElementById('prev-icon');
    pi.className = `bg-${cl} bg-opacity-10 text-${cl} rounded-circle d-flex align-items-center justify-content-center`;
    pi.style.cssText = 'width:48px;height:48px';
    pi.innerHTML = `<i class="bi ${ic} fs-5"></i>`;
    document.getElementById('icon-preview').className = `bi ${ic}`;
}
[srvTitulo, srvDesc, srvIcono, srvColor].forEach(el => el.addEventListener('input', updatePreview));

// ── Cargar desde API ──────────────────────────────────────────
async function cargar() {
    try {
        const res  = await fetch('<?= base_url('admin/servicios/listar') ?>', {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        todosServs = data.data || [];
        aplicarFiltro(1);
    } catch { Toast.error('Error al cargar servicios'); }
}

// ── Filtrar + paginar ─────────────────────────────────────────
function aplicarFiltro(page = 1) {
    const q      = document.getElementById('f-busqueda').value.toLowerCase().trim();
    const estado = document.getElementById('f-estado').value;
    const color  = document.getElementById('f-color').value;

    let filtrados = todosServs.filter(s => {
        const matchQ = !q || (s.titulo||'').toLowerCase().includes(q) || (s.descripcion||'').toLowerCase().includes(q);
        const matchE = estado === '' || String(s.activo ? '1' : '0') === estado;
        const matchC = !color || s.color === color;
        return matchQ && matchE && matchC;
    });

    currentPage = page;
    const total  = Math.ceil(filtrados.length / PER_PAGE);
    const slice  = filtrados.slice((page-1)*PER_PAGE, page*PER_PAGE);

    document.getElementById('total-badge').textContent = `${filtrados.length} total`;
    renderGrid(slice, filtrados.length, page);
    renderPaginacion(total, page, filtrados.length);
}

// ── Render tabla de servicios ─────────────────────────────────
function renderGrid(data, totalFiltrado, page) {
    const tbody = document.getElementById('servicios-grid');

    if (!data.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
            <p class="mb-0">No hay servicios con estos filtros.</p>
        </td></tr>`;
        document.getElementById('pag-info').textContent = '';
        return;
    }

    const from = (page-1)*PER_PAGE + 1;
    const to   = Math.min(page*PER_PAGE, totalFiltrado);
    document.getElementById('pag-info').textContent = `Mostrando ${from}–${to} de ${totalFiltrado} servicios`;

    tbody.innerHTML = data.map(srv => {
        const cl  = escHtml(srv.color||'primary');
        const ic  = escHtml(srv.icono||'bi-gear');
        const tit = escHtml(srv.titulo||'');
        const titMostrar = tit.length > 40 ? tit.substring(0,40)+'…' : tit;
        const desc = escHtml(srv.descripcion||'');
        const descMostrar = desc.length > 70 ? desc.substring(0,70)+'…' : desc;
        return `<tr>
            <td class="ps-4 py-2">
                <div class="bg-${cl} bg-opacity-10 text-${cl} rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px">
                    <i class="bi ${ic}"></i>
                </div>
            </td>
            <td class="py-2">
                <span class="fw-semibold small" title="${tit}">${titMostrar}</span>
            </td>
            <td class="py-2">
                <span class="text-muted small" title="${desc}">${descMostrar||'—'}</span>
            </td>
            <td class="py-2 text-center"><span class="badge bg-light text-dark border">${parseInt(srv.orden)||0}</span></td>
            <td class="py-2">
                <span class="badge bg-${srv.activo ? 'success' : 'secondary'}">${srv.activo ? 'Activo' : 'Inactivo'}</span>
            </td>
            <td class="text-end pe-4 py-2">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-editar"
                        data-id="${srv.id}"
                        data-titulo="${escHtml(srv.titulo)}"
                        data-descripcion="${escHtml(srv.descripcion||'')}"
                        data-descripcion-larga="${escHtml(srv.descripcion_larga||'')}"
                        data-icono="${ic}"
                        data-color="${cl}"
                        data-orden="${parseInt(srv.orden)||0}"
                        data-activo="${srv.activo ? '1' : '0'}"
                        title="Editar"><i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-eliminar" data-id="${srv.id}" data-nombre="${tit}" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');

    document.querySelectorAll('.btn-editar').forEach(btn => btn.addEventListener('click', () => abrirEditar(btn)));
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', () => {
            ConfirmDialog.show(`¿Eliminar el servicio "<strong>${escHtml(btn.dataset.nombre)}</strong>"?`, async () => {
                const res  = await fetch(`<?= base_url('admin/servicios/eliminar/') ?>${btn.dataset.id}`, {method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest'}});
                const data = await res.json();
                if (data.success) { Toast.success('Servicio eliminado'); cargar(); }
                else Toast.error(data.mensaje || 'Error al eliminar');
            }, {confirmLabel:'Eliminar', confirmClass:'btn-danger'});
        });
    });
}

function renderPaginacion(total, actual, totalReg) {
    const nav = document.getElementById('paginacion');
    if (total <= 1) { nav.innerHTML = ''; return; }
    const dis = c => c ? 'disabled' : '';
    let html = `
        <li class="page-item ${dis(actual<=1)}"><a class="page-link" href="#" data-page="1">«</a></li>
        <li class="page-item ${dis(actual<=1)}"><a class="page-link" href="#" data-page="${actual-1}">‹</a></li>`;
    for (let p = 1; p <= total; p++) {
        if (p===1 || p===total || Math.abs(p-actual)<=1) {
            html += `<li class="page-item ${p===actual?'active':''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        } else if (Math.abs(p-actual)===2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }
    html += `
        <li class="page-item ${dis(actual>=total)}"><a class="page-link" href="#" data-page="${actual+1}">›</a></li>
        <li class="page-item ${dis(actual>=total)}"><a class="page-link" href="#" data-page="${total}">»</a></li>`;
    nav.innerHTML = html;
    nav.querySelectorAll('[data-page]').forEach(a => a.addEventListener('click', ev => {
        ev.preventDefault();
        const p = parseInt(a.dataset.page);
        if (p >= 1 && p <= total) aplicarFiltro(p);
    }));
}

// ── Abrir modal Nuevo ─────────────────────────────────────────
document.getElementById('btn-nuevo-srv').addEventListener('click', () => {
    editingId = null;
    form.reset();
    document.getElementById('srv-icono').value = 'bi-gear';
    document.getElementById('srv-orden').value = '0';
    document.getElementById('modal-title-srv').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Servicio';
    document.getElementById('btn-submit-srv').innerHTML = '<i class="bi bi-check-circle me-2"></i>Crear Servicio';
    document.querySelectorAll('.form-error').forEach(e => e.textContent = '');
    updatePreview();
    modal.show();
});

// ── Abrir modal Editar ────────────────────────────────────────
function abrirEditar(btn) {
    editingId = btn.dataset.id;
    document.getElementById('modal-title-srv').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Servicio';
    document.getElementById('btn-submit-srv').innerHTML  = '<i class="bi bi-check-circle me-2"></i>Actualizar';
    srvTitulo.value = btn.dataset.titulo || '';
    srvDesc.value   = btn.dataset.descripcion || '';
    document.getElementById('srv-descripcion-larga').value = btn.dataset.descripcionLarga || '';
    srvIcono.value  = btn.dataset.icono || 'bi-gear';
    srvColor.value  = btn.dataset.color || 'primary';
    document.getElementById('srv-orden').value  = btn.dataset.orden || '0';
    document.getElementById('srv-activo').value = btn.dataset.activo || '1';
    document.querySelectorAll('.form-error').forEach(e => e.textContent = '');
    updatePreview();
    modal.show();
}

// ── Submit ────────────────────────────────────────────────────
form.addEventListener('submit', async e => {
    e.preventDefault();
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

    // Validación JS
    const titulo = srvTitulo.value.trim();
    const desc   = srvDesc.value.trim();
    const orden  = parseInt(document.getElementById('srv-orden').value);
    let valid = true;
    if (!titulo || titulo.length < 3) {
        document.getElementById('err-srv-titulo').textContent = 'El título es requerido (mín. 3 caracteres)';
        valid = false;
    }
    if (!desc || desc.length < 10) {
        document.getElementById('err-srv-descripcion').textContent = 'La descripción es requerida (mín. 10 caracteres)';
        valid = false;
    }
    if (isNaN(orden) || orden < 0) {
        document.getElementById('err-srv-titulo').textContent = 'El orden debe ser un número positivo';
        valid = false;
    }
    if (!valid) return;

    const btn = document.getElementById('btn-submit-srv');
    btn.disabled = true;
    const fd  = new FormData(form);
    const url = editingId
        ? `<?= base_url('admin/servicios/actualizar/') ?>${editingId}`
        : '<?= base_url('admin/servicios/crear') ?>';
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) {
            Toast.success(data.mensaje || 'Servicio guardado');
            modal.hide();
            cargar();
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([k, v]) => {
                    const el = document.getElementById(`err-srv-${k}`);
                    if (el) el.textContent = v;
                });
            }
            Toast.error(data.mensaje || 'Error al guardar');
        }
    } catch { Toast.error('Error de red'); }
    finally { btn.disabled = false; }
});

// ── Filtros con debounce ──────────────────────────────────────
let deb;
function triggerFilter() { clearTimeout(deb); deb = setTimeout(() => aplicarFiltro(1), 300); }
document.getElementById('f-busqueda').addEventListener('input', triggerFilter);
document.getElementById('f-estado').addEventListener('change', triggerFilter);
document.getElementById('f-color').addEventListener('change', triggerFilter);
document.getElementById('btn-clear').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = ''; triggerFilter();
});
document.getElementById('btn-reset').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = '';
    document.getElementById('f-estado').value   = '';
    document.getElementById('f-color').value    = '';
    aplicarFiltro(1);
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

cargar();
</script>

<?= $this->endSection() ?>
