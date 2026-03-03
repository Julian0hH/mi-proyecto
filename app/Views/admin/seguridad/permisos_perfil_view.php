<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos = session()->get('user_permisos') ?? [];
$isAdmin  = session()->get('user_type') === 'admin';
$puedeEditar = $isAdmin || !empty($permisos[3]['bitEditar']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-shield-check me-2 text-warning"></i>Permisos por Perfil</h2>
        <p class="text-muted small mb-0">Asigna acciones CRUD a cada perfil por módulo</p>
    </div>
</div>

<div class="row g-4">
    <!-- Selector de perfil -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Seleccionar Perfil</h6>
            </div>
            <div class="card-body px-4">
                <select id="sel-perfil" class="form-select mb-3">
                    <option value="">-- Elegir perfil --</option>
                    <?php foreach ($perfiles as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= esc($p['strNombrePerfil']) ?>
                        <?php if ($p['bitAdministrador']): ?> ⭐<?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <div id="info-perfil" class="text-muted small">Selecciona un perfil para ver y editar sus permisos.</div>
            </div>
        </div>
    </div>

    <!-- Tabla de permisos -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="bi bi-table me-2 text-warning"></i>Permisos del Módulo</h6>
                <?php if ($puedeEditar): ?>
                <button class="btn btn-warning btn-sm" id="btn-guardar" disabled>
                    <i class="bi bi-save me-1"></i>Guardar Permisos
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="tabla-permisos">
                        <thead>
                            <tr>
                                <th class="px-4">Módulo</th>
                                <th class="text-center">Agregar</th>
                                <th class="text-center">Editar</th>
                                <th class="text-center">Consulta</th>
                                <th class="text-center">Eliminar</th>
                                <th class="text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body">
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield fs-1 d-block mb-2 opacity-25"></i>
                                    Selecciona un perfil para ver los permisos
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const BASE_PERMISOS = '<?= base_url('admin/seguridad/permisos') ?>';
const modulos = <?= json_encode(array_values($modulos)) ?>;

let permisosCargados = {};

document.getElementById('sel-perfil').addEventListener('change', async function() {
    const idPerfil = this.value;
    const btnGuardar = document.getElementById('btn-guardar');
    const tbody = document.getElementById('tabla-body');

    if (!idPerfil) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-shield fs-1 d-block mb-2 opacity-25"></i>Selecciona un perfil</td></tr>';
        if (btnGuardar) btnGuardar.disabled = true;
        return;
    }

    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3"><div class="spinner-border spinner-border-sm text-warning"></div> Cargando...</td></tr>';

    try {
        const res  = await fetch(`${BASE_PERMISOS}/cargar/${idPerfil}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        permisosCargados = data.data || {};
        renderTabla();
        if (btnGuardar) btnGuardar.disabled = false;
    } catch { Toast.error('Error al cargar permisos'); }
});

function renderTabla() {
    const tbody = document.getElementById('tabla-body');
    if (!modulos.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted">Sin módulos registrados</td></tr>';
        return;
    }

    tbody.innerHTML = modulos.map(mod => {
        const p = permisosCargados[mod.id] || {};
        const bits = ['bitAgregar','bitEditar','bitConsulta','bitEliminar','bitDetalle'];
        const labels = ['agregar','editar','consulta','eliminar','detalle'];
        const checks = bits.map((bit, i) => `
            <td class="text-center">
                <div class="form-check d-flex justify-content-center mb-0">
                    <input class="form-check-input" type="checkbox" name="mod_${mod.id}_${labels[i]}"
                        id="chk_${mod.id}_${labels[i]}" ${p[bit] ? 'checked' : ''}>
                </div>
            </td>`).join('');

        return `<tr>
            <td class="px-4 fw-semibold"><i class="bi bi-box me-2 text-muted"></i>${escHtml(mod.strNombreModulo)}</td>
            ${checks}
        </tr>`;
    }).join('');
}

document.getElementById('btn-guardar')?.addEventListener('click', async () => {
    const idPerfil = document.getElementById('sel-perfil').value;
    if (!idPerfil) return;

    const fd = new FormData();
    fd.append('idPerfil', idPerfil);
    document.querySelectorAll('#tabla-body input[type=checkbox]').forEach(chk => {
        if (chk.checked) fd.append(chk.name, '1');
    });

    try {
        const res  = await fetch(`${BASE_PERMISOS}/guardar`, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) Toast.success(data.mensaje);
        else Toast.error(data.mensaje);
    } catch { Toast.error('Error de red'); }
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?= $this->endSection() ?>
