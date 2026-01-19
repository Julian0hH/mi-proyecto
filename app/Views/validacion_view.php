<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="card p-4 shadow-sm border-0">
        <h3 class="mb-4 text-primary"><i class="bi bi-shield-check me-2"></i>Validación de Interfaz Estricta</h3>
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?= base_url('procesar_validacion') ?>" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nombre Completo (Solo letras)</label>
                    <input type="text" name="nombre" class="form-control" value="<?= old('nombre') ?>" 
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '')" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Teléfono (10 dígitos)</label>
                    <input type="text" name="telefono" class="form-control" maxlength="10" value="<?= old('telefono') ?>" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Edad (18 - 99)</label>
                    <input type="number" name="edad" class="form-control" min="18" max="99" value="<?= old('edad') ?>" 
                           oninput="if(this.value.length > 2) this.value = this.value.slice(0,2)" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nac" class="form-control" value="<?= old('fecha_nac') ?>" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Sitio Web Personal (URL)</label>
                    <input type="url" name="sitio_web" class="form-control" placeholder="https://ejemplo.com" value="<?= old('sitio_web') ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nueva Contraseña (Mín. 8 caracteres, 1 Mayús, 1 Núm)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Confirmar Contraseña</label>
                    <input type="password" name="pass_conf" class="form-control" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-5 shadow-sm">Enviar y Validar</button>
                <button type="reset" class="btn btn-light ms-2">Limpiar</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>