<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CI4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f4f7f6; overflow-x: hidden; }
        #wrapper { display: flex; width: 100%; }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #1a1d20;
            color: #fff;
            position: fixed;
            transition: all 0.3s;
        }
        #sidebar .sidebar-header { padding: 20px; background: #141619; }
        #sidebar ul li a {
            padding: 15px 20px;
            display: block;
            color: #adb5bd;
            text-decoration: none;
        }
        #sidebar ul li a:hover { background: #2c3136; color: #fff; }
        #sidebar ul li a.active { background: #0d6efd; color: #fff; }
        #content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 40px;
            min-height: 100vh;
        }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h4>Dashboard</h4>
        </div>
        <ul class="list-unstyled">
            <li><a href="<?= base_url('/') ?>" class="<?= url_is('/') ? 'active' : '' ?>"><i class="bi bi-house-door me-2"></i> Inicio</a></li>
            <li><a href="<?= base_url('registro') ?>" class="<?= url_is('registro*') ? 'active' : '' ?>"><i class="bi bi-person-plus me-2"></i> Registro</a></li>
        </ul>
    </nav>

    <div id="content">
        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>