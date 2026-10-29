/* Employee-first attendance import. Mapping is persisted per branch/device ID. */
(() => {
    'use strict';
    const root = document.getElementById('attendance-dtr-page');
    if (!root || !document.getElementById('dtr-device-data')) return;
    const base = root.dataset.base, token = root.dataset.token;
    const devices = JSON.parse(document.getElementById('dtr-device-data').textContent);
    const $ = id => document.getElementById(id);
    const node = (tag, text, cls) => { const el = document.createElement(tag); if (text != null) el.textContent = text; if (cls) el.className = cls; return el; };
    const drafts = new Map(), confirmations = new Map();
    let batch = null, result = null, groups = [], selected = null, detailId = null, busy = false, activeCutoff = null, manualMappingOpen = false;
    const policies = [['review', 'Choose break treatment'], ['recorded', 'Recorded / manually entered break'], ['none', 'No break — explicitly confirmed']];
    const types = [['No', 'Regular / rest day'], ['Yes', 'Overtime']];
    const overrides = [['No', 'No — use employee shift'], ['Yes', 'Yes — disregard shift timing']];
    const message = (text, error = false) => { $('import-message').className = 'alert ' + (error ? 'alert-danger' : 'alert-success'); $('import-message').textContent = text; };
    const current = () => groups.find(g => g.id === selected);
    const cutoffValues = () => ({ cutoff_start: $('dtr-cutoff-start').value, cutoff_end: $('dtr-cutoff-end').value });
    const cutoffPending = () => !!activeCutoff && (cutoffValues().cutoff_start !== activeCutoff.cutoff_start || cutoffValues().cutoff_end !== activeCutoff.cutoff_end);
    const visibleRows = (group = current()) => group ? group.rows.filter(r => !activeCutoff || (r.date >= activeCutoff.cutoff_start && r.date <= activeCutoff.cutoff_end)) : [];
    const mappingGroups = () => groups.filter(group => visibleRows(group).length);
    const unmappedGroups = () => mappingGroups().filter(group => !group.employee);
    const person = id => result.employees.find(e => Number(e.id) === Number(id));
    const normalized = text => (text || '').replace(' ', 'T').slice(0, 16);
    const timeOf = text => normalized(text).slice(11, 16);
    const stampDate = text => normalized(text).slice(0, 10);
    const shortDate = date => new Date(date + 'T12:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    function draft(row) {
        if (!drafts.has(row.id)) drafts.set(row.id, { proposed: { ...row.proposed }, selected: false, dirty: false, conflict_action: 'keep', feedback: '', error: false });
        return drafts.get(row.id);
    }
    function dirty() { return [...drafts.values()].some(d => d.dirty || d.selected); }
    function invalidate() { confirmations.set(selected, false); $('dtr-employee-confirm').checked = false; }
    function cutoffInput() {
        const dates = cutoffValues();
        if (!dates.cutoff_start || !dates.cutoff_end) throw Error('Choose both cutoff start and end dates.');
        if (dates.cutoff_end < dates.cutoff_start) throw Error('Cutoff end must be on or after the start date.');
        if ((new Date(dates.cutoff_end + 'T00:00:00Z') - new Date(dates.cutoff_start + 'T00:00:00Z')) / 86400000 > 365) throw Error('Select a cutoff of up to 366 calendar days.');
        return dates;
    }
    function cutoffLabel() {
        if (!activeCutoff) return;
        const extra = result && (activeCutoff.cutoff_start < result.batch.period_start || activeCutoff.cutoff_end > result.batch.period_end)
            ? ' Part of this cutoff is outside the export period; dates without source punches stay blank.' : '';
        $('dtr-cutoff-state').textContent = cutoffPending() ? 'Cutoff dates changed. Apply cutoff before reviewing or saving.'
            : 'Applied cutoff: ' + activeCutoff.cutoff_start + ' to ' + activeCutoff.cutoff_end + ' (inclusive).' + extra;
    }
    ['dtr-cutoff-start', 'dtr-cutoff-end'].forEach(id => {
        $(id).onchange = () => {
            $('dtr-cutoff-end').min = $('dtr-cutoff-start').value;
            confirmations.clear(); drafts.forEach(d => { d.selected = false; });
            $('dtr-employee-confirm').checked = false; cutoffLabel();
            if (result) { detailId = null; $('dtr-review-modal').close(); renderGrid(); }
        };
    });
    $('dtr-apply-cutoff').onclick = async () => {
        if (busy) return;
        try {
            const dates = cutoffInput();
            if (!batch) { activeCutoff = dates; cutoffLabel(); return; }
            busy = true; $('dtr-apply-cutoff').disabled = true;
            await api(base + '/' + batch + '/cutoff', dates);
            confirmations.clear(); drafts.forEach(d => { d.selected = false; }); detailId = null;
            await load(batch, true); message('Cutoff applied. Review and save only the plotted dates. The full source report is retained.');
        } catch (e) { message(e.message, true); } finally { busy = false; $('dtr-apply-cutoff').disabled = false; updateSelection(); }
    };
    async function api(url, data = null) {
        const options = { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token }, credentials: 'same-origin' };
        if (data !== null) {
            options.method = 'POST';
            if (data instanceof FormData) options.body = data;
            else { options.headers['Content-Type'] = 'application/json'; options.body = JSON.stringify(data); }
        }
        const response = await fetch(url, options);
        let body; try { body = await response.json(); } catch { throw Error('Request failed. Reload the page and check your login.'); }
        if (!response.ok) throw Error(body.errors ? Object.values(body.errors).flat().join(' ') : (body.message || 'Request failed.'));
        return body;
    }
    function deviceOptions() {
        $('import-device').replaceChildren(new Option('Select device', ''));
        devices.filter(d => String(d.branch_idx) === $('import-branch').value).forEach(d => $('import-device').add(new Option(d.name, d.id)));
    }
    $('import-branch').onchange = deviceOptions;
    $('import-add-device').onclick = async () => {
        if (busy) return;
        try {
            if (!$('import-branch').value || !$('import-new-device').value.trim()) throw Error('Select a branch and enter a device name.');
            const device = await api(base + '/devices', { branch_idx: Number($('import-branch').value), name: $('import-new-device').value.trim() });
            if (!devices.some(d => Number(d.id) === Number(device.id))) devices.push(device);
            deviceOptions(); $('import-device').value = device.id; message('Device profile ready.');
        } catch (e) { message(e.message, true); }
    };
    function regroup() {
        const map = new Map();
        result.rows.forEach(row => {
            if (!map.has(row.external_id)) map.set(row.external_id, { id: row.external_id, name: row.external_name, employee: row.mapped_employee_idx, rows: [] });
            map.get(row.external_id).rows.push(row);
        });
        groups = [...map.values()];
        if (!groups.some(g => g.id === selected)) selected = unmappedGroups()[0]?.id || groups[0]?.id || null;
    }
    function updateFlow() {
        const relevantGroups = mappingGroups(), unmapped = unmappedGroups(), hasUnmapped = unmapped.length > 0;
        $('dtr-mapping-card').hidden = !hasUnmapped && !manualMappingOpen;
        $('dtr-logs-card').hidden = hasUnmapped;
        $('dtr-toggle-mapping').hidden = hasUnmapped || groups.length === 0;
        $('dtr-toggle-mapping').textContent = manualMappingOpen ? 'Hide mappings' : 'Review mappings';
        $('dtr-mapping-progress').textContent = hasUnmapped
            ? (relevantGroups.length - unmapped.length + ' of ' + relevantGroups.length + ' device employee IDs mapped. Map the remaining IDs to continue to the logs.')
            : 'All device employee IDs are mapped.';
    }
    async function load(id, preserve = false) {
        const data = await api(base + '/' + id + '/preview');
        const same = batch === id; result = data; batch = id;
        if (!preserve || !same) { drafts.clear(); confirmations.clear(); selected = null; detailId = null; manualMappingOpen = false; }
        activeCutoff = { cutoff_start: data.batch.cutoff_start || data.batch.period_start, cutoff_end: data.batch.cutoff_end || data.batch.period_end };
        $('dtr-cutoff-start').value = activeCutoff.cutoff_start; $('dtr-cutoff-end').value = activeCutoff.cutoff_end;
        $('dtr-cutoff-end').min = activeCutoff.cutoff_start;
        regroup(); $('import-review').hidden = false; cutoffLabel();
        $('import-source').href = base + '/' + id + '/source';
        $('import-summary').textContent = [data.batch.branch, data.batch.device, data.batch.filename, data.batch.period_start + ' to ' + data.batch.period_end].filter(Boolean).join(' · ');
        render();
    }
    $('import-history').onchange = async () => {
        if (busy || !$('import-history').value) return;
        if (dirty() && !window.confirm('Open another import and discard unsaved attendance edits?')) { $('import-history').value = batch || ''; return; }
        try { await load($('import-history').value); } catch (e) { message(e.message, true); }
    };
    $('import-upload').onsubmit = async event => {
        event.preventDefault(); if (busy) return;
        if (dirty() && !window.confirm('Upload another report and discard unsaved attendance edits?')) return;
        busy = true; event.submitter.disabled = true;
        try {
            const cutoff = cutoffInput();
            const form = new FormData(); form.append('device_id', $('import-device').value); form.append('file', $('import-file').files[0]);
            form.append('cutoff_start', cutoff.cutoff_start); form.append('cutoff_end', cutoff.cutoff_end);
            const uploaded = await api(base + '/upload', form); await load(uploaded.id);
            $('import-history').add(new Option(result.batch.filename, uploaded.id), 1); $('import-history').value = uploaded.id;
            const missingMappings = unmappedGroups().length;
            message(!groups.length
                ? 'Upload complete, but no employee IDs were found in the selected cutoff. Check the export and device profile.'
                : (missingMappings
                    ? 'Upload complete. Map the ' + missingMappings + ' remaining device employee ID(s) to continue to the logs.'
                    : 'Upload complete. All employee IDs were already mapped; the logs are ready to view.'));
        } catch (e) { message(e.message, true); } finally { busy = false; event.submitter.disabled = false; updateSelection(); }
    };
    function selectEmployee(id) { if (busy) return; $('dtr-review-modal').close(); selected = id; detailId = null; render(); }
    $('dtr-export-employee').onchange = () => selectEmployee($('dtr-export-employee').value);
    $('dtr-prev-employee').onclick = () => { const index = groups.findIndex(g => g.id === selected); if (index > 0) selectEmployee(groups[index - 1].id); };
    $('dtr-next-employee').onclick = () => { const index = groups.findIndex(g => g.id === selected); if (index < groups.length - 1) selectEmployee(groups[index + 1].id); };
    $('dtr-save-mapping').onclick = async () => {
        const group = current(); if (!group || busy) return;
        const employee = Number($('dtr-payroll-employee').value);
        if (!employee) { message('Choose an enrolled payroll employee.', true); return; }
        busy = true; $('dtr-save-mapping').disabled = true;
        try {
            await api(base + '/' + batch + '/mapping', { external_id: group.id, employee_idx: employee });
            confirmations.set(group.id, false);
            // Retain edited times, but refresh the employee and conflict snapshot for all dates.
            await load(batch, true);
            const nextUnmapped = unmappedGroups()[0];
            if (nextUnmapped) selected = nextUnmapped.id;
            else manualMappingOpen = false;
            render();
            message(nextUnmapped
                ? 'Mapping saved for device employee ID ' + group.id + '. Choose the next unmapped ID.'
                : 'All device IDs are now mapped. Opening the attendance logs; saved mappings will be reused for future uploads.');
        } catch (e) { message(e.message, true); } finally { busy = false; $('dtr-save-mapping').disabled = false; updateSelection(); }
    };
    $('dtr-payroll-employee').onchange = () => { invalidate(); $('dtr-mapping-state').textContent = 'Mapping selection changed. Click Remember mapping before saving DTR.'; updateSelection(); };
    $('dtr-toggle-mapping').onclick = () => { manualMappingOpen = !manualMappingOpen; updateFlow(); };
    function mappingPending() { const group = current(); return !group || Number($('dtr-payroll-employee').value) !== Number(group.employee); }
    function render() {
        const group = current(); updateFlow();
        if (!group) { $('dtr-send-mapped').disabled = true; return; }
        const index = groups.indexOf(group), employee = person(group.employee);
        $('dtr-export-employee').replaceChildren(); groups.forEach(g => $('dtr-export-employee').add(new Option(g.id + ' — ' + (g.name || 'Unnamed') + (g.employee ? ' · mapped' : ' · unmapped'), g.id)));
        $('dtr-export-employee').value = group.id;
        $('dtr-prev-employee').disabled = index === 0; $('dtr-next-employee').disabled = index === groups.length - 1;
        $('dtr-device-id').textContent = group.id; $('dtr-export-name').textContent = group.name;
        $('dtr-payroll-employee').replaceChildren(new Option('Select enrolled employee', ''));
        result.employees.forEach(e => $('dtr-payroll-employee').add(new Option(e.number + ' — ' + e.name, e.id)));
        $('dtr-payroll-employee').value = group.employee || '';
        $('dtr-save-mapping').textContent = employee ? 'Update mapping' : 'Remember mapping';
        $('dtr-mapping-state').className = 'mt-2 mb-0 small ' + (employee ? 'text-success' : 'text-warning');
        $('dtr-mapping-state').textContent = employee ? 'Remembered: ' + group.id + ' → ' + employee.number + ' — ' + employee.name + '. Applies to all pending dates and future uploads from this device profile.' : 'Choose the payroll employee once. This export name alone does not confirm the match.';
        $('dtr-employee-heading').textContent = (employee ? employee.name : group.name || group.id) + ' · DTR';
        $('dtr-employee-count').textContent = 'Employee ' + (index + 1) + ' of ' + groups.length + ' · ' + visibleRows(group).filter(r => ['saved','employee_pending','employee_submitted'].includes(r.status)).length + ' of ' + visibleRows(group).length + ' intervals saved/queued';
        $('dtr-employee-confirm').checked = !!confirmations.get(group.id);
        $('dtr-save-result').textContent = '';
        renderGrid(); if (detailId !== null) showDetail(detailId); else { $('dtr-interval-detail').hidden = true; $('dtr-review-modal').close(); }
    }
    function dayList() {
        const list = [], start = activeCutoff.cutoff_start, end = activeCutoff.cutoff_end;
        let d = new Date(start + 'T12:00:00'), last = new Date(end + 'T12:00:00');
        if ((last - d) / 86400000 > 365) return [...new Set(visibleRows().map(r => r.date))].sort();
        for (let n = 0; d <= last && n < 366; n++, d.setUTCDate(d.getUTCDate() + 1)) list.push(d.toISOString().slice(0, 10));
        return list;
    }
    function ready(row) {
        const p = draft(row).proposed;
        return !!(p.log_in && p.log_out && (p.break_policy === 'none' || (p.break_policy === 'recorded' && p.breaktime_start && p.breaktime_end)));
    }
    function statusFor(row) {
        const p = draft(row).proposed;
        if (row.status === 'saved') return 'Saved';
        if (row.status === 'employee_pending') return 'Awaiting employee';
        if (row.status === 'employee_submitted') return 'Employee completed — approval needed';
        if (row.status === 'kept') return 'Existing attendance kept';
        if (row.status === 'skipped') return 'Skipped';
        if (!row.employee_idx) return 'Map employee';
        if (!p.log_in || !p.log_out) return 'Employee to complete missing punch';
        if (!ready(row)) return 'Review break';
        if (row.conflicts.length) return 'Existing logs';
        if (row.warnings.length) return 'Review shift';
        return 'Ready';
    }
    function change(row, key, value) {
        const state = draft(row); state.proposed[key] = value; state.dirty = true; state.feedback = ''; invalidate();
        if (key === 'break_policy' && value === 'none') { state.proposed.breaktime_start = null; state.proposed.breaktime_end = null; }
        renderGrid(); if (detailId === row.id) showDetail(row.id);
    }
    function timeCell(row, key, label) {
        const state = draft(row), cell = node('td'), input = node('input', null, 'form-control form-control-sm');
        input.type = 'time'; input.value = timeOf(state.proposed[key]); input.setAttribute('aria-label', label + ' ' + row.date + ' interval ' + row.id);
        input.disabled = ['saved','employee_submitted','kept'].includes(row.status) || (key.startsWith('breaktime') && state.proposed.break_policy === 'none');
        input.onchange = () => { const date = stampDate(state.proposed[key]) || row.date; change(row, key, input.value ? date + 'T' + input.value : null); };
        cell.append(input);
        if (state.proposed[key] && stampDate(state.proposed[key]) !== row.date) cell.append(node('span', shortDate(stampDate(state.proposed[key])) + ' · next day', 'dtr-next-day'));
        return cell;
    }
    function optionLabel(options, value) { return options.find(([optionValue]) => optionValue === value)?.[1] || 'Not selected'; }
    function classificationCell(row) {
        const cell = node('td', null, 'dtr-classification'), proposed = draft(row).proposed;
        const summary = [optionLabel(policies, proposed.break_policy), optionLabel(types, proposed.overtime_status), optionLabel(overrides, proposed.override_default_shift)].join(' · ');
        cell.append(node('span', summary, 'dtr-classification-summary'));
        return cell;
    }
    ['dtr-review-close', 'dtr-review-done'].forEach(id => { $(id).onclick = () => $('dtr-review-modal').close(); });
    $('dtr-review-modal').addEventListener('close', () => { detailId = null; renderGrid(); });
    function renderGrid() {
        const group = current(), body = $('dtr-grid-body'); body.replaceChildren();
        const byDate = new Map(); visibleRows(group).forEach(r => { if (!byDate.has(r.date)) byDate.set(r.date, []); byDate.get(r.date).push(r); });
        dayList().forEach(date => {
            const rows = byDate.get(date) || [];
            if (!rows.length) {
                const tr = node('tr'); tr.append(node('td'), node('td', shortDate(date), 'dtr-date'));
                const empty = node('td', 'No punches in this file — no absence or hours assumed.', 'text-muted'); empty.colSpan = 7; tr.append(empty); body.append(tr); return;
            }
            rows.forEach(row => {
                const state = draft(row), tr = node('tr'); tr.dataset.row = row.id; if (state.selected) tr.className = 'dtr-row-selected';
                const selectCell = node('td'), check = node('input', null, 'form-check-input'); check.type = 'checkbox'; check.checked = state.selected;
                check.setAttribute('aria-label', 'Include ' + date + ' interval ' + row.id); check.disabled = ['saved','employee_submitted','kept'].includes(row.status) || !row.employee_idx;
                check.onchange = () => { state.selected = check.checked; invalidate(); tr.classList.toggle('dtr-row-selected', check.checked); updateSelection(); }; selectCell.append(check); tr.append(selectCell);
                const dateCell = node('td', shortDate(date), 'dtr-date');
                dateCell.append(node('span', new Date(date + 'T12:00:00').toLocaleDateString('en-US', { weekday: 'short' }) + ' · ' + (state.proposed.overtime_status === 'Yes' ? 'Overtime' : 'Regular / rest day'), 'dtr-day-note'));
                tr.append(dateCell, timeCell(row, 'log_in', 'Time in'), timeCell(row, 'breaktime_start', 'Break out'), timeCell(row, 'breaktime_end', 'Break in'), timeCell(row, 'log_out', 'Time out'));
                const status = node('td'); status.append(node('span', statusFor(row), row.status === 'saved' ? 'text-success' : 'text-warning'));
                if (state.feedback) status.append(node('p', state.feedback, 'dtr-row-message mb-0 ' + (state.error ? 'text-danger' : 'text-success'))); tr.append(status);
                tr.append(classificationCell(row));
                const action = node('td'), details = node('button', 'Review', 'btn btn-sm btn-outline-secondary'); details.type = 'button'; details.setAttribute('aria-label', 'Review details ' + date + ' interval ' + row.id); details.onclick = () => { detailId = row.id; showDetail(row.id); }; action.append(details); tr.append(action); body.append(tr);
            });
        });
        updateSelection();
    }
    function updateSelection() {
        const group = current(); if (!group) return;
        const eligible = visibleRows(group).filter(r => !['saved','employee_submitted','kept','skipped'].includes(r.status) && r.employee_idx && ready(r));
        const selectedRows = visibleRows(group).filter(r => !['saved','employee_submitted','kept','skipped'].includes(r.status) && draft(r).selected);
        $('dtr-selection-count').textContent = new Set(selectedRows.map(r => r.date)).size + ' dates / ' + selectedRows.length + ' intervals selected · cutoff ' + activeCutoff.cutoff_start + ' to ' + activeCutoff.cutoff_end;
        $('dtr-review-complete').checked = eligible.length > 0 && eligible.every(r => draft(r).selected);
        $('dtr-review-complete').disabled = cutoffPending() || !group.employee;
        $('dtr-employee-confirm').disabled = cutoffPending() || !group.employee || mappingPending() || !selectedRows.length;
        $('dtr-save-selected').disabled = busy || cutoffPending() || !group.employee || mappingPending() || !selectedRows.length || !confirmations.get(group.id);
        const hasMappedRows = groups.some(g => visibleRows(g).some(r => r.employee_idx && ['pending','employee_pending'].includes(r.status)));
        $('dtr-send-mapped').disabled = busy || cutoffPending() || !hasMappedRows;
    }
    $('dtr-review-complete').onchange = () => {
        const value = $('dtr-review-complete').checked;
        visibleRows().forEach(row => { if (!['saved','employee_submitted','kept','skipped'].includes(row.status) && row.employee_idx && ready(row)) draft(row).selected = value; });
        invalidate(); renderGrid();
    };
    $('dtr-employee-confirm').onchange = () => { confirmations.set(selected, $('dtr-employee-confirm').checked); updateSelection(); };
    function fullDateField(container, row, key, text) {
        const group = node('div', null, 'col-md-3'), label = node('label', text, 'form-label'), input = node('input', null, 'form-control');
        input.id = 'dtr-full-' + row.id + '-' + key; label.htmlFor = input.id; input.type = 'datetime-local'; input.value = normalized(draft(row).proposed[key]); input.disabled = ['saved','employee_submitted','kept'].includes(row.status);
        if (key.startsWith('breaktime') && draft(row).proposed.break_policy === 'none') input.disabled = true;
        input.onchange = () => change(row, key, input.value || null); group.append(label, input); container.append(group);
    }
    function showDetail(id) {
        const row = visibleRows().find(r => r.id === id); if (!row) { $('dtr-review-modal').close(); return; }
        detailId = id;
        const panel = $('dtr-interval-detail'), state = draft(row); panel.replaceChildren(); panel.hidden = false;
        panel.append(node('h5', row.date + ' · device employee ' + row.external_id));
        panel.append(node('p', 'Raw punches: ' + row.raw.punches.join(', ') + ' · Source: ' + row.raw.sheet + ', row ' + row.raw.row));
        if (row.raw.slots) panel.append(node('p', 'Device slots [In 1, Out 1, In 2, Out 2, OT In, OT Out]: ' + row.raw.slots.map(s => s || '—').join(' / '), 'small'));
        if (row.warnings.length) panel.append(node('p', row.warnings.join(' '), 'text-warning'));
        if (row.status === 'employee_submitted') {
            panel.append(node('p', 'Employee submitted this DTR for payroll approval' + (row.employee_submitted_at ? ' on ' + row.employee_submitted_at : '') + '.', 'text-success'));
            if (row.employee_note) panel.append(node('p', 'Employee note: ' + row.employee_note, 'dtr-conflict'));
        }
        panel.append(node('p', 'Use explicit dates here for overnight punches. Review break treatment, attendance type, and shift override for this interval.', 'small'));
        const settings = node('div', null, 'dtr-review-settings mt-3'), settingGrid = node('div', null, 'dtr-review-settings-grid');
        settings.append(node('h6', 'Attendance settings'), settingGrid);
        [['break_policy', 'Break treatment', policies], ['overtime_status', 'Attendance type', types], ['override_default_shift', 'Override default shift', overrides]].forEach(([key, labelText, options]) => {
            const group = node('div'), label = node('label', labelText, 'form-label');
            const select = node('select', null, 'form-select'); select.id = 'dtr-review-' + row.id + '-' + key; label.htmlFor = select.id;
            options.forEach(([value, text]) => select.add(new Option(text, value))); select.value = state.proposed[key]; select.disabled = ['saved','employee_submitted','kept'].includes(row.status);
            select.onchange = () => {
                state.proposed[key] = select.value; state.dirty = true; state.feedback = ''; invalidate();
                if (key === 'break_policy') {
                    if (select.value === 'none') { state.proposed.breaktime_start = null; state.proposed.breaktime_end = null; }
                    ['breaktime_start', 'breaktime_end'].forEach(dateKey => {
                        const input = $('dtr-full-' + row.id + '-' + dateKey);
                        if (input) { input.disabled = ['saved','employee_submitted','kept'].includes(row.status) || select.value === 'none'; if (select.value === 'none') input.value = ''; }
                    });
                }
                updateSelection();
            };
            group.append(label, select); settingGrid.append(group);
        });
        panel.append(settings);
        const dates = node('div', null, 'row g-2');
        [['log_in', 'Full time-in date'], ['log_out', 'Full time-out date'], ['breaktime_start', 'Full break-out date'], ['breaktime_end', 'Full break-in date']].forEach(([key, label]) => fullDateField(dates, row, key, label)); panel.append(dates);
        if (row.conflicts.length) {
            const box = node('div', null, 'dtr-conflict mt-3'); box.append(node('strong', 'Existing attendance on this date'));
            row.conflicts.forEach(c => box.append(node('p', c.type + ': ' + c.in + ' → ' + c.out + '; break ' + c.break_start + ' → ' + c.break_end, 'mb-1')));
            const label = node('label', 'For matching attendance type', 'form-label mt-2'), select = node('select', null, 'form-select');
            select.id = 'dtr-conflict-' + row.id; label.htmlFor = select.id; select.add(new Option('Keep existing attendance', 'keep')); select.add(new Option('Replace after review', 'replace')); select.value = state.conflict_action; select.disabled = ['saved','employee_submitted','kept'].includes(row.status);
            select.onchange = () => { state.conflict_action = select.value; state.dirty = true; invalidate(); updateSelection(); }; box.append(label, select); panel.append(box);
        }
        if (row.status === 'saved') panel.append(node('p', 'Saved to payroll employee ' + (person(row.employee_idx)?.name || row.employee_idx) + '. Use Employee Logs for later corrections.', 'mt-3 text-success'));
        else if (row.status === 'employee_submitted') {
            const approve = node('button', 'Approve & finalize attendance', 'btn btn-primary mt-3'); approve.type = 'button';
            approve.onclick = async () => {
                if (busy) return; approve.disabled = true;
                try {
                    const response = await api(base + '/' + batch + '/rows/' + row.id + '/approve', {});
                    row.status = response.status; row.saved_log_id = response.log_id || null; row.version = response.version || row.version + 1;
                    state.selected = false; state.dirty = false; state.feedback = response.status === 'saved' ? 'Approved and saved to Employee Logs.' : 'Existing attendance kept.';
                    const activeEmployee = selected;
                    detailId = null; $('dtr-review-modal').close(); await load(batch);
                    if (groups.some(group => group.id === activeEmployee)) selectEmployee(activeEmployee);
                } catch (e) { message(e.message, true); approve.disabled = false; }
            }; panel.append(approve);
        } else {
            const skipped = row.status === 'skipped';
            const skip = node('button', skipped ? 'Restore this interval' : 'Skip this interval', 'btn btn-outline-secondary mt-3'); skip.type = 'button';
            skip.onclick = async () => {
                if (busy) return; skip.disabled = true;
                try { const response = await api(base + '/' + batch + '/rows/' + row.id, { action: skipped ? 'restore' : 'skip', version: row.version }); row.status = response.status; row.version = response.version || row.version + 1; state.selected = false; state.dirty = false; detailId = null; $('dtr-review-modal').close(); invalidate(); render(); }
                catch (e) { message(e.message, true); } finally { skip.disabled = false; }
            }; panel.append(skip);
        }
        if (!$('dtr-review-modal').open) $('dtr-review-modal').showModal();
    }
    $('dtr-refresh').onclick = async () => {
        if (busy || !batch) return;
        if (dirty() && !window.confirm('Refresh this report and discard unsaved time edits and selections?')) return;
        try { const active = selected; await load(batch); if (groups.some(g => g.id === active)) selectEmployee(active); } catch (e) { message(e.message, true); }
    };
    function lockReview(locked) {
        $('import-review').querySelectorAll('input,select,button').forEach(control => {
            if (locked) { control.dataset.wasDisabled = String(control.disabled); control.disabled = true; }
            else { control.disabled = control.dataset.wasDisabled === 'true'; delete control.dataset.wasDisabled; }
        });
        $('import-upload').querySelectorAll('input,select,button').forEach(control => {
            if (locked) { control.dataset.wasDisabled = String(control.disabled); control.disabled = true; }
            else { control.disabled = control.dataset.wasDisabled === 'true'; delete control.dataset.wasDisabled; }
        });
        $('import-history').disabled = locked;
    }
    $('dtr-save-selected').onclick = async () => {
        const group = current(); if (busy || !group) return;
        if (cutoffPending() || !confirmations.get(group.id) || mappingPending()) { message('Apply the cutoff, confirm the employee mapping, and confirm your admin edits before saving.', true); return; }
        const rows = visibleRows(group).filter(r => !['saved','employee_submitted','kept'].includes(r.status) && draft(r).selected);
        if (!rows.length) return;
        busy = true; lockReview(true); let queued = 0, kept = 0, failed = 0, emailFailed = 0;
        try {
            for (const row of rows) {
                const state = draft(row);
                try {
                    const response = await api(base + '/' + batch + '/rows/' + row.id, { action: 'save', version: row.version, employee_idx: row.employee_idx, confirmed: true,
                        proposed: state.proposed, conflict_hash: row.conflict_hash, conflict_action: state.conflict_action });
                    state.error = false;
                    if (response.status === 'employee_pending') {
                        row.status = response.status; row.version = response.version || row.version + 1;
                        state.selected = false; state.dirty = false; state.feedback = response.email_sent === false ? 'Saved; email failed.' : (response.email_already_sent ? 'Admin edits saved; employee was already emailed.' : 'Saved and emailed to employee.'); queued++;
                        if (response.email_sent === false) emailFailed++;
                    } else { state.feedback = 'Existing attendance kept.'; state.selected = false; kept++; }
                } catch (e) { state.feedback = e.message; state.error = true; failed++; }
                $('dtr-save-result').textContent = queued + ' intervals saved/updated for employee completion · ' + kept + ' existing records kept · ' + failed + ' need review · ' + emailFailed + ' email failures';
            }
        } finally {
            confirmations.set(group.id, false); busy = false; lockReview(false); render();
            $('dtr-save-result').textContent = queued + ' intervals saved/updated for employee completion · ' + kept + ' existing records kept · ' + failed + ' need review · ' + emailFailed + ' email failures. Final attendance is posted only after payroll approval.';
        }
    };
    $('dtr-send-mapped').onclick = async () => {
        if (busy || !batch) return;
        if (cutoffPending()) { message('Apply the cutoff before saving mapped logs.', true); return; }
        if (!window.confirm('Save all mapped device logs in this cutoff and email each linked employee to complete only missing DTR details?')) return;
        const button = $('dtr-send-mapped'), activeEmployee = selected;
        busy = true; lockReview(true); button.textContent = 'Saving mapped logs…'; $('dtr-send-result').textContent = '';
        try {
            const response = await api(base + '/' + batch + '/queue-mapped', {});
            const activeBatch = batch;
            await load(activeBatch, true);
            if (groups.some(group => group.id === activeEmployee)) { selected = activeEmployee; render(); }
            const missingAccounts = response.missing_accounts || [], missingEmails = response.missing_emails || [], failures = response.email_failures || [];
            const details = [
                (response.queued || 0) + ' new intervals queued',
                (response.emails_sent || 0) + ' employee emails sent',
                (response.already_sent || 0) + ' employees already emailed',
                (response.unmapped || 0) + ' unmapped intervals',
                missingAccounts.length + ' employees missing a linked Employee account',
                missingEmails.length + ' employees missing a valid email address',
                failures.length + ' email failures'
            ].join(' · ');
            $('dtr-send-result').textContent = details
                + (missingAccounts.length ? '. Create/link Employee accounts for: ' + missingAccounts.join(', ') : '')
                + (missingEmails.length ? '. Add a valid email in Employee Information or the linked Employee account for: ' + missingEmails.join(', ') : '')
                + (failures.length ? '. Email failed for: ' + failures.join(', ') : '');
            $('dtr-send-result').className = 'mt-2 mb-0 ' + ((missingAccounts.length || missingEmails.length || failures.length || response.unmapped) ? 'text-warning' : 'text-success');
        } catch (e) { message(e.message, true); }
        finally { busy = false; lockReview(false); button.textContent = 'Save mapped logs & email employees'; render(); }
    };
    window.addEventListener('beforeunload', event => { if (dirty()) { event.preventDefault(); event.returnValue = ''; } });
})();
