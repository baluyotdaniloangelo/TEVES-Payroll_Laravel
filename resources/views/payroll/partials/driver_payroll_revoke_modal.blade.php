<div class="modal fade" id="driver-payroll-revoke-modal" tabindex="-1" aria-labelledby="driver-payroll-revoke-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="driver-payroll-revoke-form" method="POST">
            @csrf
            <div class="modal-header"><h5 class="modal-title" id="driver-payroll-revoke-title">Revoke latest driver payroll</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body">
                <p>Payroll <strong id="driver-payroll-revoke-number"></strong> will be removed from active saved payrolls. Its trip logs will become available for correction and a new review. A copy of the revoked payroll and its trips will be kept in the audit history.</p>
                <div class="mb-3"><label class="form-label" for="driver-payroll-revoke-reason">Reason for revision <span class="text-danger">*</span></label><textarea class="form-control" id="driver-payroll-revoke-reason" name="reason" maxlength="500" rows="3" required></textarea></div>
                <label class="form-check"><input class="form-check-input" type="checkbox" name="confirmed" value="1" required><span class="form-check-label">I understand this will unlock the trips for revision.</span></label>
                <div class="alert alert-danger d-none mt-3 mb-0" id="driver-payroll-revoke-error" role="alert"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary driver-payroll-action-btn" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger driver-payroll-action-btn">Revoke payroll</button></div>
        </form>
    </div></div>
</div>
