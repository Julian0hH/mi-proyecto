<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Mensajes de Contacto</h2>
        <p class="text-muted small mb-0">Tabla avanzada con filtros en tiempo real</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-primary btn-sm" id="btn-nuevo-contacto">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Contacto
        </button>
        <span class="badge bg-primary fs-6" id="total-badge">–</span>
    </div>
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

<!-- MODAL EDITAR CONTACTO -->
<div class="modal fade" id="modalContacto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-contacto-titulo"><i class="bi bi-pencil-square me-2"></i>Editar Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-contacto-loading" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
                <form id="form-editar-contacto" style="display:none">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input type="text" class="form-control form-control-sm" id="edit-nombre" maxlength="100" data-vt="name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" class="form-control form-control-sm" id="edit-email" maxlength="150" data-vt="nohtml">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Teléfono</label>
                            <input type="text" class="form-control form-control-sm" id="edit-telefono" placeholder="Opcional" maxlength="20" data-vt="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Categoría</label>
                            <select class="form-select form-select-sm" id="edit-categoria">
                                <option value="consulta">Consulta</option>
                                <option value="presupuesto">Presupuesto</option>
                                <option value="soporte">Soporte</option>
                                <option value="colaboracion">Colaboración</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Asunto</label>
                            <input type="text" class="form-control form-control-sm" id="edit-asunto" maxlength="200" data-vt="nohtml">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Mensaje</label>
                            <textarea class="form-control form-control-sm" id="edit-mensaje" rows="4" maxlength="2000" data-vt="nohtml"></textarea>
                        </div>
                        <div class="col-md-6" id="wrap-estado">
                            <label class="form-label small fw-semibold">Estado</label>
                            <select class="form-select form-select-sm" id="edit-estado">
                                <option value="pendiente">Pendiente</option>
                                <option value="leido">Leído</option>
                                <option value="respondido">Respondido</option>
                                <option value="archivado">Archivado</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end pb-1" id="wrap-meta">
                            <div class="text-muted small lh-lg">
                                <div id="edit-created-at"></div>
                                <div id="edit-ip"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary btn-sm" id="btn-guardar-contacto">
                    <i class="bi bi-floppy me-1"></i>Guardar cambios
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let debounceTimer = null;
let activeContactId = null;
let modalMode = 'editar'; // 'editar' | 'crear'
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
                        <button class="btn btn-outline-secondary btn-ver" data-id="${c.id}" title="Ver detalle"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-outline-primary btn-edit" data-id="${c.id}" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger btn-del" data-id="${c.id}" data-nombre="${escHtml(c.nombre)}" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`).join('');

        renderPaginacion(data.total_pages, page);

        // Eventos botones tabla
        tbody.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', () => verContacto(parseInt(btn.dataset.id)));
        });
        tbody.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => abrirEditar(parseInt(btn.dataset.id)));
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

    const dis = (cond) => cond ? 'disabled' : '';

    let html = `
        <li class="page-item ${dis(page <= 1)}"><a class="page-link" href="#" data-page="1" title="Primera página">«</a></li>
        <li class="page-item ${dis(page <= 1)}"><a class="page-link" href="#" data-page="${page - 1}" title="Anterior">‹</a></li>`;

    for (let p = 1; p <= totalPages; p++) {
        if (p === 1 || p === totalPages || Math.abs(p - page) <= 1) {
            html += `<li class="page-item ${p === page ? 'active' : ''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        } else if (Math.abs(p - page) === 2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }

    html += `
        <li class="page-item ${dis(page >= totalPages)}"><a class="page-link" href="#" data-page="${page + 1}" title="Siguiente">›</a></li>
        <li class="page-item ${dis(page >= totalPages)}"><a class="page-link" href="#" data-page="${totalPages}" title="Última página">»</a></li>`;

    nav.innerHTML = html;
    nav.querySelectorAll('[data-page]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const p = parseInt(link.dataset.page);
            if (p >= 1 && p <= totalPages) cargarDatos(p);
        });
    });
}

// ── Validación de campos del modal ──────────────────────────
function validarCampos() {
    limpiarValidacion();
    let ok = true;
    const setErr = (id, msg) => {
        const el = document.getElementById(id);
        el.classList.add('is-invalid');
        let fb = el.parentNode.querySelector('.invalid-feedback');
        if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; el.after(fb); }
        fb.textContent = msg;
        ok = false;
    };
    const nombre   = document.getElementById('edit-nombre').value.trim();
    const email    = document.getElementById('edit-email').value.trim();
    const telefono = document.getElementById('edit-telefono').value.trim();
    const asunto   = document.getElementById('edit-asunto').value.trim();
    const mensaje  = document.getElementById('edit-mensaje').value.trim();

    if (!nombre)                                              setErr('edit-nombre', 'El nombre es requerido');
    else if (nombre.length < 2)                              setErr('edit-nombre', 'Mínimo 2 caracteres');
    else if (nombre.length > 100)                            setErr('edit-nombre', 'Máximo 100 caracteres');
    else if (!/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-']+$/.test(nombre)) setErr('edit-nombre', 'Solo letras, espacios y guiones');

    if (!email)                                              setErr('edit-email', 'El email es requerido');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email))  setErr('edit-email', 'Formato de email inválido');
    else if (email.length > 150)                             setErr('edit-email', 'Máximo 150 caracteres');

    if (telefono && !/^[0-9+\-\s]{7,20}$/.test(telefono))   setErr('edit-telefono', 'Solo dígitos, +, - (7–20 caracteres)');
    if (asunto.length > 200)                                 setErr('edit-asunto', 'Máximo 200 caracteres');
    if (/<[^>]+>/.test(asunto))                              setErr('edit-asunto', 'No se permiten etiquetas HTML');

    if (!mensaje)                                            setErr('edit-mensaje', 'El mensaje es requerido');
    else if (mensaje.length < 10)                            setErr('edit-mensaje', 'Mínimo 10 caracteres');
    else if (mensaje.length > 2000)                          setErr('edit-mensaje', 'Máximo 2000 caracteres');

    return ok;
}
function limpiarValidacion() {
    ['edit-nombre','edit-email','edit-telefono','edit-asunto','edit-mensaje'].forEach(id => {
        document.getElementById(id).classList.remove('is-invalid','is-valid');
    });
}

