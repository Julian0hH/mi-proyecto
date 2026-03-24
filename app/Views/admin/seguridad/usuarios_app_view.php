<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos      = session('permisos') ?? [];
$ruta          = 'admin/seguridad/usuarios';
$puedeAgregar  = !empty($permisos[$ruta]['agregar']);
$puedeEditar   = !empty($permisos[$ruta]['editar']);
$puedeEliminar = !empty($permisos[$ruta]['eliminar']);
$puedeDetalle  = !empty($permisos[$ruta]['detalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-people me-2 text-info"></i>Usuarios del Sistema</h2>
        <p class="text-muted small mb-0">Gestión de usuarios con acceso al panel de administración</p>
    </div>
    <?php if ($puedeAgregar): ?>
    <button class="btn btn-info text-white" id="btn-nuevo">
        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
    </button>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="input-group input-group-sm" style="max-width:350px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar usuario...">
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
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Perfil</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    <tr><td colspan="7" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Cargando...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between px-4 py-2 border-top">
            <small class="text-muted" id="info-pag"></small>
            <nav><ul class="pagination pagination-sm mb-0" id="paginacion"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-titulo"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-usuario" novalidate>
                    <div class="row g-3">
                        <!-- Foto de perfil (solo en edición) -->
                        <div class="col-12 text-center" id="foto-section" style="display:none">
                            <div class="d-inline-block position-relative">
                                <img id="foto-preview" src="" alt="Foto"
                                     class="rounded-circle border border-2"
                                     style="width:80px;height:80px;object-fit:cover;display:none">
                                <div id="foto-placeholder" class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center mx-auto"
                                     style="width:80px;height:80px;font-size:36px">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-camera me-1"></i>Cambiar foto
                                    <input type="file" id="foto-input" accept="image/jpeg,image/png,image/webp" style="display:none">
                                </label>
                                <small class="text-muted d-block mt-1">JPG, PNG o WEBP · Máx 2 MB</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" id="nombre" class="form-control" maxlength="150" placeholder="Nombre del usuario" required>
                            <div class="form-error text-danger small" id="err-nombre"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" class="form-control" maxlength="200" placeholder="correo@ejemplo.com" required>
                            <div class="form-error text-danger small" id="err-email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña <span id="lbl-pwd-req" class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" maxlength="100" placeholder="Mín. 6 caracteres">
                                <button class="btn btn-outline-secondary" type="button" id="btn-ver-pwd">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="hint-pwd" style="display:none">Dejar vacío para no cambiar</small>
                            <div class="form-error text-danger small" id="err-password"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" id="telefono" class="form-control" maxlength="20" placeholder="+502 1234-5678">
                            <div class="form-error text-danger small" id="err-telefono"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
                            <select id="perfil_id" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($perfiles as $pf): ?>
                                <option value="<?= $pf['id'] ?>"><?= esc($pf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-error text-danger small" id="err-perfil_id"></div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="activo" checked>
                                <label class="form-check-label" for="activo">Cuenta activa</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-info text-white" id="btn-guardar">
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
                <h5 class="modal-title"><i class="bi bi-person-circle me-2 text-info"></i>Detalle del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalle-body"></div>
        </div>
    </div>
</div>

<script>
const BASE = '<?= base_url('admin/seguridad/usuarios') ?>';
const PER_PAGE = 5;
let usuarios = [], editingId = null;

const modal    = new bootstrap.Modal(document.getElementById('modalUsuario'));
const mDetalle = new bootstrap.Modal(document.getElementById('modalDetalle'));

document.getElementById('btn-ver-pwd').addEventListener('click', function() {
    const inp = document.getElementById('password');
    const icon = this.querySelector('i');
    if (inp.type === 'password') { inp.type='text'; icon.className='bi bi-eye-slash'; }
    else { inp.type='password'; icon.className='bi bi-eye'; }
});

async function cargar() {
    try {
        const res  = await fetch(`${BASE}/listar`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        usuarios = data.data || [];
        aplicarFiltro(1);
    } catch { Toast.error('Error al cargar usuarios'); }
}

function aplicarFiltro(page = 1) {
    const q = document.getElementById('f-busqueda').value.toLowerCase();
    const filtrados = q ? usuarios.filter(u =>
        u.nombre?.toLowerCase().includes(q) || u.email?.toLowerCase().includes(q)
    ) : usuarios;
    const total = Math.ceil(filtrados.length / PER_PAGE);
    renderTabla(filtrados.slice((page-1)*PER_PAGE, page*PER_PAGE), filtrados.length, page);
    renderPaginacion(total, page, filtrados.length);
}

function renderTabla(data, totalReg, page) {
    const tbody = document.getElementById('tabla-body');
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-inbox me-2"></i>Sin registros</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(u => `
        <tr>
            <td class="px-4 text-muted small">${u.id}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle overflow-hidden bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:32px;height:32px;font-size:16px">
                        ${u.foto_url
                            ? `<img src="${escHtml(u.foto_url)}" style="width:100%;height:100%;object-fit:cover" alt="">`
                            : '<i class="bi bi-person"></i>'}
                    </div>
                    <span class="fw-semibold">${escHtml(u.nombre)}</span>
                </div>
            </td>
            <td class="text-muted small">${escHtml(u.email||'-')}</td>
            <td class="text-muted small">${escHtml(u.telefono||'-')}</td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary">${escHtml(u.perfiles?.nombre||'-')}</span></td>
            <td class="text-center">
                ${u.activo
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-danger">Inactivo</span>'}
            </td>
            <td class="text-end px-4">
                <div class="btn-group btn-group-sm">
                    <?php if ($puedeDetalle): ?>
                    <button class="btn btn-outline-info btn-detalle" data-id="${u.id}" title="Detalle"><i class="bi bi-eye"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEditar): ?>
                    <button class="btn btn-outline-primary btn-editar"
                        data-id="${u.id}"
                        data-nombre="${escHtml(u.nombre)}"
                        data-email="${escHtml(u.email||'')}"
                        data-telefono="${escHtml(u.telefono||'')}"
                        data-perfil="${u.perfil_id||''}"
                        data-activo="${u.activo?'1':'0'}"
                        data-foto="${escHtml(u.foto_url||'')}"
                        title="Editar"><i class="bi bi-pencil"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEliminar): ?>
                    <button class="btn btn-outline-danger btn-eliminar" data-id="${u.id}" data-nombre="${escHtml(u.nombre)}" title="Eliminar"><i class="bi bi-trash"></i></button>
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
    if (total <= 1) { nav.innerHTML = ''; return; }
    let items = `<li class="page-item ${actual===1?'disabled':''}">
        <button class="page-link btn-pag" data-page="${actual-1}">&laquo;&laquo;</button></li>`;
    items += [...Array(total)].map((_,i) =>
        `<li class="page-item ${i+1===actual?'active':''}"><button class="page-link btn-pag" data-page="${i+1}">${i+1}</button></li>`
    ).join('');
    items += `<li class="page-item ${actual===total?'disabled':''}">
        <button class="page-link btn-pag" data-page="${actual+1}">&raquo;&raquo;</button></li>`;
    nav.innerHTML = items;
    nav.querySelectorAll('.btn-pag').forEach(b => b.addEventListener('click', () => {
        const p = +b.dataset.page;
        if (p >= 1 && p <= total) aplicarFiltro(p);
    }));
}

let debounce;
document.getElementById('f-busqueda').addEventListener('input', () => {
    clearTimeout(debounce); debounce = setTimeout(() => aplicarFiltro(1), 300);
});
document.getElementById('btn-clear').addEventListener('click', () => {
    document.getElementById('f-busqueda').value = ''; aplicarFiltro(1);
});

document.getElementById('btn-nuevo')?.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-person-plus me-2"></i>Nuevo Usuario';
    document.getElementById('form-usuario').reset();
    document.getElementById('lbl-pwd-req').style.display = '';
    document.getElementById('hint-pwd').style.display = 'none';
    document.getElementById('activo').checked = true;
    document.getElementById('foto-section').style.display = 'none';
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.getElementById('foto-input').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('foto-preview').src = e.target.result;
        document.getElementById('foto-preview').style.display = '';
        document.getElementById('foto-placeholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
    document.getElementById('nombre').value   = btn.dataset.nombre;
    document.getElementById('email').value    = btn.dataset.email;
    document.getElementById('telefono').value = btn.dataset.telefono;
    document.getElementById('perfil_id').value = btn.dataset.perfil;
    document.getElementById('activo').checked  = btn.dataset.activo === '1';
    document.getElementById('password').value  = '';
    document.getElementById('lbl-pwd-req').style.display = 'none';
    document.getElementById('hint-pwd').style.display = '';
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    // Foto
    document.getElementById('foto-section').style.display = '';
    document.getElementById('foto-input').value = '';
    const fotoUrl = btn.dataset.foto;
    if (fotoUrl) {
        document.getElementById('foto-preview').src = fotoUrl;
        document.getElementById('foto-preview').style.display = '';
        document.getElementById('foto-placeholder').style.display = 'none';
    } else {
        document.getElementById('foto-preview').style.display = 'none';
        document.getElementById('foto-placeholder').style.display = '';
    }
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detalle');
    if (!btn) return;
    const u = usuarios.find(x => x.id == btn.dataset.id);
    if (!u) return;
    document.getElementById('detalle-body').innerHTML = `
        <dl class="row mb-0">
            <dt class="col-sm-4">ID</dt><dd class="col-sm-8">${u.id}</dd>
            <dt class="col-sm-4">Nombre</dt><dd class="col-sm-8">${escHtml(u.nombre)}</dd>
            <dt class="col-sm-4">Email</dt><dd class="col-sm-8">${escHtml(u.email||'-')}</dd>
            <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">${escHtml(u.telefono||'-')}</dd>
            <dt class="col-sm-4">Perfil</dt><dd class="col-sm-8">${escHtml(u.perfiles?.nombre||'-')}</dd>
            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">${u.activo ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'}</dd>
            <dt class="col-sm-4">Último login</dt><dd class="col-sm-8">${formatDate(u.ultimo_login)}</dd>
            <dt class="col-sm-4">Creado</dt><dd class="col-sm-8">${formatDate(u.created_at)}</dd>
        </dl>`;
    mDetalle.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;
    ConfirmDialog.show(`¿Eliminar al usuario <strong>${escHtml(btn.dataset.nombre)}</strong>?`, async () => {
        const res  = await fetch(`${BASE}/eliminar/${btn.dataset.id}`, {method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) { Toast.success(data.mensaje); cargar(); }
        else Toast.error(data.mensaje);
    }, {confirmLabel:'Eliminar', confirmClass:'btn-danger'});
});

document.getElementById('btn-guardar').addEventListener('click', async function() {
    const pwdRequired = !editingId;
    const pwdRules = pwdRequired
        ? ['required', { min: 6, msg: 'Mínimo 6 caracteres' }, { max: 100 }]
        : [{ fn: v => v === '' || v.length >= 6 || 'Mínimo 6 caracteres si desea cambiarla' }];

    const valid = FormValidator.validate([
        { id: 'nombre',   label: 'Nombre',     rules: ['required', {min:3}, {max:150}, 'noHtml'] },
        { id: 'email',    label: 'Email',       rules: ['required', 'email', 'noHtml'] },
        { id: 'password', label: 'Contraseña',  rules: pwdRules },
        { id: 'telefono', label: 'Teléfono',    rules: [{max:20}, 'noHtml'] },
        { id: 'perfil_id',label: 'Perfil',      rules: ['required'] },
    ], 'form-usuario');
    if (!valid) return;

    const fd = new FormData();
    fd.append('nombre',    document.getElementById('nombre').value.trim());
    fd.append('email',     document.getElementById('email').value.trim());
    fd.append('password',  document.getElementById('password').value);
    fd.append('telefono',  document.getElementById('telefono').value.trim());
    fd.append('perfil_id', document.getElementById('perfil_id').value);
    if (document.getElementById('activo').checked) fd.append('activo', '1');

    const url = editingId ? `${BASE}/actualizar/${editingId}` : `${BASE}/crear`;
    FormValidator.btnLoad(this);
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) {
            // Subir foto si se seleccionó una (solo en edición)
            const fotoFile = document.getElementById('foto-input')?.files[0];
            if (editingId && fotoFile) {
                const fdFoto = new FormData();
                fdFoto.append('foto', fotoFile);
                try {
                    const rFoto = await fetch(`${BASE}/foto/${editingId}`, {method:'POST', body:fdFoto, headers:{'X-Requested-With':'XMLHttpRequest'}});
                    const dFoto = await rFoto.json();
                    if (!dFoto.success) Toast.warning(dFoto.mensaje);
                } catch { Toast.warning('Error al subir la foto'); }
            }
            Toast.success(data.mensaje);
            modal.hide();
            cargar();
        } else {
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
