<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card border-0 shadow-sm animate-fade-in">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-shield-lock display-4 text-primary"></i>
                    </div>
                    <h2 class="fw-bold mb-2">Iniciar Sesión</h2>
                    <p class="text-muted">Panel de Administración</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4 border-0 shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <span><?= session()->getFlashdata('success') ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-warning mb-4 border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                            <div class="flex-grow-1">
                                <strong>Errores de validación:</strong>
                                <ul class="mb-0 mt-2 ps-3">
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= base_url('login/procesar') ?>" autocomplete="off" id="form-login" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">CORREO ELECTRÓNICO</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                id="login-usuario"
                                class="form-control"
                                placeholder="correo@ejemplo.com"
                                value="<?= old('email') ?>"
                                autofocus
                                maxlength="200"
                            >
                        </div>
                        <div class="form-error text-danger small mt-1" id="err-login-usuario"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">CONTRASEÑA</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key"></i>
                            </span>
                            <input
                                type="password"
                                name="password"
                                id="login-password"
                                class="form-control"
                                placeholder="••••••••"
                                maxlength="100"
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="form-error text-danger small mt-1" id="err-login-password"></div>
                    </div>

                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="<?= esc(getenv('RECAPTCHA_SITEKEY')) ?>"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow mb-3" id="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio
                        </a>
                        <a href="<?= base_url('recuperar-password') ?>" class="text-decoration-none text-primary small">
                            <i class="bi bi-key me-1"></i>¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Conexión segura protegida
            </small>
        </div>
    </div>
</div>

<script>
const togglePassword = document.getElementById('togglePassword');
const passwordField  = document.getElementById('login-password');
const eyeIcon        = document.getElementById('eyeIcon');

if (togglePassword && passwordField && eyeIcon) {
    togglePassword.addEventListener('click', () => {
        const isText = passwordField.type === 'text';
        passwordField.type = isText ? 'password' : 'text';
        eyeIcon.className  = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
}

document.getElementById('form-login').addEventListener('submit', function(e) {
    ['login-usuario', 'login-password'].forEach(id => {
        const el = document.getElementById(id);
        const err = document.getElementById('err-' + id);
        if (el)  el.classList.remove('is-invalid');
        if (err) err.textContent = '';
    });

    const usuarioEl  = document.getElementById('login-usuario');
    const passwordEl = document.getElementById('login-password');
    let valid = true;

    const email = usuarioEl.value.trim();
    if (!email) {
        usuarioEl.classList.add('is-invalid');
        document.getElementById('err-login-usuario').textContent = 'El correo es obligatorio';
        FormValidator.shake(usuarioEl.closest('.input-group'));
        valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        usuarioEl.classList.add('is-invalid');
        document.getElementById('err-login-usuario').textContent = 'Ingresa un correo válido';
        FormValidator.shake(usuarioEl.closest('.input-group'));
        valid = false;
    }

    const pwd = passwordEl.value;
    if (!pwd) {
        passwordEl.classList.add('is-invalid');
        document.getElementById('err-login-password').textContent = 'La contraseña es obligatoria';
        FormValidator.shake(passwordEl.closest('.input-group'));
        valid = false;
    } else if (pwd.length < 6) {
        passwordEl.classList.add('is-invalid');
        document.getElementById('err-login-password').textContent = 'Mínimo 6 caracteres';
        FormValidator.shake(passwordEl.closest('.input-group'));
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        const card = document.querySelector('.card');
        card.classList.remove('input-shake');
        void card.offsetWidth;
        card.classList.add('input-shake');
        return;
    }

    FormValidator.btnLoad(document.getElementById('btn-login'));
});
</script>

<?= $this->endSection() ?>