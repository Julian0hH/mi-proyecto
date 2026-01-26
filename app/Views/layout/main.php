<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CI4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        :root { 
            --sidebar-width: 260px; 
            --sidebar-collapsed-width: 80px;
            --bg-body: #f2f5f4;        
            --bg-card: #ffffff;         
            --bg-sidebar: #2f3542;      
            --bg-sidebar-header: #262c38;
            --text-main: #2f3640;       
            --text-muted: #747d8c;      
            --accent-color: #26a69a;    
            --border-color: #dcdde1;    
        }

        [data-theme="dark"] {
            --bg-body: #1e272e;         
            --bg-card: #2f3542;   
            --bg-sidebar: #2f3542;      
            --bg-sidebar-header: #262c38;
            --text-main: #f5f6fa;       
            --text-muted: #a4b0be;      
            --accent-color: #4db6ac;    
            --border-color: #3d4144;
        }

        body { background-color: var(--bg-body); color: var(--text-main); overflow-x: hidden; transition: background 0.3s; }
        #wrapper { display: flex; width: 100%; }
        
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            color: #fff;
            position: fixed;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        #sidebar.collapsed { width: var(--sidebar-collapsed-width); }
        
        #sidebar .sidebar-header { 
            padding: 20px; 
            background: var(--bg-sidebar-header); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            height: 65px;
        }
        
        #sidebar.collapsed .sidebar-header { justify-content: center; padding: 20px 0; }
        #sidebar.collapsed .sidebar-header h4 { display: none; }
        
        #sidebar ul li a { padding: 15px 25px; display: flex; align-items: center; color: #adb5bd; text-decoration: none; white-space: nowrap; }
        #sidebar.collapsed ul li a { padding: 15px 0; justify-content: center; }
        #sidebar ul li a i { font-size: 1.25rem; }
        #sidebar ul li a span { margin-left: 15px; }
        #sidebar.collapsed ul li a span { display: none; }
        #sidebar ul li a:hover { background: #3e444d; color: #fff; }
        #sidebar ul li a.active { background: var(--accent-color); color: #fff; }
        
        #content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 40px; min-height: 100vh; transition: all 0.3s ease; }
        #content.expanded { margin-left: var(--sidebar-collapsed-width); width: calc(100% - var(--sidebar-collapsed-width)); }
        
        .card { background-color: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-main); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .breadcrumb { background: var(--bg-card); padding: 15px; border-radius: 10px; border: 1px solid var(--border-color); }
        
        .theme-toggle { 
            cursor: pointer; 
            padding: 15px 25px; 
            color: #adb5bd; 
            display: flex; 
            align-items: center; 
            border-top: 1px solid #3e444d; 
            position: absolute; 
            bottom: 0; 
            width: 100%; 
            background: var(--bg-sidebar);
        }
        
        #sidebar.collapsed .theme-toggle { justify-content: center; padding: 15px 0; }
        #sidebar.collapsed .theme-toggle span { display: none; }
        #sidebar-toggle { cursor: pointer; color: #fff; font-size: 1.4rem; }
        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        .breadcrumb-item.active {
            color: var(--text-muted);
        }
        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--text-muted);
        }
    </style>
</head>
<body>
<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4>Dashboard</h4>
            <i class="bi bi-list" id="sidebar-toggle"></i>
        </div>
        <ul class="list-unstyled">
            <li><a href="<?= base_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>"><i class="bi bi-house-door"></i><span>Inicio</span></a></li>
            <li><a href="<?= base_url('registro') ?>" class="<?= url_is('registro*') ? 'active' : '' ?>"><i class="bi bi-person-plus"></i><span>Registro</span></a></li>
            <li><a href="<?= base_url('servicios') ?>" class="<?= url_is('servicios*') ? 'active' : '' ?>"><i class="bi bi-gear"></i><span>Servicios</span></a></li>
            <li><a href="<?= base_url('validacion') ?>" class="<?= url_is('validacion*') ? 'active' : '' ?>"><i class="bi bi-check-circle"></i><span>Validación UI</span></a></li>
        </ul>
        <div class="theme-toggle" id="theme-switch">
            <i class="bi bi-moon-stars" id="theme-icon"></i>
            <span class="ms-3">Cambiar Tema</span>
        </div>
    </nav>
    <div id="content">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <?php if (isset($breadcrumbs)): ?>
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <li class="breadcrumb-item <?= $crumb['active'] ? 'active' : '' ?>">
                            <?php if (!$crumb['active']): ?><a href="<?= $crumb['url'] ?>"><?= $crumb['name'] ?></a><?php else: ?><?= $crumb['name'] ?><?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ol>
        </nav>
        <?= $this->renderSection('content') ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const themeSwitch = document.getElementById('theme-switch');
    const themeIcon = document.getElementById('theme-icon');
    const html = document.documentElement;

    if (localStorage.getItem('sidebarStatus') === 'collapsed') {
        sidebar.classList.add('collapsed');
        content.classList.add('expanded');
    }

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
        localStorage.setItem('sidebarStatus', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    });

    const currentTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', currentTheme);
    updateThemeIcon(currentTheme);

    themeSwitch.addEventListener('click', () => {
        const newTheme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.replace('bi-moon-stars', 'bi-sun');
        } else {
            themeIcon.classList.replace('bi-sun', 'bi-moon-stars');
        }
    }
</script>
</body>
</html>
