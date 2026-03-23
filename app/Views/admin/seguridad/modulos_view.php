<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos      = session('permisos') ?? [];
$ruta          = 'admin/seguridad/modulos';
$puedeAgregar  = !empty($permisos[$ruta]['agregar']);
$puedeEditar   = !empty($permisos[$ruta]['editar']);
$puedeEliminar = !empty($permisos[$ruta]['eliminar']);
$puedeDetalle  = !empty($permisos[$ruta]['detalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-grid-3x3-gap me-2 text-success"></i>Páginas Protegidas</h2>
        <p class="text-muted small mb-0">Define qué páginas del admin están bajo control de acceso</p>
    </div>
    <?php if ($puedeAgregar): ?>
    <button class="btn btn-success" id="btn-nuevo">
        <i class="bi bi-plus-circle me-1"></i>Nueva Página
    </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="input-group input-group-sm" style="max-width:350px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar módulo...">
            <button class="btn btn-outline-secondary" id="btn-clear"><i class="bi bi-x"></i></button>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="px-4">#</th>
                        <th>Nombre</th>
                        <th>Ruta base</th>
                        <th>Grupo</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between px-4 py-2 border-top">
            <small class="text-muted" id="info-pag"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginacion"></ul></nav>
        </div>
    </div>
</div>

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalModulo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-titulo"><i class="bi bi-grid-3x3-gap me-2"></i>Nueva Página</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-modulo" novalidate>
                    <input type="hidden" id="modulo-id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" id="nombre" class="form-control" maxlength="100"
                                   placeholder="Ej. Inventario, Reportes..." required>
                            <div class="form-text text-end"><span id="cnt-nombre">0</span>/100</div>
                            <div class="form-error text-danger small" id="err-nombre"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ruta base <span class="text-danger">*</span></label>
                            <input type="text" id="ruta" class="form-control" maxlength="200"
                                   placeholder="admin/inventario">
                            <div class="form-text">
                                Ruta completa (prefijo URL). Ej: <code>admin/proyectos</code>, <code>admin/seguridad/perfiles</code>
                            </div>
                            <div class="form-error text-danger small" id="err-ruta"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ícono Bootstrap</label>
                            <div class="input-group">
                                <span class="input-group-text" id="icono-preview"><i class="bi bi-circle"></i></span>
                                <input type="text" id="icono" class="form-control" maxlength="60"
                                       placeholder="bi-folder" value="bi-circle">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Grupo (sidebar)</label>
                            <input type="text" id="grupo" class="form-control" maxlength="100"
                                   placeholder="General, Seguridad..." value="General">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" id="orden" class="form-control" min="0" max="999" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo" checked>
                                <label class="form-check-label" for="activo">Activo</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-guardar">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2 text-info"></i>Detalle del Módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle-body"></div>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url('admin/seguridad/modulos') ?>';
const PER_PAGE = 8;
let modulos = [], editingId = null;

const modal    = new bootstrap.Modal(document.getElementById('modalModulo'));
const mDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));

async function cargar() {
    try {
        const res  = await fetch(`${BASE}/listar`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        modulos = data.data || [];
        aplicarFiltro(1);
    } catch { Toast.error('Error al cargar los módulos'); }
}

function aplicarFiltro(page = 1) {
    const q = document.getElementById('f-busqueda').value.toLowerCase();
    const filtrados = q ? modulos.filter(m =>
        m.nombre?.toLowerCase().includes(q) || m.ruta?.toLowerCase().includes(q) || m.grupo?.toLowerCase().includes(q)
    ) : modulos;
    const total = Math.ceil(filtrados.length / PER_PAGE);
    renderTabla(filtrados.slice((page-1)*PER_PAGE, page*PER_PAGE), filtrados.length, page);
    renderPaginacion(total, page, filtrados.length);
}

function renderTabla(data, totalReg, page) {
    const tbody = document.getElementById('tabla-body');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>Sin registros</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(m => `
        <tr>
            <td class="px-4 text-muted small">${m.id}</td>
            <td>
                <i class="${escHtml(m.icono||'bi-circle')} me-2 text-success"></i>
                <span class="fw-semibold">${escHtml(m.nombre)}</span>
            </td>
            <td><code class="small">${escHtml(m.ruta)}</code></td>
            <td><span class="badge bg-secondary text-white">${escHtml(m.grupo||'—')}</span></td>
            <td class="text-center">
                ${m.activo
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-secondary">Inactivo</span>'}
            </td>
            <td class="text-end px-4">
                <div class="btn-group btn-group-sm">
                    <?php if ($puedeDetalle): ?>
                    <button class="btn btn-outline-info btn-detalle" data-id="${m.id}" title="Detalle"><i class="bi bi-eye"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEditar): ?>
                    <button class="btn btn-outline-primary btn-editar"
                        data-id="${m.id}"
                        data-nombre="${escHtml(m.nombre)}"
                        data-ruta="${escHtml(m.ruta||'')}"
                        data-icono="${escHtml(m.icono||'bi-circle')}"
                        data-grupo="${escHtml(m.grupo||'General')}"
                        data-orden="${m.orden||0}"
                        data-activo="${m.activo?'1':'0'}"
                        title="Editar"><i class="bi bi-pencil"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEliminar): ?>
                    <button class="btn btn-outline-danger btn-eliminar" data-id="${m.id}" data-nombre="${escHtml(m.nombre)}" title="Eliminar"><i class="bi bi-trash"></i></button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>`).join('');
    FormValidator.staggerRows(tbody);
}

function renderPaginacion(total, actual, totalReg) {
    const info = document.getElementById('info-pag');
    const nav  = document.getElementById('paginacion');
    info.textContent = totalReg ? `Mostrando ${(actual-1)*PER_PAGE+1}–${Math.min(actual*PER_PAGE,totalReg)} de ${totalReg}` : '';
    nav.innerHTML = total <= 1 ? '' : [...Array(total)].map((_,i) =>
        `<li class="page-item ${i+1===actual?'active':''}"><button class="page-link btn-pag" data-page="${i+1}">${i+1}</button></li>`
    ).join('');
    nav.querySelectorAll('.btn-pag').forEach(b => b.addEventListener('click', () => aplicarFiltro(+b.dataset.page)));
}

// Preview ícono en tiempo real
document.getElementById('icono').addEventListener('input', function() {
    const el = document.querySelector('#icono-preview i');
    el.className = this.value.trim() || 'bi-circle';
});

let debounce;
document.getElementById('f-busqueda').addEventListener('input', () => {
    clearTimeout(debounce); debounce = setTimeout(() => aplicarFiltro(1), 300);
});
document.getElementById('btn-clear').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = ''; aplicarFiltro(1);
});
document.getElementById('nombre').addEventListener('input', function() {
    document.getElementById('cnt-nombre').textContent = this.value.length;
});

