<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- ===================== HERO + CARRUSEL ===================== -->
<section class="hero-section mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-5 order-lg-1 order-2">
            <div class="animate-slide-in">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3 d-inline-flex align-items-center gap-2">
                    <span class="status-dot bg-success rounded-circle" style="width:8px;height:8px;display:inline-block"></span>
                    Disponible para proyectos
                </span>
                <h1 class="display-4 fw-bold mb-3 lh-sm">
                    <?= esc($sobre_mi['titulo'] ?? 'Desarrollador Backend') ?><br>
                    <span class="text-primary">&amp; Cloud Expert</span>
                </h1>
                <p class="lead text-muted mb-4">
                    <?= esc($sobre_mi['subtitulo'] ?? 'Especialista en arquitectura de sistemas escalables y soluciones cloud modernas.') ?>
                </p>
                <div class="d-flex gap-4 mb-4 flex-wrap">
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-primary"><?= (int)($sobre_mi['experiencia_anos'] ?? 5) ?>+</div>
                        <small class="text-muted">Años exp.</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-success"><?= (int)($sobre_mi['proyectos_completados'] ?? 48) ?>+</div>
                        <small class="text-muted">Proyectos</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-4 text-warning"><?= (int)($sobre_mi['clientes_satisfechos'] ?? 32) ?>+</div>
                        <small class="text-muted">Clientes</small>
                    </div>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= base_url('portafolio') ?>" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-briefcase me-2"></i>Ver Portafolio
                    </a>
                    <a href="<?= base_url('contacto') ?>" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-envelope me-2"></i>Contactar
                    </a>
                    <?php if (!empty($sobre_mi['github_url'])): ?>
                    <a href="<?= esc($sobre_mi['github_url']) ?>" target="_blank" class="btn btn-outline-dark btn-lg">
                        <i class="bi bi-github"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- CARRUSEL -->
        <div class="col-lg-7 order-lg-2 order-1">
            <div class="animate-fade-in">
                <?php if (!empty($carrusel)): ?>
                <div id="heroCarousel" class="carousel slide carousel-fade rounded-4 overflow-hidden shadow-lg" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-indicators">
                        <?php foreach ($carrusel as $i => $img): ?>
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>"
                                class="<?= $i === 0 ? 'active' : '' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($carrusel as $i => $img): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <img src="<?= esc($img['url'] ?? '') ?>" class="d-block w-100"
                                 style="height:400px;object-fit:cover"
                                 alt="<?= esc($img['titulo'] ?? 'Portfolio') ?>"
                                 loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
                            <?php if (!empty($img['titulo'])): ?>
                            <div class="carousel-caption d-none d-md-block">
                                <h5 class="fw-bold"><?= esc($img['titulo']) ?></h5>
                                <?php if (!empty($img['descripcion'])): ?>
                                <p class="small"><?= esc($img['descripcion']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <?php else: ?>
                <div class="d-flex align-items-center justify-content-center rounded-4 bg-primary bg-opacity-5 shadow-sm" style="height:380px">
                    <div class="text-center text-muted">
                        <i class="bi bi-images fs-1 d-block mb-3 text-primary"></i>
                        <p>Carrusel pendiente de imágenes</p>
                        <?php if (session()->get('admin_logueado')): ?>
                        <a href="<?= base_url('carrusel') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i>Agregar Imágenes
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SERVICIOS ===================== -->
<?php if (!empty($servicios)): ?>
<section class="mb-5">
    <div class="text-center mb-4">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 mb-2">Servicios</span>
        <h2 class="fw-bold">¿En qué puedo ayudarte?</h2>
        <p class="text-muted">Soluciones tecnológicas especializadas para tu negocio</p>
    </div>
    <div class="row g-3">
        <?php foreach ($servicios as $srv): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body p-4">
                    <div class="bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 text-<?= esc($srv['color'] ?? 'primary') ?> rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px">
                        <i class="bi <?= esc($srv['icono'] ?? 'bi-gear') ?> fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?= esc($srv['titulo']) ?></h5>
                    <p class="text-muted small mb-0"><?= esc($srv['descripcion']) ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="<?= base_url('servicios') ?>" class="btn btn-outline-primary">
            Ver todos los servicios <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- ===================== SOBRE MÍ ===================== -->
<?php if (!empty($sobre_mi['descripcion'])): ?>
<section class="mb-5 p-4 rounded-4 shadow-sm" style="background:var(--bg-card)">
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">Sobre Mí</span>
            <h2 class="fw-bold mb-3"><?= esc($sobre_mi['titulo'] ?? '') ?></h2>
            <p class="text-muted"><?= nl2br(esc($sobre_mi['descripcion'])) ?></p>
            <div class="d-flex gap-3 mt-3 flex-wrap">
                <?php if (!empty($sobre_mi['email_contacto'])): ?>
                <a href="mailto:<?= esc($sobre_mi['email_contacto']) ?>" class="btn btn-primary">
                    <i class="bi bi-envelope me-2"></i>Enviar Email
                </a>
                <?php endif; ?>
                <a href="<?= base_url('contacto') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-chat me-2"></i>Contactar
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <?php
            $habilidades = $sobre_mi['habilidades'] ?? [];
            if (is_string($habilidades)) $habilidades = json_decode($habilidades, true) ?: [];
            if (!empty($habilidades)):
            ?>
            <h6 class="fw-bold mb-3">Habilidades Técnicas</h6>
            <?php foreach ($habilidades as $h): ?>
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="small fw-semibold"><?= esc($h['nombre'] ?? '') ?></span>
                    <span class="small text-muted"><?= (int)($h['nivel'] ?? 0) ?>%</span>
                </div>
                <div class="progress" style="height:8px;border-radius:4px">
                    <div class="progress-bar bg-primary" style="width:<?= (int)($h['nivel'] ?? 0) ?>%;border-radius:4px"></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===================== CTA ===================== -->
<section class="text-center py-5 rounded-4 mb-2" style="background:var(--primary-light)">
    <h3 class="fw-bold mb-3">¿Tienes un proyecto en mente?</h3>
    <p class="text-muted mb-4">Hablemos sobre cómo puedo ayudarte a construirlo.</p>
    <a href="<?= base_url('contacto') ?>" class="btn btn-primary btn-lg px-5">
        <i class="bi bi-chat-dots me-2"></i>Iniciar Conversación
    </a>
</section>

<?php if (session()->get('admin_logueado')): ?>
<div class="alert alert-info border-0 shadow-sm mt-4 d-flex align-items-center gap-3">
    <i class="bi bi-info-circle fs-4 text-info"></i>
    <div>
        <strong>Sesión Administrativa Activa</strong>
        <small class="d-block text-muted">Tienes acceso completo al panel — <a href="<?= base_url('admin/dashboard') ?>">ir al dashboard</a></small>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
