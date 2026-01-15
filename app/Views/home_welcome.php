<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card p-5 text-center">
        <div class="card-body">
            <h1 class="display-5 fw-bold text-dark">Bienvenido al Sistema</h1>
            <p class="lead text-muted mt-3">Gestión de usuarios conectada a PostgreSQL de forma segura.</p>
            <div class="mt-4">
                <a href="<?= base_url('registro') ?>" class="btn btn-primary btn-lg px-5 shadow-sm">
                    Acceder al Registro <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>