<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- ===================== HERO ===================== -->
<section class="hero-section mb-5 position-relative overflow-hidden">
    <!-- Decorative blobs -->
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>

    <div class="row align-items-center g-4 position-relative">
        <div class="col-lg-5 order-lg-1 order-2">
            <div class="animate-slide-in">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 mb-4 rounded-pill border" style="background:rgba(79,70,229,.06);border-color:rgba(79,70,229,.2)!important">
                    <span class="pulse-dot"></span>
                    <span class="small fw-semibold text-primary">Disponible para proyectos</span>
                </div>
                <h1 class="display-4 fw-bold mb-3 lh-sm" style="color:var(--text-heading)">
                    <?= esc($sobre_mi['titulo'] ?? 'Desarrollador Backend') ?>
                    <br><span class="gradient-text">&amp; Cloud Expert</span>
                </h1>
                <p class="lead mb-4" style="color:var(--text-muted)">
                    <?= esc($sobre_mi['subtitulo'] ?? 'Especialista en arquitectura de sistemas escalables y soluciones cloud modernas.') ?>
                </p>

                <!-- Stats -->
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <div class="stat-chip">
                        <span class="stat-number text-primary"><?= (int)($sobre_mi['experiencia_anos'] ?? 5) ?>+</span>
                        <span class="stat-label">Años exp.</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-number text-success"><?= (int)($sobre_mi['proyectos_completados'] ?? 48) ?>+</span>
                        <span class="stat-label">Proyectos</span>
                    </div>
                    <div class="stat-chip">
                        <span class="stat-number text-warning"><?= (int)($sobre_mi['clientes_satisfechos'] ?? 32) ?>+</span>
                        <span class="stat-label">Clientes</span>
                    </div>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= base_url('portafolio') ?>" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="bi bi-briefcase me-2"></i>Ver Portafolio
                    </a>
                    <a href="<?= base_url('contacto') ?>" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-envelope me-2"></i>Contactar
                    </a>
                    <?php if (!empty($sobre_mi['github_url'])): ?>
                    <a href="<?= esc($sobre_mi['github_url']) ?>" target="_blank" rel="noopener" class="btn btn-dark btn-lg">
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
                                 style="height:420px;object-fit:cover"
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
                <div class="hero-placeholder rounded-4 shadow-sm d-flex align-items-center justify-content-center" style="height:380px">
                    <div class="text-center">
                        <div class="hero-placeholder-icon mb-3">
                            <i class="bi bi-code-slash display-3 text-primary"></i>
                        </div>
                        <p class="text-muted mb-0">Construyendo soluciones innovadoras</p>
                        <small class="text-muted opacity-50">Portfolio · Supabase · CodeIgniter 4</small>
                        <?php if (session()->get('admin_logueado')): ?>
                        <div class="mt-3">
                            <a href="<?= base_url('carrusel') ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-1"></i>Agregar Imágenes
                            </a>
                        </div>
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
    <div class="section-header text-center mb-4">
        <span class="section-eyebrow">Servicios</span>
        <h2 class="fw-bold">¿En qué puedo ayudarte?</h2>
        <p class="text-muted">Soluciones tecnológicas especializadas para impulsar tu negocio</p>
    </div>
    <div class="row g-3">
        <?php foreach ($servicios as $srv): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 hover-card service-card-home">
                <div class="card-body p-4">
                    <div class="service-icon-wrap bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 mb-3">
                        <i class="bi <?= esc($srv['icono'] ?? 'bi-gear') ?> text-<?= esc($srv['color'] ?? 'primary') ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?= esc($srv['titulo']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($srv['descripcion']) ?></p>
                    <a href="<?= base_url('detalles/' . (int)$srv['id']) ?>" class="stretched-link text-primary small fw-semibold text-decoration-none">
                        Ver detalles <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
        <a href="<?= base_url('servicios') ?>" class="btn btn-outline-primary rounded-pill px-4">
            Ver todos los servicios <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</section>
