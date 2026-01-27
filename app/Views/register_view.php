<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in">
    <div>
        <h2 class="fw-bold m-0"><i class="bi bi-people-fill text-primary me-2"></i>Usuarios</h2>
        <p class="text-muted small mb-0">Gestión de usuarios en Supabase</p>
    </div>
    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
        <i class="bi bi-wifi me-1"></i> Conectado
    </span>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning mb-4 shadow-sm border-0">
        <ul class="mb-0 ps-3">
        <?php foreach(session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 h-100 border-0 shadow-sm hover-card">
            <h5 class="fw-bold mb-4 border-bottom pb-2">Registrar Nuevo</h5>
            <form action="<?= base_url('guardar') ?>" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">NOMBRE</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="nombre" class="form-control" 
                               required minlength="3" maxlength="50"
                               pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+"
                               oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                               placeholder="Nombre completo" value="<?= old('nombre') ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">EMAIL</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" 
                               required maxlength="100"
                               placeholder="correo@ejemplo.com" value="<?= old('email') ?>">
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="g-recaptcha" data-sitekey="<?= $sitekey ?? '' ?>"></div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-save me-2"></i> Guardar
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100 overflow-hidden border-0 shadow-sm">
            <div class="card-header bg-transparent py-3 border-bottom">
                <h5 class="fw-bold m-0">Directorio</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Usuario</th>
                            <th>Email</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($usuarios)): ?>
                            <?php foreach($usuarios as $u): ?>
                            <tr>
                                <td class="ps-4 fw-bold">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width:35px;height:35px;">
                                            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                        </div>
                                        <?= esc($u['nombre']) ?>
                                    </div>
                                </td>
                                <td class="text-muted"><?= esc($u['email']) ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('eliminar/'.$u['id']) ?>" class="btn btn-sm btn-light text-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center p-5 text-muted">No hay registros aún.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>