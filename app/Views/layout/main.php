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

<!-- Toast Container -->
<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999"></div>

<!-- Overlay móvil -->
<div id="sidebar-overlay"></div>

<div id="wrapper">
    <!-- ======================== SIDEBAR ======================== -->
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
            <button id="sidebar-toggle" class="sidebar-toggle-btn" title="Contraer menú">
                <i class="bi bi-layout-sidebar-reverse"></i>
            </button>
        </div>

        <div class="sidebar-search">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" id="sidebar-search-input" class="form-control" placeholder="Buscar...">
            </div>
        </div>

        <?php
        // ── Helpers de permisos ────────────────────────────────
        $sessionPermisos = session()->get('user_permisos') ?? [];
        $userType        = session()->get('user_type') ?? 'admin';
        $isAdminUser     = ($userType === 'admin');
        $isLogued        = (bool) session()->get('admin_logueado');

        function navPuede(array $perms, int $modId, bool $isAdmin): bool {
            if ($isAdmin) return true;
            return !empty($perms[$modId]['bitConsulta']);
        }

        // Secciones de menú
        $verSeg   = $isAdminUser || navPuede($sessionPermisos,1,$isAdminUser) || navPuede($sessionPermisos,2,$isAdminUser) || navPuede($sessionPermisos,3,$isAdminUser) || navPuede($sessionPermisos,4,$isAdminUser);
        $verPri1  = $isAdminUser || navPuede($sessionPermisos,5,$isAdminUser) || navPuede($sessionPermisos,6,$isAdminUser);
        $verPri2  = $isAdminUser || navPuede($sessionPermisos,7,$isAdminUser) || navPuede($sessionPermisos,8,$isAdminUser);

        $isSegSection  = url_is('admin/seguridad*');
        $isPri1Section = url_is('admin/principal1*');
        $isPri2Section = url_is('admin/principal2*');
        $isOldAdmin    = url_is('admin*') && !$isSegSection && !$isPri1Section && !$isPri2Section;
        ?>

        <ul class="sidebar-nav" id="sidebar-nav">

            <?php if ($isAdminUser): ?>
            <!-- ── PORTAFOLIO (solo admin legacy) ── -->
            <li class="nav-section"><span>Portafolio</span></li>
            <li>
                <a href="<?= base_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>">
                    <i class="bi bi-house-door nav-icon-blue"></i><span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('portafolio') ?>" class="<?= url_is('portafolio*') ? 'active' : '' ?>">
                    <i class="bi bi-briefcase nav-icon-purple"></i><span>Portafolio</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('servicios') ?>" class="<?= url_is('servicios*') || url_is('detalles*') || url_is('contratar*') ? 'active' : '' ?>">
                    <i class="bi bi-grid-3x3-gap nav-icon-cyan"></i><span>Servicios</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('sobre-mi') ?>" class="<?= url_is('sobre-mi*') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle nav-icon-green"></i><span>Sobre Mí</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('contacto') ?>" class="<?= url_is('contacto*') ? 'active' : '' ?>">
                    <i class="bi bi-envelope nav-icon-orange"></i><span>Contacto</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($isLogued): ?>

            <?php if ($isAdminUser): ?>
            <!-- ── ADMINISTRACIÓN PORTAFOLIO (solo admin legacy) ── -->
            <li class="nav-section">
                <span>Administración</span>
                <span class="nav-section-badge">Admin</span>
            </li>
            <li class="nav-accordion <?= $isOldAdmin ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi bi-speedometer2 nav-icon-indigo"></i>
                    <span>Panel Admin</span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <li>
                        <a href="<?= base_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart-line"></i><span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/sobre-mi') ?>" class="<?= url_is('admin/sobre-mi*') ? 'active' : '' ?>">
                            <i class="bi bi-person-badge"></i><span>Sobre Mí</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('carrusel') ?>" class="<?= url_is('carrusel*') ? 'active' : '' ?>">
                            <i class="bi bi-images"></i><span>Carrusel</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/proyectos') ?>" class="<?= url_is('admin/proyectos*') ? 'active' : '' ?>">
                            <i class="bi bi-folder-symlink"></i><span>Proyectos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/servicios') ?>" class="<?= url_is('admin/servicios*') ? 'active' : '' ?>">
                            <i class="bi bi-gear-wide-connected"></i><span>Servicios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/contactos') ?>" class="<?= url_is('admin/contactos*') ? 'active' : '' ?>">
                            <i class="bi bi-chat-left-dots"></i>
                            <span>Contactos</span>
                            <span class="badge-count" id="badge-contactos" style="display:none"></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/roles') ?>" class="<?= url_is('admin/roles*') ? 'active' : '' ?>">
                            <i class="bi bi-shield-lock"></i><span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('registro') ?>" class="<?= url_is('registro*') ? 'active' : '' ?>">
                            <i class="bi bi-people"></i><span>Usuarios Legacy</span>
                        </a>
                    </li>
                </ul>
            </li>
            <?php else: ?>
            <!-- Dashboard para usuarios app -->
            <li class="nav-section"><span>General</span></li>
            <li>
                <a href="<?= base_url('admin/dashboard') ?>" class="<?= url_is('admin/dashboard*') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 nav-icon-indigo"></i><span>Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- ── SEGURIDAD ── -->
            <?php if ($verSeg): ?>
            <li class="nav-section">
                <span>Seguridad</span>
                <span class="nav-section-badge">Seg</span>
            </li>
            <li class="nav-accordion <?= $isSegSection ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi bi-shield-lock-fill nav-icon-orange"></i>
                    <span>Seguridad</span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <?php if (navPuede($sessionPermisos,1,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/seguridad/perfiles') ?>" class="<?= url_is('admin/seguridad/perfiles*') ? 'active' : '' ?>">
                            <i class="bi bi-person-badge"></i><span>Perfil</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (navPuede($sessionPermisos,2,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/seguridad/modulos') ?>" class="<?= url_is('admin/seguridad/modulos*') ? 'active' : '' ?>">
                            <i class="bi bi-grid-3x3-gap"></i><span>Módulo</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (navPuede($sessionPermisos,3,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/seguridad/permisos') ?>" class="<?= url_is('admin/seguridad/permisos*') ? 'active' : '' ?>">
                            <i class="bi bi-shield-check"></i><span>Permisos-Perfil</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (navPuede($sessionPermisos,4,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/seguridad/usuarios') ?>" class="<?= url_is('admin/seguridad/usuarios*') ? 'active' : '' ?>">
                            <i class="bi bi-people"></i><span>Usuario</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── VENTAS (Principal 1) ── -->
            <?php if ($verPri1): ?>
            <li class="nav-section"><span>Ventas</span></li>
            <li class="nav-accordion <?= $isPri1Section ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi bi-graph-up-arrow nav-icon-cyan"></i>
                    <span>Ventas</span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <?php if (navPuede($sessionPermisos,5,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/principal1/modulo1') ?>" class="<?= url_is('admin/principal1/modulo1*') ? 'active' : '' ?>">
                            <i class="bi bi-funnel"></i><span>Pipeline de Ventas</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (navPuede($sessionPermisos,6,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/principal1/modulo2') ?>" class="<?= url_is('admin/principal1/modulo2*') ? 'active' : '' ?>">
                            <i class="bi bi-people"></i><span>Clientes y Leads</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <!-- ── OPERACIONES (Principal 2) ── -->
            <?php if ($verPri2): ?>
            <li class="nav-section"><span>Operaciones</span></li>
            <li class="nav-accordion <?= $isPri2Section ? 'open' : '' ?>">
                <a href="#" class="nav-accordion-toggle" onclick="toggleAccordion(this);return false;">
                    <i class="bi bi-kanban nav-icon-purple"></i>
                    <span>Operaciones</span>
                    <i class="bi bi-chevron-down accordion-arrow ms-auto"></i>
                </a>
                <ul class="nav-accordion-body">
                    <?php if (navPuede($sessionPermisos,7,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/principal2/modulo1') ?>" class="<?= url_is('admin/principal2/modulo1*') ? 'active' : '' ?>">
                            <i class="bi bi-kanban"></i><span>Gestión de Proyectos</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (navPuede($sessionPermisos,8,$isAdminUser)): ?>
                    <li>
                        <a href="<?= base_url('admin/principal2/modulo2') ?>" class="<?= url_is('admin/principal2/modulo2*') ? 'active' : '' ?>">
                            <i class="bi bi-bar-chart-line"></i><span>Reportes y Analítica</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>

            <?php endif; // isLogued ?>
        </ul>

        <div class="sidebar-footer">
            <?php if (session()->get('admin_logueado')): ?>
                <div class="user-info">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="user-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div class="flex-grow-1 text-truncate">
                            <small class="d-block fw-semibold user-name"><?= esc(session()->get('admin_nombre') ?? 'Admin') ?></small>
                            <small class="text-muted user-email"><?= esc(session()->get('admin_email') ?? '') ?></small>
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

        <div class="theme-toggle" id="theme-switch" title="Cambiar tema">
            <i class="bi bi-moon-stars" id="theme-icon"></i>
            <span class="ms-2 theme-label">Tema Oscuro</span>
        </div>
    </nav>

    <!-- ======================== CONTENT ======================== -->
    <div id="content">
        <!-- Topbar -->
        <div class="topbar d-flex align-items-center mb-4 gap-3">
            <button class="btn btn-outline-secondary d-md-none" id="mobile-opener">
                <i class="bi bi-list fs-5"></i>
            </button>

            <!-- Breadcrumbs -->
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

            <!-- Notificaciones (solo admin) -->
            <?php if (session()->get('admin_logueado')): ?>
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

            <!-- Acceso rápido móvil -->
            <div class="d-md-none">
                <?php if (session()->get('admin_logueado')): ?>
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

        <!-- Contenido principal -->
        <div class="animate-fade-in">
            <?php
            /*
             * BUG CORREGIDO: Bootstrap JS se cargaba DESPUÉS de renderSection('content').
             * Todas las vistas tienen inline <script> con `new bootstrap.Modal(...)` que
             * se ejecutan en el momento que el HTML se parsea — antes de que el bundle
             * de Bootstrap estuviera disponible → "bootstrap is not defined".
             * Solución: cargar Bootstrap bundle ANTES de renderizar el contenido.
             */
            ?>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="<?= base_url('js/toast.js') ?>"></script>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<!-- Modal simulador de errores -->
<?php if (session()->get('admin_logueado')): ?>
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
// ===== TEMA =====
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

// ===== SIDEBAR =====
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

// ===== BÚSQUEDA SIDEBAR =====
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

<?php if (session()->get('admin_logueado')): ?>
// ===== NOTIFICACIONES =====
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

// ===== SIMULADOR DE ERRORES =====
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

// ===== ACORDEÓN SIDEBAR =====
function toggleAccordion(el) {
    const li = el.closest('.nav-accordion');
    li.classList.toggle('open');
}
document.querySelectorAll('.nav-accordion.open').forEach(li => li.classList.add('open'));

// ===== HELPERS =====
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