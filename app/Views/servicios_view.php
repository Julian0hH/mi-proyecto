<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="card p-5 text-center">
        <h1>Nuestros Servicios</h1>
        <p class="text-muted">Esta es la segunda pantalla para demostrar Breadcrumbs estáticos.</p>
        <div class="mt-4">
            <a href="<?= base_url('detalles') ?>" class="btn btn-outline-primary">Ver Detalles de Servicio</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>