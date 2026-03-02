<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="text-center mb-5">
    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 mb-3">
        <i class="bi bi-grid-3x3-gap me-2"></i>Catálogo de Servicios
    </span>
    <h2 class="fw-bold display-5 mb-3">Nuestras Soluciones Profesionales</h2>
    <p class="text-muted lead mx-auto" style="max-width:600px">
        Tecnología de vanguardia para impulsar tu negocio al siguiente nivel
    </p>
</div>

<?php if (!empty($servicios)): ?>
<div class="row g-4 justify-content-center mb-5">
    <?php foreach ($servicios as $srv): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body text-center p-4 d-flex flex-column">
                <div class="bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4 mx-auto"
                     style="width:90px;height:90px">
                    <i class="bi <?= esc($srv['icono'] ?? 'bi-gear') ?> fs-1 text-<?= esc($srv['color'] ?? 'primary') ?>"></i>
                </div>
                <h4 class="fw-bold mb-2"><?= esc($srv['titulo']) ?></h4>
                <p class="text-muted mb-4 flex-grow-1"><?= esc($srv['descripcion']) ?></p>
                <?php if (!empty($srv['precio_desde'])): ?>
                <div class="mb-3">
                    <span class="badge bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 text-<?= esc($srv['color'] ?? 'primary') ?> px-3 py-2 rounded-pill fs-6 fw-bold">
                        Desde $<?= number_format((float)$srv['precio_desde'], 2) ?>
                    </span>
                </div>
                <?php endif; ?>
                <a href="<?= base_url('detalles/' . (int)$srv['id']) ?>"
                   class="btn btn-<?= esc($srv['color'] ?? 'primary') ?> rounded-pill px-4 mt-auto">
                    Ver Detalles <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="text-center py-5 text-muted mb-5">
    <i class="bi bi-grid fs-1 d-block mb-3 opacity-25"></i>
    <p class="lead">Los servicios estarán disponibles próximamente.</p>
    <?php if (session()->get('admin_logueado')): ?>
    <a href="<?= base_url('admin/servicios') ?>" class="btn btn-sm btn-primary mt-2">
        <i class="bi bi-plus me-1"></i>Agregar Servicios
    </a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm bg-primary text-white mb-4">
    <div class="card-body p-5 text-center">
        <h3 class="fw-bold mb-3">¿Necesitas una solución personalizada?</h3>
        <p class="lead mb-4 opacity-90">Hablemos de tus requisitos específicos y creamos un plan a medida.</p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="<?= base_url('contacto') ?>" class="btn btn-light btn-lg px-5 shadow">
                <i class="bi bi-chat-dots me-2"></i>Contactar
            </a>
            <a href="<?= base_url('portafolio') ?>" class="btn btn-outline-light btn-lg px-5">
                <i class="bi bi-briefcase me-2"></i>Ver Portafolio
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
