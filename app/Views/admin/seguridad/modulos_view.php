<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos = session()->get('user_permisos') ?? [];
$isAdmin  = session()->get('user_type') === 'admin';
$puedeAgregar  = $isAdmin || !empty($permisos[2]['bitAgregar']);
$puedeEditar   = $isAdmin || !empty($permisos[2]['bitEditar']);
$puedeEliminar = $isAdmin || !empty($permisos[2]['bitEliminar']);
$puedeDetalle  = $isAdmin || !empty($permisos[2]['bitDetalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-grid-3x3-gap me-2 text-success"></i>Módulos</h2>
        <p class="text-muted small mb-0">Gestión de módulos del sistema</p>
    </div>
    <?php if ($puedeAgregar): ?>
    <button class="btn btn-success" id="btn-nuevo">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Módulo
    </button>
    <?php endif; ?>
</div>

<!-- Búsqueda -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="input-group input-group-sm" style="max-width:350px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar módulo...">
            <button class="btn btn-outline-secondary" id="btn-clear"><i class="bi bi-x"></i></button>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="px-4">#</th>
                        <th>Nombre del Módulo</th>
                        <th>Fecha</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    <tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</td></tr>
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
                <h5 class="modal-title" id="modal-titulo"><i class="bi bi-grid-3x3-gap me-2"></i>Nuevo Módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-modulo" novalidate>
                    <input type="hidden" id="modulo-id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Módulo <span class="text-danger">*</span></label>
                        <input type="text" id="strNombreModulo" class="form-control" maxlength="100"
                               placeholder="Ej. Inventario, Ventas..." required data-vt="alnum">
                        <div class="form-text text-end"><span id="cnt-nombre">0</span>/100</div>
                        <div class="form-error text-danger small" id="err-strNombreModulo"></div>
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
const PER_PAGE = 5;
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
    const filtrados = q ? modulos.filter(m => m.strNombreModulo?.toLowerCase().includes(q)) : modulos;
    const total = Math.ceil(filtrados.length / PER_PAGE);
    const slice = filtrados.slice((page-1)*PER_PAGE, page*PER_PAGE);
    renderTabla(slice, filtrados.length);
    renderPaginacion(total, page, filtrados.length);
}

function renderTabla(data, total) {
    const tbody = document.getElementById('tabla-body');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>Sin registros</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(m => `
        <tr>
            <td class="px-4 text-muted small">${m.id}</td>
            <td><span class="fw-semibold"><i class="bi bi-box me-2 text-success"></i>${escHtml(m.strNombreModulo)}</span></td>
            <td class="text-muted small">${formatDate(m.created_at)}</td>
            <td class="text-end px-4">
                <div class="btn-group btn-group-sm">
                    <?php if ($puedeDetalle): ?>
                    <button class="btn btn-outline-info btn-detalle" data-id="${m.id}" title="Detalle"><i class="bi bi-eye"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEditar): ?>
                    <button class="btn btn-outline-primary btn-editar" data-id="${m.id}" data-nombre="${escHtml(m.strNombreModulo)}" title="Editar"><i class="bi bi-pencil"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEliminar): ?>
                    <button class="btn btn-outline-danger btn-eliminar" data-id="${m.id}" data-nombre="${escHtml(m.strNombreModulo)}" title="Eliminar"><i class="bi bi-trash"></i></button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>`).join('');
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

let debounce;
document.getElementById('f-busqueda').addEventListener('input', () => {
    clearTimeout(debounce); debounce = setTimeout(() => aplicarFiltro(1), 300);
});
document.getElementById('btn-clear').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = ''; aplicarFiltro(1);
});
document.getElementById('strNombreModulo').addEventListener('input', function() {
    document.getElementById('cnt-nombre').textContent = this.value.length;
});

document.getElementById('btn-nuevo')?.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-grid-3x3-gap me-2"></i>Nuevo Módulo';
    document.getElementById('form-modulo').reset();
    document.getElementById('cnt-nombre').textContent = '0';
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Módulo';
    document.getElementById('strNombreModulo').value = btn.dataset.nombre;
    document.getElementById('cnt-nombre').textContent = btn.dataset.nombre.length;
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
            <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">${escHtml(m.strNombreModulo)}</dd>
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

document.getElementById('btn-guardar').addEventListener('click', async () => {
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    const nombre = document.getElementById('strNombreModulo').value.trim();
    if (!nombre) { document.getElementById('err-strNombreModulo').textContent = 'El nombre es obligatorio'; return; }
    const fd = new FormData();
    fd.append('strNombreModulo', nombre);
    const url = editingId ? `${BASE}/actualizar/${editingId}` : `${BASE}/crear`;
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) { Toast.success(data.mensaje); modal.hide(); cargar(); }
        else { if (data.errors) Object.entries(data.errors).forEach(([k,v]) => { const el = document.getElementById('err-'+k); if(el) el.textContent=v; }); else Toast.error(data.mensaje); }
    } catch { Toast.error('Error de red'); }
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatDate(s) { return s ? new Date(s).toLocaleDateString('es-ES',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }

cargar();
</script>

<?= $this->endSection() ?>
