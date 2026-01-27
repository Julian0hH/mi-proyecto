<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Página No Encontrada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="<?= base_url('css/style.css') ?>" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-body);
        }
        .container { text-align: center; }
        .error-code {
            font-size: 10rem;
            font-weight: 800;
            color: var(--text-muted);
            opacity: 0.1;
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -80%);
            z-index: 0;
            letter-spacing: -10px;
        }
        .content { position: relative; z-index: 1; }
        .astronaut {
            font-size: 5rem;
            animation: float 4s ease-in-out infinite;
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(10deg); }
        }
    </style>
</head>
<body>

    <div class="error-code">404</div>

    <div class="container content">
        <div class="astronaut">
            <i class="bi bi-rocket-takeoff-fill"></i>
        </div>
        
        <h1 class="fw-bold mb-3">¿Te has perdido?</h1>
        <p class="text-muted mb-5 fs-5">
            La ruta que buscas no existe o ha sido movida a otro lugar.
        </p>
        
        <a href="<?= base_url() ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
            Regresar a Tierra Firme
        </a>
    </div>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'light');
    </script>
</body>
</html>