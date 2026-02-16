<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mb-5 animate-fade-in">
    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 mb-3 rounded-pill">
        <i class="bi bi-briefcase-fill me-2"></i>Portafolio
    </span>
    <h2 class="fw-bold display-5 mb-3">Mis Proyectos</h2>
    <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Explora los trabajos más recientes y destacados
    </p>
</div>

<div id="proyectosContainer" class="row g-4"></div>

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
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted d-block mb-3"></i>
                        <h4 class="text-muted mb-2">No hay proyectos disponibles</h4>
                        <p class="text-muted small">Vuelve pronto para ver nuevos trabajos</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    proyectos.forEach(proyecto => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        
        const imagenesHTML = proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0
            ? `<img src="${proyecto.imagenes_urls[0]}" class="card-img-top" alt="${proyecto.titulo}" style="height: 220px; object-fit: cover;">`
            : '<div class="bg-light d-flex align-items-center justify-content-center" style="height: 220px;"><i class="bi bi-image display-1 text-muted"></i></div>';
        
        const tecnologiasArray = proyecto.tecnologias ? proyecto.tecnologias.split(',') : [];
        const tecnologiasBadges = tecnologiasArray.slice(0, 3).map(tech => 
            `<span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">${tech.trim()}</span>`
        ).join('');
        const mastech = tecnologiasArray.length > 3 ? `<span class="badge bg-secondary">+${tecnologiasArray.length - 3}</span>` : '';
        
        col.innerHTML = `
            <div class="card h-100 border-0 shadow-sm hover-card">
                ${imagenesHTML}
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-2">${proyecto.titulo}</h5>
                    <p class="card-text text-muted flex-grow-1">${proyecto.descripcion || 'Sin descripción'}</p>
                    ${tecnologiasArray.length > 0 ? `<div class="mb-3">${tecnologiasBadges}${mastech}</div>` : ''}
                </div>
                <div class="card-footer bg-white border-0 pt-0">
                    ${proyecto.link ? `<a href="${proyecto.link}" target="_blank" class="btn btn-primary w-100 rounded-pill shadow-sm"><i class="bi bi-link-45deg me-1"></i>Ver Proyecto</a>` : '<button class="btn btn-outline-secondary w-100 rounded-pill" disabled>Sin enlace</button>'}
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}
</script>

<?= $this->endSection() ?>