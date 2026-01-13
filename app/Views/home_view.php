<!DOCTYPE html>
<html>
<head>
    <title>CI4 + Supabase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <div class="row">
        <div class="col-md-4">
            <h3>Enviar Mensaje</h3>
            <form action="<?= base_url('guardar') ?>" method="POST">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Mensaje</label>
                    <textarea name="mensaje" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Guardar en Supabase</button>
            </form>
        </div>

        <div class="col-md-8">
            <h3>Registros Guardados</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Mensaje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mensajes as $m): ?>
                    <tr>
                        <td><?= $m['id'] ?></td>
                        <td><?= $m['nombre'] ?></td>
                        <td><?= $m['mensaje'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>