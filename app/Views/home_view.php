<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="col-lg-10">
        <div class="text-center mb-5">
            <div class="mb-4">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-3 px-4 py-2 fs-6">
                    <i class="bi bi-rocket-takeoff me-2"></i>Versión 2.0 - Sistema Modular
                </span>
            </div>
            <h1 class="display-3 fw-bold mb-4 animate-slide-in">
                Bienvenido al Panel de Administración
            </h1>
            <p class="lead text-muted mb-5 mx-auto" style="max-width: 700px;">
                Sistema administrativo profesional con conexión a Supabase, validaciones estrictas,
                gestión de proyectos y diseño adaptable. Desarrollado con CodeIgniter 4 y Bootstrap 5.
            </p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-people-fill text-primary fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Gestión de Usuarios</h5>
                        <p class="text-muted small mb-3">
                            Registro y administración completa con conexión a Supabase
                        </p>
                        <a href="<?= base_url('registro') ?>" class="btn btn-outline-primary btn-sm">
                            Explorar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-shield-check text-success fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Validaciones Estrictas</h5>
                        <p class="text-muted small mb-3">
                            Formularios con validación en tiempo real y seguridad avanzada
                        </p>
                        <a href="<?= base_url('validacion') ?>" class="btn btn-outline-success btn-sm">
                            Probar <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-grid text-warning fs-1"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Catálogo de Servicios</h5>
                        <p class="text-muted small mb-3">
                            Explora las soluciones disponibles y sus características
                        </p>
                        <a href="<?= base_url('servicios') ?>" class="btn btn-outline-warning btn-sm">
                            Ver más <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h3 class="fw-bold mb-4">¿Listo para comenzar?</h3>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="bi bi-rocket-takeoff me-2"></i>Comenzar Ahora
                </a>
                <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary btn-lg px-5">
                    <i class="bi bi-book me-2"></i>Ver Documentación
                </a>
            </div>
        </div>

        <?php if (session()->get('admin_logueado')): ?>
        <div class="alert alert-info border-0 shadow-sm mt-5 text-center">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Sesión Administrativa Activa</strong> - Tienes acceso completo al sistema
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>