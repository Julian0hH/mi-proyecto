<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Servicios</h2>
        <p class="text-muted small mb-0">Gestiona los servicios de tu portafolio</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalServicio">
        <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
    </button>
</div>

<!-- CONSTRUCTOR VISUAL (cards tipo grid) -->
<div class="row g-3 mb-4" id="servicios-grid">
    <?php foreach ($servicios as $srv): ?>
    <div class="col-md-6 col-xl-3 servicio-card" data-id="<?= $srv['id'] ?>">
        <div class="card border-0 shadow-sm h-100 position-relative">
            <?php if (!($srv['activo'] ?? true)): ?>
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 rounded" style="z-index:1"></div>
            <span class="badge bg-secondary position-absolute top-0 end-0 m-2" style="z-index:2">Inactivo</span>
            <?php endif; ?>
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="service-icon-preview bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 text-<?= esc($srv['color'] ?? 'primary') ?> rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;min-width:48px">
                        <i class="bi <?= esc($srv['icono'] ?? 'bi-gear') ?> fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1"><?= esc($srv['titulo']) ?></h6>
                        <small class="text-muted">Orden: <?= (int)($srv['orden'] ?? 0) ?></small>
                    </div>
                </div>
                <p class="text-muted small mb-3"><?= esc(substr($srv['descripcion'] ?? '', 0, 100)) ?>...</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary flex-grow-1 btn-editar"
                            data-id="<?= $srv['id'] ?>"
                            data-titulo="<?= esc($srv['titulo']) ?>"
                            data-descripcion="<?= esc($srv['descripcion']) ?>"
                            data-descripcion-larga="<?= esc($srv['descripcion_larga'] ?? '') ?>"
                            data-icono="<?= esc($srv['icono'] ?? 'bi-gear') ?>"
                            data-color="<?= esc($srv['color'] ?? 'primary') ?>"
                            data-orden="<?= (int)($srv['orden'] ?? 0) ?>"
                            data-activo="<?= ($srv['activo'] ?? true) ? '1' : '0' ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="<?= $srv['id'] ?>" data-nombre="<?= esc($srv['titulo']) ?>">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($servicios)): ?>
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
        <p>No hay servicios. Crea el primero.</p>
    </div>
    <?php endif; ?>
</div>

<!-- MODAL CREAR/EDITAR -->
<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title-srv"><i class="bi bi-gear me-2"></i>Nuevo Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-servicio">
                <input type="hidden" id="srv-id" name="id" value="">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" id="srv-titulo" class="form-control" required maxlength="200" placeholder="Ej: Gestión de Bases de Datos" data-vt="nohtml">
                            <div class="form-error" id="err-srv-titulo"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción corta <span class="text-danger">*</span></label>
                            <textarea name="descripcion" id="srv-descripcion" class="form-control" rows="2" required placeholder="Descripción breve para la tarjeta" maxlength="500" data-vt="nohtml"></textarea>
                            <div class="form-error" id="err-srv-descripcion"></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción larga</label>
                            <textarea name="descripcion_larga" id="srv-descripcion-larga" class="form-control" rows="4" placeholder="Descripción detallada del servicio" maxlength="3000" data-vt="nohtml"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Icono Bootstrap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i id="icon-preview" class="bi bi-gear"></i></span>
                                <input type="text" name="icono" id="srv-icono" class="form-control" value="bi-gear" placeholder="bi-gear" maxlength="50" data-vt="icon">
                            </div>
                            <div class="form-text">Ver <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Color</label>
                            <select name="color" id="srv-color" class="form-select">
                                <option value="primary">Azul (primary)</option>
                                <option value="info">Celeste (info)</option>
                                <option value="success">Verde (success)</option>
                                <option value="warning">Amarillo (warning)</option>
                                <option value="danger">Rojo (danger)</option>
                                <option value="secondary">Gris (secondary)</option>
                                <option value="dark">Oscuro (dark)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" name="orden" id="srv-orden" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Estado</label>
                            <select name="activo" id="srv-activo" class="form-select">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                        <!-- PREVIEW -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-muted">Vista previa</label>
                            <div class="card border p-3" id="srv-preview">
                                <div class="d-flex gap-3 align-items-start">
                                    <div id="prev-icon" class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                                        <i class="bi bi-gear fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 id="prev-titulo" class="fw-bold mb-1">Título del Servicio</h6>
                                        <p id="prev-desc" class="text-muted small mb-0">Descripción del servicio</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-srv">
                        <i class="bi bi-check-circle me-2"></i>Guardar Servicio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const modal   = new bootstrap.Modal(document.getElementById('modalServicio'));
