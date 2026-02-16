<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Profesional - AdminPanel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

<div id="sidebar-overlay"></div>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4 class="m-0 fw-bold">AdminPanel</h4>
            <button id="sidebar-toggle" class="sidebar-toggle-btn">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <ul>
            <li>
                <a href="<?= base_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('registro') ?>" class="<?= url_is('registro*') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('servicios') ?>" class="<?= url_is('servicios*') ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i>
                    <span>Servicios</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('validacion') ?>" class="<?= url_is('validacion*') ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i>
                    <span>Validación</span>
                </a>
            </li>
            
            <?php if (session()->get('admin_logueado')): ?>
            <li>
                <a href="<?= base_url('carrusel') ?>" class="<?= url_is('carrusel*') ? 'active' : '' ?>">
                    <i class="bi bi-images"></i>
                    <span>Carrusel</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/proyectos') ?>" class="<?= url_is('admin/proyectos*') ? 'active' : '' ?>">
                    <i class="bi bi-folder"></i>
                    <span>Mis Proyectos</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="sidebar-footer">
            <?php if (session()->get('admin_logueado')): ?>
                <div class="user-info">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <div class="flex-grow-1 text-truncate">
                            <small class="d-block text-muted">Admin</small>
                            <span class="fw-semibold small"><?= esc(session()->get('admin_email')) ?></span>
                        </div>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Iniciar Sesión</span>
                </a>
            <?php endif; ?>
        </div>

        <div class="theme-toggle" id="theme-switch">
            <i class="bi bi-moon-stars" id="theme-icon"></i>
            <span class="ms-2 theme-label">Tema Oscuro</span>
        </div>
    </nav>

    <div id="content">
        <div class="d-flex align-items-center mb-4">
            <button class="btn btn-outline-secondary d-md-none me-3" id="mobile-opener">
                <i class="bi bi-list"></i>
            </button>
            
            <nav aria-label="breadcrumb" class="flex-grow-1">
                <ol class="breadcrumb mb-0">
                    <?php if (isset($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <li class="breadcrumb-item <?= $crumb['active'] ? 'active' : '' ?>">
                                <?php if (!$crumb['active']): ?>
                                    <a href="<?= $crumb['url'] ?>"><?= $crumb['name'] ?></a>
                                <?php else: ?>
                                    <?= $crumb['name'] ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>

            <div class="ms-auto d-md-none">
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

        <div class="animate-fade-in">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const html = document.documentElement;
const sidebar = document.getElementById('sidebar');
const content = document.getElementById('content');
const overlay = document.getElementById('sidebar-overlay');
const toggleBtn = document.getElementById('sidebar-toggle');
const mobileOpener = document.getElementById('mobile-opener');

const savedTheme = localStorage.getItem('theme') || 'light';
setTheme(savedTheme);

document.getElementById('theme-switch').addEventListener('click', () => {
    const newTheme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
});

function setTheme(theme) {
    html.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    const icon = document.getElementById('theme-icon');
    const label = document.querySelector('.theme-label');
    if (theme === 'dark') {
        icon.className = 'bi bi-sun';
        if (label) label.textContent = 'Tema Claro';
    } else {
        icon.className = 'bi bi-moon-stars';
        if (label) label.textContent = 'Tema Oscuro';
    }
}

if (localStorage.getItem('sidebarState') === 'collapsed' && window.innerWidth > 768) {
    sidebar.classList.add('collapsed');
    content.classList.add('expanded');
}

toggleBtn.addEventListener('click', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
        localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    } else {
        sidebar.classList.remove('mobile-show');
        overlay.classList.remove('show');
    }
});

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
</script>
</body>
</html>