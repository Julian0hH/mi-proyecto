<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Roles y Permisos</h2>
        <p class="text-muted small mb-0">Gestión de roles del sistema y asignación a usuarios</p>
    </div>
    <button class="btn btn-primary" id="btn-nuevo-rol">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Rol
    </button>
</div>

<div class="row g-4">
    <!-- ROLES -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Roles del Sistema</h6>
            </div>
            <div class="card-body px-4" id="roles-container">
                <?php foreach ($roles as $rol): ?>
                <?php
                $permisos = $rol['permisos'] ?? '{}';
                if (is_string($permisos)) $permisos = json_decode($permisos, true) ?: [];
                $rolColors = ['admin'=>'danger','tecnico'=>'warning','usuario'=>'info'];
                $rolColor  = $rolColors[$rol['nombre']] ?? 'secondary';
                ?>
                <div class="card border mb-3" id="rol-card-<?= $rol['id'] ?>">
                    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-2 px-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-<?= $rolColor ?>"><?= esc($rol['nombre']) ?></span>
                            <small class="text-muted"><?= esc($rol['descripcion'] ?? '') ?></small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-editar-rol" data-id="<?= $rol['id'] ?>" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-eliminar-rol" data-id="<?= $rol['id'] ?>" data-nombre="<?= esc($rol['nombre']) ?>" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body py-2 px-3">
                        <div class="row g-1">
                            <?php
                            $modulos = ['proyectos'=>'Proyectos','carrusel'=>'Carrusel','usuarios'=>'Usuarios','roles'=>'Roles','servicios'=>'Servicios','sobre_mi'=>'Sobre Mí','contactos'=>'Contactos','notificaciones'=>'Notif.'];
                            foreach ($modulos as $key => $label):
                                $tiene = $permisos[$key] ?? false;
                            ?>
                            <div class="col-6">
                                <span class="badge <?= $tiene ? 'bg-success' : 'bg-light text-muted' ?> bg-opacity-75 small w-100">
                                    <i class="bi bi-<?= $tiene ? 'check' : 'x' ?> me-1"></i><?= $label ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- USUARIOS -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>Usuarios Registrados</h6>
                <div class="input-group input-group-sm" style="max-width:220px">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="f-busqueda-usr" class="form-control" placeholder="Buscar usuario...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Usuario</th>
                                <th>Rol Actual</th>
                                <th>Estado</th>
                                <th class="pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-usuarios">
                            <?php if (empty($usuarios)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados</td></tr>
                            <?php else: ?>
                            <?php foreach ($usuarios as $u):
                                $rolColors = ['admin'=>'danger','tecnico'=>'warning','usuario'=>'info'];
                                $rn = $u['rol_nombre'] ?? 'usuario';
                                $rc = $rolColors[$rn] ?? 'secondary';
                            ?>
                            <tr class="fila-usuario" data-nombre="<?= strtolower(esc($u['nombre'])) ?>" data-email="<?= strtolower(esc($u['email'])) ?>">
                                <td class="ps-4">
                                    <div class="fw-semibold small"><?= esc($u['nombre']) ?></div>
                                    <div class="text-muted" style="font-size:.75rem"><?= esc($u['email']) ?></div>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm select-rol" data-id="<?= esc($u['id']) ?>" style="width:120px">
                                        <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $u['rol_id'] == $r['id'] ? 'selected' : '' ?>>
                                            <?= esc($r['nombre']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input toggle-activo" type="checkbox"
                                               data-id="<?= esc($u['id']) ?>"
                                               <?= ($u['activo'] ?? true) ? 'checked' : '' ?>
                                               title="<?= ($u['activo'] ?? true) ? 'Activo' : 'Inactivo' ?>">
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <button class="btn btn-sm btn-primary btn-guardar-rol" data-id="<?= esc($u['id']) ?>">
                                        <i class="bi bi-check me-1"></i>Guardar
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Paginación usuarios -->
                <div class="d-flex align-items-center justify-content-between px-4 py-2 border-top">
                    <small class="text-muted" id="usr-pag-info"></small>
                    <nav><ul class="pagination pagination-sm mb-0" id="usr-paginacion"></ul></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREAR / EDITAR ROL -->
<div class="modal fade" id="modalRol" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-rol-titulo"><i class="bi bi-shield-lock me-2"></i>Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-rol" novalidate>
                <input type="hidden" id="rol-edit-id">
                <div class="modal-body">
                    <!-- Nombre (solo en crear) -->
                    <div class="mb-3" id="wrap-nombre-rol">
                        <label class="form-label fw-semibold">Nombre del Rol <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="rol-nombre" name="nombre"
                               maxlength="50" placeholder="Ej. supervisor, vendedor" data-vt="user">
                        <div class="form-text">Solo letras, números, guiones y guiones bajos. Sin espacios.</div>
                        <div class="form-error text-danger small" id="err-rol-nombre"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" class="form-control" id="rol-descripcion" name="descripcion"
                               maxlength="200" data-vt="nohtml" placeholder="Descripción breve del rol">
                        <div class="form-error text-danger small" id="err-rol-desc"></div>
                    </div>
                    <label class="form-label fw-semibold">Permisos de Acceso</label>
                    <div class="row g-2" id="permisos-checks">
                        <?php
                        $modulos = ['proyectos'=>'Proyectos','carrusel'=>'Carrusel','usuarios'=>'Usuarios','roles'=>'Roles','servicios'=>'Servicios','sobre_mi'=>'Sobre Mí','contactos'=>'Contactos','notificaciones'=>'Notificaciones'];
                        foreach ($modulos as $key => $label):
                        ?>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input perm-check" type="checkbox"
                                       id="perm-<?= $key ?>" name="permisos[<?= $key ?>]" value="1" data-key="<?= $key ?>">
                                <label class="form-check-label small" for="perm-<?= $key ?>"><?= $label ?></label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-guardar-rol">
                        <i class="bi bi-check2 me-1"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modalRol  = new bootstrap.Modal(document.getElementById('modalRol'));
const rolesData = <?= json_encode($roles) ?>;
let rolModalMode = 'crear'; // 'crear' | 'editar'

const PER_PAGE_USR = 5;
let currentPageUsr = 1;
const allFilas = Array.from(document.querySelectorAll('.fila-usuario'));

// ── Paginación + búsqueda de usuarios ─────────────────────────
function filtrarUsuarios(page = 1) {
    const q = document.getElementById('f-busqueda-usr').value.toLowerCase().trim();
    const filtradas = q
        ? allFilas.filter(f => f.dataset.nombre.includes(q) || f.dataset.email.includes(q))
        : allFilas;
    currentPageUsr = page;
    const total = Math.ceil(filtradas.length / PER_PAGE_USR);
    allFilas.forEach(f => f.style.display = 'none');
    filtradas.slice((page-1)*PER_PAGE_USR, page*PER_PAGE_USR).forEach(f => f.style.display = '');
    renderPagUsr(total, page, filtradas.length);
}

function renderPagUsr(total, actual, totalReg) {
    const info = document.getElementById('usr-pag-info');
    const nav  = document.getElementById('usr-paginacion');
    const ini  = (actual-1)*PER_PAGE_USR + 1;
    const fin  = Math.min(actual*PER_PAGE_USR, totalReg);
    info.textContent = totalReg ? `Mostrando ${ini}–${fin} de ${totalReg}` : 'Sin resultados';
    nav.innerHTML = total <= 1 ? '' : [...Array(total)].map((_,i) =>
        `<li class="page-item ${i+1===actual?'active':''}"><button class="page-link" data-p="${i+1}">${i+1}</button></li>`
    ).join('');
    nav.querySelectorAll('[data-p]').forEach(b => b.addEventListener('click', () => filtrarUsuarios(+b.dataset.p)));
}

let debUsr;
document.getElementById('f-busqueda-usr').addEventListener('input', () => {
    clearTimeout(debUsr); debUsr = setTimeout(() => filtrarUsuarios(1), 300);
});

// ── Abrir modal Nuevo Rol ──────────────────────────────────────
document.getElementById('btn-nuevo-rol').addEventListener('click', () => {
    rolModalMode = 'crear';
    document.getElementById('modal-rol-titulo').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Rol';
    document.getElementById('form-rol').reset();
    document.getElementById('rol-edit-id').value = '';
    document.getElementById('wrap-nombre-rol').style.display = '';
    document.getElementById('rol-nombre').required = true;
    document.querySelectorAll('.form-error').forEach(e => e.textContent = '');
    modalRol.show();
});

// ── Editar Rol ─────────────────────────────────────────────────
document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar-rol');
    if (!btn) return;
    rolModalMode = 'editar';
    const id  = parseInt(btn.dataset.id);
    const rol = rolesData.find(r => r.id === id);
    if (!rol) return;
    document.getElementById('modal-rol-titulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Permisos del Rol';
    document.getElementById('rol-edit-id').value = id;
    document.getElementById('rol-descripcion').value = rol.descripcion || '';
    document.getElementById('wrap-nombre-rol').style.display = 'none';
    document.getElementById('rol-nombre').required = false;
    const permisos = typeof rol.permisos === 'string' ? JSON.parse(rol.permisos || '{}') : (rol.permisos || {});
    document.querySelectorAll('.perm-check').forEach(cb => { cb.checked = !!permisos[cb.dataset.key]; });
    document.querySelectorAll('.form-error').forEach(e => e.textContent = '');
    modalRol.show();
});

// ── Eliminar Rol ───────────────────────────────────────────────
document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-eliminar-rol');
    if (!btn) return;
    ConfirmDialog.show(
        `¿Eliminar el rol <strong>${escHtml(btn.dataset.nombre)}</strong>?<br><small class="text-muted">Los usuarios con este rol perderán el acceso.</small>`,
        async () => {
            const res  = await fetch(`<?= base_url('admin/roles/eliminar/') ?>${btn.dataset.id}`, {
                method: 'DELETE', headers: {'X-Requested-With':'XMLHttpRequest'}
            });
            const data = await res.json();
            if (data.success) { Toast.success(data.mensaje); setTimeout(() => location.reload(), 700); }
            else Toast.error(data.mensaje || 'Error al eliminar');
        },
        {confirmLabel: 'Eliminar', confirmClass: 'btn-danger'}
    );
});

