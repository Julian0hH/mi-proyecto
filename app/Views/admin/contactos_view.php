<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Mensajes de Contacto</h2>
        <p class="text-muted small mb-0">Tabla avanzada con filtros en tiempo real</p>
    </div>
    <span class="badge bg-primary fs-6" id="total-badge">–</span>
</div>

<!-- FILTROS COMBINABLES -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Búsqueda</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="f-busqueda" class="form-control" placeholder="Nombre, email, asunto...">
                    <button class="btn btn-outline-secondary" id="btn-clear-search" type="button" title="Limpiar">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Estado</label>
                <select id="f-estado" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="leido">Leído</option>
                    <option value="respondido">Respondido</option>
                    <option value="archivado">Archivado</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">Categoría</label>
                <select id="f-categoria" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    <option value="consulta">Consulta</option>
                    <option value="presupuesto">Presupuesto</option>
                    <option value="soporte">Soporte</option>
                    <option value="colaboracion">Colaboración</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1"><i class="bi bi-calendar me-1"></i>Desde</label>
                <input type="date" id="f-fecha-desde" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1"><i class="bi bi-calendar me-1"></i>Hasta</label>
                <input type="date" id="f-fecha-hasta" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-1">
                <button class="btn btn-outline-secondary btn-sm w-100" id="btn-reset-filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TABLA -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabla-contactos">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nombre / Email</th>
                        <th>Asunto</th>
                        <th>Categoría</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th class="text-center pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-contactos">
                    <tr><td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Cargando mensajes...</p>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINACIÓN -->
    <div class="card-footer border-0 bg-transparent d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 py-3 px-4">
        <small class="text-muted" id="paginacion-info">–</small>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="paginacion-nav"></ul>
        </nav>
    </div>
</div>

<!-- MODAL VER DETALLE -->
<div class="modal fade" id="modalContacto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-envelope-open me-2"></i>Detalle del Mensaje</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-contacto-body">
                <div class="text-center py-3"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <select class="form-select form-select-sm w-auto" id="select-estado-modal">
                    <option value="pendiente">Pendiente</option>
                    <option value="leido">Leído</option>
                    <option value="respondido">Respondido</option>
                    <option value="archivado">Archivado</option>
                </select>
                <button class="btn btn-primary btn-sm" id="btn-actualizar-estado">
                    <i class="bi bi-check me-1"></i>Actualizar Estado
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let debounceTimer = null;
let activeContactId = null;
const modalContacto = new bootstrap.Modal(document.getElementById('modalContacto'));

function getFilters() {
    return {
        busqueda:    document.getElementById('f-busqueda').value.trim(),
        estado:      document.getElementById('f-estado').value,
        categoria:   document.getElementById('f-categoria').value,
        fecha_desde: document.getElementById('f-fecha-desde').value,
        fecha_hasta: document.getElementById('f-fecha-hasta').value,
    };
}

