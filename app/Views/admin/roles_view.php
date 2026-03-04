<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Roles y Permisos</h2>
        <p class="text-muted small mb-0">Gestión avanzada de roles y asignación a usuarios</p>
    </div>
</div>

<div class="row g-4">
    <!-- ROLES -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-primary"></i>Roles del Sistema</h6>
            </div>
            <div class="card-body px-4">
                <?php foreach ($roles as $rol): ?>
                <?php
                $permisos = $rol['permisos'] ?? '{}';
                if (is_string($permisos)) $permisos = json_decode($permisos, true) ?: [];
                $rolColors = ['admin'=>'danger','tecnico'=>'warning','usuario'=>'info'];
                $rolColor  = $rolColors[$rol['nombre']] ?? 'secondary';
                ?>
                <div class="card border mb-3">
                    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-2 px-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-<?= $rolColor ?>"><?= esc($rol['nombre']) ?></span>
                            <small class="text-muted"><?= esc($rol['descripcion'] ?? '') ?></small>
                        </div>
                        <button class="btn btn-sm btn-outline-primary btn-editar-rol" data-id="<?= $rol['id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
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
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-success"></i>Usuarios Registrados</h6>
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
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No hay usuarios registrados</td></tr>
                            <?php else: ?>
                            <?php foreach ($usuarios as $u):
                                $rolColors = ['admin'=>'danger','tecnico'=>'warning','usuario'=>'info'];
                                $rn = $u['rol_nombre'] ?? 'usuario';
                                $rc = $rolColors[$rn] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold small"><?= esc($u['nombre']) ?></div>
                                    <div class="text-muted tiny"><?= esc($u['email']) ?></div>
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
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ROL -->
<div class="modal fade" id="modalEditarRol" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Editar Permisos del Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-rol">
                <input type="hidden" id="rol-edit-id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" class="form-control" id="rol-descripcion" name="descripcion" maxlength="200" data-vt="nohtml">
                    </div>
                    <label class="form-label fw-semibold">Permisos</label>
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
                    <button type="submit" class="btn btn-primary">Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modalRol  = new bootstrap.Modal(document.getElementById('modalEditarRol'));
const rolesData = <?= json_encode($roles) ?>;

// Editar rol
document.querySelectorAll('.btn-editar-rol').forEach(btn => {
    btn.addEventListener('click', () => {
        const id  = parseInt(btn.dataset.id);
        const rol = rolesData.find(r => r.id === id);
        if (!rol) return;
        document.getElementById('rol-edit-id').value = id;
        document.getElementById('rol-descripcion').value = rol.descripcion || '';
        const permisos = typeof rol.permisos === 'string' ? JSON.parse(rol.permisos || '{}') : (rol.permisos || {});
        document.querySelectorAll('.perm-check').forEach(cb => {
            cb.checked = !!permisos[cb.dataset.key];
        });
        modalRol.show();
    });
});

// Submit rol
document.getElementById('form-rol').addEventListener('submit', async e => {
    e.preventDefault();
    const id = document.getElementById('rol-edit-id').value;
    const fd = new FormData(e.target);
    // Asegura que permisos no marcados se envíen como 0
    ['proyectos','carrusel','usuarios','roles','servicios','sobre_mi','contactos','notificaciones'].forEach(k => {
        if (!fd.has(`permisos[${k}]`)) fd.append(`permisos[${k}]`, '0');
    });
    const res  = await fetch(`<?= base_url('admin/roles/actualizar/') ?>${id}`, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();
    if (data.success) { Toast.success('Permisos actualizados'); modalRol.hide(); setTimeout(() => location.reload(), 800); }
    else Toast.error(data.mensaje || 'Error al actualizar');
});

// Guardar rol de usuario
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
</script>

<?= $this->endSection() ?>
