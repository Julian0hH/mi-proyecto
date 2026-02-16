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
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-plus-circle-fill me-2"></i>Panel de Administración
        </h5>
    </div>
    <div class="card-body p-4">
        <form id="formSubirImagen" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">TÍTULO</label>
                    <input type="text" class="form-control" id="titulo" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                    <input type="text" class="form-control" id="descripcion">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold">IMAGEN</label>
                    <input type="file" class="form-control" id="imagen" accept="image/*" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success px-4 shadow">
                        <i class="bi bi-upload me-2"></i>Subir Imagen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">
                <i class="bi bi-list-ul me-2"></i>Gestión de Imágenes
            </h5>
            <span class="badge bg-primary" id="totalImagenes">0</span>
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

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-fill me-2"></i>Editar Imagen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarImagen">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TÍTULO</label>
                        <input type="text" class="form-control" id="editarTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                        <input type="text" class="form-control" id="editarDescripcion">
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

document.addEventListener('DOMContentLoaded', () => {
    cargarImagenes();
});

async function cargarImagenes() {
    try {
        const response = await fetch('<?= base_url('carrusel/listar') ?>');
        const data = await response.json();
        
        if (data.status === 'success') {
            imagenes = data.data;
            renderizarCarrusel();
            renderizarTabla();
        }
    } catch (error) {
        console.error('Error al cargar imágenes:', error);
        mostrarAlerta('Error al cargar imágenes', 'danger');
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
            </div>
        `;
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
            </div>
        `;
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
    
    tbody.innerHTML = '';
    if (badge) badge.textContent = imagenes.length;
    
    if (imagenes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No hay imágenes registradas</h5>
                    <p class="text-muted small">Utiliza el formulario para agregar la primera imagen</p>
                </td>
            </tr>
        `;
        return;
    }
    
    imagenes.forEach(img => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-4 py-3">
                <img src="${img.url}" width="80" height="60" class="rounded shadow-sm" style="object-fit: cover;">
            </td>
            <td class="py-3">
                <span class="fw-semibold">${img.titulo}</span>
            </td>
            <td class="py-3">
                <span class="text-muted">${img.descripcion || '-'}</span>
            </td>
            <td class="text-end pe-4 py-3">
                <button class="btn btn-sm btn-warning me-2" onclick="abrirModalEditar(${img.id}, '${img.titulo.replace(/'/g, "\\'")}', '${(img.descripcion || '').replace(/'/g, "\\'")}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarImagen(${img.id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('formSubirImagen')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('titulo', document.getElementById('titulo').value);
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('imagen', document.getElementById('imagen').files[0]);
    
    try {
        const response = await fetch('<?= base_url('carrusel/subir') ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Imagen subida correctamente', 'success');
            e.target.reset();
            cargarImagenes();
        } else {
            mostrarAlerta('Error: ' + data.message, 'danger');
        }
    } catch (error) {
        mostrarAlerta('Error al subir imagen', 'danger');
    }
});

function abrirModalEditar(id, titulo, descripcion) {
    document.getElementById('editarId').value = id;
    document.getElementById('editarTitulo').value = titulo;
    document.getElementById('editarDescripcion').value = descripcion;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

async function guardarEdicion() {
    const id = document.getElementById('editarId').value;
    const formData = new FormData();
    formData.append('titulo', document.getElementById('editarTitulo').value);
    formData.append('descripcion', document.getElementById('editarDescripcion').value);
    
    try {
        const response = await fetch(`<?= base_url('carrusel/actualizar/') ?>${id}`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Imagen actualizada', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            cargarImagenes();
        }
    } catch (error) {
        mostrarAlerta('Error al actualizar', 'danger');
    }
}

async function eliminarImagen(id) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    
    try {
        const response = await fetch(`<?= base_url('carrusel/eliminar/') ?>${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Imagen eliminada', 'success');
            cargarImagenes();
        }
    } catch (error) {
        mostrarAlerta('Error al eliminar', 'danger');
    }
}

function mostrarAlerta(mensaje, tipo) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `
        <i class="bi bi-${tipo === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 4000);
}
</script>

<?= $this->endSection() ?>