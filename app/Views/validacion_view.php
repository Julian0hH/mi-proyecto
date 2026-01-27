<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="mb-4 animate-slide-in">
    <h2 class="fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Validación Estricta</h2>
    <p class="text-muted">Formulario con bloqueo de caracteres (Input Masking) y límites de longitud.</p>
</div>

<?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger shadow-sm border-0">
        <ul class="mb-0 ps-3">
        <?php foreach(session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success fw-bold border-0 shadow-sm">
        <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="<?= base_url('procesar_validacion') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">NOMBRE</label>
                    <input type="text" name="nombre" class="form-control" 
                           required minlength="2" maxlength="50"
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                           placeholder="Ej. Juan"
                           value="<?= old('nombre') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">APELLIDO</label>
                    <input type="text" name="apellido" class="form-control" 
                           required minlength="2" maxlength="50"
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                           placeholder="Ej. Pérez"
                           value="<?= old('apellido') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">EMAIL</label>
                    <input type="email" name="email" class="form-control" 
                           required maxlength="100"
                           pattern="^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9@._+-]/g, '')"
                           placeholder="usuario@empresa.com"
                           value="<?= old('email') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">TELÉFONO</label>
                    <input type="tel" name="telefono" class="form-control" 
                           required minlength="7" maxlength="15"
                           pattern="^[0-9]+$"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                           placeholder="Solo números"
                           value="<?= old('telefono') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">EDAD</label>
                    <input type="number" name="edad" class="form-control" 
                           required min="18" max="120" maxlength="3"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)"
                           value="<?= old('edad') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">PAÍS</label>
                    <input type="text" name="pais" class="form-control" 
                           required maxlength="50"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                           value="<?= old('pais') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">SEXO</label>
                    <select name="sexo" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="masculino" <?= old('sexo') === 'masculino' ? 'selected' : '' ?>>Masculino</option>
                        <option value="femenino" <?= old('sexo') === 'femenino' ? 'selected' : '' ?>>Femenino</option>
                        <option value="otro" <?= old('sexo') === 'otro' ? 'selected' : '' ?>>Otro</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">FECHA NACIMIENTO</label>
                    <input type="date" name="fecha_nac" class="form-control" required 
                           max="<?= date('Y-m-d') ?>"
                           value="<?= old('fecha_nac') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">SITIO WEB (HTTPS)</label>
                    <input type="url" name="sitio_web" class="form-control" 
                           required maxlength="200"
                           pattern="https://.*"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]/g, '')"
                           placeholder="https://misitio.com"
                           value="<?= old('sitio_web') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">CONTRASEÑA</label>
                    <input type="password" name="password" class="form-control" 
                           required minlength="12" maxlength="64"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9@$!%*?&]/g, '')"
                           placeholder="Min 12: A-Z, a-z, 0-9, @$!%*?&">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small text-muted">CONFIRMAR CONTRASEÑA</label>
                    <input type="password" name="pass_conf" class="form-control" 
                           required minlength="12" maxlength="64"
                           oninput="this.value = this.value.replace(/[^a-zA-Z0-9@$!%*?&]/g, '')"
                           placeholder="Repite la contraseña">
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-muted">ARCHIVO (Imagen)</label>
                    <input type="file" name="archivo" class="form-control" 
                           accept=".jpg,.jpeg,.png,image/png,image/jpeg" 
                           required>
                    <div class="form-text">Formatos permitidos: .jpg, .jpeg, .png (Máx 2MB)</div>
                </div>

                <div class="col-12 mt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terminos" id="terminos" required>
                        <label class="form-check-label user-select-none" for="terminos">
                            He leído y acepto los <a href="#" class="text-decoration-none">términos y condiciones</a>
                        </label>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase letter-spacing-1">
                        <i class="bi bi-shield-lock-fill me-2"></i> Validar Datos Seguros
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>