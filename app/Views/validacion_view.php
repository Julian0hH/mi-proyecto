<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= base_url('prueba_error') ?>" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-bug me-1"></i> Probar Error
        </a>
    </div>

    <div class="card p-4 shadow-sm border-0">
        <h3 class="mb-4 text-primary"><i class="bi bi-shield-lock me-2"></i>Validación Estricta</h3>
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                <?php foreach(session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('procesar_validacion') ?>" method="POST" autocomplete="off">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" 
                           required maxlength="50"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                           value="<?= old('nombre') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" 
                           required maxlength="10"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           value="<?= old('telefono') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Edad</label>
                    <input type="number" name="edad" class="form-control" 
                           required min="18" max="99"
                           oninput="if(this.value.length > 2) this.value = this.value.slice(0,2)"
                           value="<?= old('edad') ?>">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha Nacimiento</label>
                    <input type="date" name="fecha_nac" class="form-control" required value="<?= old('fecha_nac') ?>">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Sitio Web</label>
                    <input type="url" name="sitio_web" class="form-control" required placeholder="https://..." value="<?= old('sitio_web') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="password" id="pass1" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass1')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Mín 8 caracteres, 1 Mayúscula, 1 Número</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <input type="password" name="pass_conf" id="pass2" class="form-control" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass2')">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-5">Validar Datos</button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

<?= $this->endSection() ?>