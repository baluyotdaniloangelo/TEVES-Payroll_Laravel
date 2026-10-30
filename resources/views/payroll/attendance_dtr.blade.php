@extends('layouts.layout')
@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/attendance-dtr.css') }}?v=20261030">
@endpush
@section('content')
<div class="page-wrapper"><div class="content container-fluid" id="attendance-dtr-page" data-base="{{ url('/attendance-import') }}" data-token="{{ csrf_token() }}">
    <div class="page-header dtr-page-heading"><div><span class="dtr-eyebrow">Attendance import</span><h3 class="page-title">DTR Plotting</h3><p>Upload device logs, match employees, then review one date at a time.</p></div><a href="{{ route('employee_attendance_logs') }}">Back to Employee Logs</a></div>
    @if(!$ready)
        <div class="alert alert-info">Attendance import is awaiting database setup. Existing manual attendance is available.</div>
    @else
    <div id="import-message" role="status" aria-live="polite"></div>
    @if($branches->isEmpty())
        <div class="alert alert-warning" role="alert">No branches are assigned to this account. Branch access must be configured before importing attendance.
            @if(strcasecmp(trim((string)$data->user_type), 'Admin') === 0)<a href="{{ url('/user') }}">Open user management to review branch access.</a>@else Ask your administrator to assign the branches you handle.@endif
        </div>
    @endif
    <div class="card dtr-step-card dtr-upload-card"><div class="card-body">
        <div class="dtr-step-heading"><span class="dtr-step-number">1</span><div><h4>Upload attendance</h4><p>Select the branch and device profile used for this export. Employee mappings are remembered for each profile.</p></div></div>
        <form id="import-upload" class="row g-3">
            <div class="col-md-4"><label for="import-branch" class="form-label">Branch</label>
                <select id="import-branch" class="form-select" required @disabled($branches->isEmpty())><option value="">{{ $branches->isEmpty() ? 'No assigned branches' : 'Select branch' }}</option>
                    @foreach($branches as $branch)<option value="{{ $branch->branch_id }}">{{ $branch->branch_code }} — {{ $branch->branch_name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4"><label for="import-device" class="form-label">Device / source profile</label><select id="import-device" class="form-select" required><option value="">Select a branch first</option></select></div>
            <div class="col-md-4"><label for="import-new-device" class="form-label">New device name</label>
                <div class="input-group"><input id="import-new-device" class="form-control" maxlength="100" placeholder="e.g. Front counter biometric"><button type="button" class="btn btn-outline-primary" id="import-add-device">Add device</button></div>
            </div>
            <div class="col-md-4"><label for="dtr-cutoff-start" class="form-label">Cutoff start date</label><input id="dtr-cutoff-start" name="cutoff_start" type="date" class="form-control" required></div>
            <div class="col-md-4"><label for="dtr-cutoff-end" class="form-label">Cutoff end date</label><input id="dtr-cutoff-end" name="cutoff_end" type="date" class="form-control" required></div>
            <div class="col-md-4 d-flex align-items-end"><button id="dtr-apply-cutoff" type="button" class="btn btn-outline-primary">Apply cutoff</button></div>
            <div class="col-12"><p id="dtr-cutoff-state" class="small mb-0" aria-live="polite">Choose the desired cutoff. Only attendance dates within this range will be plotted and saved.</p></div>
            <div class="col-md-8"><label for="import-file" class="form-label">Attendance export</label><input type="file" id="import-file" class="form-control" accept=".xls,.xlsx,.csv,.txt" required></div>
            <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary" type="submit">Upload and plot DTR</button></div>
        </form>
        <div class="mt-3"><label for="import-history" class="form-label">Resume a recent import</label><select id="import-history" class="form-select"><option value="">Choose an uploaded report</option>
            @foreach($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->created_at }} — {{ $batch->filename }}</option>@endforeach
        </select></div>
    </div></div>
    <section id="import-review" hidden>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2"><p id="import-summary" class="small text-muted mb-0"></p><a id="import-source" class="btn btn-sm btn-outline-secondary" href="#">Download original export</a></div>
        <div id="dtr-mapping-card" class="card dtr-step-card"><div class="card-body">
            <div class="dtr-step-heading"><span class="dtr-step-number">2</span><div><h4>Employee mapping</h4><p>Connect each device ID to its payroll employee.</p></div></div>
            <p id="dtr-mapping-progress" class="small text-warning" aria-live="polite"></p>
            <div class="row g-3 align-items-end">
                <div class="col-md-6"><label for="dtr-export-employee" class="form-label">Employee in export</label><select id="dtr-export-employee" class="form-select"></select></div>
                <div class="col-md-6 d-flex gap-2"><button id="dtr-prev-employee" type="button" class="btn btn-outline-secondary">Previous employee</button><button id="dtr-next-employee" type="button" class="btn btn-outline-secondary">Next employee</button></div>
            </div>
            <div class="dtr-mapping mt-3">
                <div><span class="text-muted small d-block">Device employee ID</span><strong id="dtr-device-id"></strong><span id="dtr-export-name" class="d-block"></span></div>
                <span class="dtr-map-arrow" aria-hidden="true">→</span>
                <div><label for="dtr-payroll-employee" class="form-label">Payroll employee</label><select id="dtr-payroll-employee" class="form-select"></select></div>
                <button id="dtr-save-mapping" class="btn btn-primary" type="button">Remember mapping</button>
            </div>
            <p id="dtr-mapping-state" class="mt-2 mb-0 small" aria-live="polite"></p>
        </div></div>
        <div id="dtr-logs-card" class="card dtr-step-card"><div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div><span class="dtr-eyebrow">2 · Employee logs</span><h4 id="dtr-employee-heading" class="mb-0">Employee DTR</h4><span id="dtr-employee-count" class="small"></span></div>
                <div class="d-flex flex-wrap gap-2"><button id="dtr-toggle-mapping" class="btn btn-outline-secondary" type="button" hidden>Review mappings</button><button id="dtr-send-mapped" class="btn btn-primary" type="button" disabled>Save mapped logs &amp; email employees</button></div>
            </div>
            <div class="dtr-log-employee"><label for="dtr-log-employee" class="form-label">Employee to view</label><select id="dtr-log-employee" class="form-select"></select></div>
            <p class="mt-2 mb-0">After checking the employee mappings, save all mapped device logs and email each employee to complete only missing DTR details. You can return to review or edit them later.</p>
            <p id="dtr-send-result" class="mt-2 mb-0" role="status" aria-live="polite"></p>
            <p class="mt-2">Employees will complete missing punches and break details. You can return here anytime to review or edit a queued interval. Blank dates are not counted as absences.</p>
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                <label class="form-check mb-0"><input type="checkbox" id="dtr-review-complete" class="form-check-input"><span class="form-check-label">Select complete intervals on this date to review/edit</span></label>
                <button id="dtr-refresh" class="btn btn-outline-secondary" type="button">Refresh saved data</button>
            </div>
            <div class="dtr-log-workspace"><aside class="dtr-date-panel" aria-label="Dates in cutoff"><h5>Dates in cutoff</h5><div id="dtr-date-nav" class="dtr-date-nav"></div></aside><div class="dtr-day-panel"><div class="table-responsive dtr-grid-wrap"><table class="table dtr-grid align-middle"><thead><tr>
                <th scope="col">Include</th><th scope="col">Date</th><th scope="col">Time in</th><th scope="col">Break out</th><th scope="col">Break in</th><th scope="col">Time out</th><th scope="col">Status</th><th scope="col">Classification</th><th scope="col">Details</th>
            </tr></thead><tbody id="dtr-grid-body"></tbody></table></div></div></div>
            <dialog id="dtr-review-modal" class="dtr-modal" aria-labelledby="dtr-review-title">
                <div class="dtr-modal-header"><div><h4 id="dtr-review-title" class="mb-1">Review attendance interval</h4><p class="text-muted mb-0">Review raw punches, dates, and settings. Changes are included when you save this employee's DTR.</p></div><button type="button" id="dtr-review-close" class="btn-close" aria-label="Close"></button></div>
                <div id="dtr-interval-detail" class="dtr-detail mt-3" hidden></div>
                <div class="dtr-modal-actions"><button type="button" id="dtr-review-done" class="btn btn-primary">Done</button></div>
            </dialog>
            <label class="form-check mt-3"><input type="checkbox" id="dtr-employee-confirm" class="form-check-input"><span class="form-check-label">I reviewed the selected queued intervals before saving my edits.</span></label>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3"><span id="dtr-selection-count" aria-live="polite"></span><button id="dtr-save-selected" type="button" class="btn btn-outline-primary">Save selected admin edits</button></div>
            <p id="dtr-save-result" class="mt-2 mb-0" role="status" aria-live="polite"></p>
        </div></div>
    </section>
    <script id="dtr-device-data" type="application/json">{!! json_encode($devices, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('js/attendance-dtr.js') }}?v=20261030-guided-plotting" defer></script>
    @endif
</div></div>
@endsection
