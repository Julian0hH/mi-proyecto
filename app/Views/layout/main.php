<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? esc($pageTitle) . ' | ' : '' ?>DevSoft Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999"></div>

<div id="sidebar-overlay"></div>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center gap-2">
                <div class="sidebar-logo">
                    <i class="bi bi-braces"></i>
                </div>
                <div class="sidebar-brand-text">
                    <span class="fw-bold">Dev</span><span style="color:#818cf8;font-weight:700">Soft</span><span class="fw-light opacity-75" style="font-size:.75em;display:block;margin-top:-2px;letter-spacing:.06em">SOLUTIONS</span>
                </div>
            </div>
        </div>

        <div class="sidebar-search">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="sidebar-search-input" class="form-control" placeholder="Buscar...">
            </div>
        </div>

        <?php
        $permisos       = session('permisos')        ?? [];
        $sidebarModulos = session('sidebar_modulos') ?? [];
        $isLogued       = (bool) session('logueado');

        function canSee(array $p, string $ruta): bool {
            return !empty($p[$ruta]['consulta']);
        }
        $rutaUrlMap = [
            'admin/carrusel' => 'carrusel',
        ];

        $grupoMeta = [
            'Portafolio' => ['icono' => 'bi-pencil-square',      'color' => 'nav-icon-blue'],
            'Seguridad'  => ['icono' => 'bi-shield-lock-fill',   'color' => 'nav-icon-orange'],
        ];
        $grupoMetaDefault = ['icono' => 'bi-folder2-open', 'color' => 'nav-icon-cyan'];

        $gruposMods = [];
        foreach ($sidebarModulos as $mod) {
            if ($mod['grupo'] === 'General') continue;
            $gruposMods[$mod['grupo']][] = $mod;
        }

        $isPortSection = url_is('/') || url_is('portafolio*') || url_is('servicios*')
                      || url_is('detalles*') || url_is('contratar*') || url_is('sobre-mi*') || url_is('contacto*');
        ?>

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-section"><span>Portafolio</span></li>
            <li class="nav-accordion <?= $isPortSection ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi bi-globe2 nav-icon-cyan"></i>
                    <span>Sitio Público</span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <li>
                        <a href="<?= base_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>">
                            <i class="bi bi-house-door"></i><span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('portafolio') ?>" class="<?= url_is('portafolio*') ? 'active' : '' ?>">
                            <i class="bi bi-briefcase"></i><span>Portafolio</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('servicios') ?>" class="<?= url_is('servicios*') || url_is('detalles*') || url_is('contratar*') ? 'active' : '' ?>">
                            <i class="bi bi-grid-3x3-gap"></i><span>Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('sobre-mi') ?>" class="<?= url_is('sobre-mi*') ? 'active' : '' ?>">
                            <i class="bi bi-person-circle"></i><span>Sobre Mí</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('contacto') ?>" class="<?= url_is('contacto*') ? 'active' : '' ?>">
                            <i class="bi bi-envelope"></i><span>Contacto</span>
                        </a>
                    </li>
                </ul>
            </li>

            <?php if ($isLogued): ?>
            <li class="nav-section"><span>General</span></li>
            <li>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 nav-icon-indigo"></i><span>Dashboard</span>
                </a>
            </li>

            <?php foreach ($gruposMods as $grupo => $mods):
                $meta    = $grupoMeta[$grupo] ?? $grupoMetaDefault;
                $isOpen  = false;
                foreach ($mods as $m) {
                    $u = $rutaUrlMap[$m['ruta']] ?? $m['ruta'];
                    if (url_is($u.'*') || url_is($m['ruta'].'*')) { $isOpen = true; break; }
                }
            ?>
            <li class="nav-section"><span><?= esc($grupo) ?></span></li>
            <li class="nav-accordion <?= $isOpen ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi <?= esc($meta['icono']) ?> <?= esc($meta['color']) ?>"></i>
                    <span><?= esc($grupo) ?></span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <?php foreach ($mods as $mod):
                        $url = $rutaUrlMap[$mod['ruta']] ?? $mod['ruta'];
                    ?>
                    <li>
                        <a href="<?= base_url($url) ?>" class="<?= (url_is($url.'*') || url_is($mod['ruta'].'*')) ? 'active' : '' ?>">
                            <i class="bi <?= esc($mod['icono']) ?>"></i>
                            <span><?= esc($mod['nombre']) ?></span>
                            <?php if ($mod['ruta'] === 'admin/contactos'): ?>
                            <span class="badge-count" id="badge-contactos" style="display:none"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endforeach; ?>

            <?php endif; // isLogued ?>
        </ul>

        <div class="sidebar-footer">
            <?php if (session('logueado')): ?>
                <div class="user-info">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="user-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="flex-grow-1 text-truncate">
                            <small class="d-block fw-semibold user-name"><?= esc(session('user_nombre') ?? 'Admin') ?></small>
                            <small class="text-muted user-email"><?= esc(session('user_email') ?? '') ?></small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary flex-grow-1" id="btn-simulate-error" title="Simular error">
                            <i class="bi bi-bug"></i> <span>Error</span>
                        </button>
                        <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger flex-grow-1">
                            <i class="bi bi-box-arrow-right"></i> <span>Salir</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i><span class="ms-2">Iniciar Sesión</span>
                </a>
            <?php endif; ?>
        </div>

        <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Contraer menú">
            <i class="bi bi-layout-sidebar-reverse"></i>
            <span class="ms-2 theme-label">Contraer menú</span>
        </button>

        <div class="theme-toggle" id="theme-switch" title="Cambiar tema">
            <i class="bi bi-moon-stars" id="theme-icon"></i>
            <span class="ms-2 theme-label">Tema Oscuro</span>
        </div>
    </nav>

    <div id="content">
        <div class="topbar d-flex align-items-center mb-4 gap-3">
            <button class="btn btn-outline-secondary d-md-none" id="mobile-opener">
                <i class="bi bi-list fs-5"></i>
            </button>

            <nav aria-label="breadcrumb" class="flex-grow-1">
                <ol class="breadcrumb mb-0 align-items-center">
                    <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                        <?php
                        $icons = [
                            'Inicio'           => 'bi-house-door',
                            'Portafolio'       => 'bi-briefcase',
                            'Servicios'        => 'bi-grid-3x3-gap',
                            'Detalles'         => 'bi-info-circle',
                            'Contratar'        => 'bi-credit-card',
                            'Contacto'         => 'bi-envelope',
                            'Sobre Mí'         => 'bi-person-circle',
                            'Login'            => 'bi-box-arrow-in-right',
                            'Registro'         => 'bi-person-plus',
                            'Dashboard'        => 'bi-speedometer2',
                            'Proyectos'        => 'bi-folder-symlink',
                            'Administración'   => 'bi-shield-lock',
                            'Admin'            => 'bi-speedometer2',
                            'Seguridad'        => 'bi-shield-lock-fill',
                            'Perfiles'         => 'bi-person-badge',
                            'Módulos'          => 'bi-grid-3x3-gap',
                            'Permisos-Perfil'  => 'bi-shield-check',
                            'Usuarios'         => 'bi-people',
                            'Principal 1'      => 'bi-layout-text-window',
                            'Principal 2'      => 'bi-layout-text-window-reverse',
                            'Módulo 1.1'       => 'bi-file-earmark',
                            'Módulo 1.2'       => 'bi-file-earmark-text',
                            'Módulo 2.1'       => 'bi-file-earmark',
                            'Módulo 2.2'       => 'bi-file-earmark-text',
                        ];
                        ?>
                        <?php foreach ($breadcrumbs as $i => $crumb): ?>
                            <li class="breadcrumb-item <?= ($crumb['active'] ?? false) ? 'active fw-semibold' : '' ?>">
                                <?php $icon = $icons[$crumb['name']] ?? null; ?>
                                <?php if (!($crumb['active'] ?? false)): ?>
                                    <a href="<?= $crumb['url'] ?>" class="text-decoration-none d-inline-flex align-items-center gap-1">
                                        <?php if ($icon && $i === 0): ?><i class="bi <?= $icon ?> small"></i><?php endif; ?>
                                        <?= esc($crumb['name']) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <?php if ($icon): ?><i class="bi <?= $icon ?> small text-primary"></i><?php endif; ?>
                                        <?= esc($crumb['name']) ?>
                                    </span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>

            <?php if (session('logueado')): ?>
            <div class="dropdown">
                <button class="btn btn-outline-secondary position-relative" id="btn-notificaciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="badge-notif" id="badge-noti" style="display:none">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg noti-dropdown" style="width:340px;max-height:420px;overflow-y:auto">
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                        <strong class="small">Notificaciones</strong>
                        <button class="btn btn-link btn-sm p-0 text-muted" id="btn-mark-all-read">Marcar todas leídas</button>
                    </div>
                    <div id="noti-list"><p class="text-center text-muted small py-3">Cargando...</p></div>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-md-none">
                <?php if (session('logueado')): ?>
                    <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="animate-fade-in">
            <?php
            ?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="<?= base_url('js/toast.js') ?>"></script>
            <script src="<?= base_url('js/validator.js') ?>"></script>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
