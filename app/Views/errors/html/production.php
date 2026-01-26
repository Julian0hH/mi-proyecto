<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ups, algo pasó</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #495057;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 40px;
            max-width: 500px;
        }
        .illustration {
            font-size: 80px;
            color: #adb5bd; /* Gris suave en lugar de rojo */
            margin-bottom: 20px;
        }
        h1 { font-size: 24px; font-weight: 600; margin-bottom: 15px; }
        p { font-size: 16px; color: #6c757d; margin-bottom: 30px; }
        .btn-back {
            background-color: #2f3640;
            color: #fff;
            padding: 10px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn-back:hover { background-color: #1e272e; color: #fff; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="illustration">¯\_(ツ)_/¯</div>
        <h1>Tuvimos un pequeño problema</h1>
        <p>No eres tú, somos nosotros. Algo no funcionó como esperábamos, pero ya estamos trabajando en ello.</p>
        <a href="/" class="btn-back">Volver al Inicio</a>
    </div>
</body>
</html>