const form    = document.getElementById('form-servicio');
let editingId = null;

// Preview live
const srvTitulo = document.getElementById('srv-titulo');
const srvDesc   = document.getElementById('srv-descripcion');
const srvIcono  = document.getElementById('srv-icono');
const srvColor  = document.getElementById('srv-color');

function updatePreview() {
    document.getElementById('prev-titulo').textContent = srvTitulo.value || 'Título del Servicio';
    document.getElementById('prev-desc').textContent   = srvDesc.value || 'Descripción del servicio';
    const ic = srvIcono.value || 'bi-gear';
    const cl = srvColor.value || 'primary';
    document.getElementById('prev-icon').className = `bg-${cl} bg-opacity-10 text-${cl} rounded-circle d-flex align-items-center justify-content-center`;
    document.getElementById('prev-icon').style.cssText = 'width:48px;height:48px';
    document.getElementById('prev-icon').innerHTML = `<i class="bi ${ic} fs-5"></i>`;
    document.getElementById('icon-preview').className = `bi ${ic}`;
}
[srvTitulo, srvDesc, srvIcono, srvColor].forEach(el => el.addEventListener('input', updatePreview));

// Botón nuevo
document.querySelector('[data-bs-target="#modalServicio"]').addEventListener('click', () => {
    editingId = null;
    form.reset();
    document.getElementById('modal-title-srv').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Servicio';
    document.getElementById('btn-submit-srv').innerHTML = '<i class="bi bi-check-circle me-2"></i>Crear Servicio';
    updatePreview();
});

// Botón editar
document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-editar');
    if (!btn) return;
    editingId = btn.dataset.id;
    document.getElementById('modal-title-srv').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Servicio';
    document.getElementById('btn-submit-srv').innerHTML  = '<i class="bi bi-check-circle me-2"></i>Actualizar';
    srvTitulo.value = btn.dataset.titulo || '';
    srvDesc.value   = btn.dataset.descripcion || '';
    document.getElementById('srv-descripcion-larga').value = btn.dataset.descripcionLarga || '';
    srvIcono.value  = btn.dataset.icono || 'bi-gear';
    srvColor.value  = btn.dataset.color || 'primary';
    document.getElementById('srv-orden').value  = btn.dataset.orden || '0';
    document.getElementById('srv-activo').value = btn.dataset.activo || '1';
    updatePreview();
    modal.show();
});

// Submit
form.addEventListener('submit', async e => {
    e.preventDefault();
    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    const btn = document.getElementById('btn-submit-srv');
    btn.disabled = true;
    const fd  = new FormData(form);
    const url = editingId
        ? `<?= base_url('admin/servicios/actualizar/') ?>${editingId}`
        : '<?= base_url('admin/servicios/crear') ?>';
    try {
        const res  = await fetch(url, {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) {
            Toast.success(data.mensaje || 'Servicio guardado');
            modal.hide();
            setTimeout(() => location.reload(), 800);
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([k, v]) => {
                    const el = document.getElementById(`err-srv-${k}`);
                    if (el) el.textContent = v;
                });
            }
            Toast.error(data.mensaje || 'Error al guardar');
        }
    } catch { Toast.error('Error de red'); }
    finally { btn.disabled = false; }
});

// Eliminar
document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-eliminar');
    if (!btn) return;
    ConfirmDialog.show(`¿Eliminar el servicio "<strong>${btn.dataset.nombre}</strong>"?`, async () => {
        try {
            const res  = await fetch(`<?= base_url('admin/servicios/eliminar/') ?>${btn.dataset.id}`, {method:'DELETE', headers:{'X-Requested-With':'XMLHttpRequest'}});
            const data = await res.json();
            if (data.success) { Toast.success('Servicio eliminado'); setTimeout(() => location.reload(), 800); }
            else Toast.error(data.mensaje || 'Error al eliminar');
        } catch { Toast.error('Error de red'); }
    });
});
</script>

<?= $this->endSection() ?>
