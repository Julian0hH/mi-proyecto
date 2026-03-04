<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="mb-4 animate-slide-in">
    <h2 class="fw-bold">
        <i class="bi bi-images text-primary me-2"></i>Carrusel de Imágenes
    </h2>
    <p class="text-muted">Gestión visual de tu portafolio</p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div id="carouselPortfolio" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators" id="carouselIndicators"></div>
            <div class="carousel-inner rounded" id="carouselInner" style="max-height: 500px;">
                <div class="carousel-item active">
                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 500px;">
                        <div class="text-center text-muted">
                            <div class="spinner-border mb-3" role="status"></div>
                            <p>Cargando imágenes...</p>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselPortfolio" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselPortfolio" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
</div>

<?php if (session()->get('admin_logueado')): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">
                <i class="bi bi-list-ul me-2"></i>Gestión de Imágenes
                <span class="badge bg-primary ms-2" id="totalImagenes">0</span>
            </h5>
            <button class="btn btn-primary btn-sm" id="btn-nueva-imagen">
                <i class="bi bi-plus-lg me-1"></i>Nueva Imagen
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Vista Previa</th>
                        <th class="py-3">Título</th>
                        <th class="py-3">Descripción</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaImagenes"></tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal Nueva Imagen -->
<div class="modal fade" id="modalSubir" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Imagen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formSubirImagen" novalidate>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TÍTULO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="titulo" name="titulo" maxlength="100" data-vt="nohtml" required>
                        <div class="form-error text-danger small mt-1" id="err-titulo"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" maxlength="255" data-vt="nohtml">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">IMAGEN <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*" required>
                        <div class="form-error text-danger small mt-1" id="err-imagen"></div>
                    </div>
                    <div id="preview-container" class="d-none mb-3 text-center">
                        <img id="imagen-preview" src="" alt="Preview" class="img-thumbnail" style="max-height:200px; object-fit:cover;">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-guardar-imagen">
                    <i class="bi bi-upload me-1"></i>Subir Imagen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-fill me-2"></i>Editar Imagen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarImagen" novalidate>
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TÍTULO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editarTitulo" maxlength="100" data-vt="nohtml" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                        <input type="text" class="form-control" id="editarDescripcion" maxlength="255" data-vt="nohtml">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">
                    <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let imagenes = [];
const modalSubir = new bootstrap.Modal(document.getElementById('modalSubir'));
const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));

document.addEventListener('DOMContentLoaded', () => {
    cargarImagenes();

    document.getElementById('btn-nueva-imagen')?.addEventListener('click', () => {
        document.getElementById('formSubirImagen').reset();
        document.getElementById('preview-container').classList.add('d-none');
        document.getElementById('err-titulo').textContent = '';
        document.getElementById('err-imagen').textContent = '';
        modalSubir.show();
    });

    document.getElementById('imagen').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) { document.getElementById('preview-container').classList.add('d-none'); return; }
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imagen-preview').src = e.target.result;
            document.getElementById('preview-container').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('btn-guardar-imagen').addEventListener('click', subirImagen);
});

async function cargarImagenes() {
    try {
        const response = await fetch('<?= base_url('carrusel/listar') ?>');
        const data = await response.json();
        if (data.success === true) {
            imagenes = data.data;
            renderizarCarrusel();
            renderizarTabla();
        }
    } catch (error) {
        Toast.error('Error al cargar imágenes');
    }
}

function renderizarCarrusel() {
    const inner = document.getElementById('carouselInner');
    const indicators = document.getElementById('carouselIndicators');

    if (imagenes.length === 0) {
        inner.innerHTML = `
            <div class="carousel-item active">
                <div class="d-flex align-items-center justify-content-center bg-light" style="height: 500px;">
                    <div class="text-center text-muted">
                        <i class="bi bi-image display-1"></i>
                        <p class="mt-3">No hay imágenes en el carrusel</p>
                    </div>
                </div>
            </div>`;
        indicators.innerHTML = '';
        return;
    }

    inner.innerHTML = '';
    indicators.innerHTML = '';

    imagenes.forEach((img, index) => {
        const div = document.createElement('div');
        div.className = `carousel-item ${index === 0 ? 'active' : ''}`;
        div.innerHTML = `
            <img src="${img.url}" class="d-block w-100" alt="${img.titulo}" style="max-height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded p-3">
                <h5>${img.titulo}</h5>
                <p>${img.descripcion || ''}</p>
            </div>`;
        inner.appendChild(div);

        const button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('data-bs-target', '#carouselPortfolio');
        button.setAttribute('data-bs-slide-to', index);
        if (index === 0) button.className = 'active';
        indicators.appendChild(button);
    });
}

