<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="card p-5">
        <h1>Detalles del Servicio</h1>
        <p class="text-muted">Esta es la tercera pantalla (Nivel 3) de los Breadcrumbs.</p>
        <hr>
        <p>Información detallada sobre el servicio seleccionado.</p>
        <a href="<?= base_url('servicios') ?>" class="btn btn-secondary btn-sm">Volver a Servicios</a>
    </div>
</div>
<?= $this->endSection() ?>