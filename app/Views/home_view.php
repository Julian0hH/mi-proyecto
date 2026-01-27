<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="col-lg-8 text-center">
        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary mb-3 px-4 py-2">
            Versión 2.0
        </span>
        <h1 class="display-4 fw-bold mb-4">Bienvenido al Panel</h1>
        <p class="lead text-muted mb-5">
            Sistema administrativo modular con conexión a Supabase, validaciones estrictas y diseño adaptable.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-rocket-takeoff me-2"></i> Iniciar
            </a>
            <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary btn-lg px-5">
                Documentación
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>