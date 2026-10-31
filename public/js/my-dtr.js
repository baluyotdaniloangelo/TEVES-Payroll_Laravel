document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-my-dtr]');
    if (!page) return;

    page.querySelectorAll('[data-dtr-open]').forEach(button => {
        button.addEventListener('click', () => {
            const dialog = document.getElementById('dtr-dialog-' + button.dataset.dtrOpen);
            if (dialog && !dialog.open) dialog.showModal();
        });
    });
    page.querySelectorAll('.dtr-close-review').forEach(button => {
        button.addEventListener('click', () => button.closest('dialog')?.close());
    });
    page.querySelectorAll('.dtr-review-dialog').forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        });
    });

    page.querySelectorAll('[data-dtr-form]').forEach(form => {
        const breakFields = form.querySelector('[data-break-fields]');
        if (!breakFields) return;
        const update = () => {
            const policy = form.querySelector('input[name="proposed[break_policy]"]:checked')?.value ||
                form.querySelector('input[type="hidden"][name="proposed[break_policy]"]')?.value;
            const recorded = policy === 'recorded';
            breakFields.classList.toggle('is-inactive', !recorded);
            const hint = breakFields.querySelector('[data-break-hint]');
            const correction = form.dataset.dtrCorrection === '1';
            if (hint) hint.textContent = recorded
                ? (correction ? 'Check or correct the recorded break times.' : 'Enter only the missing break times.')
                : 'Select Recorded break above to enter break times. Choose No break if you did not take a recorded break.';
            breakFields.querySelectorAll('[data-break-input]').forEach(input => {
                input.disabled = !recorded;
                input.required = recorded && input.hasAttribute('data-break-time');
            });
        };
        form.querySelectorAll('input[name="proposed[break_policy]"]').forEach(input =>
            input.addEventListener('change', update));
        update();
    });

    if (page.dataset.dtrOldRow) {
        const dialog = document.getElementById('dtr-dialog-' + page.dataset.dtrOldRow);
        if (dialog) dialog.showModal();
    }
});
