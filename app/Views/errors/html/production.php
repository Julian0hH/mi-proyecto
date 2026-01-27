<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title>Error del Sistema</title>
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
            overflow: hidden;
            background: var(--bg-body);
        }
        .error-container {
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            background: -webkit-linear-gradient(45deg, var(--primary-color), #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            animation: float 6s ease-in-out infinite;
        }
        .error-icon-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20rem;
            color: var(--primary-color);
            opacity: 0.03;
            z-index: -1;
            animation: pulse 10s infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(0.8); }
            50% { transform: translate(-50%, -50%) scale(1.2); }
            100% { transform: translate(-50%, -50%) scale(0.8); }
        }
    </style>
</head>
<body>

    <div class="error-icon-bg">
        <i class="bi bi-bug-fill"></i>
    </div>

    <div class="error-container p-4">
        <div class="error-code">500</div>
        
        <h2 class="fw-bold mb-3 display-6">¡Ups! Algo salió mal</h2>
        
        <p class="lead text-muted mb-5 mx-auto" style="max-width: 500px;">
            Nuestro equipo de ingenieros ya ha sido notificado y están trabajando para solucionarlo. Por favor intenta más tarde.
        </p>

        <div class="d-flex justify-content-center gap-3">
            <a href="<?= base_url() ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg">
                <i class="bi bi-house-door-fill me-2"></i> Volver al Inicio
            </a>
            <button onclick="window.location.reload()" class="btn btn-outline-secondary rounded-pill px-4 py-3 fw-bold">
                <i class="bi bi-arrow-clockwise me-2"></i> Recargar
            </button>
        </div>
        
        <div class="mt-5 text-muted small opacity-50">
            Código de Referencia: <span class="font-monospace">ERR-SYS-CRITICAL</span>
        </div>
    </div>

    <script>
        // Detectar tema automáticamente
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'light');
    </script>
</body>
</html>