<?php if (session('logueado')): ?>
<div class="modal fade" id="modalSimularError" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title"><i class="bi bi-bug me-2"></i>Simulador de Errores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Simula errores controlados para visualizar el comportamiento en desarrollo vs producción. <strong>No afecta la aplicación.</strong></p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger" onclick="simularError('generico')"><i class="bi bi-exclamation-triangle me-2"></i>Error 500 - Genérico</button>
                    <button class="btn btn-outline-warning" onclick="simularError('db')"><i class="bi bi-database-x me-2"></i>Error 503 - Base de Datos</button>
                    <button class="btn btn-outline-info" onclick="simularError('auth')"><i class="bi bi-shield-x me-2"></i>Error 401 - Autenticación</button>
                    <button class="btn btn-outline-secondary" onclick="simularError('notfound')"><i class="bi bi-search me-2"></i>Error 404 - No Encontrado</button>
                </div>
                <div id="error-result" class="mt-3" style="display:none"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const html = document.documentElement;
const savedTheme = localStorage.getItem('theme') || 'light';
setTheme(savedTheme);

document.getElementById('theme-switch').addEventListener('click', () => {
    setTheme(html.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
});

function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    const icon = document.getElementById('theme-icon');
    const label = document.querySelector('.theme-label');
    icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
    if (label) label.textContent = theme === 'dark' ? 'Tema Claro' : 'Tema Oscuro';
}

