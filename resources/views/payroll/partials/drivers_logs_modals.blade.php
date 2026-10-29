<div class="modal custom-modal fade" id="DriversLogDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Delete driver trip</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body"><p>Delete this trip log?</p><dl class="row mb-0"><dt class="col-4">Driver</dt><dd class="col-8" id="delete_driver_name"></dd><dt class="col-4">Travel date</dt><dd class="col-8" id="delete_driver_date"></dd></dl><div class="alert alert-danger d-none mt-3" id="delete_driver_error" role="alert"></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-danger" id="deleteDriversLogConfirmed">Delete trip</button></div>
    </div></div>
</div>

<div class="modal custom-modal fade" id="drivers_logs_details_modal" tabindex="-1" aria-labelledby="modal_title_action_drivers_logs" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="modal_title_action_drivers_logs">Add driver trip</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
        <div class="modal-body">
            <div class="alert alert-danger d-none" id="drivers_logs_form_summary" role="alert"></div>
            <form id="DriversLogsForm" novalidate>
                <input type="hidden" id="drivers_logs_id" name="drivers_logs_id" value="0">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="drivers_branch_idx">Branch <span class="text-danger">*</span></label><select class="form-select" id="drivers_branch_idx" name="branch_idx" required><option value="">Select branch</option></select><div class="invalid-feedback" data-error-for="branch_idx"></div></div>
                    <div class="col-md-6"><label class="form-label" for="drivers_idx">Driver's name <span class="text-danger">*</span></label><select class="form-select" id="drivers_idx" name="employee_idx" required disabled><option value="">Select a branch first</option></select><div class="invalid-feedback" data-error-for="employee_idx"></div></div>
                    <div class="col-12"><label class="form-label" for="travel_date">Travel date <span class="text-danger">*</span></label><input class="form-control" type="date" id="travel_date" name="travel_date" required><div class="invalid-feedback" data-error-for="travel_date"></div></div>
                    <div class="col-12"><label class="form-label" for="plate_number">Plate number <span class="text-danger">*</span></label><input class="form-control" type="text" id="plate_number" name="plate_number" maxlength="255" required><div class="invalid-feedback" data-error-for="plate_number"></div></div>
                    <div class="col-12"><label class="form-label" for="loading_terminal">Loading terminal <span class="text-danger">*</span></label><input class="form-control" type="text" id="loading_terminal" name="loading_terminal" maxlength="255" required><div class="invalid-feedback" data-error-for="loading_terminal"></div></div>
                    <div class="col-12"><label class="form-label" for="destination">Destination <span class="text-danger">*</span></label><input class="form-control" type="text" id="destination" name="destination" maxlength="255" required><div class="invalid-feedback" data-error-for="destination"></div></div>
                    <div class="col-md-6"><label class="form-label" for="volume">Volume <span class="text-danger">*</span></label><input class="form-control" type="number" id="volume" name="volume" min="0.01" step="0.01" required><div class="invalid-feedback" data-error-for="volume"></div></div>
                    <div class="col-md-6"><label class="form-label" for="rate_per_liter">Rate per liter <span class="text-danger">*</span></label><input class="form-control" type="number" id="rate_per_liter" name="rate_per_liter" min="0.01" step="0.01" required><div class="invalid-feedback" data-error-for="rate_per_liter"></div></div>
                </div>
                <div class="modal-footer px-0 pb-0 mt-4"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" id="submit_drivers_logs_details" type="submit"><span class="button-label">Save trip</span><span class="spinner-border spinner-border-sm ms-2 d-none" aria-hidden="true"></span></button></div>
            </form>
        </div>
    </div></div>
</div>
