<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos      = session('permisos') ?? [];
$ruta          = 'admin/seguridad/perfiles';
$puedeAgregar  = !empty($permisos[$ruta]['agregar']);
$puedeEditar   = !empty($permisos[$ruta]['editar']);
$puedeEliminar = !empty($permisos[$ruta]['eliminar']);
$puedeDetalle  = !empty($permisos[$ruta]['detalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-shield-check me-2 text-primary"></i>Perfiles de Acceso</h2>
        <p class="text-muted small mb-0">Define los perfiles que determinan qué puede hacer cada usuario</p>
    </div>
    <?php if ($puedeAgregar): ?>
    <button class="btn btn-primary" id="btn-nuevo">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Perfil
    </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="input-group input-group-sm" style="max-width:350px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar perfil...">
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
                        <th>Descripción</th>
                        <th class="text-center">Estado</th>
                        <th>Creado</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between px-4 py-2 border-top" id="paginacion-wrap">
            <small class="text-muted" id="info-pag"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginacion"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPerfil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-titulo"><i class="bi bi-shield-check me-2"></i>Nuevo Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-perfil" novalidate>
                    <input type="hidden" id="perfil-id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="nombre" class="form-control" maxlength="100"
                               placeholder="Ej. Editor, Supervisor..." required>
                        <div class="form-text text-end"><span id="cnt-nombre">0</span>/100</div>
                        <div class="form-error text-danger small" id="err-nombre"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea id="descripcion" class="form-control" rows="2" maxlength="255"
                                  placeholder="Describe brevemente el propósito de este perfil..."></textarea>
                    </div>
                    <div class="mb-1">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="activo" checked>
                            <label class="form-check-label" for="activo">Perfil activo</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-guardar">
                    <i class="bi bi-check2 me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2 text-info"></i>Detalle del Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle-body"></div>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url('admin/seguridad/perfiles') ?>';
const PER_PAGE = 5;
let perfiles = [], editingId = null, currentPage = 1;

const modal    = new bootstrap.Modal(document.getElementById('modalPerfil'));
const mDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));

async function cargar() {
    try {
        const res  = await fetch(`${BASE}/listar`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        perfiles = data.data || [];
        aplicarFiltro(1);
    } catch { Toast.error('Error al cargar los perfiles'); }
}

function aplicarFiltro(page = 1) {
    const q = document.getElementById('f-busqueda').value.toLowerCase();
    const filtrados = q ? perfiles.filter(p => p.nombre?.toLowerCase().includes(q)) : perfiles;
    currentPage = page;
    const total = Math.ceil(filtrados.length / PER_PAGE);
    renderTabla(filtrados.slice((page-1)*PER_PAGE, page*PER_PAGE), filtrados.length);
    renderPaginacion(total, page, filtrados.length);
}

function renderTabla(data, total) {
    const tbody = document.getElementById('tabla-body');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>Sin registros</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(p => `
        <tr>
            <td class="px-4 text-muted small">${p.id}</td>
            <td><span class="fw-semibold">${escHtml(p.nombre)}</span></td>
            <td class="text-muted small">${escHtml(p.descripcion || '—')}</td>
            <td class="text-center">
                ${p.activo
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-secondary">Inactivo</span>'}
            </td>
            <td class="text-muted small">${formatDate(p.created_at)}</td>
            <td class="text-end px-4">
                <div class="btn-group btn-group-sm">
                    <?php if ($puedeDetalle): ?>
                    <button class="btn btn-outline-info btn-detalle" data-id="${p.id}" title="Detalle">
                        <i class="bi bi-eye"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ($puedeEditar): ?>
                    <button class="btn btn-outline-primary btn-editar"
                        data-id="${p.id}"
                        data-nombre="${escHtml(p.nombre)}"
                        data-descripcion="${escHtml(p.descripcion||'')}"
                        data-activo="${p.activo ? '1' : '0'}"
                        title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <?php endif; ?>
                    <?php if ($puedeEliminar): ?>
                    <button class="btn btn-outline-danger btn-eliminar"
                        data-id="${p.id}" data-nombre="${escHtml(p.nombre)}" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>`).join('');
    FormValidator.staggerRows(tbody);
}

function renderPaginacion(total, actual, totalReg) {
    const info = document.getElementById('info-pag');
    const nav  = document.getElementById('paginacion');
    const ini  = (actual-1)*PER_PAGE + 1;
    const fin  = Math.min(actual*PER_PAGE, totalReg);
    info.textContent = totalReg ? `Mostrando ${ini}–${fin} de ${totalReg}` : '';
    nav.innerHTML = total <= 1 ? '' : [...Array(total)].map((_,i) =>
        `<li class="page-item ${i+1===actual?'active':''}">
            <button class="page-link btn-pag" data-page="${i+1}">${i+1}</button></li>`
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
document.getElementById('nombre').addEventListener('input', function() {
    document.getElementById('cnt-nombre').textContent = this.value.length;
});

document.getElementById('btn-nuevo')?.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-shield-check me-2"></i>Nuevo Perfil';
    document.getElementById('form-perfil').reset();
    document.getElementById('cnt-nombre').textContent = '0';
    document.getElementById('activo').checked = true;
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Perfil';
    document.getElementById('nombre').value      = btn.dataset.nombre;
    document.getElementById('descripcion').value = btn.dataset.descripcion;
    document.getElementById('activo').checked    = btn.dataset.activo === '1';
    document.getElementById('cnt-nombre').textContent = btn.dataset.nombre.length;
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detalle');
    if (!btn) return;
    const p = perfiles.find(x => x.id == btn.dataset.id);
    if (!p) return;
    document.getElementById('detalle-body').innerHTML = `
        <dl class="row mb-0">
            <dt class="col-sm-4">ID</dt><dd class="col-sm-8">${p.id}</dd>
            <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">${escHtml(p.nombre)}</dd>
            <dt class="col-sm-4">Descripción</dt><dd class="col-sm-8">${escHtml(p.descripcion||'—')}</dd>
            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">${p.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'}</dd>
            <dt class="col-sm-4">Creado</dt><dd class="col-sm-8">${formatDate(p.created_at)}</dd>
        </dl>`;
    mDetalle.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;
    ConfirmDialog.show(
        `¿Eliminar el perfil <strong>${escHtml(btn.dataset.nombre)}</strong>?`,
        async () => {
            const res = await fetch(`${BASE}/eliminar/${btn.dataset.id}`, {
                method: 'DELETE', headers: {'X-Requested-With':'XMLHttpRequest'}
            });
            const data = await res.json();
            if (data.success) { Toast.success(data.mensaje); cargar(); }
            else Toast.error(data.mensaje);
        },
        {confirmLabel: 'Eliminar', confirmClass: 'btn-danger'}
    );
});

document.getElementById('btn-guardar').addEventListener('click', async function() {
    const valid = FormValidator.validate([
        { id: 'nombre', label: 'Nombre', rules: [
            'required',
            { min: 2, msg: 'Mínimo 2 caracteres' },
            { max: 100, msg: 'Máximo 100 caracteres' },
            'noHtml',
        ]},
    ], 'form-perfil');
    if (!valid) return;

    const fd = new FormData();
    fd.append('nombre',      document.getElementById('nombre').value.trim());
    fd.append('descripcion', document.getElementById('descripcion').value.trim());
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

function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatDate(s) {
    return s ? new Date(s).toLocaleDateString('es-ES',{day:'2-digit',month:'short',year:'numeric'}) : '-';
}

cargar();
</script>

<?= $this->endSection() ?>
