
const FormValidator = (() => {

    const DANGER_PATTERNS = [
        /<script[\s\S]*>/i,
        /javascript\s*:/i,
        /on\w+\s*=/i,
        /data\s*:\s*text\s*\/\s*(html|javascript)/i,
        /vbscript\s*:/i,
        /expression\s*\(/i,
        /\bunion\b[\s\S]+\bselect\b/i,
        /\bdrop\b[\s\S]+\b(table|database|schema)\b/i,
        /\binsert\b[\s\S]+\binto\b/i,
        /\bdelete\b[\s\S]+\bfrom\b/i,
        /\bupdate\b[\s\S]+\bset\b/i,
        /\bexec\s*\(/i,
        /\bxp_cmdshell\b/i,
        /;\s*(drop|delete|insert|update|alter|truncate)\b/i,
        /'\s*or\s+'?[\d']/i,
        /'\s*;\s*/,
        /--\s/,
        /\/\*[\s\S]*\*\//,
    ];

    function hasDangerous(value) {
        const v = String(value || '');
        return DANGER_PATTERNS.some(p => p.test(v));
    }

    function shake(el) {
        if (!el) return;
        el.classList.remove('input-shake');
        void el.offsetWidth; // reflow para reiniciar animación
        el.classList.add('input-shake');
        el.addEventListener('animationend', () => el.classList.remove('input-shake'), { once: true });
    }

    function setError(fieldId, message) {
        const el    = document.getElementById(fieldId);
        const errEl = document.getElementById('err-' + fieldId);
        if (el) {
            el.classList.add('is-invalid');
            shake(el.closest('.input-group') || el);
        }
        if (errEl) errEl.textContent = message;
    }

    function clearErrors(formOrId) {
        const form = typeof formOrId === 'string'
            ? document.getElementById(formOrId)
            : formOrId;
        if (!form) return;
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.form-error').forEach(el => { el.textContent = ''; });
    }

    /**
     * Validar un conjunto de campos.
     *
     * @param {Array<{id, label, rules}>} fields
     *   Cada regla puede ser:
     *     - 'required'        → obligatorio
     *     - 'email'           → formato email
     *     - 'noHtml'          → sin etiquetas HTML
     *     - { min: N }        → longitud mínima
     *     - { max: N }        → longitud máxima
     *     - { match: 'otherId', msg? }  → debe coincidir con otro campo
     *     - { fn: (v) => true | 'msg' }  → función personalizada
     *
     * @param {string|Element} [formOrId] - limpiar errores antes de validar
     * @returns {boolean}
     */
    function validate(fields, formOrId) {
        if (formOrId) clearErrors(formOrId);
        let allValid = true;

        for (const field of fields) {
            const el = document.getElementById(field.id);
            if (!el) continue;

            const value = el.type === 'checkbox' ? (el.checked ? '1' : '') : el.value;

            if (hasDangerous(value)) {
                setError(field.id, 'Contenido no permitido detectado');
                allValid = false;
                continue;
            }

            let fieldValid = true;

            for (const rule of (field.rules || [])) {
                let result = true;

                if (rule === 'required') {
                    result = value.trim() !== ''
                        ? true
                        : `${field.label || 'Este campo'} es obligatorio`;

                } else if (rule === 'email') {
                    if (value.trim() !== '') {
                        result = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value.trim())
                            ? true
                            : 'Formato de correo inválido';
                    }

                } else if (rule === 'noHtml') {
                    result = !/<[^>]+>/.test(value) ? true : 'No se permiten etiquetas HTML';

                } else if (typeof rule === 'object') {
                    if (rule.min !== undefined) {
                        result = value.trim().length >= rule.min
                            ? true
                            : (rule.msg || `Mínimo ${rule.min} caracteres`);

                    } else if (rule.max !== undefined) {
                        result = value.trim().length <= rule.max
                            ? true
                            : (rule.msg || `Máximo ${rule.max} caracteres`);

                    } else if (rule.match !== undefined) {
                        const other = document.getElementById(rule.match);
                        result = (other && other.value === value)
                            ? true
                            : (rule.msg || 'Los valores no coinciden');

                    } else if (typeof rule.fn === 'function') {
                        result = rule.fn(value);
                    }
                }

                if (result !== true) {
                    setError(field.id, typeof result === 'string' ? result : `${field.label || 'Campo'} inválido`);
                    allValid = false;
                    fieldValid = false;
                    break;
                }
            }

            if (fieldValid && el.value !== '') {
                el.classList.remove('is-invalid');
            }
        }

        return allValid;
    }

    function btnLoad(btn) {
        if (!btn) return;
        btn.dataset.origHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Procesando...';
        btn.disabled = true;
        btn.classList.add('btn-loading');
    }

    function btnDone(btn) {
        if (!btn) return;
        if (btn.dataset.origHtml) btn.innerHTML = btn.dataset.origHtml;
        btn.disabled = false;
        btn.classList.remove('btn-loading');
    }

    function staggerRows(tbody) {
        if (!tbody) return;
        tbody.querySelectorAll('tr').forEach((tr, i) => {
            tr.classList.add('tr-anim');
            tr.style.animationDelay = `${i * 0.045}s`;
        });
    }

    return { validate, clearErrors, setError, shake, hasDangerous, btnLoad, btnDone, staggerRows };
})();
