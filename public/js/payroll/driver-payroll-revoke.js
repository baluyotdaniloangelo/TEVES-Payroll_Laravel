(function () {
    'use strict';
    const modalElement = document.getElementById('driver-payroll-revoke-modal');
    const form = document.getElementById('driver-payroll-revoke-form');
    if (!modalElement || !form) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const error = document.getElementById('driver-payroll-revoke-error');

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-revoke-driver-payroll');
        if (!button) return;
        form.reset();
        form.action = button.dataset.url;
        document.getElementById('driver-payroll-revoke-number').textContent = button.dataset.payroll || '';
        error.classList.add('d-none');
        error.textContent = '';
        modal.show();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const submit = form.querySelector('[type="submit"]');
        submit.disabled = true;
        error.classList.add('d-none');
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form)
            });
            const result = await response.json();
            if (!response.ok) {
                const firstError = Object.values(result.errors || {})[0];
                throw new Error((Array.isArray(firstError) && firstError[0]) || result.message || 'Hindi ma-revoke ang payroll.');
            }
            window.location.assign(result.redirect);
        } catch (failure) {
            error.textContent = failure.message || 'Hindi ma-revoke ang payroll.';
            error.classList.remove('d-none');
        } finally {
            submit.disabled = false;
        }
    });
})();
