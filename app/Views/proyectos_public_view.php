<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mb-5 animate-fade-in">
    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 mb-3 rounded-pill">
        <i class="bi bi-briefcase-fill me-2"></i>Portafolio Profesional
    </span>
    <h2 class="fw-bold display-5 mb-3">Mis Proyectos</h2>
    <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Explora los trabajos más recientes y destacados del portafolio
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
                        <div class="mb-4">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                        </div>
                        <h3 class="fw-bold mb-3 text-muted">Próximamente</h3>
                        <p class="text-muted mb-0">Estamos trabajando en nuevos proyectos increíbles.</p>
                        <p class="text-muted small">¡Vuelve pronto para verlos!</p>
                    </div>
                </div>
            </div>
        `;
        return;
    }
    
    proyectos.forEach((proyecto, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4';
        
        let carruselHTML = '';
        const carouselId = `carousel${proyecto.id}`;
        
        if (proyecto.imagenes_urls && proyecto.imagenes_urls.length > 0) {
            const indicators = proyecto.imagenes_urls.map((_, idx) => 
                `<button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${idx}" ${idx === 0 ? 'class="active"' : ''}></button>`
            ).join('');
            
            const slides = proyecto.imagenes_urls.map((url, idx) => `
                <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                    <img src="${url}" class="d-block w-100" alt="${proyecto.titulo}" style="height: 250px; object-fit: cover;">
                </div>
            `).join('');
            
            carruselHTML = `
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                    ${proyecto.imagenes_urls.length > 1 ? `<div class="carousel-indicators">${indicators}</div>` : ''}
                    <div class="carousel-inner">
                        ${slides}
                    </div>
                    ${proyecto.imagenes_urls.length > 1 ? `
                        <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    ` : ''}
                </div>
            `;
        } else {
            carruselHTML = `
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                    <i class="bi bi-image display-1 text-muted"></i>
                </div>
            `;
        }
        
        const tecnologiasArray = proyecto.tecnologias ? proyecto.tecnologias.split(',') : [];
        const tecnologiasBadges = tecnologiasArray.slice(0, 4).map(tech => 
            `<span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">${tech.trim()}</span>`
        ).join('');
        const masTech = tecnologiasArray.length > 4 ? `<span class="badge bg-secondary mb-1">+${tecnologiasArray.length - 4}</span>` : '';
        
        const descripcionCorta = proyecto.descripcion 
            ? (proyecto.descripcion.length > 120 ? proyecto.descripcion.substring(0, 120) + '...' : proyecto.descripcion)
            : 'Sin descripción disponible';
        
        col.innerHTML = `
            <div class="card h-100 border-0 shadow-sm hover-card">
                ${carruselHTML}
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold mb-2">${proyecto.titulo}</h5>
                    <p class="card-text text-muted flex-grow-1 mb-3">${descripcionCorta}</p>
                    ${tecnologiasArray.length > 0 ? `
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2 fw-semibold">TECNOLOGÍAS</small>
                            ${tecnologiasBadges}${masTech}
                        </div>
                    ` : ''}
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0">
                    ${proyecto.link ? `
                        <a href="${proyecto.link}" target="_blank" class="btn btn-primary w-100 rounded-pill shadow-sm">
                            <i class="bi bi-link-45deg me-1"></i>Ver Proyecto
                        </a>
                    ` : `
                        <button class="btn btn-outline-secondary w-100 rounded-pill" disabled>
                            <i class="bi bi-lock me-1"></i>Sin enlace disponible
                        </button>
                    `}
                </div>
            </div>
        `;
        container.appendChild(col);
    });
}
</script>

<?= $this->endSection() ?>