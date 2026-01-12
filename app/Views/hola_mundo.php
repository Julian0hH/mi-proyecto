<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="card shadow-lg" style="width: 25rem;">
            <div class="card-body text-center">
                <h1 class="card-title text-primary">¡Hola Mundo!</h1>
                <p class="card-text">Bienvenido, <strong><?= $usuario ?></strong>.</p>
                <hr>
                <p class="text-muted small">Hoy es: <?= $fecha ?></p>
                <button class="btn btn-primary w-100">Comenzar Proyecto</button>
            </div>
        </div>
    </div>

</body>
</html>