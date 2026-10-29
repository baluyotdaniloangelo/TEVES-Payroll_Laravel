(function ($, config) {
    'use strict';

    const form = document.getElementById('DriversLogsForm');
    if (!form || !config) return;

    const modalElement = document.getElementById('drivers_logs_details_modal');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('DriversLogDeleteModal'));
    const branchSelect = $('#drivers_branch_idx');
    const driverSelect = $('#drivers_idx');
    const feedback = $('#drivers-logs-feedback');
    let pendingDeleteId = null;
    let table;
    let comparisonTable;

    function showFeedback(type, message) {
        feedback.removeClass('d-none alert-success alert-danger alert-warning')
            .addClass('alert-' + type).text(message);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function clearErrors() {
        $('#drivers_logs_form_summary').addClass('d-none').empty();
        $(form).find('.is-invalid').removeClass('is-invalid');
        $(form).find('[data-error-for]').empty();
    }

    function showErrors(errors, fallbackMessage) {
        clearErrors();
        const messages = [];
        Object.keys(errors || {}).forEach(function (field) {
            const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            const input = $(form).find('[name="' + field + '"]');
            input.addClass('is-invalid');
            $(form).find('[data-error-for="' + field + '"]').text(message);
            messages.push(message);
        });
        if (!messages.length && fallbackMessage) messages.push(fallbackMessage);
        $('#drivers_logs_form_summary').removeClass('d-none').text(messages.join(' '));
    }

    function clearForm() {
        form.reset();
        form.classList.remove('was-validated');
        clearErrors();
        $('#drivers_logs_id').val('0');
        $('#modal_title_action_drivers_logs').text('Add driver trip');
        $('#submit_drivers_logs_details .button-label').text('Save trip');
        $('#travel_date').val(config.today);
        driverSelect.html('<option value="">Select a branch first</option>').prop('disabled', true);
        $(form).find('input[type="number"]').val('');
    }

    function loadBranches(selectedId) {
        branchSelect.html('<option value="">Loading branches…</option>').prop('disabled', true);
        return $.ajax({ url: config.branches, method: 'POST', dataType: 'json', data: { _token: config.csrfToken } })
            .done(function (branches) {
                branchSelect.html('<option value="">Select branch</option>');
                (branches || []).forEach(function (branch) {
                    $('<option>').val(branch.branch_id).text(branch.branch_code + ' — ' + branch.branch_name).appendTo(branchSelect);
                });
                branchSelect.prop('disabled', false);
                if (selectedId) branchSelect.val(String(selectedId));
                if (!branches || !branches.length) showFeedback('warning', 'Walang branch na puwedeng piliin.');
            })
            .fail(function () {
                branchSelect.html('<option value="">Unable to load branches</option>');
                showFeedback('danger', 'Hindi ma-load ang branch list. I-refresh ang page at subukan ulit.');
            });
    }

    function loadDrivers(branchId, selectedId) {
        driverSelect.html('<option value="">Loading drivers…</option>').prop('disabled', true);
        if (!branchId) {
            driverSelect.html('<option value="">Select a branch first</option>');
            return $.Deferred().resolve().promise();
        }
        return $.ajax({ url: config.drivers, method: 'POST', dataType: 'json', data: { branchID: branchId, _token: config.csrfToken } })
            .done(function (drivers) {
                driverSelect.html('<option value="">Select driver</option>');
                (drivers || []).forEach(function (driver) {
                    const name = (driver.employee_full_name || [driver.employee_last_name, driver.employee_first_name, driver.employee_middle_name, driver.employee_extension_name]
                        .filter(Boolean).join(' ')).trim();
                    $('<option>').val(driver.employee_id).text(name).appendTo(driverSelect);
                });
                driverSelect.prop('disabled', false);
                if (selectedId) driverSelect.val(String(selectedId));
                if (!drivers || !drivers.length) showFeedback('warning', 'Walang driver na naka-assign sa branch na ito.');
            })
            .fail(function () {
                driverSelect.html('<option value="">Unable to load drivers</option>');
                showFeedback('danger', 'Hindi ma-load ang driver list para sa branch.');
            });
    }

    branchSelect.on('change', function () {
        loadDrivers($(this).val());
    });

    $(document).on('click', '.js-add-driver-log', function (event) {
        event.preventDefault();
        clearForm();
        loadBranches().always(function () { modal.show(); });
    });

    table = $('#DriversLogsListDatatable').DataTable({
        processing: true,
        responsive: true,
        serverSide: true,
        stateSave: true,
        ajax: config.list,
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-end' },
            { data: 'travel_date' },
            { data: 'employee_number' },
            { data: 'employee_full_name' },
            { data: 'volume', render: $.fn.dataTable.render.number(',', '.', 2, '') },
            { data: 'rate_per_liter', render: $.fn.dataTable.render.number(',', '.', 2, '') },
            { data: 'gross_amount', render: $.fn.dataTable.render.number(',', '.', 2, '') },
            { data: 'trip_pay', render: $.fn.dataTable.render.number(',', '.', 2, '') },
            { data: 'action', orderable: false, searchable: false, className: 'text-center' },
            { data: 'plate_number' },
            { data: 'loading_terminal' },
            { data: 'destination' }
        ]
    });

    function loadComparisonTable() {
        if (comparisonTable) {
            comparisonTable.columns.adjust();
            comparisonTable.responsive.recalc();
            comparisonTable.ajax.reload(null, false);
            return;
        }
        comparisonTable = $('#DriversLogsComputationTable').DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            stateSave: true,
            ajax: config.comparison,
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'travel_date' },
                { data: 'employee_number' },
                { data: 'employee_full_name' },
                { data: 'gross_amount', render: $.fn.dataTable.render.number(',', '.', 2, '₱') },
                { data: 'saved_rate', render: function (value) { return Number(value || 0).toFixed(2) + '%'; } },
                { data: 'saved_trip_pay', render: $.fn.dataTable.render.number(',', '.', 2, '₱') },
                { data: 'current_rate', render: function (value) { return Number(value || 0).toFixed(2) + '%'; } },
                { data: 'recalculated_trip_pay', render: $.fn.dataTable.render.number(',', '.', 2, '₱') },
                { data: 'difference', render: $.fn.dataTable.render.number(',', '.', 2, '₱') },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    document.getElementById('driver-comparison-tab').addEventListener('shown.bs.tab', loadComparisonTable);

    $(document).on('click', '.js-recalculate-driver-log', function () {
        const button = $(this).prop('disabled', true).text('Updating…');
        $.ajax({
            url: config.recalculate,
            method: 'POST',
            dataType: 'json',
            data: { drivers_logs_id: button.data('id'), _token: config.csrfToken }
        }).done(function (response) {
            table.ajax.reload(null, false);
            if (comparisonTable) comparisonTable.ajax.reload(null, false);
            showFeedback('success', response.message || 'Na-update ang computation.');
        }).fail(function (xhr) {
            const message = (xhr.responseJSON && xhr.responseJSON.message) || 'Hindi na-update ang trip computation.';
            showFeedback('danger', message);
            if (comparisonTable) comparisonTable.ajax.reload(null, false);
        }).always(function () { button.prop('disabled', false).text('Use current rate'); });
    });

    $(form).on('submit', function (event) {
        event.preventDefault();
        clearErrors();
        form.classList.add('was-validated');
        if (!form.checkValidity()) {
            $('#drivers_logs_form_summary').removeClass('d-none').text('Pakisagutan nang tama ang lahat ng required fields.');
            form.reportValidity();
            return;
        }

        const button = $('#submit_drivers_logs_details');
        button.prop('disabled', true);
        button.find('.button-label').text('Saving…');
        button.find('.spinner-border').removeClass('d-none');
        $.ajax({
            url: config.save,
            method: 'POST',
            dataType: 'json',
            data: $(form).serialize() + '&_token=' + encodeURIComponent(config.csrfToken)
        }).done(function (response) {
            modal.hide();
            table.ajax.reload(null, false);
            showFeedback('success', response.message || 'Na-save ang driver trip details.');
        }).fail(function (xhr) {
            const body = xhr.responseJSON || {};
            showErrors(body.errors || {}, body.message || 'Hindi na-save ang driver trip. Suriin ang details at subukan ulit.');
        }).always(function () {
            button.prop('disabled', false);
            button.find('.button-label').text($('#drivers_logs_id').val() === '0' ? 'Save trip' : 'Update trip');
            button.find('.spinner-border').addClass('d-none');
        });
    });

    $(document).on('click', '.js-edit-driver-log', function () {
        const id = $(this).data('id');
        clearForm();
        $('#modal_title_action_drivers_logs').text('Edit driver trip');
        $('#submit_drivers_logs_details .button-label').text('Update trip');
        $.ajax({ url: config.info, method: 'POST', dataType: 'json', data: { DriverlogsID: id, _token: config.csrfToken } })
            .done(function (log) {
                $('#drivers_logs_id').val(log.drivers_logs_id);
                loadBranches(log.selected_branch_id).then(function () {
                    return loadDrivers(log.selected_branch_id, log.employee_idx);
                }).always(function () {
                    $('#travel_date').val(log.travel_date);
                    $('#plate_number').val(log.plate_number || '');
                    $('#loading_terminal').val(log.loading_terminal || '');
                    $('#destination').val(log.destination || '');
                    $('#volume').val(log.volume);
                    $('#rate_per_liter').val(log.rate_per_liter);
                    modal.show();
                });
            })
            .fail(function (xhr) {
                showFeedback('danger', (xhr.responseJSON && xhr.responseJSON.message) || 'Hindi ma-load ang driver trip para i-edit.');
            });
    });

    $(document).on('click', '.js-delete-driver-log', function () {
        const id = $(this).data('id');
        $('#delete_driver_error').addClass('d-none').empty();
        $.ajax({ url: config.info, method: 'POST', dataType: 'json', data: { DriverlogsID: id, _token: config.csrfToken } })
            .done(function (log) {
                pendingDeleteId = log.drivers_logs_id;
                $('#delete_driver_name').text(log.employee_full_name || '');
                $('#delete_driver_date').text(log.travel_date || '');
                deleteModal.show();
            })
            .fail(function () { showFeedback('danger', 'Hindi ma-load ang driver trip na pipiliing burahin.'); });
    });

    $('#deleteDriversLogConfirmed').on('click', function () {
        if (!pendingDeleteId) return;
        const button = $(this).prop('disabled', true);
        $.ajax({ url: config.delete, method: 'POST', dataType: 'json', data: { DriverlogsID: pendingDeleteId, _token: config.csrfToken } })
            .done(function (response) {
                deleteModal.hide();
                table.ajax.reload(null, false);
                showFeedback('success', response.message || 'Na-delete ang driver trip.');
                pendingDeleteId = null;
            })
            .fail(function (xhr) {
                const message = (xhr.responseJSON && xhr.responseJSON.message) || 'Hindi ma-delete ang driver trip.';
                $('#delete_driver_error').removeClass('d-none').text(message);
            }).always(function () { button.prop('disabled', false); });
    });

    loadBranches();
})(jQuery, window.driverLogsConfig);
