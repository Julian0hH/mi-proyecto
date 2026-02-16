<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mb-5">
    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 mb-3">
        <i class="bi bi-grid-3x3-gap me-2"></i>Catálogo de Servicios
    </span>
    <h2 class="fw-bold display-5 mb-3">Nuestras Soluciones Profesionales</h2>
    <p class="text-muted lead mx-auto" style="max-width: 600px;">
        Tecnología de vanguardia para impulsar tu negocio al siguiente nivel
    </p>
</div>

<div class="row g-4 justify-content-center mb-5">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4">
                    <i class="bi bi-laptop fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-3">Desarrollo Web</h4>
                <p class="text-muted mb-4">
                    Aplicaciones web robustas y escalables desarrolladas con CodeIgniter 4,
                    diseño responsive y las mejores prácticas de la industria.
                </p>
                <ul class="list-unstyled text-start mb-4">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Backend con CodeIgniter 4</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Frontend con Bootstrap 5</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Diseño responsive</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Optimización SEO</span>
                    </li>
                </ul>
                <a href="<?= base_url('detalles') ?>" class="btn btn-primary rounded-pill px-4 shadow">
                    Ver Detalles <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4">
                    <i class="bi bi-database fs-1 text-success"></i>
                </div>
                <h4 class="fw-bold mb-3">Integración de Datos</h4>
                <p class="text-muted mb-4">
                    Conexiones seguras a bases de datos en la nube con Supabase,
                    PostgreSQL y gestión eficiente de información.
                </p>
                <ul class="list-unstyled text-start mb-4">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Supabase Integration</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>API RESTful</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>PostgreSQL optimizado</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Real-time sync</span>
                    </li>
                </ul>
                <button class="btn btn-outline-success rounded-pill px-4" disabled>
                    Próximamente
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4">
                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-4">
                    <i class="bi bi-shield-lock fs-1 text-warning"></i>
                </div>
                <h4 class="fw-bold mb-3">Seguridad Avanzada</h4>
                <p class="text-muted mb-4">
                    Auditoría de código, validaciones estrictas, protección contra
                    ataques comunes y certificaciones de seguridad.
                </p>
                <ul class="list-unstyled text-start mb-4">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Validación de entrada</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Protección XSS/CSRF</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Encriptación de datos</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <span>Auditorías periódicas</span>
                    </li>
                </ul>
                <button class="btn btn-outline-warning rounded-pill px-4" disabled>
                    Próximamente
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-primary text-white">
    <div class="card-body p-5 text-center">
        <h3 class="fw-bold mb-3">¿Necesitas una solución personalizada?</h3>
        <p class="lead mb-4 opacity-90">
            Contáctanos para discutir tus requisitos específicos y crear un plan a medida
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= base_url('registro') ?>" class="btn btn-light btn-lg px-5 shadow">
                <i class="bi bi-chat-dots me-2"></i>Comenzar Consulta
            </a>
            <a href="<?= base_url('detalles') ?>" class="btn btn-outline-light btn-lg px-5">
                <i class="bi bi-info-circle me-2"></i>Más Información
            </a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-light mt-4">
    <div class="card-body p-5 text-center">
        <div class="mb-3">
            <i class="bi bi-briefcase-fill display-4 text-primary"></i>
        </div>
        <h4 class="fw-bold mb-3">Explora Nuestro Portafolio</h4>
        <p class="text-muted mb-4">
            Descubre proyectos reales que hemos desarrollado con estas tecnologías.<br>
            Ve casos de uso, implementaciones y resultados comprobados.
        </p>
        <a href="<?= base_url('proyectos') ?>" class="btn btn-primary btn-lg px-5 rounded-pill shadow">
            <i class="bi bi-arrow-right-circle me-2"></i>Ver Proyectos Realizados
        </a>
    </div>
</div>

<?= $this->endSection() ?>