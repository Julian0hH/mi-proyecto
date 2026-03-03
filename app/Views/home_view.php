<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<!-- ═══════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════ -->
<section class="hero-section mb-5 position-relative overflow-hidden">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob hero-blob-3"></div>

    <div class="row align-items-center g-4 position-relative">
        <div class="col-lg-6 order-lg-1 order-2">
            <div class="animate-slide-in">

                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 mb-4 rounded-pill ds-badge-available">
                    <span class="pulse-dot"></span>
                    <span class="small fw-semibold text-primary">Aceptando nuevos proyectos · <?= date('Y') ?></span>
                </div>

                <h1 class="display-4 fw-bold mb-3 lh-sm" style="color:var(--text-heading)">
                    Transformamos tus ideas<br>
                    en <span class="gradient-text">software profesional</span>
                </h1>
                <p class="lead mb-4" style="color:var(--text-muted)">
                    <?= esc($sobre_mi['subtitulo'] ?? 'Desarrollamos soluciones web a medida: aplicaciones empresariales, APIs, plataformas cloud y sistemas de gestión que impulsan tu negocio.') ?>
                </p>

                <!-- Stats chips con counter animado -->
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <div class="stat-chip reveal">
                        <span class="stat-number text-primary counter" data-target="<?= (int)($sobre_mi['proyectos_completados'] ?? 48) ?>">0</span>
                        <span class="stat-label">Proyectos entregados</span>
                    </div>
                    <div class="stat-chip reveal">
                        <span class="stat-number text-success counter" data-target="<?= (int)($sobre_mi['clientes_satisfechos'] ?? 32) ?>">0</span>
                        <span class="stat-label">Clientes satisfechos</span>
                    </div>
                    <div class="stat-chip reveal">
                        <span class="stat-number text-warning counter" data-target="<?= (int)($sobre_mi['experiencia_anos'] ?? 5) ?>">0</span>
                        <span class="stat-label">Años de experiencia</span>
                    </div>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?= base_url('servicios') ?>" class="btn btn-primary btn-lg px-4 shadow-sm btn-glow">
                        <i class="bi bi-lightning-fill me-2"></i>Ver Servicios
                    </a>
                    <a href="<?= base_url('contacto') ?>" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-envelope me-2"></i>Solicitar Cotización
                    </a>
                </div>

                <!-- Trust badges -->
                <div class="d-flex align-items-center gap-3 mt-4 flex-wrap">
                    <span class="ds-trust-badge"><i class="bi bi-patch-check-fill text-success me-1"></i>Entrega garantizada</span>
                    <span class="ds-trust-badge"><i class="bi bi-shield-fill-check text-primary me-1"></i>Código seguro</span>
                    <span class="ds-trust-badge"><i class="bi bi-headset text-info me-1"></i>Soporte incluido</span>
                </div>
            </div>
        </div>

        <!-- Carrusel o placeholder visual -->
        <div class="col-lg-6 order-lg-2 order-1">
            <div class="animate-fade-in position-relative">
                <?php if (!empty($carrusel)): ?>
                <div id="heroCarousel" class="carousel slide carousel-fade rounded-4 overflow-hidden shadow-2xl" data-bs-ride="carousel" data-bs-interval="5000">
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
                                 style="height:440px;object-fit:cover"
                                 alt="<?= esc($img['titulo'] ?? 'DevSoft Solutions') ?>"
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
                <div class="ds-hero-visual rounded-4 shadow-2xl d-flex align-items-center justify-content-center" style="height:420px">
                    <div class="ds-code-window">
                        <div class="ds-code-bar">
                            <span class="ds-dot" style="background:#ff5f57"></span>
                            <span class="ds-dot" style="background:#febc2e"></span>
                            <span class="ds-dot" style="background:#28c840"></span>
                            <span class="ms-2 text-muted" style="font-size:.7rem">devsoft.solutions/app</span>
                        </div>
                        <div class="ds-code-body">
                            <div class="ds-code-line"><span class="ds-kw">class</span> <span class="ds-cls">DevSoftSolution</span> {</div>
                            <div class="ds-code-line ps-3"><span class="ds-fn">build</span>(<span class="ds-str">yourIdea</span>) {</div>
                            <div class="ds-code-line ps-5"><span class="ds-kw">return</span> <span class="ds-str">'Éxito garantizado'</span>;</div>
                            <div class="ds-code-line ps-3">}</div>
                            <div class="ds-code-line">}</div>
                            <div class="ds-code-line mt-2 ds-comment">// Listos para iniciar tu proyecto</div>
                            <div class="ds-cursor">▌</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Floating card: último proyecto -->
                <div class="ds-float-card animate-float">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <div>
                        <div class="small fw-bold">Proyecto entregado</div>
                        <div class="tiny text-muted">Plataforma SaaS · +40% productividad</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     TECH STACK
