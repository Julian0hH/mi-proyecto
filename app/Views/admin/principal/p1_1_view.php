<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$permisos = session()->get('user_permisos') ?? [];
$isAdmin  = session()->get('user_type') === 'admin';
$puedeAgregar  = $isAdmin || !empty($permisos[5]['bitAgregar']);
$puedeEditar   = $isAdmin || !empty($permisos[5]['bitEditar']);
$puedeEliminar = $isAdmin || !empty($permisos[5]['bitEliminar']);
$puedeDetalle  = $isAdmin || !empty($permisos[5]['bitDetalle']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            <i class="bi <?= esc($icono ?? 'bi-funnel-fill') ?> me-2 text-<?= esc($color ?? 'primary') ?>"></i><?= esc($titulo ?? 'Pipeline de Ventas') ?>
        </h2>
        <p class="text-muted small mb-0"><?= esc($subtitulo ?? 'Gestión y seguimiento del proceso comercial') ?></p>
    </div>
    <span class="badge bg-secondary fs-6 py-2 px-3"><i class="bi bi-info-circle me-1"></i>Vista estática</span>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
    <i class="bi bi-info-circle-fill fs-4 text-info flex-shrink-0"></i>
    <div>
        <strong>Módulo de demostración.</strong> Los botones de acción están habilitados visualmente
        pero no ejecutan operaciones a base de datos.
    </div>
</div>

<!-- Barra de acciones -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2 px-3 d-flex align-items-center gap-2 flex-wrap">
        <?php if ($puedeAgregar): ?>
        <button class="btn btn-primary btn-sm" onclick="demoAccion('Agregar')">
            <i class="bi bi-plus-circle me-1"></i>Agregar
        </button>
        <?php endif; ?>
        <?php if ($puedeEditar): ?>
        <button class="btn btn-outline-primary btn-sm" onclick="demoAccion('Editar')">
            <i class="bi bi-pencil me-1"></i>Editar
        </button>
        <?php endif; ?>
        <?php if ($puedeEliminar): ?>
        <button class="btn btn-outline-danger btn-sm" onclick="demoAccion('Eliminar')">
            <i class="bi bi-trash me-1"></i>Eliminar
        </button>
        <?php endif; ?>
        <?php if ($puedeDetalle): ?>
        <button class="btn btn-outline-info btn-sm" onclick="demoAccion('Consultar')">
            <i class="bi bi-eye me-1"></i>Consultar
        </button>
        <?php endif; ?>
        <div class="ms-auto">
            <div class="input-group input-group-sm" style="width:220px">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="Buscar..." disabled>
            </div>
        </div>
    </div>
</div>

<!-- Tabla estática -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="px-4">#</th>
                        <th>Oportunidad</th>
                        <th>Cliente</th>
                        <th>Valor Est.</th>
                        <th>Etapa</th>
                        <th>Cierre Estimado</th>
                        <th class="text-end px-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-funnel fs-1 d-block mb-2 opacity-25"></i>
                            <strong>Sin oportunidades en el pipeline</strong>
                            <p class="small mb-0 mt-1">Módulo estático — sin conexión a BD</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between px-4 py-2 border-top">
            <small class="text-muted">Mostrando 0 de 0 registros</small>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link">«</a></li>
                    <li class="page-item active"><a class="page-link">1</a></li>
                    <li class="page-item disabled"><a class="page-link">»</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
function demoAccion(accion) {
    Toast.info(`Acción <strong>${accion}</strong> — módulo estático sin conexión a BD`);
}
</script>

<?= $this->endSection() ?>
