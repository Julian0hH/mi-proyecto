/**
 * Toast & Confirm Dialog System
 * Portfolio Pro - Sistema de notificaciones globales
 */

// ============================================================
// TOAST NOTIFICATIONS
// ============================================================
const Toast = (() => {
    const container = () => document.getElementById('toast-container');

    const icons = {
        success: 'bi-check-circle-fill',
        error:   'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info:    'bi-info-circle-fill',
    };

    const bgClasses = {
        success: 'toast-success',
        error:   'toast-error',
        warning: 'toast-warning',
        info:    'toast-info',
    };

    function show(message, type = 'info', duration = 4000) {
        const cont = container();
        if (!cont) return;

        const id   = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2, 7);
        const icon = icons[type]    || icons.info;
        const cls  = bgClasses[type] || bgClasses.info;

        const el = document.createElement('div');
        el.id        = id;
        el.className = `toast toast-custom ${cls} show align-items-center border-0 mb-2`;
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.innerHTML = `
            <div class="d-flex align-items-center gap-2 p-3">
                <i class="bi ${icon} fs-5 toast-icon"></i>
                <div class="toast-body flex-grow-1 p-0">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-2" onclick="document.getElementById('${id}').remove()"></button>
            </div>
            <div class="toast-progress-bar"></div>`;

        cont.appendChild(el);

        // Animación de entrada
        requestAnimationFrame(() => el.classList.add('toast-enter'));

        // Auto-dismiss
        const progressBar = el.querySelector('.toast-progress-bar');
        if (progressBar) {
            progressBar.style.animationDuration = duration + 'ms';
            progressBar.classList.add('toast-progress-animate');
        }

        const timer = setTimeout(() => dismiss(el), duration);
        el.querySelector('.btn-close').addEventListener('click', () => clearTimeout(timer));
    }

    function dismiss(el) {
        if (!el || !el.parentNode) return;
        el.classList.add('toast-exit');
        el.addEventListener('animationend', () => el.remove(), { once: true });
        setTimeout(() => el.remove(), 500);
    }

    return {
        success: (msg, dur)  => show(msg, 'success', dur),
        error:   (msg, dur)  => show(msg, 'error',   dur || 5000),
        warning: (msg, dur)  => show(msg, 'warning', dur),
        info:    (msg, dur)  => show(msg, 'info',    dur),
        show,
    };
})();

// ============================================================
// CONFIRM DIALOG (Reemplaza window.confirm)
// ============================================================
const ConfirmDialog = (() => {
    let modalEl = null;

    function ensureModal() {
        if (document.getElementById('confirm-modal-global')) return;
        const div = document.createElement('div');
        div.innerHTML = `
            <div class="modal fade" id="confirm-modal-global" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                            </div>
                            <div id="confirm-msg" class="mb-4">¿Estás seguro?</div>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-outline-secondary" id="confirm-cancel">Cancelar</button>
                                <button class="btn btn-danger"            id="confirm-ok">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(div.firstElementChild);
        modalEl = new bootstrap.Modal(document.getElementById('confirm-modal-global'));
    }

    function show(message, onConfirm, options = {}) {
        ensureModal();
        const el = document.getElementById('confirm-modal-global');
        el.querySelector('#confirm-msg').innerHTML = message;

        const okBtn  = el.querySelector('#confirm-ok');
        okBtn.textContent  = options.confirmLabel || 'Confirmar';
        okBtn.className    = `btn btn-${options.confirmClass || 'danger'}`;

        const clone = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(clone, okBtn);

        clone.addEventListener('click', () => {
            modalEl.hide();
            if (typeof onConfirm === 'function') onConfirm();
        });

        el.querySelector('#confirm-cancel').addEventListener('click', () => modalEl.hide(), { once: true });
        modalEl.show();
    }

    return { show };
})();

// ============================================================
// FLASH MESSAGES: Convertir sesión PHP a toasts
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Lee data-toast attributes si existen
    document.querySelectorAll('[data-toast]').forEach(el => {
        const type = el.dataset.toast;
        const msg  = el.dataset.msg;
        if (type && msg) {
            setTimeout(() => Toast[type]?.(msg), 200);
            el.remove();
        }
    });
});
