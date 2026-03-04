<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in">
    <div>
        <h2 class="fw-bold m-0"><i class="bi bi-folder-fill text-primary me-2"></i>Panel de Proyectos</h2>
        <p class="text-muted small mb-0 mt-1">Gestiona tu portafolio profesional</p>
    </div>
    <button class="btn btn-primary" id="btn-nuevo-proy">
        <i class="bi bi-plus-circle me-1"></i>Nuevo Proyecto
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body border-bottom p-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="f-busqueda-proyectos" class="form-control" placeholder="Buscar por título, descripción o tecnología...">
            <button class="btn btn-outline-secondary" id="btn-clear-proyectos" type="button"><i class="bi bi-x"></i></button>
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
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted mt-2 mb-0">Cargando proyectos...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer border-0 bg-transparent d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 py-3 px-4">
        <small class="text-muted" id="paginacion-info-proyectos">–</small>
        <nav><ul class="pagination pagination-sm mb-0" id="paginacion-nav-proyectos"></ul></nav>
    </div>
</div>

<!-- MODAL CREAR PROYECTO -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearProyecto" enctype="multipart/form-data" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="titulo" placeholder="Ej. Sistema de Gestión" required maxlength="200" data-vt="nohtml">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Enlace</label>
                            <input type="url" class="form-control" id="link" placeholder="https://github.com/...">
                            <div class="form-text">GitHub, Demo o sitio web</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tecnologías</label>
                            <input type="text" class="form-control" id="tecnologias" placeholder="PHP, MySQL, Bootstrap" maxlength="300" data-vt="nohtml">
                            <div class="form-text">Separadas por comas</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Imágenes</label>
                            <input type="file" class="form-control" id="imagenes" accept="image/*" multiple>
                            <div class="form-text">Ctrl/Cmd + Click para múltiples</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="descripcion" rows="4" placeholder="Describe el proyecto..." required maxlength="3000" data-vt="nohtml"></textarea>
                        </div>
                        <div class="col-12" id="imagePreviewContainer" style="display:none">
                            <label class="form-label fw-semibold small text-muted">Vista previa</label>
                            <div id="imagePreview" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Crear Proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR PROYECTO -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2"></i>Editar Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditarProyecto">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título</label>
                        <input type="text" class="form-control" id="editarTitulo" required maxlength="200" data-vt="nohtml">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea class="form-control" id="editarDescripcion" rows="4" maxlength="3000" data-vt="nohtml"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Enlace</label>
                        <input type="url" class="form-control" id="editarLink">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tecnologías</label>
                        <input type="text" class="form-control" id="editarTecnologias" maxlength="300" data-vt="nohtml">
                        <div class="form-text">Separadas por comas</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Agregar Imágenes</label>
                        <input type="file" class="form-control" id="editarImagenes" accept="image/*" multiple>
                        <div class="form-text">Opcional — se agregarán a las existentes</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarEdicion()">
                    <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GALERÍA -->
<div class="modal fade" id="modalImagenes" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-images me-2"></i>Galería: <span id="tituloProyectoImagenes"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="galeriaImagenes" class="row g-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
let proyectos = [];
let currentPageP = 1;
let debounceP    = null;
const PER_PAGE_P = 5;

const modalCrear  = new bootstrap.Modal(document.getElementById('modalCrear'));
const modalEditar = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditar'));

// ── Nuevo Proyecto ─────────────────────────────────────────────
document.getElementById('btn-nuevo-proy').addEventListener('click', () => {
    document.getElementById('formCrearProyecto').reset();
    document.getElementById('imagePreviewContainer').style.display = 'none';
    document.getElementById('imagePreview').innerHTML = '';
    modalCrear.show();
});

