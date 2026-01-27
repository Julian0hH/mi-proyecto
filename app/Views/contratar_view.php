<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row g-4 animate-fade-in">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <h5 class="fw-bold m-0">Finalizar Contratación</h5>
            </div>
            <div class="card-body p-4">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">NOMBRE DE EMPRESA</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">NIT / RFC</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold">DIRECCIÓN DE FACTURACIÓN</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">MÉTODO DE PAGO</label>
                            <select class="form-select">
                                <option>Tarjeta de Crédito</option>
                                <option>PayPal</option>
                                <option>Transferencia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">CICLO</label>
                            <select class="form-select">
                                <option>Mensual</option>
                                <option>Anual (-20%)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-transparent p-4 d-flex justify-content-between align-items-center">
                <a href="<?= base_url('detalles') ?>" class="text-decoration-none text-muted">
                    <i class="bi bi-arrow-left me-1"></i> Cancelar
                </a>
                <button class="btn btn-success px-5 rounded-pill fw-bold">
                    Pagar y Activar
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Resumen del Pedido</h5>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Desarrollo Web Avanzado</span>
                    <span class="fw-bold">$499.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 opacity-75">
                    <span>Configuración Inicial</span>
                    <span>$50.00</span>
                </div>
                <div class="d-flex justify-content-between mb-4 opacity-75">
                    <span>Impuestos (16%)</span>
                    <span>$87.84</span>
                </div>
                
                <hr class="border-white opacity-25">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5">Total a Pagar</span>
                    <span class="fs-2 fw-bold">$636.84</span>
                </div>
                
                <div class="alert alert-white bg-white bg-opacity-10 border-0 text-white small">
                    <i class="bi bi-lock-fill me-1"></i> Transacción segura SSL de 256 bits.
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>