<div class="container mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold"><i class="bi bi-briefcase me-3"></i>Portafolio de Proyectos</h1>
        <p class="lead text-muted">Explora mis trabajos más recientes</p>
    </div>

    <div id="proyectosContainer" class="row g-4"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarProyectos);

async function cargarProyectos() {
    try {
        const response = await fetch('<?= base_url('proyectos/listar') ?>');
        const data = await response.json();
        
        if (data.status === 'success') {
            renderizarProyectos(data.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderizarProyectos(proyectos) {
    const container = document.getElementById('proyectosContainer');
    container.innerHTML = '';
    
    if (proyectos.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="lead text-muted mt-3">No hay proyectos disponibles</p>
            </div>
        `;
        return;
    }
    
    proyectos.forEach(proyecto => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        
        const imagenesHTML = proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0
            ? `<img src="${proyecto.imagenes_urls[0]}" class="card-img-top" alt="${proyecto.titulo}" style="height: 200px; object-fit: cover;">`
            : '<div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;"><i class="bi bi-image display-1 text-white"></i></div>';
        
        col.innerHTML = `
            <div class="card h-100 shadow-sm">
                ${imagenesHTML}
                <div class="card-body">
                    <h5 class="card-title">${proyecto.titulo}</h5>
                    <p class="card-text text-muted">${proyecto.descripcion}</p>
                    ${proyecto.tecnologias ? `<p class="small"><strong>Tecnologías:</strong> ${proyecto.tecnologias}</p>` : ''}
                </div>
                <div class="card-footer bg-white">
                    ${proyecto.link ? `<a href="${proyecto.link}" target="_blank" class="btn btn-primary btn-sm w-100"><i class="bi bi-link-45deg me-1"></i>Ver Proyecto</a>` : ''}
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}
</script>