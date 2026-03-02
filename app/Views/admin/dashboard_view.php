<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard</h2>
        <p class="text-muted small mb-0">Panel de control general del portafolio</p>
    </div>
    <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-eye me-1"></i>Ver Portafolio
    </a>
</div>

<!-- TARJETAS ESTADÍSTICAS -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['key'=>'proyectos',        'label'=>'Proyectos',        'icon'=>'bi-folder2-open',    'color'=>'primary', 'link'=>base_url('admin/proyectos')],
        ['key'=>'contactos',        'label'=>'Mensajes',          'icon'=>'bi-chat-left-dots',  'color'=>'info',    'link'=>base_url('admin/contactos')],
        ['key'=>'contactos_nuevos', 'label'=>'Sin Leer',          'icon'=>'bi-envelope-open',   'color'=>'warning', 'link'=>base_url('admin/contactos')],
        ['key'=>'usuarios',         'label'=>'Usuarios',          'icon'=>'bi-people',          'color'=>'success', 'link'=>base_url('registro')],
        ['key'=>'carrusel',         'label'=>'Imágenes Carrusel', 'icon'=>'bi-images',          'color'=>'secondary','link'=>base_url('carrusel')],
        ['key'=>'notificaciones',   'label'=>'Notif. Pendientes', 'icon'=>'bi-bell',            'color'=>'danger',  'link'=>'#'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
    <div class="col-sm-6 col-xl-4">
        <a href="<?= $card['link'] ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm stat-card hover-card h-100">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="stat-icon bg-<?= $card['color'] ?> bg-opacity-10 text-<?= $card['color'] ?>">
                        <i class="bi <?= $card['icon'] ?> fs-3"></i>
                    </div>
                    <div>
                        <div class="stat-number fw-bold fs-2"><?= $stats[$card['key']] ?? 0 ?></div>
                        <div class="text-muted small"><?= $card['label'] ?></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <!-- ACTIVIDAD RECIENTE -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-activity me-2 text-primary"></i>Actividad Reciente</h6>
                <a href="<?= base_url('admin/contactos') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body px-4">
                <?php if (empty($actividad)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>Sin actividad reciente
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($actividad as $item): ?>
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-start gap-3">
                                <div class="activity-avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;min-width:36px">
                                    <i class="bi bi-person small"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold small"><?= esc($item['nombre'] ?? '') ?></span>
                                        <span class="badge rounded-pill bg-<?= ($item['estado'] ?? '') === 'pendiente' ? 'warning' : 'success' ?> bg-opacity-75 small">
                                            <?= esc($item['estado'] ?? 'pendiente') ?>
                                        </span>
                                    </div>
                                    <div class="text-muted small"><?= esc($item['email'] ?? '') ?></div>
                                    <div class="text-muted tiny"><?= esc(substr($item['mensaje'] ?? '', 0, 80)) ?>...</div>
                                    <div class="text-muted tiny mt-1">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= date('d M Y H:i', strtotime($item['created_at'] ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- NOTIFICACIONES + ACCESOS RÁPIDOS -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-warning"></i>Notificaciones Recientes</h6>
            </div>
            <div class="card-body px-4 pb-3">
                <?php if (empty($notificaciones)): ?>
                    <p class="text-muted small text-center py-2">Sin notificaciones</p>
                <?php else: ?>
                    <?php foreach (array_slice($notificaciones, 0, 4) as $n): ?>
                    <div class="d-flex gap-2 align-items-start py-2 border-bottom">
                        <span class="noti-dot noti-<?= esc($n['tipo'] ?? 'info') ?> mt-1"></span>
                        <div>
                            <div class="small fw-semibold"><?= esc($n['titulo'] ?? '') ?></div>
                            <div class="tiny text-muted"><?= esc(substr($n['mensaje'] ?? '', 0, 60)) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-lightning me-2 text-success"></i>Accesos Rápidos</h6>
            </div>
            <div class="card-body px-4 pb-3">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/sobre-mi') ?>" class="btn btn-outline-primary btn-sm text-start">
                        <i class="bi bi-person-badge me-2"></i>Editar Perfil
                    </a>
                    <a href="<?= base_url('carrusel') ?>" class="btn btn-outline-info btn-sm text-start">
                        <i class="bi bi-images me-2"></i>Gestionar Carrusel
                    </a>
                    <a href="<?= base_url('admin/proyectos') ?>" class="btn btn-outline-success btn-sm text-start">
                        <i class="bi bi-plus-circle me-2"></i>Nuevo Proyecto
                    </a>
                    <a href="<?= base_url('admin/servicios') ?>" class="btn btn-outline-warning btn-sm text-start">
                        <i class="bi bi-gear me-2"></i>Editar Servicios
                    </a>
                    <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-secondary btn-sm text-start">
                        <i class="bi bi-shield-lock me-2"></i>Gestionar Roles
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
