<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in">
    <div>
        <h2 class="fw-bold m-0">
            <i class="bi bi-folder-fill text-primary me-2"></i>Panel de Proyectos
        </h2>
        <p class="text-muted small mb-0 mt-1">Gestiona tu portafolio profesional</p>
    </div>
    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
        <i class="bi bi-person-circle me-1"></i><?= esc(session()->get('admin_email')) ?>
    </span>
</div>

<div id="estadoVacio" class="card border-0 shadow-sm mb-4" style="display: none;">
    <div class="card-body text-center py-5">
        <div class="mb-4">
            <i class="bi bi-folder-plus display-1 text-primary"></i>
        </div>
        <h3 class="fw-bold mb-3">¡Comienza tu Portafolio!</h3>
        <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
            Aún no tienes proyectos registrados. Crea tu primer proyecto y muestra tu trabajo al mundo.
            Puedes agregar múltiples imágenes, describir tecnologías y compartir enlaces.
        </p>
        <button class="btn btn-primary btn-lg px-5 rounded-pill shadow" onclick="document.getElementById('formCrearProyecto').scrollIntoView({behavior: 'smooth'})">
            <i class="bi bi-plus-circle me-2"></i>Crear Primer Proyecto
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-success text-white border-0">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-plus-circle-fill me-2"></i>Crear Nuevo Proyecto
        </h5>
    </div>
    <div class="card-body p-4">
        <form id="formCrearProyecto" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold">TÍTULO DEL PROYECTO</label>
                    <input type="text" class="form-control" id="titulo" placeholder="Ej. Sistema de Gestión Empresarial" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold">ENLACE</label>
                    <input type="url" class="form-control" id="link" placeholder="https://github.com/usuario/proyecto">
                    <div class="form-text">GitHub, Demo, o sitio web</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold">TECNOLOGÍAS</label>
                    <input type="text" class="form-control" id="tecnologias" placeholder="PHP, MySQL, Bootstrap, JavaScript">
                    <div class="form-text">Separadas por comas</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small fw-bold">IMÁGENES DEL PROYECTO</label>
                    <input type="file" class="form-control" id="imagenes" accept="image/*" multiple>
                    <div class="form-text">Selecciona múltiples imágenes (Ctrl/Cmd + Click)</div>
                </div>
                <div class="col-12">
                    <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                    <textarea class="form-control" id="descripcion" rows="4" placeholder="Describe las características principales, objetivos y resultados del proyecto..." required></textarea>
                </div>
                <div class="col-12" id="imagePreviewContainer" style="display: none;">
                    <label class="form-label text-muted small fw-bold">VISTA PREVIA DE IMÁGENES</label>
                    <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success px-4 shadow">
                        <i class="bi bi-plus-circle me-2"></i>Crear Proyecto
                    </button>
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-2"></i>Limpiar
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
                <i class="bi bi-list-ul me-2"></i>Proyectos Registrados
            </h5>
            <span class="badge bg-primary" id="totalProyectos">0</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">Imágenes</th>
                        <th class="py-3">Información</th>
                        <th class="py-3">Tecnologías</th>
                        <th class="py-3">Enlace</th>
                        <th class="text-end pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaProyectos">
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0">Cargando proyectos...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-fill me-2"></i>Editar Proyecto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditarProyecto">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TÍTULO</label>
                        <input type="text" class="form-control" id="editarTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPCIÓN</label>
                        <textarea class="form-control" id="editarDescripcion" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">ENLACE</label>
                        <input type="url" class="form-control" id="editarLink">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">TECNOLOGÍAS</label>
                        <input type="text" class="form-control" id="editarTecnologias">
                        <div class="form-text">Separadas por comas</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
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

<div class="modal fade" id="modalImagenes" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-images me-2"></i>Galería del Proyecto: <span id="tituloProyectoImagenes"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="galeriaImagenes" class="row g-3"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let proyectos = [];

document.addEventListener('DOMContentLoaded', cargarProyectos);

document.getElementById('imagenes').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    
    preview.innerHTML = '';
    
    if (files.length > 0) {
        container.style.display = 'block';
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'position-relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="rounded shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                    <span class="position-absolute top-0 start-0 badge bg-primary m-1">${index + 1}</span>
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = 'none';
    }
});

document.getElementById('formCrearProyecto').addEventListener('reset', function() {
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imagePreview').innerHTML = '';
});

