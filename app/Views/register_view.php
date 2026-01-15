<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Usuarios</h2>
        <span class="badge bg-success p-2">Conexión Activa</span>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="mb-3">Nuevo Registro</h5>
                <form action="<?= base_url('guardar') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="g-recaptcha mb-4" data-sitekey="<?= $sitekey ?>"></div>
                    <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="mb-3">Listado de Usuarios</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-muted">
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($usuarios as $user): ?>
                            <tr>
                                <td class="fw-bold"><?= $user['nombre'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td class="text-end">
                                    <a href="<?= base_url('eliminar/'.$user['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>