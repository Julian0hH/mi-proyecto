<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisosSession = session()->get('user_permisos') ?? [];
$isAdmin         = session()->get('user_type') === 'admin';
$puedeAgregar    = $isAdmin || !empty($permisosSession[4]['bitAgregar']);
$puedeEditar     = $isAdmin || !empty($permisosSession[4]['bitEditar']);
$puedeEliminar   = $isAdmin || !empty($permisosSession[4]['bitEliminar']);
$puedeDetalle    = $isAdmin || !empty($permisosSession[4]['bitDetalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-people me-2 text-info"></i>Usuarios</h2>
        <p class="text-muted small mb-0">Gestión de usuarios del sistema con control de acceso</p>
    </div>
    <?php if ($puedeAgregar): ?>
    <button class="btn btn-info text-white" id="btn-nuevo">
        <i class="bi bi-person-plus me-1"></i>Nuevo Usuario
    </button>
    <?php endif; ?>
</div>

<!-- Búsqueda -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3">
        <div class="input-group input-group-sm" style="max-width:350px">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda" class="form-control" placeholder="Buscar usuario...">
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
                        <th>Imagen</th>
                        <th>Usuario</th>
                        <th>Correo</th>
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

<!-- Modal Crear/Editar -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-titulo"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-usuario" enctype="multipart/form-data" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario <span class="text-danger">*</span></label>
                            <input type="text" id="strNombreUsuario" class="form-control" maxlength="100" placeholder="nombre_usuario" required data-vt="user">
                            <div class="form-error text-danger small" id="err-strNombreUsuario"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo</label>
                            <input type="email" id="strCorreo" class="form-control" maxlength="150" placeholder="correo@ejemplo.com">
                            <div class="form-error text-danger small" id="err-strCorreo"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contraseña <span id="lbl-pwd-req" class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" id="strPwd" class="form-control" maxlength="100" placeholder="Mín. 6 caracteres">
                                <button class="btn btn-outline-secondary" type="button" id="btn-ver-pwd">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="hint-pwd">Dejar vacío para no cambiar (solo en edición)</small>
                            <div class="form-error text-danger small" id="err-strPwd"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Celular</label>
                            <input type="text" id="strNumeroCelular" class="form-control" maxlength="20"
                                   placeholder="+502 1234-5678"
                                   data-vt="phone">
                            <div class="form-error text-danger small" id="err-strNumeroCelular"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
                            <select id="idPerfil" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($perfiles as $pf): ?>
                                <option value="<?= $pf['id'] ?>"><?= esc($pf['strNombrePerfil']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-error text-danger small" id="err-idPerfil"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="idEstadoUsuario" checked>
                                <label class="form-check-label" for="idEstadoUsuario">Activo</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Imagen de perfil</label>
                            <input type="file" id="imagen" class="form-control" accept="image/*">
                            <div class="mt-2" id="img-preview-wrap" style="display:none">
                                <img id="img-preview" src="" alt="Preview" class="rounded" style="max-height:80px;border:2px solid var(--border-color)">
                                <small class="d-block text-muted mt-1">Previsualización</small>
                            </div>
                            <div id="img-actual-wrap" style="display:none;margin-top:8px">
                                <img id="img-actual" src="" alt="Imagen actual" class="rounded" style="max-height:60px;">
                                <small class="d-block text-muted">Imagen actual</small>
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

// Preview imagen
document.getElementById('imagen').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) { document.getElementById('img-preview-wrap').style.display='none'; return; }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('img-preview').src = e.target.result;
        document.getElementById('img-preview-wrap').style.display = 'block';
    };
    reader.readAsDataURL(file);
});