async function cargarProyectos() {
    try {
        const response = await fetch('<?= base_url('proyectos/listar') ?>');
        const data = await response.json();
        
        if (data.status === 'success') {
            proyectos = data.data;
            renderizarTabla();
            
            const estadoVacio = document.getElementById('estadoVacio');
            if (proyectos.length === 0 && estadoVacio) {
                estadoVacio.style.display = 'block';
            } else if (estadoVacio) {
                estadoVacio.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarAlerta('Error al cargar proyectos', 'danger');
    }
}

function renderizarTabla() {
    const tbody = document.getElementById('tablaProyectos');
    const badge = document.getElementById('totalProyectos');
    tbody.innerHTML = '';
    if (badge) badge.textContent = proyectos.length;
    
    if (proyectos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                    <h5 class="text-muted mb-2">No hay proyectos registrados</h5>
                    <p class="text-muted small">Utiliza el formulario superior para crear tu primer proyecto</p>
                </td>
            </tr>
        `;
        return;
    }
    
    proyectos.forEach(proyecto => {
        const tr = document.createElement('tr');
        
        let imagenesHTML = '';
        if (proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0) {
            imagenesHTML = `
                <div class="d-flex align-items-center gap-1">
                    ${proyecto.imagenes_urls.slice(0, 3).map(url => 
                        `<img src="${url}" width="50" height="50" class="rounded shadow-sm" style="object-fit: cover;">`
                    ).join('')}
                    ${proyecto.imagenes_urls.length > 3 ? 
                        `<span class="badge bg-secondary">+${proyecto.imagenes_urls.length - 3}</span>` : ''}
                </div>
            `;
        } else {
            imagenesHTML = '<span class="text-muted small"><i class="bi bi-image"></i> Sin imágenes</span>';
        }
        
        const descripcionCorta = proyecto.descripcion ? 
            (proyecto.descripcion.length > 100 ? proyecto.descripcion.substring(0, 100) + '...' : proyecto.descripcion) : 
            'Sin descripción';
        
        const tecnologiasArray = proyecto.tecnologias ? proyecto.tecnologias.split(',') : [];
        const tecnologiasHTML = tecnologiasArray.length > 0 ?
            `<span class="badge bg-info bg-opacity-10 text-info">${tecnologiasArray[0].trim()}</span>
             ${tecnologiasArray.length > 1 ? `<span class="badge bg-secondary">+${tecnologiasArray.length - 1}</span>` : ''}` :
            '<span class="text-muted">-</span>';
        
        tr.innerHTML = `
            <td class="ps-4 py-3">${imagenesHTML}</td>
            <td class="py-3">
                <div>
                    <strong class="d-block mb-1">${proyecto.titulo}</strong>
                    <small class="text-muted">${descripcionCorta}</small>
                </div>
            </td>
            <td class="py-3">${tecnologiasHTML}</td>
            <td class="py-3">
                ${proyecto.link ? 
                    `<a href="${proyecto.link}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver proyecto">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>` : 
                    '<span class="text-muted">-</span>'}
            </td>
            <td class="text-end pe-4 py-3">
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-info" onclick='verImagenes(${JSON.stringify(proyecto).replace(/'/g, "&#39;")})' title="Ver imágenes">
                        <i class="bi bi-images"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick='abrirModalEditar(${JSON.stringify(proyecto).replace(/'/g, "&#39;")})' title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarProyecto(${proyecto.id})" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

document.getElementById('formCrearProyecto').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('titulo', document.getElementById('titulo').value);
    formData.append('descripcion', document.getElementById('descripcion').value);
    formData.append('link', document.getElementById('link').value);
    formData.append('tecnologias', document.getElementById('tecnologias').value);
    
    const imagenes = document.getElementById('imagenes').files;
    for (let i = 0; i < imagenes.length; i++) {
        formData.append('imagenes[]', imagenes[i]);
    }
    
    try {
        const response = await fetch('<?= base_url('proyectos/crear') ?>', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Proyecto creado exitosamente', 'success');
            e.target.reset();
            cargarProyectos();
        } else {
            mostrarAlerta('Error: ' + data.message, 'danger');
        }
    } catch (error) {
        mostrarAlerta('Error al crear proyecto', 'danger');
    }
});

function abrirModalEditar(proyecto) {
    document.getElementById('editarId').value = proyecto.id;
    document.getElementById('editarTitulo').value = proyecto.titulo;
    document.getElementById('editarDescripcion').value = proyecto.descripcion || '';
    document.getElementById('editarLink').value = proyecto.link || '';
    document.getElementById('editarTecnologias').value = proyecto.tecnologias || '';
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}

async function guardarEdicion() {
    const id = document.getElementById('editarId').value;
    const formData = new FormData();
    formData.append('titulo', document.getElementById('editarTitulo').value);
    formData.append('descripcion', document.getElementById('editarDescripcion').value);
    formData.append('link', document.getElementById('editarLink').value);
    formData.append('tecnologias', document.getElementById('editarTecnologias').value);
    
    try {
        const response = await fetch(`<?= base_url('proyectos/actualizar/') ?>${id}`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Proyecto actualizado', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            cargarProyectos();
        }
    } catch (error) {
        mostrarAlerta('Error al actualizar', 'danger');
    }
}

function verImagenes(proyecto) {
    const modal = new bootstrap.Modal(document.getElementById('modalImagenes'));
    document.getElementById('tituloProyectoImagenes').textContent = proyecto.titulo;
    
    const galeria = document.getElementById('galeriaImagenes');
    galeria.innerHTML = '';
    
    if (!proyecto.imagenes_urls || proyecto.imagenes_urls.length === 0) {
        galeria.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-image display-1 text-muted"></i>
                <p class="text-muted mt-3">Este proyecto no tiene imágenes</p>
            </div>
        `;
    } else {
        proyecto.imagenes_urls.forEach((url, index) => {
            const div = document.createElement('div');
            div.className = 'col-md-4';
            div.innerHTML = `
                <div class="card border-0 shadow-sm">
                    <img src="${url}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body text-center p-2">
                        <small class="text-muted">Imagen ${index + 1}</small>
                    </div>
                </div>
            `;
            galeria.appendChild(div);
        });
    }
    
    modal.show();
}

async function eliminarProyecto(id) {
    if (!confirm('¿Estás seguro de eliminar este proyecto? Esta acción no se puede deshacer y eliminará todas sus imágenes.')) return;
    
    try {
        const response = await fetch(`<?= base_url('proyectos/eliminar/') ?>${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Proyecto eliminado correctamente', 'success');
            cargarProyectos();
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