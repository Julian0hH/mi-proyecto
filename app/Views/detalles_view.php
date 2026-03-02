<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php if (empty($servicio)): ?>
<div class="text-center py-5">
    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
    <h4 class="text-muted">Servicio no encontrado</h4>
    <p class="text-muted small">El servicio que buscas no existe o ya no está disponible.</p>
    <a href="<?= base_url('servicios') ?>" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left me-2"></i>Ver todos los servicios
    </a>
</div>
<?php else: ?>

<div class="card border-0 shadow-sm overflow-hidden animate-fade-in">
    <div class="row g-0">
        <!-- Panel visual izquierdo -->
        <div class="col-md-4 d-flex align-items-center justify-content-center p-5"
             style="background:var(--bg-card);min-height:260px">
            <div class="text-center">
                <div class="bg-<?= esc($servicio['color'] ?? 'primary') ?> bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                     style="width:130px;height:130px">
                    <i class="bi <?= esc($servicio['icono'] ?? 'bi-gear') ?> text-<?= esc($servicio['color'] ?? 'primary') ?>"
                       style="font-size:3.5rem"></i>
                </div>
                <?php if (!empty($servicio['precio_desde'])): ?>
                <div class="fw-bold fs-2 text-<?= esc($servicio['color'] ?? 'primary') ?>">
                    Desde $<?= number_format((float)$servicio['precio_desde'], 2) ?>
                </div>
                <small class="text-muted">precio estimado</small>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contenido derecho -->
        <div class="col-md-8">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <span class="badge bg-<?= esc($servicio['color'] ?? 'primary') ?> px-3 py-2 rounded-pill">Disponible</span>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle me-1"></i>Activo
                    </span>
                </div>

                <h2 class="fw-bold mb-3"><?= esc($servicio['titulo']) ?></h2>
                <p class="lead text-muted mb-4"><?= esc($servicio['descripcion']) ?></p>

                <?php
                $caracteristicas = $servicio['caracteristicas'] ?? [];
                if (is_string($caracteristicas)) $caracteristicas = json_decode($caracteristicas, true) ?: [];
                if (!empty($caracteristicas)):
                ?>
                <div class="row g-2 mb-4">
                    <?php foreach ($caracteristicas as $c): ?>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                            <span><?= esc($c) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a href="<?= base_url('contratar/' . (int)$servicio['id']) ?>"
                       class="btn btn-<?= esc($servicio['color'] ?? 'primary') ?> btn-lg px-5 rounded-pill shadow-sm">
                        <i class="bi bi-lightning me-2"></i>Contratar Ahora
                    </a>
                    <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                        <i class="bi bi-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Otros Servicios -->
<?php if (!empty($otros_servicios)): ?>
<div class="mt-5">
    <h5 class="fw-bold mb-3">Otros Servicios</h5>
    <div class="row g-3">
        <?php foreach ($otros_servicios as $otro): ?>
        <div class="col-sm-6 col-lg-3">
            <a href="<?= base_url('detalles/' . (int)$otro['id']) ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm hover-card h-100">
                    <div class="card-body p-3 text-center">
                        <i class="bi <?= esc($otro['icono'] ?? 'bi-gear') ?> fs-3 text-<?= esc($otro['color'] ?? 'primary') ?> d-block mb-2"></i>
                        <small class="fw-semibold"><?= esc($otro['titulo']) ?></small>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<?= $this->endSection() ?>