const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const overlay = document.getElementById('sidebar-overlay');

if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
    content.classList.add('expanded');
}

document.getElementById('sidebar-toggle').addEventListener('click', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
        localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
        if (sidebar.classList.contains('collapsed')) {
            document.querySelectorAll('.nav-accordion.open').forEach(el => el.classList.remove('open'));
        }
    } else {
        sidebar.classList.remove('mobile-show');
        overlay.classList.remove('show');
    }
});

const mobileOpener = document.getElementById('mobile-opener');
if (mobileOpener) {
    mobileOpener.addEventListener('click', () => {
        sidebar.classList.add('mobile-show');
        overlay.classList.add('show');
    });
}

overlay.addEventListener('click', () => {
    sidebar.classList.remove('mobile-show');
    overlay.classList.remove('show');
});

const searchInput = document.getElementById('sidebar-search-input');
if (searchInput) {
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        document.querySelectorAll('#sidebar-nav li:not(.nav-section)').forEach(li => {
            const text = li.textContent.toLowerCase();
            li.style.display = q === '' || text.includes(q) ? '' : 'none';
        });
    });
}

<?php if (session('logueado')): ?>
function cargarNotificaciones() {
    fetch('<?= base_url('admin/notificaciones') ?>')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('badge-noti');
            const list  = document.getElementById('noti-list');
            if (data.no_leidas > 0) {
                badge.style.display = 'inline-flex';
                badge.textContent   = data.no_leidas > 9 ? '9+' : data.no_leidas;
            } else {
                badge.style.display = 'none';
            }
            if (!data.data || data.data.length === 0) {
                list.innerHTML = '<p class="text-center text-muted small py-3">Sin notificaciones</p>';
                return;
            }
            list.innerHTML = data.data.map(n => `
                <div class="noti-item px-3 py-2 border-bottom ${n.leido ? '' : 'noti-unread'}" data-id="${n.id}">
                    <div class="d-flex align-items-start gap-2">
                        <span class="noti-dot noti-${n.tipo}"></span>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold">${escapeHtml(n.titulo)}</div>
                            <div class="small text-muted">${escapeHtml(n.mensaje || '')}</div>
                            <div class="tiny text-muted">${formatDate(n.created_at)}</div>
                        </div>
                        ${!n.leido ? `<button class="btn btn-link btn-sm p-0 text-muted btn-mark-read" data-id="${n.id}" title="Marcar leída"><i class="bi bi-check2"></i></button>` : ''}
                    </div>
                </div>`).join('');

            list.querySelectorAll('.btn-mark-read').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.stopPropagation();
                    fetch(`<?= base_url('admin/notificaciones/leida/') ?>${btn.dataset.id}`, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
                        .then(() => cargarNotificaciones());
                });
            });
        }).catch(() => {});
}

