<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px">
                        <i class="bi bi-shield-lock fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-1">Recuperar Contraseña</h4>
                    <p class="text-muted small">Ingresa tu email para recibir un código de verificación de 6 dígitos.</p>
                </div>

                <!-- PASO 1: Email -->
                <div id="paso-email">
                    <form id="form-email" novalidate>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Correo electrónico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="inp-recovery-email" class="form-control" required
                                       placeholder="tu@email.com" autocomplete="email">
                            </div>
                            <div class="form-error" id="err-rec-email"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="btn-send-code">
                            <i class="bi bi-send me-2"></i>Enviar Código
                        </button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('login') ?>" class="text-muted small">
                            <i class="bi bi-arrow-left me-1"></i>Volver al login
                        </a>
                    </div>
                </div>

                <!-- PASO 2: Código de verificación -->
                <div id="paso-codigo" style="display:none">
                    <div class="alert alert-info border-0 mb-4 small">
                        <i class="bi bi-info-circle me-2"></i>
                        Revisa tu correo. Ingresa el código de <strong>6 dígitos</strong> enviado a <strong id="email-display"></strong>.
                    </div>
                    <form id="form-codigo" novalidate>
                        <input type="hidden" id="verified-email" name="email">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-center d-block">Código de verificación</label>
                            <input type="text" name="token" id="inp-token" class="form-control text-center fs-3 fw-bold letter-spacing-lg"
                                   required maxlength="6" minlength="6" pattern="[0-9]{6}"
                                   placeholder="000000" autocomplete="one-time-code" inputmode="numeric">
                            <div class="form-error" id="err-rec-token"></div>
                        </div>
                        <button type="submit" class="btn btn-success w-100" id="btn-verify-code">
                            <i class="bi bi-check-circle me-2"></i>Verificar Código
                        </button>
                    </form>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-link btn-sm text-muted p-0" id="btn-back-email">
                            <i class="bi bi-arrow-left me-1"></i>Cambiar email
                        </button>
                        <button class="btn btn-link btn-sm text-primary p-0" id="btn-resend">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reenviar código
                        </button>
                    </div>
                </div>

                <!-- PASO 3: Nueva contraseña -->
                <div id="paso-password" style="display:none">
                    <div class="alert alert-success border-0 mb-4 small">
                        <i class="bi bi-check-circle me-2"></i>
                        Código verificado. Ingresa tu nueva contraseña.
                    </div>
                    <form id="form-password" novalidate>
                        <input type="hidden" id="final-email" name="email">
                        <input type="hidden" id="final-token" name="token">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" name="password" id="inp-new-pass" class="form-control" required
                                       minlength="12" placeholder="Mínimo 12 caracteres">
                                <button class="btn btn-outline-secondary" type="button" id="btn-toggle-pass">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <!-- Indicador de fortaleza -->
                            <div class="mt-2">
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar" id="pass-strength-bar" style="width:0%;transition:width 0.3s"></div>
                                </div>
                                <small id="pass-strength-text" class="text-muted">Ingresa una contraseña</small>
                            </div>
                            <div class="mt-2 small text-muted">
                                <div id="req-len"    class="req-item"><i class="bi bi-circle me-1"></i>Mínimo 12 caracteres</div>
                                <div id="req-upper"  class="req-item"><i class="bi bi-circle me-1"></i>Una mayúscula</div>
                                <div id="req-lower"  class="req-item"><i class="bi bi-circle me-1"></i>Una minúscula</div>
                                <div id="req-number" class="req-item"><i class="bi bi-circle me-1"></i>Un número</div>
                                <div id="req-special"class="req-item"><i class="bi bi-circle me-1"></i>Un carácter especial (@$!%*?&)</div>
                            </div>
                            <div class="form-error" id="err-rec-password"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirmar contraseña</label>
                            <input type="password" name="password_confirm" id="inp-confirm-pass" class="form-control" required
                                   placeholder="Repite la contraseña">
                            <div class="form-error" id="err-rec-password_confirm"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="btn-change-pass">
                            <i class="bi bi-lock me-2"></i>Cambiar Contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentEmail = '';
let currentToken = '';

// --- PASO 1: Enviar código ---
document.getElementById('form-email').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('btn-send-code');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    document.getElementById('err-rec-email').textContent = '';

    const fd  = new FormData(e.target);
    const res = await fetch('<?= base_url('recuperar-password/enviar-codigo') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Código';

    if (data.success) {
        currentEmail = document.getElementById('inp-recovery-email').value;
        document.getElementById('email-display').textContent  = currentEmail;
        document.getElementById('verified-email').value       = currentEmail;
        document.getElementById('paso-email').style.display   = 'none';
        document.getElementById('paso-codigo').style.display  = 'block';
        Toast.success('Código enviado. Revisa tu email.');
        document.getElementById('inp-token').focus();
    } else {
        if (data.errors && data.errors.email) {
            document.getElementById('err-rec-email').textContent = data.errors.email;
        }
        Toast.error(data.mensaje || 'Error al enviar el código.');
    }
});

