<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>400</title>
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'light');
    </script>
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { max-width: 400px; text-align: center; padding: 2rem; }
        .icon { font-size: 3rem; color: #e11d48; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⚠️</div>
        <h3 class="fw-bold mb-2">Solicitud Incorrecta</h3>
        <p class="text-muted mb-4">Formato inválido.</p>
        <a href="javascript:history.back()" class="btn btn-outline-danger rounded-pill w-100">Reintentar</a>
    </div>
</body>
</html>