async function cargarDatos(page = 1) {
    currentPage = page;
    const tbody  = document.getElementById('tbody-contactos');
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

    const filters = getFilters();
    const params  = new URLSearchParams({ ...filters, page });

    try {
        const res  = await fetch(`<?= base_url('admin/contactos/listar') ?>?${params}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();

        if (!data.success || !data.data.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay mensajes con estos filtros</td></tr>`;
            document.getElementById('total-badge').textContent = '0';
            document.getElementById('paginacion-info').textContent = '';
            document.getElementById('paginacion-nav').innerHTML = '';
            return;
        }

        document.getElementById('total-badge').textContent = `${data.total} total`;
        const from = (page - 1) * data.per_page + 1;
        const to   = Math.min(page * data.per_page, data.total);
        document.getElementById('paginacion-info').textContent = `Mostrando ${from}–${to} de ${data.total} registros`;

        const estadoColors = {pendiente:'warning',leido:'info',respondido:'success',archivado:'secondary'};

        tbody.innerHTML = data.data.map((c, i) => `
            <tr class="${!c.leido ? 'table-unread' : ''}" data-id="${c.id}">
                <td class="ps-4 text-muted small">${from + i}</td>
                <td>
                    <div class="fw-semibold small">${escHtml(c.nombre)}</div>
                    <div class="text-muted tiny">${escHtml(c.email)}</div>
                    ${c.telefono ? `<div class="text-muted tiny">${escHtml(c.telefono)}</div>` : ''}
                </td>
                <td class="small">${escHtml(c.asunto || '–')}</td>
                <td><span class="badge bg-light text-dark border">${escHtml(c.categoria || 'consulta')}</span></td>
                <td><span class="badge bg-${estadoColors[c.estado] || 'secondary'} bg-opacity-75">${escHtml(c.estado || 'pendiente')}</span></td>
                <td class="text-muted small">${formatDate(c.created_at)}</td>
                <td class="text-center pe-4">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-ver" data-id="${c.id}" title="Ver detalle"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-danger btn-del" data-id="${c.id}" data-nombre="${escHtml(c.nombre)}" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`).join('');

        renderPaginacion(data.total_pages, page);

        // Eventos botones tabla
        tbody.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', () => verContacto(parseInt(btn.dataset.id)));
        });
        tbody.querySelectorAll('.btn-del').forEach(btn => {
            btn.addEventListener('click', () => {
                ConfirmDialog.show(`¿Eliminar el mensaje de <strong>${btn.dataset.nombre}</strong>?`, async () => {
                    const r = await fetch(`<?= base_url('admin/contactos/eliminar/') ?>${btn.dataset.id}`, {method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest'}});
                    const d = await r.json();
                    if (d.success) { Toast.success('Mensaje eliminado'); cargarDatos(currentPage); }
                    else Toast.error(d.mensaje || 'Error al eliminar');
                });
            });
        });

    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Error al cargar datos</td></tr>`;
        Toast.error('Error de conexión al cargar mensajes');
    }
}

function renderPaginacion(totalPages, page) {
    const nav = document.getElementById('paginacion-nav');
    if (totalPages <= 1) { nav.innerHTML = ''; return; }

    let html = `<li class="page-item ${page <= 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${page-1}">‹</a></li>`;

    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || Math.abs(p - page) <= 1) {
            html += `<li class="page-item ${p === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        } else if (Math.abs(p - page) === 2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }
    html += `<li class="page-item ${page >= totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${page+1}">›</a></li>`;

    nav.innerHTML = html;
    nav.querySelectorAll('[data-page]').forEach(link => {
        link.addEventListener('click', e => { e.preventDefault(); const p = parseInt(link.dataset.page); if (p >= 1 && p <= totalPages) cargarDatos(p); });
    });
}

async function verContacto(id) {
    activeContactId = id;
    document.getElementById('modal-contacto-body').innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>';
    modalContacto.show();
    const res  = await fetch(`<?= base_url('admin/contactos/ver/') ?>${id}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if (!data.success) { document.getElementById('modal-contacto-body').innerHTML = '<p class="text-danger">Error al cargar</p>'; return; }
    const c = data.data;
    document.getElementById('select-estado-modal').value = c.estado || 'pendiente';
    document.getElementById('modal-contacto-body').innerHTML = `
        <div class="row g-3">
            <div class="col-6"><strong>Nombre:</strong> ${escHtml(c.nombre)}</div>
            <div class="col-6"><strong>Email:</strong> <a href="mailto:${escHtml(c.email)}">${escHtml(c.email)}</a></div>
            ${c.telefono ? `<div class="col-6"><strong>Teléfono:</strong> ${escHtml(c.telefono)}</div>` : ''}
            <div class="col-6"><strong>Categoría:</strong> ${escHtml(c.categoria || '–')}</div>
            ${c.asunto ? `<div class="col-12"><strong>Asunto:</strong> ${escHtml(c.asunto)}</div>` : ''}
            <div class="col-12">
                <strong>Mensaje:</strong>
                <div class="bg-body-secondary rounded p-3 mt-1">${escHtml(c.mensaje)}</div>
            </div>
            <div class="col-6 text-muted small"><i class="bi bi-clock me-1"></i>${formatDate(c.created_at)}</div>
            <div class="col-6 text-muted small"><i class="bi bi-globe me-1"></i>${escHtml(c.ip_origen || '–')}</div>
        </div>`;
    cargarDatos(currentPage);
}

document.getElementById('btn-actualizar-estado').addEventListener('click', async () => {
    if (!activeContactId) return;
    const estado = document.getElementById('select-estado-modal').value;
    const fd = new FormData();
    fd.append('estado', estado);
    const res  = await fetch(`<?= base_url('admin/contactos/actualizar/') ?>${activeContactId}`, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if (data.success) { Toast.success('Estado actualizado'); modalContacto.hide(); cargarDatos(currentPage); }
    else Toast.error('Error al actualizar');
});

// Filtros con debounce
function triggerFilter() { clearTimeout(debounceTimer); debounceTimer = setTimeout(() => cargarDatos(1), 350); }
['f-busqueda','f-estado','f-categoria','f-fecha-desde','f-fecha-hasta'].forEach(id => {
    document.getElementById(id).addEventListener('input', triggerFilter);
});

document.getElementById('btn-clear-search').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = '';
    triggerFilter();
});

document.getElementById('btn-reset-filters').addEventListener('click', () => {
    ['f-busqueda','f-fecha-desde','f-fecha-hasta'].forEach(id => document.getElementById(id).value = '');
    ['f-estado','f-categoria'].forEach(id => document.getElementById(id).value = '');
    cargarDatos(1);
});

function escHtml(s) { if(!s) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatDate(s) { if(!s) return '–'; return new Date(s).toLocaleString('es-ES',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); }

// Carga inicial
cargarDatos(1);
</script>

<?= $this->endSection() ?>
