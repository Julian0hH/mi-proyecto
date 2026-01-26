<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        /* Copia mínima de tus estilos para mantener coherencia si el layout falla */
        :root { --bg-body: #f2f5f4; --text-main: #2f3640; --accent-color: #26a69a; }
        [data-theme="dark"] { --bg-body: #1e272e; --text-main: #f5f6fa; }
        
        body { 
            background-color: var(--bg-body); 
            color: var(--text-main);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }
        .error-card {
            text-align: center;
            padding: 40px;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: var(--accent-color);
            line-height: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-card">
            <div class="error-code">404</div>
            <h2 class="mb-3">¡Ups! Página no encontrada</h2>
            <p class="lead mb-4 text-muted">La ruta que buscas no existe o se ha movido.</p>
            <a href="<?= base_url() ?>" class="btn btn-primary btn-lg px-5 rounded-pill">
                <i class="bi bi-house-door me-2"></i> Volver al Inicio
            </a>
        </div>
    </div>

    <script>
        // Script simple para detectar el tema actual del usuario
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);
    </script>
</body>
</html>