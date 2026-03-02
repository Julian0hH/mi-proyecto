<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php if (empty($servicio)): ?>
<div class="text-center py-5">
    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
    <h4 class="text-muted">Servicio no encontrado</h4>
    <a href="<?= base_url('servicios') ?>" class="btn btn-primary mt-3">
        <i class="bi bi-arrow-left me-2"></i>Ver servicios
    </a>
</div>
<?php else: ?>

<div class="row g-4 animate-fade-in">

    <!-- Resumen del servicio -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-<?= esc($servicio['color'] ?? 'primary') ?> bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:56px;height:56px">
                        <i class="bi <?= esc($servicio['icono'] ?? 'bi-gear') ?> fs-3 text-<?= esc($servicio['color'] ?? 'primary') ?>"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0"><?= esc($servicio['titulo']) ?></h5>
                        <?php if (!empty($servicio['precio_desde'])): ?>
                        <span class="badge bg-<?= esc($servicio['color'] ?? 'primary') ?> bg-opacity-10 text-<?= esc($servicio['color'] ?? 'primary') ?> px-2 py-1 rounded-pill">
                            Desde $<?= number_format((float)$servicio['precio_desde'], 2) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <p class="text-muted small mb-3"><?= esc($servicio['descripcion']) ?></p>

                <div class="p-3 rounded-3 bg-success bg-opacity-10 small text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i>
                    Pago 100% seguro a través de plataformas certificadas. No gestionamos tu información de pago directamente.
                </div>
            </div>
        </div>
    </div>

    <!-- Métodos de pago -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="fw-bold m-0">
                    <i class="bi bi-credit-card me-2 text-primary"></i>Selecciona tu método de pago
                </h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Elige tu plataforma preferida. Serás redirigido de forma segura para completar el pago.
                </p>

                <div class="d-grid gap-3">

                    <?php if (!empty($paypal_link)): ?>
                    <a href="<?= esc($paypal_link) ?>" target="_blank" rel="noopener noreferrer"
                       class="btn btn-lg border-0 shadow-sm d-flex align-items-center gap-3 p-4 text-white"
                       style="background:#003087;border-radius:12px">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white"
                             style="width:44px;height:44px;flex-shrink:0">
                            <i class="bi bi-paypal text-primary fs-4"></i>
                        </div>
                        <div class="text-start flex-grow-1">
                            <div class="fw-bold">Pagar con PayPal</div>
                            <small class="opacity-75">Tarjeta, cuenta PayPal o transferencia bancaria</small>
                        </div>
                        <i class="bi bi-arrow-up-right fs-5"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($mercadopago_link)): ?>
                    <a href="<?= esc($mercadopago_link) ?>" target="_blank" rel="noopener noreferrer"
                       class="btn btn-lg border-0 shadow-sm d-flex align-items-center gap-3 p-4 text-white"
                       style="background:#009ee3;border-radius:12px">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white"
                             style="width:44px;height:44px;flex-shrink:0">
                            <i class="bi bi-credit-card-2-front text-info fs-4"></i>
                        </div>
                        <div class="text-start flex-grow-1">
                            <div class="fw-bold">Pagar con Mercado Pago</div>
                            <small class="opacity-75">Tarjeta, cuotas sin interés, efectivo o cuenta MP</small>
                        </div>
                        <i class="bi bi-arrow-up-right fs-5"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (empty($paypal_link) && empty($mercadopago_link)): ?>
                    <div class="alert alert-info border-0 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle fs-5 flex-shrink-0"></i>
                        <span>Los métodos de pago en línea no están configurados aún. Por favor contáctanos para coordinar el pago.</span>
                    </div>
                    <?php endif; ?>

                    <!-- Contacto alternativo (siempre visible) -->
                    <a href="<?= base_url('contacto') ?>?asunto=<?= urlencode('Contratar: ' . ($servicio['titulo'] ?? '')) ?>"
                       class="btn btn-outline-secondary btn-lg d-flex align-items-center gap-3 p-3"
                       style="border-radius:12px">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                             style="width:44px;height:44px;flex-shrink:0">
                            <i class="bi bi-chat-dots text-primary fs-4"></i>
                        </div>
                        <div class="text-start flex-grow-1">
                            <div class="fw-semibold">Consultar antes de pagar</div>
                            <small class="text-muted">Escríbenos y te asesoramos sin compromiso</small>
                        </div>
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

                <div class="mt-4 p-3 rounded-3 small text-muted" style="background:var(--bg-card)">
                    <i class="bi bi-lock-fill me-1 text-success"></i>
                    Transacciones protegidas por SSL 256-bit. No almacenamos datos de tarjetas ni credenciales de pago.
                </div>
            </div>
            <div class="card-footer bg-transparent border-top p-3">
                <a href="<?= base_url('detalles/' . (int)$servicio['id']) ?>" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Volver a detalles del servicio
                </a>
            </div>
        </div>
    </div>

</div>

<?php endif; ?>

<?= $this->endSection() ?>
