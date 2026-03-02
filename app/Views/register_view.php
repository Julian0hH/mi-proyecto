<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">

        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm animate-fade-in">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span><?= session()->getFlashdata('success') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm animate-fade-in">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <span><?= session()->getFlashdata('error') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-warning mb-4 border-0 shadow-sm animate-fade-in">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-circle-fill me-2 mt-1 fs-5"></i>
                <div>
                    <strong>Errores de validación:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        <?php foreach ((array)session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width:64px;height:64px">
                        <i class="bi bi-person-plus fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Crear Cuenta</h4>
                    <p class="text-muted small">Regístrate para mantenerte en contacto.</p>
                </div>

                <form action="<?= base_url('guardar') ?>" method="POST" id="form-registro" autocomplete="off" novalidate>
                    <?= csrf_field() ?>

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="nombre" id="inp-reg-nombre" class="form-control"
                                   required minlength="3" maxlength="50"
                                   placeholder="Ej. Juan Pérez"
                                   value="<?= old('nombre') ?>"
                                   oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'').slice(0,50);cntNombre.textContent=this.value.length">
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-error small text-danger" id="err-reg-nombre"></div>
                            <small class="text-muted ms-auto"><span id="cntNombre">0</span>/50</small>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="inp-reg-email" class="form-control"
                                   required maxlength="100"
                                   placeholder="correo@ejemplo.com"
                                   value="<?= old('email') ?>"
                                   oninput="this.value=this.value.replace(/[^a-zA-Z0-9@._+\-]/g,'').slice(0,100);cntEmail.textContent=this.value.length">
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-error small text-danger" id="err-reg-email"></div>
                            <small class="text-muted ms-auto"><span id="cntEmail">0</span>/100</small>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="inp-reg-password" class="form-control"
                                   required minlength="8" maxlength="100"
                                   placeholder="Mínimo 8 caracteres"
                                   oninput="validarPassword()">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('inp-reg-password','ico-pass1')">
                                <i class="bi bi-eye" id="ico-pass1"></i>
                            </button>
                        </div>
                        <div class="form-error small text-danger mt-1" id="err-reg-password"></div>
                    </div>

                    <!-- Confirmar contraseña -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirmar contraseña <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="pass_conf" id="inp-reg-passconf" class="form-control"
                                   required minlength="8" maxlength="100"
                                   placeholder="Repite la contraseña"
                                   oninput="validarPassword()">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePass('inp-reg-passconf','ico-pass2')">
                                <i class="bi bi-eye" id="ico-pass2"></i>
                            </button>
                        </div>
                        <div class="form-error small text-danger mt-1" id="err-reg-passconf"></div>
                    </div>

                    <!-- reCAPTCHA -->
                    <?php if (!empty($sitekey)): ?>
                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="<?= esc($sitekey) ?>"></div>
                    </div>
                    <?php else: ?>
                    <input type="hidden" name="g-recaptcha-response" value="dev-bypass">
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" id="btn-reg-submit">
                        <i class="bi bi-person-plus me-2"></i>Registrarse
                    </button>
                </form>

                <div class="row g-2 mt-4 text-center">
                    <div class="col-4">
                        <div class="p-2 rounded bg-success bg-opacity-10">
                            <i class="bi bi-shield-check text-success d-block mb-1"></i>
                            <small class="text-muted" style="font-size:.7rem">Datos seguros</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-primary bg-opacity-10">
                            <i class="bi bi-ban text-primary d-block mb-1"></i>
                            <small class="text-muted" style="font-size:.7rem">Sin spam</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 rounded bg-warning bg-opacity-10">
                            <i class="bi bi-x-circle text-warning d-block mb-1"></i>
                            <small class="text-muted" style="font-size:.7rem">Cancela cuando quieras</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const regNombre = document.getElementById('inp-reg-nombre');
const regEmail  = document.getElementById('inp-reg-email');
const cntNombre = document.getElementById('cntNombre');
const cntEmail  = document.getElementById('cntEmail');

// Inicializar contadores si hay valor previo (old input)
if (regNombre.value) cntNombre.textContent = regNombre.value.length;
if (regEmail.value)  cntEmail.textContent  = regEmail.value.length;

regNombre.addEventListener('blur', () => {
    const ok = regNombre.value.trim().length >= 3 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(regNombre.value.trim());
    regNombre.classList.toggle('is-valid',   ok && regNombre.value.length > 0);
    regNombre.classList.toggle('is-invalid', !ok && regNombre.value.length > 0);
    document.getElementById('err-reg-nombre').textContent = (!ok && regNombre.value.length > 0)
        ? 'Solo letras, mínimo 3 caracteres.' : '';
});

regEmail.addEventListener('blur', () => {
    const ok = /^[a-zA-Z0-9._+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(regEmail.value.trim());
    regEmail.classList.toggle('is-valid',   ok && regEmail.value.length > 0);
    regEmail.classList.toggle('is-invalid', !ok && regEmail.value.length > 0);
    document.getElementById('err-reg-email').textContent = (!ok && regEmail.value.length > 0)
        ? 'Ingresa un email válido.' : '';
});

function togglePass(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    const isPass = inp.type === 'password';
    inp.type = isPass ? 'text' : 'password';
    ico.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

function validarPassword() {
    const pass = document.getElementById('inp-reg-password');
    const conf = document.getElementById('inp-reg-passconf');
    const errP = document.getElementById('err-reg-password');
    const errC = document.getElementById('err-reg-passconf');

    const ok = pass.value.length >= 8;
    pass.classList.toggle('is-valid',   ok && pass.value.length > 0);
    pass.classList.toggle('is-invalid', !ok && pass.value.length > 0);
    errP.textContent = (!ok && pass.value.length > 0) ? 'Mínimo 8 caracteres.' : '';

    if (conf.value.length > 0) {
        const match = pass.value === conf.value;
        conf.classList.toggle('is-valid',   match);
        conf.classList.toggle('is-invalid', !match);
        errC.textContent = !match ? 'Las contraseñas no coinciden.' : '';
    }
}
</script>

<?= $this->endSection() ?>
