<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-folder me-2"></i>Mis Proyectos</h1>
        <div>
            <span class="me-3"><i class="bi bi-person-circle me-1"></i><?= esc(session()->get('admin_email')) ?></span>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </a>
        </div>
    </div>

    <div class="card mb-4 shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto</h5>
        </div>
        <div class="card-body">
            <form id="formCrearProyecto" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Título del Proyecto</label>
                        <input type="text" class="form-control" id="titulo" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Link (GitHub, Demo, etc.)</label>
                        <input type="url" class="form-control" id="link" placeholder="https://...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tecnologías (separadas por comas)</label>
                        <input type="text" class="form-control" id="tecnologias" placeholder="PHP, MySQL, Bootstrap">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Imágenes (múltiples)</label>
                        <input type="file" class="form-control" id="imagenes" accept="image/*" multiple>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="descripcion" rows="3" required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle me-2"></i>Crear Proyecto
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Gestión de Proyectos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Imágenes</th>
                            <th>Título</th>
                            <th>Tecnologías</th>
                            <th>Link</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProyectos"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Editar Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarProyecto">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" class="form-control" id="editarTitulo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="editarDescripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Link</label>
                        <input type="url" class="form-control" id="editarLink">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tecnologías</label>
                        <input type="text" class="form-control" id="editarTecnologias">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
let proyectos = [];

document.addEventListener('DOMContentLoaded', cargarProyectos);

async function cargarProyectos() {
    try {
        const response = await fetch('<?= base_url('proyectos/listar') ?>');
        const data = await response.json();
        
        if (data.status === 'success') {
            proyectos = data.data;
            renderizarTabla();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderizarTabla() {
    const tbody = document.getElementById('tablaProyectos');
    tbody.innerHTML = '';
    
    if (proyectos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                    No hay proyectos registrados
                </td>
            </tr>
        `;
        return;
    }
    
    proyectos.forEach(proyecto => {
        const tr = document.createElement('tr');
        
        let imagenesHTML = '';
        if (proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0) {
            imagenesHTML = proyecto.imagenes_urls.map(url => 
                `<img src="${url}" width="60" class="rounded shadow-sm me-1">`
            ).join('');
        } else {
            imagenesHTML = '<span class="text-muted">Sin imágenes</span>';
        }
        
        tr.innerHTML = `
            <td>${imagenesHTML}</td>
            <td>
                <strong>${proyecto.titulo}</strong><br>
                <small class="text-muted">${proyecto.descripcion?.substring(0, 50)}...</small>
            </td>
            <td><span class="badge bg-info">${proyecto.tecnologias || 'N/A'}</span></td>
            <td>${proyecto.link ? `<a href="${proyecto.link}" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>` : '-'}</td>
            <td>
                <button class="btn btn-sm btn-warning me-2" onclick='abrirModalEditar(${JSON.stringify(proyecto)})'>
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarProyecto(${proyecto.id})">
                    <i class="bi bi-trash"></i>
                </button>
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
            mostrarAlerta('Proyecto creado correctamente', 'success');
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

async function eliminarProyecto(id) {
    if (!confirm('¿Eliminar este proyecto?')) return;
    
    try {
        const response = await fetch(`<?= base_url('proyectos/eliminar/') ?>${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            mostrarAlerta('Proyecto eliminado', 'success');
            cargarProyectos();
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