function renderizarTabla() {
    const tbody = document.getElementById('tablaImagenes');
    const badge = document.getElementById('totalImagenes');
    if (!tbody) return;

    if (badge) badge.textContent = imagenes.length;

    if (imagenes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No hay imágenes registradas</h5>
                    <p class="text-muted small">Haz clic en "Nueva Imagen" para agregar la primera</p>
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = imagenes.map(img => {
        const tit = (img.titulo || '').replace(/'/g, "\\'");
        const desc = (img.descripcion || '').replace(/'/g, "\\'");
        const titMostrar = img.titulo.length > 40 ? img.titulo.substring(0, 40) + '…' : img.titulo;
        const descMostrar = (img.descripcion || '').length > 60 ? img.descripcion.substring(0, 60) + '…' : (img.descripcion || '—');
        return `<tr>
            <td class="ps-4 py-3">
                <img src="${img.url}" width="80" height="60" class="rounded shadow-sm" style="object-fit: cover;">
            </td>
            <td class="py-3"><span class="fw-semibold small" title="${img.titulo}">${titMostrar}</span></td>
            <td class="py-3"><span class="text-muted small" title="${img.descripcion || ''}">${descMostrar}</span></td>
            <td class="text-end pe-4 py-3">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-warning" title="Editar" onclick="abrirModalEditar(${img.id}, '${tit}', '${desc}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" title="Eliminar" onclick="eliminarImagen(${img.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

async function subirImagen() {
    const titulo = document.getElementById('titulo').value.trim();
    const imagenFile = document.getElementById('imagen').files[0];
    let valid = true;

    document.getElementById('err-titulo').textContent = '';
    document.getElementById('err-imagen').textContent = '';

    if (!titulo) {
        document.getElementById('err-titulo').textContent = 'El título es obligatorio.';
        valid = false;
    }
    if (!imagenFile) {
        document.getElementById('err-imagen').textContent = 'Selecciona una imagen.';
        valid = false;
    }
    if (!valid) return;

    const btn = document.getElementById('btn-guardar-imagen');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Subiendo…';

    const formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('imagenes[]', imagenFile);

    try {
        const response = await fetch('<?= base_url('admin/carrusel/subir') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success === true) {
            modalSubir.hide();
            Toast.success('Imagen subida correctamente');
            cargarImagenes();
        } else {
            Toast.error('Error: ' + (data.message || 'No se pudo subir la imagen'));
        }
    } catch (error) {
        Toast.error('Error al subir imagen');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-upload me-1"></i>Subir Imagen';
    }
}

function abrirModalEditar(id, titulo, descripcion) {
    document.getElementById('editarId').value = id;
    document.getElementById('editarTitulo').value = titulo;
    document.getElementById('editarDescripcion').value = descripcion;
    modalEditar.show();
}

async function guardarEdicion() {
    const id = document.getElementById('editarId').value;
    const formData = new FormData();
    formData.append('titulo', document.getElementById('editarTitulo').value);
    formData.append('descripcion', document.getElementById('editarDescripcion').value);

    try {
        const response = await fetch(`<?= base_url('admin/carrusel/actualizar/') ?>${id}`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.success === true) {
            modalEditar.hide();
            Toast.success('Imagen actualizada');
            cargarImagenes();
        } else {
            Toast.error('Error: ' + (data.message || 'No se pudo actualizar'));
        }
    } catch (error) {
        Toast.error('Error al actualizar');
    }
}

async function eliminarImagen(id) {
    ConfirmDialog.show('¿Eliminar esta imagen del carrusel?', async () => {
        try {
            const response = await fetch(`<?= base_url('admin/carrusel/eliminar/') ?>${id}`, {
                method: 'DELETE'
            });
            const data = await response.json();

            if (data.success === true) {
                Toast.success('Imagen eliminada');
                cargarImagenes();
            } else {
                Toast.error('Error: ' + (data.message || 'No se pudo eliminar'));
            }
        } catch (error) {
            Toast.error('Error al eliminar');
        }
    }, { confirmLabel: 'Eliminar', confirmClass: 'btn-danger' });
}
</script>

<?= $this->endSection() ?>
