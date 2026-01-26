<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Usuarios</h2>
        <span class="badge bg-success p-2">Conexión Activa</span>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm border-0"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger shadow-sm border-0"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0 ps-3">
            <?php foreach(session()->getFlashdata('errors') as $e): ?>
                <li><?= esc($e) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-4">
                <h5 class="mb-3">Nuevo Registro</h5>
                <form action="<?= base_url('guardar') ?>" method="POST" autocomplete="off">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" 
                               required maxlength="50"
                               oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                               value="<?= old('nombre') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               required maxlength="100"
                               value="<?= old('email') ?>">
                    </div>
                    
                    <div class="g-recaptcha mb-4" data-sitekey="<?= $sitekey ?? '' ?>"></div>
                    
                    <button type="submit" class="btn btn-primary w-100">Guardar Usuario</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="mb-3">Usuarios Registrados</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr><th>Nombre</th><th>Email</th><th class="text-end">Acción</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($usuarios)): ?>
                                <?php foreach($usuarios as $u): ?>
                                <tr>
                                    <td><?= esc($u['nombre']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('eliminar/'.$u['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Borrar?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>