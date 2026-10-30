document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-my-dtr]');
    if (!page) return;

    const tabs = [...page.querySelectorAll('[data-dtr-tab]')];
    const panels = [...page.querySelectorAll('[data-dtr-panel]')];

    function selectDate(id, focus = false) {
        tabs.forEach(tab => {
            const selected = tab.dataset.dtrTab === id;
            tab.classList.toggle('is-active', selected);
            tab.setAttribute('aria-selected', String(selected));
            tab.tabIndex = selected ? 0 : -1;
            if (selected && focus) tab.focus();
        });
        panels.forEach(panel => {
            const selected = panel.dataset.dtrPanel === id;
            panel.hidden = !selected;
            panel.classList.toggle('is-active', selected);
        });
    }

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => selectDate(tab.dataset.dtrTab));
        tab.addEventListener('keydown', event => {
            if (!['ArrowRight', 'ArrowLeft', 'ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            const next = event.key === 'Home' ? 0 : event.key === 'End' ? tabs.length - 1 :
                (index + (['ArrowRight', 'ArrowDown'].includes(event.key) ? 1 : -1) + tabs.length) % tabs.length;
            selectDate(tabs[next].dataset.dtrTab, true);
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
            if (hint) hint.textContent = recorded ? 'Enter only the missing break times.' :
                'Select Recorded break above to enter break times. Choose No break if you did not take a recorded break.';
            breakFields.querySelectorAll('[data-break-input]').forEach(input => {
                input.disabled = !recorded;
                input.required = recorded && input.hasAttribute('data-break-time');
            });
        };
        form.querySelectorAll('input[name="proposed[break_policy]"]').forEach(input =>
            input.addEventListener('change', update));
        update();
    });

    const selected = tabs.find(tab => tab.classList.contains('is-active'));
    if (selected) selectDate(selected.dataset.dtrTab);
});