═══════════════════════════════════════════════════════════ -->
<section class="mb-5 reveal">
    <div class="text-center mb-3">
        <p class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.12em">Tecnologías que dominamos</p>
    </div>
    <div class="ds-tech-track">
        <div class="ds-tech-list">
            <?php
            $techs = [
                ['icon'=>'bi-filetype-php',   'name'=>'PHP 8',       'color'=>'#7c3aed'],
                ['icon'=>'bi-database',        'name'=>'PostgreSQL',  'color'=>'#336791'],
                ['icon'=>'bi-cloud',           'name'=>'Supabase',    'color'=>'#3ecf8e'],
                ['icon'=>'bi-code-slash',      'name'=>'CodeIgniter', 'color'=>'#ef4444'],
                ['icon'=>'bi-bootstrap',       'name'=>'Bootstrap',   'color'=>'#7952b3'],
                ['icon'=>'bi-server',          'name'=>'REST API',    'color'=>'#0891b2'],
                ['icon'=>'bi-git',             'name'=>'Git / CI·CD', 'color'=>'#f05032'],
                ['icon'=>'bi-clouds',          'name'=>'Cloud',       'color'=>'#2563eb'],
                ['icon'=>'bi-lock-fill',       'name'=>'JWT / Auth',  'color'=>'#d97706'],
                ['icon'=>'bi-bar-chart-line',  'name'=>'Analytics',   'color'=>'#059669'],
                ['icon'=>'bi-filetype-js',     'name'=>'JavaScript',  'color'=>'#f59e0b'],
                ['icon'=>'bi-phone',           'name'=>'Responsive',  'color'=>'#8b5cf6'],
            ];
            $repeated = array_merge($techs, $techs); // duplicate for infinite scroll
            foreach ($repeated as $t): ?>
            <div class="ds-tech-pill">
                <i class="bi <?= $t['icon'] ?>" style="color:<?= $t['color'] ?>"></i>
                <span><?= $t['name'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     SERVICIOS
═══════════════════════════════════════════════════════════ -->
<?php if (!empty($servicios)): ?>
<section class="mb-5">
    <div class="section-header text-center mb-4 reveal">
        <span class="section-eyebrow">Nuestros Servicios</span>
        <h2 class="fw-bold">¿Qué podemos construir para ti?</h2>
        <p class="text-muted">Soluciones tecnológicas personalizadas para llevar tu empresa al siguiente nivel</p>
    </div>
    <div class="row g-3">
        <?php foreach ($servicios as $idx => $srv): ?>
        <div class="col-sm-6 col-xl-3 reveal" style="animation-delay:<?= $idx * 80 ?>ms">
            <div class="card border-0 shadow-sm h-100 hover-card service-card-home">
                <div class="card-body p-4">
                    <div class="service-icon-wrap bg-<?= esc($srv['color'] ?? 'primary') ?> bg-opacity-10 mb-3">
                        <i class="bi <?= esc($srv['icono'] ?? 'bi-gear') ?> text-<?= esc($srv['color'] ?? 'primary') ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?= esc($srv['titulo']) ?></h5>
                    <p class="text-muted small mb-3"><?= esc($srv['descripcion']) ?></p>
                    <?php if (!empty($srv['precio_desde'])): ?>
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">Desde $<?= number_format((float)$srv['precio_desde']) ?></span>
                    </div>
                    <?php endif; ?>
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

<!-- ═══════════════════════════════════════════════════════════
     PROYECTOS DESTACADOS
═══════════════════════════════════════════════════════════ -->
<section class="mb-5">
    <div class="section-header text-center mb-4 reveal">
        <span class="section-eyebrow eyebrow-purple">Nuestro Portfolio</span>
        <h2 class="fw-bold">Proyectos Que Hablan Por Nosotros</h2>
        <p class="text-muted">Casos de éxito de empresas que confiaron en DevSoft Solutions</p>
    </div>
    <div id="homeProyectos" class="row g-4">
        <div class="col-12 text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm me-2 text-primary"></div>Cargando proyectos...
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="<?= base_url('portafolio') ?>" class="btn btn-outline-primary rounded-pill px-4">
            Ver portfolio completo <i class="bi bi-briefcase ms-1"></i>
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     PROCESO DE TRABAJO
═══════════════════════════════════════════════════════════ -->
<section class="mb-5 p-4 p-lg-5 rounded-4 reveal" style="background:var(--bg-card)">
    <div class="section-header text-center mb-5">
        <span class="section-eyebrow eyebrow-cyan">Metodología Ágil</span>
        <h2 class="fw-bold">Cómo desarrollamos tu proyecto</h2>
        <p class="text-muted">Un proceso probado para entregar resultados de calidad</p>
    </div>
    <div class="row g-4 text-center">
        <?php
        $pasos = [
            ['icon'=>'bi-chat-dots',       'color'=>'primary', 'n'=>'01', 'titulo'=>'Consultoría',   'desc'=>'Analizamos tus necesidades, objetivos y definimos el alcance técnico del proyecto.'],
            ['icon'=>'bi-diagram-3',        'color'=>'info',    'n'=>'02', 'titulo'=>'Arquitectura',  'desc'=>'Diseñamos la solución: BD, APIs, UI/UX. Eliges tecnologías y apruebas el plan.'],
            ['icon'=>'bi-code-slash',       'color'=>'success', 'n'=>'03', 'titulo'=>'Desarrollo',    'desc'=>'Sprints ágiles con demos semanales. Código limpio, probado y documentado.'],
            ['icon'=>'bi-rocket-takeoff',   'color'=>'warning', 'n'=>'04', 'titulo'=>'Lanzamiento',   'desc'=>'Despliegue en producción, capacitación del equipo y soporte post-entrega garantizado.'],
        ];
        ?>
        <?php foreach ($pasos as $paso): ?>
        <div class="col-md-6 col-xl-3 reveal">
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

<!-- ═══════════════════════════════════════════════════════════
     SOBRE NOSOTROS + SKILLS
═══════════════════════════════════════════════════════════ -->
<?php if (!empty($sobre_mi['descripcion'])): ?>
<section class="mb-5 reveal">
    <div class="row align-items-center g-5">
        <div class="col-lg-5">
            <span class="section-eyebrow eyebrow-green">Quiénes Somos</span>
            <h2 class="fw-bold mb-3 mt-2">Equipo de desarrolladores expertos</h2>
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
                <a href="<?= base_url('sobre-mi') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-arrow-right me-1"></i>Conoce al equipo
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
                <h6 class="fw-bold mb-4 text-muted small text-uppercase letter-spacing-1">Stack Tecnológico</h6>
                <div class="row g-3">
                <?php foreach ($habilidades as $h): $nivel = (int)($h['nivel'] ?? 0); ?>
                <div class="col-12">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold"><?= esc($h['nombre'] ?? '') ?></span>
                        <span class="small fw-bold" style="color:var(--primary-color)"><?= $nivel ?>%</span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:4px">
                        <div class="progress-bar skill-bar" data-width="<?= $nivel ?>" style="width:0%;border-radius:4px;background:linear-gradient(90deg,#4f46e5,#7c3aed);transition:width 1.2s cubic-bezier(.4,0,.2,1)"></div>
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

<!-- ═══════════════════════════════════════════════════════════
     TESTIMONIOS
═══════════════════════════════════════════════════════════ -->
<section class="mb-5">
    <div class="section-header text-center mb-4 reveal">
        <span class="section-eyebrow" style="color:#d97706;background:rgba(217,119,6,.08)">Testimonios</span>
        <h2 class="fw-bold">Lo que dicen nuestros clientes</h2>
        <p class="text-muted">Empresas que transformaron sus operaciones con nuestras soluciones</p>
    </div>
    <div class="row g-4">
        <?php
        $testimonios = [
            ['nombre'=>'María González',   'cargo'=>'CEO, TechRetail',      'avatar'=>'MG', 'color'=>'primary',
             'texto'=>'DevSoft desarrolló nuestra plataforma de e-commerce en tiempo récord. Las ventas online crecieron un 180% en el primer trimestre tras el lanzamiento.'],
            ['nombre'=>'Carlos Mendoza',   'cargo'=>'CTO, FinanceApp',      'avatar'=>'CM', 'color'=>'success',
             'texto'=>'La API financiera que construyeron maneja +50,000 transacciones diarias sin un solo fallo. Código impecable, documentación excelente y soporte 24/7.'],
            ['nombre'=>'Ana Martínez',     'cargo'=>'Directora, EduOnline',  'avatar'=>'AM', 'color'=>'warning',
             'texto'=>'Transformaron nuestro proceso de matrícula completamente. Lo que tardaba semanas ahora toma minutos. El ROI fue inmediato desde el primer mes.'],
        ];
        ?>
        <?php foreach ($testimonios as $idx => $t): ?>
        <div class="col-md-4 reveal" style="animation-delay:<?= $idx * 100 ?>ms">
            <div class="ds-testimonial-card h-100">
                <div class="ds-quote-icon"><i class="bi bi-quote"></i></div>
                <p class="ds-testimonial-text">"<?= $t['texto'] ?>"</p>
                <div class="d-flex align-items-center gap-3 mt-auto">
                    <div class="ds-avatar bg-<?= $t['color'] ?> bg-opacity-15 text-<?= $t['color'] ?>">
                        <?= $t['avatar'] ?>
                    </div>
                    <div>
                        <div class="fw-semibold small"><?= $t['nombre'] ?></div>
                        <div class="tiny text-muted"><?= $t['cargo'] ?></div>
                        <div class="mt-1">
                            <?php for ($s=0;$s<5;$s++): ?>
                            <i class="bi bi-star-fill" style="color:#f59e0b;font-size:.65rem"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     ESTADÍSTICAS GENERALES
═══════════════════════════════════════════════════════════ -->
<section class="mb-5 reveal">
    <div class="ds-stats-bar rounded-4 p-4 p-lg-5">
        <div class="row g-4 text-center text-white">
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-black counter" data-target="<?= (int)($sobre_mi['proyectos_completados'] ?? 48) ?>">0</div>
                <div class="small opacity-75">Proyectos Completados</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-black counter" data-target="<?= (int)($sobre_mi['clientes_satisfechos'] ?? 32) ?>">0</div>
                <div class="small opacity-75">Clientes Satisfechos</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-black counter" data-target="<?= (int)($sobre_mi['experiencia_anos'] ?? 5) ?>">0</div>
                <div class="small opacity-75">Años de Experiencia</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-1 fw-black">99<span style="font-size:.5em">%</span></div>
                <div class="small opacity-75">Tasa de Satisfacción</div>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     CTA FINAL
═══════════════════════════════════════════════════════════ -->
<section class="cta-section text-center py-5 px-4 rounded-4 mb-2 reveal">
    <div class="cta-glow"></div>
    <div class="position-relative">
        <span class="badge rounded-pill bg-white bg-opacity-20 text-white px-3 py-2 mb-3 small fw-semibold">
            <i class="bi bi-stars me-1"></i>¿Tienes un proyecto en mente?
        </span>
        <h2 class="fw-bold text-white mb-3 display-6">Construyamos algo increíble juntos</h2>
        <p class="mb-4 opacity-75 text-white lead">Primera consultoría gratuita. Sin compromisos. Respondemos en menos de 24h.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= base_url('contacto') ?>" class="btn btn-light btn-lg px-5 shadow fw-semibold btn-cta-pulse">
                <i class="bi bi-send me-2 text-primary"></i>Solicitar Cotización Gratis
            </a>
            <a href="<?= base_url('servicios') ?>" class="btn btn-outline-light btn-lg px-4">
                <i class="bi bi-grid me-2"></i>Ver Servicios
            </a>
        </div>
    </div>
</section>

<?php if (session()->get('admin_logueado')): ?>
<div class="alert alert-primary border-0 shadow-sm mt-4 d-flex align-items-center gap-3">
    <i class="bi bi-shield-fill-check fs-4 text-primary"></i>
    <div>
        <strong>Sesión Administrativa Activa — <?= esc(session()->get('admin_nombre') ?? 'Admin') ?></strong>
        <small class="d-block text-muted">
            <a href="<?= base_url('admin/dashboard') ?>">Ir al Dashboard</a>
        </small>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Cargar proyectos destacados ─────────────────────────────
    fetch('<?= base_url('proyectos/listar') ?>')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('homeProyectos');
            const proyectos = (data.status === 'success' ? data.data : []).filter(p => p.destacado).slice(0, 3);
            if (!proyectos.length) {
                container.innerHTML = `<div class="col-12 text-center py-4 text-muted"><i class="bi bi-folder2-open display-4 d-block mb-2 opacity-25"></i><small>Los proyectos se mostrarán aquí pronto.</small></div>`;
                return;
            }
            container.innerHTML = proyectos.map((p, i) => {
                const techs   = Array.isArray(p.tecnologias) ? p.tecnologias : (p.tecnologias ? String(p.tecnologias).split(',') : []);
                const badges  = techs.slice(0,4).map(t => `<span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1">${escHtml(t.trim())}</span>`).join('');
                const desc    = p.descripcion ? (p.descripcion.length > 120 ? p.descripcion.substring(0, 120) + '…' : p.descripcion) : '';
                const colors  = ['primary','success','warning'];
                return `
                <div class="col-md-6 col-lg-4 reveal" style="animation-delay:${i*100}ms">
                    <div class="card border-0 shadow-sm h-100 hover-card ds-project-card">
                        <div class="ds-project-header bg-${colors[i%3]} bg-opacity-10">
                            <i class="bi bi-folder2-open fs-2 text-${colors[i%3]} opacity-50"></i>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <h5 class="fw-bold mb-0 me-2">${escHtml(p.titulo)}</h5>
                                ${p.destacado ? '<span class="badge bg-warning text-dark flex-shrink-0"><i class="bi bi-star-fill me-1"></i>Destacado</span>' : ''}
                            </div>
                            <p class="text-muted small flex-grow-1 mb-3">${escHtml(desc)}</p>
                            <div class="mb-3">${badges}</div>
                            ${p.link
                                ? `<a href="${escHtml(p.link)}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill mt-auto"><i class="bi bi-link-45deg me-1"></i>Ver proyecto</a>`
                                : `<button class="btn btn-sm btn-outline-secondary rounded-pill mt-auto" disabled><i class="bi bi-lock me-1"></i>Proyecto privado</button>`}
                        </div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(() => { document.getElementById('homeProyectos').innerHTML = ''; });

    // ── Scroll Reveal (IntersectionObserver) ────────────────────
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // ── Animated Counters ────────────────────────────────────────
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el     = entry.target;
            const target = +el.dataset.target;
            const duration = 1600;
            const start  = performance.now();
            function update(now) {
                const elapsed = now - start;
                const progress = Math.min(elapsed / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3); // ease out cubic
                el.textContent = Math.round(eased * target) + (el.dataset.suffix || '+');
                if (progress < 1) requestAnimationFrame(update);
            }
            requestAnimationFrame(update);
            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.counter').forEach(el => {
        el.dataset.suffix = '+';
        counterObserver.observe(el);
    });

    // ── Skill bars ───────────────────────────────────────────────
    const skillObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.querySelectorAll('.skill-bar').forEach(bar => {
                bar.style.width = bar.dataset.width + '%';
            });
            skillObserver.unobserve(entry.target);
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.skill-bar').forEach(bar => {
        const section = bar.closest('section');
        if (section) skillObserver.observe(section);
    });
});

function escHtml(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?= $this->endSection() ?>
