<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm overflow-hidden animate-fade-in">
    <div class="row g-0">
        <div class="col-md-5 d-flex align-items-center justify-content-center p-5 bg-light">
            <div class="text-center">
                <i class="bi bi-code-square text-primary mb-3" style="font-size: 6rem;"></i>
                <h4 class="fw-bold">Full Stack</h4>
                <p class="text-muted">Enterprise Edition</p>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary px-3 py-2 rounded-pill">Disponible</span>
                    <h3 class="fw-bold m-0 text-primary">$499 <small class="text-muted fs-6">/mes</small></h3>
                </div>

                <h2 class="fw-bold mb-3">Desarrollo Web Avanzado</h2>
                <p class="lead text-muted mb-4">
                    Solución completa para empresas que requieren escalabilidad, seguridad y alto rendimiento.
                </p>
                
                <div class="row g-3 mb-5">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Backend CI4</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Frontend Bootstrap</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>API RESTful</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Soporte 24/7</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <a href="<?= base_url('contratar') ?>" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                        Contratar Ahora <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    <a href="<?= base_url('servicios') ?>" class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>