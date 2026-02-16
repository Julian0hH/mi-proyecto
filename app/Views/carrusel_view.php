<div class="container mt-5">
    <h1 class="mb-4"><i class="bi bi-images me-2"></i>Carrusel de Imágenes</h1>

    <div id="carouselPortfolio" class="carousel slide mb-5 shadow-lg" data-bs-ride="carousel">
        <div class="carousel-indicators" id="carouselIndicators"></div>
        <div class="carousel-inner rounded" id="carouselInner">
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

    <?php if (session()->get('admin_logueado')): ?>
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Panel de Administración</h5>
        </div>
        <div class="card-body">
            <form id="formSubirImagen" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" class="form-control" id="titulo" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" class="form-control" id="descripcion">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Imagen</label>
                        <input type="file" class="form-control" id="imagen" accept="image/*" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-upload me-2"></i>Subir Imagen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Gestión de Imágenes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Vista Previa</th>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaImagenes"></tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarImagen">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" class="form-control" id="editarTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" class="form-control" id="editarDescripcion">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar</button>
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
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (imagenes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                    No hay imágenes registradas
                </td>
            </tr>
        `;
        return;
    }
    
    imagenes.forEach(img => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><img src="${img.url}" width="80" class="rounded shadow-sm"></td>
            <td class="fw-semibold">${img.titulo}</td>
            <td>${img.descripcion || '-'}</td>
            <td>
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
    alertDiv.className = `alert alert-${tipo} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 3000);
}
</script>

<style>
.carousel-item img {
    border-radius: 8px;
}
</style>