// Ver contraseña
document.getElementById('btn-ver-pwd').addEventListener('click', function() {
    const inp = document.getElementById('strPwd');
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
        u.strNombreUsuario?.toLowerCase().includes(q) || u.strCorreo?.toLowerCase().includes(q)
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
                ${u.imagen
                    ? `<img src="${escHtml(u.imagen)}" alt="img" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">`
                    : `<div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:18px"><i class="bi bi-person"></i></div>`}
            </td>
            <td><span class="fw-semibold">${escHtml(u.strNombreUsuario)}</span></td>
            <td class="text-muted small">${escHtml(u.strCorreo||'-')}</td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary">${escHtml(u.perfiles?.strNombrePerfil||'-')}</span></td>
            <td class="text-center">
                ${u.idEstadoUsuario
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
                        data-usuario="${escHtml(u.strNombreUsuario)}"
                        data-correo="${escHtml(u.strCorreo||'')}"
                        data-celular="${escHtml(u.strNumeroCelular||'')}"
                        data-perfil="${u.idPerfil||''}"
                        data-estado="${u.idEstadoUsuario?'1':'0'}"
                        data-imagen="${escHtml(u.imagen||'')}"
                        title="Editar"><i class="bi bi-pencil"></i></button>
                    <?php endif; ?>
                    <?php if ($puedeEliminar): ?>
                    <button class="btn btn-outline-danger btn-eliminar" data-id="${u.id}" data-nombre="${escHtml(u.strNombreUsuario)}" title="Eliminar"><i class="bi bi-trash"></i></button>
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

document.getElementById('btn-nuevo')?.addEventListener('click', () => {
    editingId = null;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-person-plus me-2"></i>Nuevo Usuario';
    document.getElementById('form-usuario').reset();
    document.getElementById('img-preview-wrap').style.display = 'none';
    document.getElementById('img-actual-wrap').style.display = 'none';
    document.getElementById('lbl-pwd-req').style.display = '';
    document.getElementById('hint-pwd').style.display = 'none';
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
    document.getElementById('strNombreUsuario').value = btn.dataset.usuario;
    document.getElementById('strCorreo').value = btn.dataset.correo;
    document.getElementById('strNumeroCelular').value = btn.dataset.celular;
    document.getElementById('idPerfil').value = btn.dataset.perfil;
    document.getElementById('idEstadoUsuario').checked = btn.dataset.estado === '1';
    document.getElementById('strPwd').value = '';
    document.getElementById('lbl-pwd-req').style.display = 'none';
    document.getElementById('hint-pwd').style.display = '';
    document.getElementById('img-preview-wrap').style.display = 'none';
    if (btn.dataset.imagen) {
        document.getElementById('img-actual').src = btn.dataset.imagen;
        document.getElementById('img-actual-wrap').style.display = '';
    } else {
        document.getElementById('img-actual-wrap').style.display = 'none';
    }
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    modal.show();
});

document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-detalle');
    if (!btn) return;
    const u = usuarios.find(x => x.id == btn.dataset.id);
    if (!u) return;
    document.getElementById('detalle-body').innerHTML = `
        <div class="text-center mb-3">
            ${u.imagen
                ? `<img src="${escHtml(u.imagen)}" class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">`
                : `<div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;font-size:40px"><i class="bi bi-person"></i></div>`}
        </div>
        <dl class="row mb-0">
            <dt class="col-sm-4">ID</dt><dd class="col-sm-8">${u.id}</dd>
            <dt class="col-sm-4">Usuario</dt><dd class="col-sm-8">${escHtml(u.strNombreUsuario)}</dd>
            <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">${escHtml(u.strCorreo||'-')}</dd>
            <dt class="col-sm-4">Celular</dt><dd class="col-sm-8">${escHtml(u.strNumeroCelular||'-')}</dd>
            <dt class="col-sm-4">Perfil</dt><dd class="col-sm-8">${escHtml(u.perfiles?.strNombrePerfil||'-')}</dd>
            <dt class="col-sm-4">Estado</dt>
            <dd class="col-sm-8">${u.idEstadoUsuario?'<span class="badge bg-success">Activo</span>':'<span class="badge bg-danger">Inactivo</span>'}</dd>
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

document.getElementById('btn-guardar').addEventListener('click', async () => {
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    const fd = new FormData();
    fd.append('strNombreUsuario', document.getElementById('strNombreUsuario').value.trim());
    fd.append('strCorreo',        document.getElementById('strCorreo').value.trim());
    fd.append('strPwd',           document.getElementById('strPwd').value);
    fd.append('strNumeroCelular', document.getElementById('strNumeroCelular').value.trim());
    fd.append('idPerfil',         document.getElementById('idPerfil').value);
    if (document.getElementById('idEstadoUsuario').checked) fd.append('idEstadoUsuario', '1');

    const imgFile = document.getElementById('imagen').files[0];
    if (imgFile) fd.append('imagen', imgFile);

    const url = editingId ? `${BASE}/actualizar/${editingId}` : `${BASE}/crear`;
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) { Toast.success(data.mensaje); modal.hide(); cargar(); }
        else {
            if (data.errors) Object.entries(data.errors).forEach(([k,v]) => {
                const el = document.getElementById('err-'+k); if(el) el.textContent=v;
            });
            else Toast.error(data.mensaje);
        }
    } catch { Toast.error('Error de red'); }
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function formatDate(s) { return s ? new Date(s).toLocaleDateString('es-ES',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }

cargar();
</script>

<?= $this->endSection() ?>