document.getElementById('btn-nuevo')?.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-grid-3x3-gap me-2"></i>Nueva Página';
    document.getElementById('form-modulo').reset();
    document.getElementById('cnt-nombre').textContent = '0';
    document.getElementById('icono').value  = 'bi-circle';
    document.getElementById('grupo').value  = 'General';
    document.getElementById('orden').value  = '0';
    document.getElementById('activo').checked = true;
    document.querySelector('#icono-preview i').className = 'bi-circle';
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Página';
    document.getElementById('nombre').value  = btn.dataset.nombre;
    document.getElementById('ruta').value    = btn.dataset.ruta;
    document.getElementById('icono').value   = btn.dataset.icono;
    document.getElementById('grupo').value   = btn.dataset.grupo;
    document.getElementById('orden').value   = btn.dataset.orden;
    document.getElementById('activo').checked = btn.dataset.activo === '1';
    document.getElementById('cnt-nombre').textContent = btn.dataset.nombre.length;
    document.querySelector('#icono-preview i').className = btn.dataset.icono;
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detalle');
    if (!btn) return;
    const m = modulos.find(x => x.id == btn.dataset.id);
    if (!m) return;
    document.getElementById('detalle-body').innerHTML = `
        <dl class="row mb-0">
            <dt class="col-sm-4">ID</dt><dd class="col-sm-8">${m.id}</dd>
            <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">${escHtml(m.nombre)}</dd>
            <dt class="col-sm-4">Ruta</dt><dd class="col-sm-8"><code>${escHtml(m.ruta)}</code></dd>
            <dt class="col-sm-4">Ícono</dt><dd class="col-sm-8"><i class="${escHtml(m.icono||'bi-circle')} me-1"></i><code>${escHtml(m.icono||'')}</code></dd>
            <dt class="col-sm-4">Grupo</dt><dd class="col-sm-8">${escHtml(m.grupo||'—')}</dd>
            <dt class="col-sm-4">Orden</dt><dd class="col-sm-8">${m.orden}</dd>
            <dt class="col-sm-4">Estado</dt><dd class="col-sm-8">${m.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'}</dd>
            <dt class="col-sm-4">Creado</dt><dd class="col-sm-8">${formatDate(m.created_at)}</dd>
        </dl>`;
    mDetalle.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;
    ConfirmDialog.show(`¿Eliminar el módulo <strong>${escHtml(btn.dataset.nombre)}</strong>?`, async () => {
        const res  = await fetch(`${BASE}/eliminar/${btn.dataset.id}`, {method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) { Toast.success(data.mensaje); cargar(); }
        else Toast.error(data.mensaje);
    }, {confirmLabel:'Eliminar', confirmClass:'btn-danger'});
});

document.getElementById('btn-guardar').addEventListener('click', async function() {
    const valid = FormValidator.validate([
        { id: 'nombre', label: 'Nombre', rules: ['required', {min:3}, {max:100}, 'noHtml'] },
        { id: 'ruta',   label: 'Ruta',   rules: [
            'required',
            { min: 3, msg: 'Mínimo 3 caracteres' },
            { max: 200 },
            'noHtml',
            { fn: v => /^[a-zA-Z0-9\-\/]+$/.test(v) || 'Solo letras, números, guiones y barras' },
        ]},
    ], 'form-modulo');
    if (!valid) return;

    const fd = new FormData();
    fd.append('nombre', document.getElementById('nombre').value.trim());
    fd.append('ruta',   document.getElementById('ruta').value.trim());
    fd.append('icono',  document.getElementById('icono').value.trim());
    fd.append('grupo',  document.getElementById('grupo').value.trim());
    fd.append('orden',  document.getElementById('orden').value);
    if (document.getElementById('activo').checked) fd.append('activo', '1');

    const url = editingId ? `${BASE}/actualizar/${editingId}` : `${BASE}/crear`;
    FormValidator.btnLoad(this);
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) { Toast.success(data.mensaje); modal.hide(); cargar(); }
        else {
            if (data.errors) Object.entries(data.errors).forEach(([k,v]) => FormValidator.setError(k, v));
            else Toast.error(data.mensaje);
        }
    } catch { Toast.error('Error de red'); }
    finally { FormValidator.btnDone(this); }
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatDate(s) { return s ? new Date(s).toLocaleDateString('es-ES',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }

cargar();
</script>

<?= $this->endSection() ?>
