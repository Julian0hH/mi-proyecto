<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mb-5">
    <h2 class="fw-bold">Nuestras Soluciones</h2>
    <p class="text-muted">Catálogo de servicios disponibles.</p>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-4">
                    <i class="bi bi-laptop fs-2"></i>
                </div>
                <h4 class="fw-bold mb-3">Desarrollo Web</h4>
                <p class="text-muted mb-4">Aplicaciones web robustas con CodeIgniter 4.</p>
                <a href="<?= base_url('detalles') ?>" class="btn btn-outline-primary rounded-pill px-4">Ver Detalles</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-4">
                    <i class="bi bi-database fs-2"></i>
                </div>
                <h4 class="fw-bold mb-3">Base de Datos</h4>
                <p class="text-muted mb-4">Integración segura con Supabase y PostgreSQL.</p>
                <button class="btn btn-outline-secondary rounded-pill px-4" disabled>Próximamente</button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100 p-4 text-center">
            <div class="card-body">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex p-3 mb-4">
                    <i class="bi bi-shield-lock fs-2"></i>
                </div>
                <h4 class="fw-bold mb-3">Seguridad</h4>
                <p class="text-muted mb-4">Auditoría de validaciones y protección.</p>
                <button class="btn btn-outline-secondary rounded-pill px-4" disabled>Próximamente</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>