// Preview imágenes en modal crear
document.getElementById('imagenes').addEventListener('change', function() {
    const files = this.files;
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imagePreviewContainer');
    preview.innerHTML = '';
    if (files.length > 0) {
        container.style.display = 'block';
        Array.from(files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'position-relative';
                div.innerHTML = `<img src="${e.target.result}" class="rounded shadow-sm" style="width:100px;height:80px;object-fit:cover">
                    <span class="position-absolute top-0 start-0 badge bg-primary m-1">${i+1}</span>`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else {
        container.style.display = 'none';
    }
});

// ── Cargar proyectos ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', cargarProyectos);

async function cargarProyectos() {
    try {
        const res  = await fetch('<?= base_url('proyectos/listar') ?>');
        const data = await res.json();
        if (data.success) {
            proyectos = data.data;
            aplicarFiltro(1);
        }
    } catch { Toast.error('Error al cargar proyectos'); }
}

function aplicarFiltro(page) {
    currentPageP = page;
    const q = (document.getElementById('f-busqueda-proyectos').value || '').toLowerCase().trim();
    const filtrados = q === '' ? proyectos : proyectos.filter(p => {
        const tecs = Array.isArray(p.tecnologias) ? p.tecnologias.join(' ') : (p.tecnologias || '');
        return (p.titulo||'').toLowerCase().includes(q)
            || (p.descripcion||'').toLowerCase().includes(q)
            || tecs.toLowerCase().includes(q);
    });
    const totalPages = Math.max(1, Math.ceil(filtrados.length / PER_PAGE_P));
    const safePage   = Math.min(page, totalPages);
    const from = filtrados.length === 0 ? 0 : (safePage-1)*PER_PAGE_P + 1;
    const to   = Math.min(safePage*PER_PAGE_P, filtrados.length);
    const slice = filtrados.slice((safePage-1)*PER_PAGE_P, safePage*PER_PAGE_P);

    document.getElementById('paginacion-info-proyectos').textContent =
        filtrados.length === 0 ? '' : `Mostrando ${from}–${to} de ${filtrados.length} proyectos`;
    renderizarTabla(slice);
    renderPaginacionP(totalPages, safePage);
}

function renderPaginacionP(totalPages, page) {
    const nav = document.getElementById('paginacion-nav-proyectos');
    if (totalPages <= 1) { nav.innerHTML = ''; return; }
    const dis = c => c ? 'disabled' : '';
    let html = `<li class="page-item ${dis(page<=1)}"><a class="page-link" href="#" data-page="1">«</a></li>
                <li class="page-item ${dis(page<=1)}"><a class="page-link" href="#" data-page="${page-1}">‹</a></li>`;
    for (let p = 1; p <= totalPages; p++) {
        if (p===1||p===totalPages||Math.abs(p-page)<=1)
            html += `<li class="page-item ${p===page?'active':''}"><a class="page-link" href="#" data-page="${p}">${p}</a></li>`;
        else if (Math.abs(p-page)===2)
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    html += `<li class="page-item ${dis(page>=totalPages)}"><a class="page-link" href="#" data-page="${page+1}">›</a></li>
             <li class="page-item ${dis(page>=totalPages)}"><a class="page-link" href="#" data-page="${totalPages}">»</a></li>`;
    nav.innerHTML = html;
    nav.querySelectorAll('[data-page]').forEach(a => a.addEventListener('click', e => {
        e.preventDefault();
        const p = parseInt(a.dataset.page);
        if (p >= 1 && p <= totalPages) aplicarFiltro(p);
    }));
}

function renderizarTabla(slice) {
    const tbody = document.getElementById('tablaProyectos');
    tbody.innerHTML = '';
    if (!slice || slice.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">
            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
            <h5 class="text-muted mb-2">No hay proyectos</h5>
            <p class="text-muted small">Ajusta la búsqueda o crea un nuevo proyecto</p>
        </td></tr>`;
        return;
    }
    slice.forEach(proyecto => {
        const tr = document.createElement('tr');
        const imgs = proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0
            ? `<div class="d-flex gap-1">${proyecto.imagenes_urls.slice(0,2).map(u=>`<img src="${u}" width="50" height="40" class="rounded" style="object-fit:cover">`).join('')}
               ${proyecto.imagenes_urls.length > 2 ? `<span class="badge bg-secondary align-self-center">+${proyecto.imagenes_urls.length-2}</span>` : ''}</div>`
            : '<span class="text-muted small"><i class="bi bi-image"></i></span>';

        const titulo = (proyecto.titulo||'');
        const tituloMostrar = titulo.length > 45 ? titulo.substring(0,45)+'…' : titulo;
        const desc  = (proyecto.descripcion||'');
        const descMostrar = desc.length > 80 ? desc.substring(0,80)+'…' : desc;

        const tecs = Array.isArray(proyecto.tecnologias) ? proyecto.tecnologias : (proyecto.tecnologias||'').split(',');
        const tecsHTML = tecs.filter(t=>t.trim()).length > 0
            ? `<span class="badge bg-info bg-opacity-10 text-info">${escHtml(tecs[0].trim())}</span>${tecs.length>1?`<span class="badge bg-secondary ms-1">+${tecs.length-1}</span>`:''}`
            : '<span class="text-muted">-</span>';

        tr.innerHTML = `
            <td class="ps-4 py-2">${imgs}</td>
            <td class="py-2" style="max-width:220px">
                <div class="fw-semibold small text-truncate" title="${escHtml(titulo)}">${escHtml(tituloMostrar)}</div>
                <small class="text-muted">${escHtml(descMostrar)}</small>
            </td>
            <td class="py-2">${tecsHTML}</td>
            <td class="py-2">
                ${proyecto.link ? `<a href="${escHtml(proyecto.link)}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i></a>` : '<span class="text-muted">-</span>'}
            </td>
            <td class="text-end pe-4 py-2">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-info" onclick='verImagenes(${JSON.stringify(proyecto).replace(/'/g,"&#39;")})' title="Ver imágenes"><i class="bi bi-images"></i></button>
                    <button class="btn btn-warning" onclick='abrirModalEditar(${JSON.stringify(proyecto).replace(/'/g,"&#39;")})' title="Editar"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger" onclick="eliminarProyecto(${proyecto.id})" title="Eliminar"><i class="bi bi-trash"></i></button>
                </div>
            </td>`;
        tbody.appendChild(tr);
    });
}

// ── Crear proyecto ─────────────────────────────────────────────
document.getElementById('formCrearProyecto').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.submitter || e.target.querySelector('[type=submit]');
    btn.disabled = true;
    const fd = new FormData();
    fd.append('titulo',      document.getElementById('titulo').value);
    fd.append('descripcion', document.getElementById('descripcion').value);
    fd.append('link',        document.getElementById('link').value);
    fd.append('tecnologias', document.getElementById('tecnologias').value);
    Array.from(document.getElementById('imagenes').files).forEach(f => fd.append('imagenes[]', f));
    try {
        const res  = await fetch('<?= base_url('admin/proyectos/crear') ?>', {method:'POST', body:fd});
        const data = await res.json();
        if (data.success) {
            Toast.success('Proyecto creado exitosamente');
            modalCrear.hide();
            e.target.reset();
            document.getElementById('imagePreviewContainer').style.display = 'none';
            cargarProyectos();
        } else {
            Toast.error(data.message || 'Error al crear');
        }
    } catch { Toast.error('Error de red'); }
    finally { btn.disabled = false; }
});

// ── Editar ─────────────────────────────────────────────────────
function abrirModalEditar(proyecto) {
    document.getElementById('editarId').value           = proyecto.id;
    document.getElementById('editarTitulo').value       = proyecto.titulo;
    document.getElementById('editarDescripcion').value  = proyecto.descripcion || '';
    document.getElementById('editarLink').value         = proyecto.link || '';
    document.getElementById('editarTecnologias').value  = Array.isArray(proyecto.tecnologias)
        ? proyecto.tecnologias.join(', ') : (proyecto.tecnologias || '');
    document.getElementById('editarImagenes').value = '';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditar')).show();
}

async function guardarEdicion() {
    const id = document.getElementById('editarId').value;
    const fd = new FormData();
    fd.append('titulo',       document.getElementById('editarTitulo').value);
    fd.append('descripcion',  document.getElementById('editarDescripcion').value);
    fd.append('link',         document.getElementById('editarLink').value);
    fd.append('tecnologias',  document.getElementById('editarTecnologias').value);
    Array.from(document.getElementById('editarImagenes').files).forEach(f => fd.append('imagenes[]', f));
    try {
        const res  = await fetch(`<?= base_url('admin/proyectos/actualizar/') ?>${id}`, {method:'POST', body:fd});
        const data = await res.json();
        if (data.success) {
            Toast.success('Proyecto actualizado');
            bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
            cargarProyectos();
        } else {
            Toast.error(data.message || 'Error al actualizar');
        }
    } catch { Toast.error('Error de red'); }
}

// ── Galería ────────────────────────────────────────────────────
function verImagenes(proyecto) {
    document.getElementById('tituloProyectoImagenes').textContent = proyecto.titulo;
    const galeria = document.getElementById('galeriaImagenes');
    galeria.innerHTML = !proyecto.imagenes_urls || proyecto.imagenes_urls.length === 0
        ? `<div class="text-center py-5"><i class="bi bi-image display-1 text-muted"></i><p class="text-muted mt-3">Sin imágenes</p></div>`
        : proyecto.imagenes_urls.map((url, i) => `
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <img src="${url}" class="card-img-top" style="height:200px;object-fit:cover">
                    <div class="card-body text-center p-2"><small class="text-muted">Imagen ${i+1}</small></div>
                </div>
            </div>`).join('');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('modalImagenes')).show();
}

// ── Eliminar ───────────────────────────────────────────────────
async function eliminarProyecto(id) {
    ConfirmDialog.show('¿Eliminar este proyecto? Se eliminarán también sus imágenes.', async () => {
        const res  = await fetch(`<?= base_url('admin/proyectos/eliminar/') ?>${id}`, {method:'DELETE'});
        const data = await res.json();
        if (data.success) { Toast.success('Proyecto eliminado'); cargarProyectos(); }
        else Toast.error('Error al eliminar');
    }, {confirmLabel:'Eliminar', confirmClass:'btn-danger'});
}

// ── Buscador ───────────────────────────────────────────────────
document.getElementById('f-busqueda-proyectos').addEventListener('input', () => {
    clearTimeout(debounceP);
    debounceP = setTimeout(() => aplicarFiltro(1), 300);
});
document.getElementById('btn-clear-proyectos').addEventListener('click', () => {
    document.getElementById('f-busqueda-proyectos').value = '';
    aplicarFiltro(1);
});

function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?= $this->endSection() ?>