<?php endif; ?>

<!-- ===================== PROYECTOS DESTACADOS ===================== -->
<section class="mb-5">
    <div class="section-header text-center mb-4">
        <span class="section-eyebrow eyebrow-purple">Portafolio</span>
        <h2 class="fw-bold">Proyectos Destacados</h2>
        <p class="text-muted">Algunos de los trabajos más recientes y relevantes</p>
    </div>
    <div id="homeProyectos" class="row g-4">
        <div class="col-12 text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div>Cargando proyectos...
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="<?= base_url('portafolio') ?>" class="btn btn-outline-primary rounded-pill px-4">
            Ver portafolio completo <i class="bi bi-briefcase ms-1"></i>
        </a>
    </div>
</section>

<!-- ===================== PROCESO ===================== -->
<section class="mb-5 p-4 p-lg-5 rounded-4" style="background:var(--bg-card)">
    <div class="section-header text-center mb-5">
        <span class="section-eyebrow eyebrow-cyan">Metodología</span>
        <h2 class="fw-bold">Cómo trabajo</h2>
    </div>
    <div class="row g-4 text-center">
        <?php
        $pasos = [
            ['icon' => 'bi-search',         'color' => 'primary', 'n' => '01', 'titulo' => 'Análisis',      'desc' => 'Entiendo a fondo tus necesidades y objetivos antes de escribir una sola línea de código.'],
            ['icon' => 'bi-diagram-3',       'color' => 'info',    'n' => '02', 'titulo' => 'Arquitectura',  'desc' => 'Diseño la solución técnica óptima: base de datos, APIs, estructura y tecnologías adecuadas.'],
            ['icon' => 'bi-code-slash',      'color' => 'success', 'n' => '03', 'titulo' => 'Desarrollo',    'desc' => 'Implementación iterativa con código limpio, seguro y bien documentado.'],
            ['icon' => 'bi-rocket-takeoff',  'color' => 'warning', 'n' => '04', 'titulo' => 'Despliegue',    'desc' => 'Entrega en producción con CI/CD, pruebas y soporte post-lanzamiento.'],
        ];
        ?>
        <?php foreach ($pasos as $paso): ?>
        <div class="col-md-6 col-xl-3">
            <div class="proceso-step">
                <div class="proceso-num text-<?= $paso['color'] ?>"><?= $paso['n'] ?></div>
                <div class="proceso-icon bg-<?= $paso['color'] ?> bg-opacity-10 text-<?= $paso['color'] ?>">
                    <i class="bi <?= $paso['icon'] ?> fs-3"></i>
                </div>
                <h5 class="fw-bold mt-3 mb-2"><?= $paso['titulo'] ?></h5>
                <p class="text-muted small mb-0"><?= $paso['desc'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===================== SOBRE MÍ + SKILLS ===================== -->
<?php if (!empty($sobre_mi['descripcion'])): ?>
<section class="mb-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-5">
            <span class="section-eyebrow eyebrow-green">Sobre Mí</span>
            <h2 class="fw-bold mb-3 mt-2"><?= esc($sobre_mi['titulo'] ?? '') ?></h2>
            <p class="text-muted mb-4"><?= nl2br(esc($sobre_mi['descripcion'])) ?></p>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php if (!empty($sobre_mi['linkedin_url'])): ?>
                <a href="<?= esc($sobre_mi['linkedin_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-linkedin me-1"></i>LinkedIn
                </a>
                <?php endif; ?>
                <?php if (!empty($sobre_mi['github_url'])): ?>
                <a href="<?= esc($sobre_mi['github_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-github me-1"></i>GitHub
                </a>
                <?php endif; ?>
                <?php if (!empty($sobre_mi['email_contacto'])): ?>
                <a href="mailto:<?= esc($sobre_mi['email_contacto']) ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-envelope me-1"></i>Email
                </a>
                <?php endif; ?>
                <a href="<?= base_url('sobre-mi') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-person me-1"></i>Más sobre mí
                </a>
            </div>
        </div>
        <div class="col-lg-7">
            <?php
            $habilidades = $sobre_mi['habilidades'] ?? [];
            if (is_string($habilidades)) $habilidades = json_decode($habilidades, true) ?: [];
            if (!empty($habilidades)):
            ?>
            <div class="p-4 rounded-4 shadow-sm" style="background:var(--bg-card)">
                <h6 class="fw-bold mb-4 text-muted small text-uppercase letter-spacing-1">Habilidades Técnicas</h6>
                <div class="row g-3">
                <?php foreach ($habilidades as $h): $nivel = (int)($h['nivel'] ?? 0); ?>
                <div class="col-12">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= esc($h['nombre'] ?? '') ?></span>
                        <span class="small fw-bold" style="color:var(--primary-color)"><?= $nivel ?>%</span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:4px;background:rgba(79,70,229,.1)">
                        <div class="progress-bar" style="width:<?= $nivel ?>%;border-radius:4px;background:linear-gradient(90deg,#4f46e5,#7c3aed)"></div>
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ===================== CTA ===================== -->
<section class="cta-section text-center py-5 px-4 rounded-4 mb-2">
    <div class="cta-glow"></div>
    <div class="position-relative">
        <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 mb-3 small fw-semibold">
            <i class="bi bi-stars me-1"></i>¿Listo para empezar?
        </span>
        <h2 class="fw-bold text-white mb-3">¿Tienes un proyecto en mente?</h2>
        <p class="mb-4 opacity-75 text-white lead">Hablemos sobre cómo construirlo juntos. Sin compromisos.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= base_url('contacto') ?>" class="btn btn-light btn-lg px-5 shadow fw-semibold">
                <i class="bi bi-chat-dots me-2 text-primary"></i>Iniciar Conversación
            </a>
            <a href="<?= base_url('servicios') ?>" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-grid me-2"></i>Ver Servicios
            </a>
        </div>
    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('<?= base_url('proyectos/listar') ?>')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('homeProyectos');
            const proyectos = (data.status === 'success' ? data.data : []).filter(p => p.destacado).slice(0, 3);
            if (!proyectos.length) {
                container.innerHTML = `<div class="col-12 text-center py-4 text-muted"><i class="bi bi-inbox display-4 d-block mb-2 opacity-25"></i><small>Los proyectos se mostrarán aquí pronto.</small></div>`;
                return;
            }
            container.innerHTML = proyectos.map(p => {
                const techs = Array.isArray(p.tecnologias) ? p.tecnologias : (p.tecnologias ? String(p.tecnologias).split(',') : []);
                const badges = techs.slice(0,3).map(t => `<span class="badge bg-primary bg-opacity-10 text-primary me-1">${escapeHtml(t.trim())}</span>`).join('');
                const desc = p.descripcion ? (p.descripcion.length > 100 ? p.descripcion.substring(0, 100) + '…' : p.descripcion) : '';
                return `
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <h5 class="fw-bold mb-0">${escapeHtml(p.titulo)}</h5>
                                ${p.destacado ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Destacado</span>' : ''}
                            </div>
                            <p class="text-muted small flex-grow-1 mb-3">${escapeHtml(desc)}</p>
                            <div class="mb-3">${badges}</div>
                            ${p.link ? `<a href="${escapeHtml(p.link)}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-auto"><i class="bi bi-link-45deg me-1"></i>Ver proyecto</a>` : `<button class="btn btn-sm btn-outline-secondary rounded-pill mt-auto" disabled><i class="bi bi-lock me-1"></i>Privado</button>`}
                        </div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(() => {
            document.getElementById('homeProyectos').innerHTML = '';
        });
});

function escapeHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?= $this->endSection() ?>