// ── Submit Rol (crear o editar) ────────────────────────────────
document.getElementById('form-rol').addEventListener('submit', async e => {
    e.preventDefault();
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

    const id     = document.getElementById('rol-edit-id').value;
    const nombre = document.getElementById('rol-nombre').value.trim();
    const desc   = document.getElementById('rol-descripcion').value.trim();

    // Validar nombre solo en crear
    if (rolModalMode === 'crear') {
        if (!nombre) {
            document.getElementById('err-rol-nombre').textContent = 'El nombre es requerido';
            return;
        }
        if (nombre.length < 2 || nombre.length > 50) {
            document.getElementById('err-rol-nombre').textContent = 'Entre 2 y 50 caracteres';
            return;
        }
        if (!/^[a-zA-Z0-9_\-]+$/.test(nombre)) {
            document.getElementById('err-rol-nombre').textContent = 'Solo letras, números, - y _. Sin espacios ni símbolos.';
            return;
        }
    }
    if (desc.length > 200) {
        document.getElementById('err-rol-desc').textContent = 'Máximo 200 caracteres';
        return;
    }

    const fd = new FormData(e.target);
    // Asegurar que permisos no marcados se envíen como false
    ['proyectos','carrusel','usuarios','roles','servicios','sobre_mi','contactos','notificaciones'].forEach(k => {
        if (!fd.has(`permisos[${k}]`)) fd.append(`permisos[${k}]`, '0');
    });

    const url = rolModalMode === 'crear'
        ? '<?= base_url('admin/roles/crear') ?>'
        : `<?= base_url('admin/roles/actualizar/') ?>${id}`;

    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) {
            Toast.success(data.mensaje);
            modalRol.hide();
            setTimeout(() => location.reload(), 700);
        } else {
            Toast.error(data.mensaje || 'Error al guardar');
        }
    } catch { Toast.error('Error de red'); }
});

// ── Guardar rol de usuario ─────────────────────────────────────
document.querySelectorAll('.btn-guardar-rol').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.id;
        const row    = btn.closest('tr');
        const rolId  = row.querySelector('.select-rol').value;
        const activo = row.querySelector('.toggle-activo').checked;
        const fd = new FormData();
        fd.append('usuario_id', userId);
        fd.append('rol_id', rolId);
        const r1 = await fetch('<?= base_url('admin/roles/asignar-usuario') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const d1 = await r1.json();
        const fd2 = new FormData();
        fd2.append('usuario_id', userId);
        fd2.append('activo', activo ? '1' : '0');
        const r2 = await fetch('<?= base_url('admin/roles/toggle-usuario') ?>', {method:'POST', body:fd2, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const d2 = await r2.json();
        if (d1.success && d2.success) Toast.success('Usuario actualizado correctamente');
        else Toast.error('Error al actualizar usuario');
    });
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

// Inicializar paginación usuarios
filtrarUsuarios(1);
</script>

<?= $this->endSection() ?>