document.getElementById('btn-mark-all-read')?.addEventListener('click', () => {
    fetch('<?= base_url('admin/notificaciones/todas-leidas') ?>', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(() => cargarNotificaciones());
});

document.getElementById('btn-notificaciones')?.addEventListener('click', cargarNotificaciones);
cargarNotificaciones();
setInterval(cargarNotificaciones, 30000);

document.getElementById('btn-simulate-error')?.addEventListener('click', () => {
    new bootstrap.Modal(document.getElementById('modalSimularError')).show();
});

function simularError(tipo) {
    const result = document.getElementById('error-result');
    result.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm text-warning"></div> Simulando...</div>';
    result.style.display = 'block';
    fetch(`<?= base_url('admin/simular-error') ?>?tipo=${tipo}`)
        .then(r => r.json())
        .then(data => {
            result.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <strong>${escapeHtml(data.tipo)}</strong> (HTTP ${data.codigo_http})<br>
                    <small>${escapeHtml(data.mensaje)}</small><br>
                    <small class="text-muted">Entorno: <code>${data.entorno}</code> | Stack visible: ${data.detalles_visibles ? 'Sí' : 'No (producción)'}</small>
                </div>`;
            Toast.warning(`Error simulado: ${data.tipo}`);
        })
        .catch(() => { result.innerHTML = '<div class="alert alert-danger">Error de red</div>'; });
}
<?php endif; ?>

function toggleAccordion(el) {
    const li = el.closest('.nav-accordion');
    li.classList.toggle('open');
}
document.querySelectorAll('.nav-accordion.open').forEach(li => li.classList.add('open'));

(function() {
    const VT_PATTERNS = {
        'name':    /[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-\']/g,
        'alpha':   /[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-\']/g,
        'num':     /[^0-9]/g,
        'decimal': /[^0-9.]/g,
        'phone':   /[^0-9+\-\s()]/g,
        'user':    /[^a-zA-Z0-9_\-]/g,
        'icon':    /[^a-zA-Z0-9\-]/g,
        'nohtml':  /[<>]/g,
        'alnum':   /[^a-zA-Z0-9áéíóúÁÉÍÓÚüÜñÑ\s\-]/g,
        'alnumsym':/[<>'"`;]/g,
        'slug':    /[^a-zA-Z0-9\-\/]/g,
    };

    const BLOCK_PATTERNS = [/<script/i, /javascript:/i, /on\w+\s*=/i];
    document.addEventListener('input', function(e) {
        const el = e.target;
        if (!el.dataset.vt) return;
        const v = el.value;
        if (BLOCK_PATTERNS.some(p => p.test(v))) {
            el.value = el.value.replace(/<script[\s\S]*/gi, '').replace(/javascript:/gi, '').replace(/on\w+\s*=/gi, '');
        }
    }, true);

    document.addEventListener('input', function(e) {
        const el = e.target;
        const vt = el.dataset.vt;
        if (!vt || !VT_PATTERNS[vt]) return;
        const pattern = VT_PATTERNS[vt];
        const pos = el.selectionStart;
        const prev = el.value;
        const cleaned = prev.replace(pattern, '');
        if (cleaned !== prev) {
            el.value = cleaned;
            const diff = prev.length - cleaned.length;
            el.setSelectionRange(Math.max(0, pos - diff), Math.max(0, pos - diff));
        }
    }, true);

    document.addEventListener('keydown', function(e) {
        if (e.target.type === 'number' && (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+')) {
            e.preventDefault();
        }
    }, true);

    document.querySelectorAll('input[type="number"]').forEach(function(inp) {
        if (inp.getAttribute('min') === null) inp.setAttribute('min', '0');
    });
})();

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function formatDate(str) {
    if (!str) return '';
    return new Date(str).toLocaleString('es-ES', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
}
</script>

<script>
document.querySelectorAll('#content .modal, #wrapper .modal').forEach(function(el) {
    document.body.appendChild(el);
});
</script>
</body>
</html>