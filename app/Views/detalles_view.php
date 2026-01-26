<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card p-5 text-center border-0 shadow-sm" style="background: var(--bg-card);">
        <div class="mb-4">
            <i class="bi bi-laptop fs-1" style="color: var(--accent-color);"></i>
        </div>
        <h2 class="fw-bold mb-3">Desarrollo Web Full Stack</h2>
        <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
            Ofrecemos soluciones completas utilizando CodeIgniter 4, bases de datos optimizadas 
            y interfaces modernas que se adaptan a cualquier dispositivo.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <button class="btn btn-primary px-4">Contratar Ahora</button>
            <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary px-4">Volver</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>