// ── Abrir modal en modo Crear ────────────────────────────────
function abrirCrear() {
    modalMode = 'crear';
    activeContactId = null;
    document.getElementById('modal-contacto-titulo').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Contacto';
    document.getElementById('modal-contacto-loading').style.display = 'none';
    document.getElementById('form-editar-contacto').style.display   = '';
    document.getElementById('wrap-estado').style.display = 'none';
    document.getElementById('wrap-meta').style.display   = 'none';
    ['edit-nombre','edit-email','edit-telefono','edit-asunto','edit-mensaje'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('edit-categoria').value = 'consulta';
    limpiarValidacion();
    modalContacto.show();
}

// ── Abrir modal en modo Editar ───────────────────────────────
function abrirEditar(id) {
    modalMode = 'editar';
    document.getElementById('modal-contacto-titulo').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Contacto';
    document.getElementById('wrap-estado').style.display = '';
    document.getElementById('wrap-meta').style.display   = '';
    limpiarValidacion();
    verContacto(id);
}

// ── Abrir modal en modo Ver (solo lectura + marcar leído) ────
async function verContacto(id) {
    activeContactId = id;
    document.getElementById('modal-contacto-loading').style.display = '';
    document.getElementById('form-editar-contacto').style.display = 'none';
    modalContacto.show();

    try {
        const res  = await fetch(`<?= base_url('admin/contactos/ver/') ?>${id}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (!data.success) { Toast.error('Error al cargar el contacto'); modalContacto.hide(); return; }
        const c = data.data;

        document.getElementById('edit-nombre').value    = c.nombre    || '';
        document.getElementById('edit-email').value     = c.email     || '';
        document.getElementById('edit-telefono').value  = c.telefono  || '';
        document.getElementById('edit-asunto').value    = c.asunto    || '';
        document.getElementById('edit-mensaje').value   = c.mensaje   || '';
        document.getElementById('edit-categoria').value = c.categoria || 'consulta';
        document.getElementById('edit-estado').value    = c.estado    || 'pendiente';
        document.getElementById('edit-created-at').innerHTML = `<i class="bi bi-clock me-1"></i>${formatDate(c.created_at)}`;
        document.getElementById('edit-ip').innerHTML         = `<i class="bi bi-globe me-1"></i>${escHtml(c.ip_origen || '–')}`;

        document.getElementById('modal-contacto-loading').style.display = 'none';
        document.getElementById('form-editar-contacto').style.display   = '';

        cargarDatos(currentPage);
    } catch (err) {
        Toast.error('Error de conexión');
        modalContacto.hide();
    }
}

// ── Guardar (crear o editar) ─────────────────────────────────
document.getElementById('btn-guardar-contacto').addEventListener('click', async () => {
    if (!validarCampos()) return;

    const fd = new FormData();
    fd.append('nombre',    document.getElementById('edit-nombre').value.trim());
    fd.append('email',     document.getElementById('edit-email').value.trim());
    fd.append('telefono',  document.getElementById('edit-telefono').value.trim());
    fd.append('asunto',    document.getElementById('edit-asunto').value.trim());
    fd.append('mensaje',   document.getElementById('edit-mensaje').value.trim());
    fd.append('categoria', document.getElementById('edit-categoria').value);
    if (modalMode === 'editar') fd.append('estado', document.getElementById('edit-estado').value);

    const url = modalMode === 'crear'
        ? `<?= base_url('admin/contactos/crear') ?>`
        : `<?= base_url('admin/contactos/actualizar/') ?>${activeContactId}`;

    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();

        if (data.success) {
            Toast.success(modalMode === 'crear' ? 'Contacto creado correctamente' : 'Contacto actualizado');
            modalContacto.hide();
            cargarDatos(currentPage);
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, msg]) => {
                    const map = {nombre:'edit-nombre', email:'edit-email', telefono:'edit-telefono', asunto:'edit-asunto', mensaje:'edit-mensaje'};
                    if (map[field]) {
                        const el = document.getElementById(map[field]);
                        el.classList.add('is-invalid');
                        let fb = el.parentNode.querySelector('.invalid-feedback');
                        if (!fb) { fb = document.createElement('div'); fb.className = 'invalid-feedback'; el.after(fb); }
                        fb.textContent = msg;
                    }
                });
            } else {
                Toast.error(data.mensaje || 'Error al guardar los cambios');
            }
        }
    } catch (err) {
        Toast.error('Error de conexión');
    }
});

document.getElementById('btn-nuevo-contacto').addEventListener('click', abrirCrear);

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