// --- PASO 2: Verificar código ---
document.getElementById('form-codigo').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('btn-verify-code');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verificando...';
    document.getElementById('err-rec-token').textContent = '';

    const fd  = new FormData(e.target);
    const res = await fetch('<?= base_url('recuperar-password/verificar') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Verificar Código';

    if (data.success) {
        currentToken = document.getElementById('inp-token').value;
        document.getElementById('final-email').value          = currentEmail;
        document.getElementById('final-token').value          = currentToken;
        document.getElementById('paso-codigo').style.display  = 'none';
        document.getElementById('paso-password').style.display= 'block';
        Toast.success('Código correcto.');
    } else {
        document.getElementById('err-rec-token').textContent = data.mensaje || 'Código inválido.';
        document.getElementById('inp-token').classList.add('is-invalid');
        Toast.error(data.mensaje || 'Código inválido o expirado.');
    }
});

// --- PASO 3: Cambiar contraseña ---
document.getElementById('form-password').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('btn-change-pass');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    document.querySelectorAll('[id^="err-rec-"]').forEach(el => el.textContent = '');

    const fd  = new FormData(e.target);
    const res = await fetch('<?= base_url('recuperar-password/cambiar') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-lock me-2"></i>Cambiar Contraseña';

    if (data.success) {
        Toast.success(data.mensaje || 'Contraseña actualizada.');
        setTimeout(() => { window.location.href = data.redirect || '<?= base_url('login') ?>'; }, 1500);
    } else {
        if (data.errors) {
            Object.entries(data.errors).forEach(([k, v]) => {
                const el = document.getElementById(`err-rec-${k}`);
                if (el) el.textContent = v;
            });
        }
        Toast.error(data.mensaje || 'Error al cambiar contraseña.');
    }
});

// --- Reenviar código ---
document.getElementById('btn-resend').addEventListener('click', async () => {
    if (!currentEmail) return;
    const fd = new FormData();
    fd.append('email', currentEmail);
    await fetch('<?= base_url('recuperar-password/enviar-codigo') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
    Toast.info('Código reenviado. Revisa tu email.');
});

document.getElementById('btn-back-email').addEventListener('click', () => {
    document.getElementById('paso-codigo').style.display = 'none';
    document.getElementById('paso-email').style.display  = 'block';
});

// --- Mostrar/ocultar contraseña ---
document.getElementById('btn-toggle-pass').addEventListener('click', () => {
    const inp = document.getElementById('inp-new-pass');
    inp.type  = inp.type === 'password' ? 'text' : 'password';
    document.querySelector('#btn-toggle-pass i').className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

// --- Indicador de fortaleza ---
document.getElementById('inp-new-pass').addEventListener('input', e => {
    const v    = e.target.value;
    const reqs = {
        'req-len':     v.length >= 12,
        'req-upper':   /[A-Z]/.test(v),
        'req-lower':   /[a-z]/.test(v),
        'req-number':  /\d/.test(v),
        'req-special': /[@$!%*?&]/.test(v),
    };
    let score = Object.values(reqs).filter(Boolean).length;
    Object.entries(reqs).forEach(([id, ok]) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.className = ok ? 'req-item text-success' : 'req-item text-muted';
        el.innerHTML = `<i class="bi bi-${ok ? 'check-circle-fill' : 'circle'} me-1"></i>${el.textContent.trim()}`;
    });
    const bar = document.getElementById('pass-strength-bar');
    const txt = document.getElementById('pass-strength-text');
    const levels = [
        {w:'20%', cls:'bg-danger',  label:'Muy débil'},
        {w:'40%', cls:'bg-warning', label:'Débil'},
        {w:'60%', cls:'bg-info',    label:'Regular'},
        {w:'80%', cls:'bg-primary', label:'Fuerte'},
        {w:'100%',cls:'bg-success', label:'Muy fuerte'},
    ];
    const lv = levels[Math.max(0, score - 1)] || levels[0];
    bar.style.width = v.length ? lv.w : '0%';
    bar.className   = `progress-bar ${v.length ? lv.cls : ''}`;
    txt.textContent = v.length ? lv.label : 'Ingresa una contraseña';
    txt.className   = `small ${v.length ? lv.cls.replace('bg-','text-') : 'text-muted'}`;
});

// --- Confirmar contraseña ---
document.getElementById('inp-confirm-pass').addEventListener('input', e => {
    const match = e.target.value === document.getElementById('inp-new-pass').value;
    e.target.classList.toggle('is-valid',   match && e.target.value.length > 0);
    e.target.classList.toggle('is-invalid', !match && e.target.value.length > 0);
    document.getElementById('err-rec-password_confirm').textContent = (!match && e.target.value) ? 'Las contraseñas no coinciden.' : '';
});

// Formato token: solo números
document.getElementById('inp-token').addEventListener('input', e => {
    e.target.value = e.target.value.replace(/\D/g,'').slice(0,6);
});
</script>

<?= $this->endSection() ?>
