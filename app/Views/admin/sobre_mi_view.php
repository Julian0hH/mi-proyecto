<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Sobre Mí</h2>
        <p class="text-muted small mb-0">Edita tu perfil profesional público</p>
    </div>
    <a href="<?= base_url('sobre-mi') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-eye me-1"></i>Vista Pública
    </a>
</div>

<?php $sm = $sobre_mi ?? []; ?>

<form id="form-sobre-mi">
    <div class="row g-4">
        <!-- COLUMNA IZQUIERDA: Información principal -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Información Principal</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título Profesional <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required maxlength="200"
                               value="<?= esc($sm['titulo'] ?? 'Desarrollador Backend & Cloud') ?>"
                               placeholder="Ej: Desarrollador Backend Senior">
                        <div class="form-error" id="err-titulo"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subtítulo</label>
                        <input type="text" name="subtitulo" class="form-control" maxlength="300"
                               value="<?= esc($sm['subtitulo'] ?? '') ?>"
                               placeholder="Ej: Especialista en cloud y bases de datos">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control" rows="6" required minlength="20"
                                  placeholder="Cuéntanos sobre ti, tu experiencia y lo que te apasiona..."><?= esc($sm['descripcion'] ?? '') ?></textarea>
                        <div class="form-text"><span id="desc-count">0</span>/2000 caracteres</div>
                        <div class="form-error" id="err-descripcion"></div>
                    </div>
                </div>
            </div>

            <!-- HABILIDADES -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2 text-success"></i>Habilidades</h6>
                    <button type="button" class="btn btn-sm btn-outline-success" id="btn-add-skill">
                        <i class="bi bi-plus me-1"></i>Agregar
                    </button>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="skills-container">
                        <?php
                        $habilidades = $sm['habilidades'] ?? [];
                        if (is_string($habilidades)) $habilidades = json_decode($habilidades, true) ?: [];
                        if (empty($habilidades)):
                            $habilidades = [
                                ['nombre'=>'PHP / CodeIgniter','nivel'=>90],
                                ['nombre'=>'PostgreSQL / Supabase','nivel'=>85],
                                ['nombre'=>'AWS / Cloud','nivel'=>80],
                            ];
                        endif;
                        foreach ($habilidades as $h): ?>
                        <div class="skill-row row g-2 align-items-center mb-2">
                            <div class="col-5">
                                <input type="text" name="habilidad_nombre[]" class="form-control form-control-sm"
                                       value="<?= esc($h['nombre'] ?? '') ?>" placeholder="Nombre de habilidad">
                            </div>
                            <div class="col-5">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="range" name="habilidad_nivel[]" class="form-range skill-range"
                                           min="0" max="100" value="<?= (int)($h['nivel'] ?? 75) ?>">
                                    <span class="skill-val small fw-semibold" style="min-width:32px"><?= (int)($h['nivel'] ?? 75) ?>%</span>
                                </div>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-skill w-100">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Estadísticas + Redes -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-award me-2 text-warning"></i>Estadísticas</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Años de Experiencia</label>
                        <input type="number" name="experiencia_anos" class="form-control" min="0" max="50"
                               value="<?= (int)($sm['experiencia_anos'] ?? 0) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Proyectos Completados</label>
                        <input type="number" name="proyectos_completados" class="form-control" min="0"
                               value="<?= (int)($sm['proyectos_completados'] ?? 0) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Clientes Satisfechos</label>
                        <input type="number" name="clientes_satisfechos" class="form-control" min="0"
                               value="<?= (int)($sm['clientes_satisfechos'] ?? 0) ?>">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-share me-2 text-info"></i>Redes Sociales</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small"><i class="bi bi-linkedin text-primary me-1"></i>LinkedIn</label>
                        <input type="url" name="linkedin_url" class="form-control form-control-sm"
                               value="<?= esc($sm['linkedin_url'] ?? '') ?>" placeholder="https://linkedin.com/in/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small"><i class="bi bi-github me-1"></i>GitHub</label>
                        <input type="url" name="github_url" class="form-control form-control-sm"
                               value="<?= esc($sm['github_url'] ?? '') ?>" placeholder="https://github.com/...">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small"><i class="bi bi-envelope text-danger me-1"></i>Email de contacto</label>
                        <input type="email" name="email_contacto" class="form-control form-control-sm"
                               value="<?= esc($sm['email_contacto'] ?? '') ?>" placeholder="tu@email.com">
                        <div class="form-error" id="err-email_contacto"></div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg" id="btn-guardar">
                <i class="bi bi-check-circle me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</form>

<script>
const form = document.getElementById('form-sobre-mi');
const descArea = form.querySelector('[name="descripcion"]');
const descCount = document.getElementById('desc-count');

descCount.textContent = descArea.value.length;
descArea.addEventListener('input', () => descCount.textContent = descArea.value.length);

// Habilidades: slider live
document.getElementById('skills-container').addEventListener('input', e => {
    if (e.target.classList.contains('skill-range')) {
        e.target.nextElementSibling.textContent = e.target.value + '%';
    }
});

// Agregar habilidad
document.getElementById('btn-add-skill').addEventListener('click', () => {
    const container = document.getElementById('skills-container');
    const div = document.createElement('div');
    div.className = 'skill-row row g-2 align-items-center mb-2';
    div.innerHTML = `
        <div class="col-5"><input type="text" name="habilidad_nombre[]" class="form-control form-control-sm" placeholder="Nombre de habilidad"></div>
        <div class="col-5"><div class="d-flex align-items-center gap-2">
            <input type="range" name="habilidad_nivel[]" class="form-range skill-range" min="0" max="100" value="75">
            <span class="skill-val small fw-semibold" style="min-width:32px">75%</span>
        </div></div>
        <div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-skill w-100"><i class="bi bi-trash"></i></button></div>`;
    container.appendChild(div);
    div.querySelector('.skill-range').addEventListener('input', e => {
        e.target.nextElementSibling.textContent = e.target.value + '%';
    });
});

// Eliminar habilidad
document.getElementById('skills-container').addEventListener('click', e => {
    if (e.target.closest('.btn-remove-skill')) {
        e.target.closest('.skill-row').remove();
    }
});

// Submit
form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    document.querySelectorAll('.form-error').forEach(el => el.textContent = '');

    const fd = new FormData(form);
    try {
        const res  = await fetch('<?= base_url('admin/sobre-mi/guardar') ?>', {method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'}});
        const data = await res.json();
        if (data.success) {
            Toast.success(data.mensaje || 'Guardado correctamente');
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, msg]) => {
                    const el = document.getElementById(`err-${field}`);
                    if (el) el.textContent = msg;
                });
            }
            Toast.error(data.mensaje || 'Error al guardar');
        }
    } catch (err) {
        Toast.error('Error de red al guardar');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Cambios';
    }
});
</script>

<?= $this->endSection() ?>
