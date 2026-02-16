<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in">
    <div>
        <h2 class="fw-bold m-0">
            <i class="bi bi-people-fill text-primary me-2"></i>Gestión de Usuarios
        </h2>
        <p class="text-muted small mb-0 mt-1">Registro y administración con Supabase</p>
    </div>
    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
        <i class="bi bi-wifi me-1"></i>Conectado
    </span>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 animate-fade-in">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <span><?= session()->getFlashdata('success') ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 animate-fade-in">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <span><?= session()->getFlashdata('error') ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-warning mb-4 shadow-sm border-0 animate-fade-in">
        <div class="d-flex align-items-start">
            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
            <div class="flex-grow-1">
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <?php foreach(session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm hover-card h-100">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('guardar') ?>" method="POST" autocomplete="off">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">NOMBRE COMPLETO</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input 
                                type="text" 
                                name="nombre" 
                                class="form-control" 
                                required 
                                minlength="3" 
                                maxlength="50"
                                pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                placeholder="Ej. Juan Pérez"
                                value="<?= old('nombre') ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control" 
                                required 
                                maxlength="100"
                                pattern="^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                oninput="this.value = this.value.replace(/[^a-zA-Z0-9@._+-]/g, '')"
                                placeholder="correo@ejemplo.com"
                                value="<?= old('email') ?>"
                            >
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="<?= esc($sitekey ?? '') ?>"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow">
                        <i class="bi bi-save me-2"></i>Guardar Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0">
                        <i class="bi bi-list-ul me-2"></i>Directorio de Usuarios
                    </h5>
                    <span class="badge bg-primary">
                        <?= count($usuarios ?? []) ?> registros
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3">
                                    <i class="bi bi-person me-1"></i>Usuario
                                </th>
                                <th class="py-3">
                                    <i class="bi bi-envelope me-1"></i>Email
                                </th>
                                <th class="text-end pe-4 py-3">
                                    <i class="bi bi-gear me-1"></i>Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($usuarios)): ?>
                                <?php foreach($usuarios as $u): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width:40px;height:40px;">
                                                <strong><?= strtoupper(substr($u['nombre'], 0, 1)) ?></strong>
                                            </div>
                                            <span class="fw-semibold"><?= esc($u['nombre']) ?></span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-muted"><?= esc($u['email']) ?></span>
                                    </td>
                                    <td class="text-end pe-4 py-3">
                                        <a 
                                            href="<?= base_url('eliminar/'.$u['id']) ?>" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('¿Confirma que desea eliminar a <?= esc($u['nombre']) ?>?');"
                                            title="Eliminar usuario"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center p-5">
                                        <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">No hay usuarios registrados aún</p>
                                        <small class="text-muted">Utiliza el formulario para agregar el primero</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>