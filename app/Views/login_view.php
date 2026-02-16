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

                <form method="POST" action="<?= base_url('login/procesar') ?>" autocomplete="off">
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
                                class="form-control" 
                                placeholder="admin@ejemplo.com" 
                                required 
                                value="<?= old('email') ?>"
                                autofocus
                            >
                        </div>
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
                                id="password" 
                                class="form-control" 
                                placeholder="••••••••" 
                                required
                            >
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="<?= esc(getenv('RECAPTCHA_SITEKEY')) ?>"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </button>

                    <div class="text-center">
                        <a href="<?= base_url('/') ?>" class="text-decoration-none text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Volver al inicio
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
const password = document.getElementById('password');
const eyeIcon = document.getElementById('eyeIcon');

if (togglePassword && password && eyeIcon) {
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });
}
</script>

<?= $this->endSection() ?>