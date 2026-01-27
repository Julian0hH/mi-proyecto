<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 animate-slide-in">
    <div>
        <h2 class="fw-bold m-0">Validación Avanzada</h2>
        <p class="text-muted small mb-0">Laboratorio de pruebas de formularios seguros</p>
    </div>
    <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
        <i class="bi bi-shield-check me-2"></i>Modo Estricto
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger mb-4 shadow-sm border-0">
        <ul class="mb-0 ps-3">
        <?php foreach(session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?= base_url('procesar_validacion') ?>" method="POST" enctype="multipart/form-data" autocomplete="off">
    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">NOMBRE</label>
                            <input type="text" name="nombre" class="form-control" required pattern="[a-zA-Z\s]+" 
                                   oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                   value="<?= old('nombre') ?>" placeholder="Ej. Roberto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">APELLIDOS</label>
                            <input type="text" name="apellido" class="form-control" required pattern="[a-zA-Z\s]+" 
                                   oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                                   value="<?= old('apellido') ?>" placeholder="Ej. Gómez">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">EMAIL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required value="<?= old('email') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">TELÉFONO</label>
                            <div class="input-group">
                                <select class="form-select" name="pais" style="max-width: 100px;">
                                    <option value="+52">MX</option>
                                    <option value="+1">US</option>
                                    <option value="+34">ES</option>
                                </select>
                                <input type="tel" name="telefono" class="form-control" required pattern="[0-9]{10}" maxlength="10" 
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')" value="<?= old('telefono') ?>">
                            </div>
                        </div>

                        <div class="col-12 pt-2">
                            <label class="form-label small fw-bold text-muted d-block mb-2">GÉNERO</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="sexo" id="male" value="masculino" <?= old('sexo')=='masculino'?'checked':'' ?> required>
                                <label class="btn btn-outline-secondary" for="male">Masculino</label>

                                <input type="radio" class="btn-check" name="sexo" id="female" value="femenino" <?= old('sexo')=='femenino'?'checked':'' ?>>
                                <label class="btn btn-outline-secondary" for="female">Femenino</label>

                                <input type="radio" class="btn-check" name="sexo" id="other" value="otro" <?= old('sexo')=='otro'?'checked':'' ?>>
                                <label class="btn btn-outline-secondary" for="other">Otro</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-primary"><i class="bi bi-shield-lock me-2"></i>Seguridad</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CONTRASEÑA</label>
                        <div class="input-group">
                            <input type="password" name="password" id="p1" class="form-control" required minlength="8">
                            <button class="btn btn-light border" type="button" onclick="togglePass('p1')"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CONFIRMAR</label>
                        <div class="input-group">
                            <input type="password" name="pass_conf" id="p2" class="form-control" required>
                            <button class="btn btn-light border" type="button" onclick="togglePass('p2')"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom">
                    <h6 class="fw-bold m-0 text-primary"><i class="bi bi-folder me-2"></i>Datos Extra</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NACIMIENTO</label>
                        <input type="date" name="fecha_nac" class="form-control" required max="<?= date('Y-m-d') ?>" value="<?= old('fecha_nac') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">EDAD</label>
                        <input type="number" name="edad" class="form-control" required min="18" value="<?= old('edad') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">WEB</label>
                        <input type="url" name="sitio_web" class="form-control" placeholder="https://" required value="<?= old('sitio_web') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">DOCUMENTO</label>
                        <input type="file" name="archivo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terminos" id="terms" required>
                        <label class="form-check-label" for="terms">Acepto los términos y condiciones.</label>
                    </div>
                    <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">
                        Validar Datos <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function togglePass(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
</script>

<?= $this->endSection() ?>