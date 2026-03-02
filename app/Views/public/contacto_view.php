<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3">Contacto</span>
            <h2 class="fw-bold">¿Hablamos?</h2>
            <p class="text-muted">Completa el formulario y te responderé a la brevedad posible.</p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form id="form-contacto" novalidate>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="inp-nombre" class="form-control"
                                   required minlength="2" maxlength="100"
                                   placeholder="Tu nombre">
                            <div class="invalid-feedback" id="err-nombre"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="inp-email" class="form-control"
                                   required maxlength="150"
                                   placeholder="tu@correo.com">
                            <div class="invalid-feedback" id="err-email"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="tel" name="telefono" id="inp-telefono" class="form-control"
                                   pattern="[0-9+\-\s]{7,20}" maxlength="20"
                                   placeholder="+34 600 000 000">
                            <div class="invalid-feedback" id="err-telefono"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de consulta</label>
                            <select name="categoria" class="form-select">
                                <option value="consulta">Consulta general</option>
                                <option value="presupuesto">Solicitar presupuesto</option>
                                <option value="soporte">Soporte técnico</option>
                                <option value="colaboracion">Propuesta de colaboración</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Asunto</label>
                            <input type="text" name="asunto" class="form-control" maxlength="200"
                                   placeholder="Breve descripción del tema">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
                            <textarea name="mensaje" id="inp-mensaje" class="form-control" rows="5"
                                      required minlength="10" maxlength="2000"
                                      placeholder="Cuéntame en qué puedo ayudarte..."></textarea>
                            <div class="d-flex justify-content-between mt-1">
                                <div class="invalid-feedback d-block" id="err-mensaje"></div>
                                <small class="text-muted ms-auto"><span id="msg-count">0</span>/2000</small>
                            </div>
                        </div>

                        <!-- Validación frontend en tiempo real -->
                        <div class="col-12">
                            <div class="d-flex gap-2 flex-wrap small text-muted mb-2">
                                <span id="check-nombre" class="validate-hint"><i class="bi bi-circle me-1"></i>Nombre válido</span>
                                <span id="check-email"  class="validate-hint"><i class="bi bi-circle me-1"></i>Email válido</span>
                                <span id="check-msg"    class="validate-hint"><i class="bi bi-circle me-1"></i>Mensaje ≥ 10 caracteres</span>
                            </div>
                        </div>

                        <!-- reCAPTCHA -->
                        <?php if (!empty($sitekey)): ?>
                        <div class="col-12">
                            <div class="g-recaptcha" data-sitekey="<?= esc($sitekey) ?>"></div>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="g-recaptcha-response" value="dev-bypass">
                        <?php endif; ?>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="btn-enviar">
                                <i class="bi bi-send me-2"></i>Enviar Mensaje
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Éxito -->
                <div id="contacto-success" class="text-center py-4" style="display:none">
                    <div class="success-animation mb-3">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:4rem"></i>
                    </div>
                    <h4 class="fw-bold text-success mb-2">¡Mensaje enviado!</h4>
                    <p class="text-muted">Gracias por contactarme. Te responderé a la brevedad.</p>
                    <button class="btn btn-outline-primary mt-2" id="btn-nuevo-mensaje">
                        <i class="bi bi-plus me-2"></i>Enviar otro mensaje
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const form     = document.getElementById('form-contacto');
const msgArea  = document.getElementById('inp-mensaje');
const msgCount = document.getElementById('msg-count');

// Contador de caracteres
msgArea.addEventListener('input', () => { msgCount.textContent = msgArea.value.length; });

// Validación en tiempo real por campo
function setHint(id, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = ok ? 'validate-hint text-success' : 'validate-hint text-muted';
    el.innerHTML = `<i class="bi bi-${ok ? 'check-circle-fill' : 'circle'} me-1"></i>${el.textContent.replace(/^[✓○] /,'').trim()}`;
}
function setFieldError(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    if (msg) { el.textContent = msg; el.style.display = 'block'; }
    else { el.textContent = ''; el.style.display = 'none'; }
}

document.getElementById('inp-nombre').addEventListener('input', e => {
    const ok = e.target.value.trim().length >= 2 && /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(e.target.value.trim());
    setHint('check-nombre', ok);
    setFieldError('err-nombre', ok ? '' : 'Solo letras, mínimo 2 caracteres.');
    e.target.classList.toggle('is-invalid', !ok && e.target.value.length > 0);
    e.target.classList.toggle('is-valid', ok);
});
document.getElementById('inp-email').addEventListener('input', e => {
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e.target.value.trim());
    setHint('check-email', ok);
    setFieldError('err-email', ok ? '' : 'Ingresa un email válido.');
    e.target.classList.toggle('is-invalid', !ok && e.target.value.length > 0);
    e.target.classList.toggle('is-valid', ok);
});
msgArea.addEventListener('input', e => {
    const ok = e.target.value.trim().length >= 10;
    setHint('check-msg', ok);
    setFieldError('err-mensaje', ok ? '' : 'El mensaje debe tener al menos 10 caracteres.');
    e.target.classList.toggle('is-invalid', !ok && e.target.value.length > 0);
    e.target.classList.toggle('is-valid', ok);
});

// Submit
form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('btn-enviar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

    document.querySelectorAll('.invalid-feedback').forEach(el => { el.textContent = ''; el.style.display = 'none'; });

    try {
        const res  = await fetch('<?= base_url('contacto/enviar') ?>', {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            form.style.display = 'none';
            document.getElementById('contacto-success').style.display = 'block';
            Toast.success(data.mensaje || '¡Mensaje enviado!');
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, msg]) => {
                    const el = document.getElementById(`err-${field}`);
                    if (el) { el.textContent = msg; el.style.display = 'block'; }
                    const inp = document.querySelector(`[name="${field}"]`);
                    if (inp) inp.classList.add('is-invalid');
                });
            }
            Toast.error(data.mensaje || 'Error al enviar el mensaje.');
        }
    } catch (err) {
        Toast.error('Error de conexión. Intenta de nuevo.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send me-2"></i>Enviar Mensaje';
    }
});

document.getElementById('btn-nuevo-mensaje').addEventListener('click', () => {
    form.reset();
    msgCount.textContent = '0';
    document.querySelectorAll('.is-valid,.is-invalid').forEach(el => el.classList.remove('is-valid','is-invalid'));
    form.style.display = 'block';
    document.getElementById('contacto-success').style.display = 'none';
});
</script>

<?= $this->endSection() ?>
