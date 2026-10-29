@extends('layouts.employee')

@section('content')
<div class="page-header"><div><h3 class="page-title">My DTR</h3><p class="text-muted mb-0">{{ $employee->employee_full_name }} · {{ $employee->employee_number }}</p></div><a class="btn btn-outline-secondary" href="{{ route('logout-user') }}">Log out</a></div>
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
<div class="alert alert-info">Complete missing punches and submit each DTR interval. Payroll will review it before it is added to finalized attendance.</div>
@forelse($items as $item)
    @php($proposal = $item['proposal'])
    <div class="card mb-3"><div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div><h5 class="mb-1">{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }} · {{ $item['branch_code'] }} — {{ $item['branch_name'] }}</h5><small class="text-muted">{{ $item['cutoff_start'] }} to {{ $item['cutoff_end'] }} · {{ $item['filename'] }}</small></div>
            <span class="badge {{ $item['status'] === 'employee_submitted' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $item['status'] === 'employee_submitted' ? 'Submitted to payroll' : 'Needs your completion' }}</span>
        </div>
        <p class="small text-muted mb-2">Device punches: {{ implode(', ', $item['raw']['punches'] ?? []) }}</p>
        @if($item['missing'])
            <p class="mb-3"><strong>Needs your attention:</strong> {{ implode(', ', $item['missing']) }}. Captured device punches are locked; fill only blank details.</p>
            @if(in_array('break treatment', $item['missing'], true))<p class="small text-muted">If you choose Recorded break, complete only the missing break-out or break-in time. Choose No break if you did not take a recorded break.</p>@endif
        @else
            <p class="text-success mb-3">No punch fields are missing. Confirm the recorded attendance and submit it to payroll.</p>
        @endif
        @if($item['warnings'])<p class="text-warning">{{ implode(' ', $item['warnings']) }}</p>@endif
        @if($item['status'] === 'employee_submitted')
            <p class="text-success mb-0">Your DTR was submitted. Payroll will review it before finalizing.</p>
            @if($item['note'])<p class="mt-2 mb-0"><strong>Your note:</strong> {{ $item['note'] }}</p>@endif
        @else
        <form method="post" action="{{ route('my-dtr.submit', $item['id']) }}" class="row g-3">
            @csrf
            <input type="hidden" name="version" value="{{ $item['version'] }}">
            <div class="col-md-3"><label class="form-label">Time in</label><input @required(empty($proposal['log_in'])) @readonly(!empty($proposal['log_in'])) type="datetime-local" class="form-control" name="proposed[log_in]" value="{{ old('proposed.log_in', str_replace(' ', 'T', substr((string)($proposal['log_in'] ?? ''),0,16))) }}"></div>
            <div class="col-md-3"><label class="form-label">Time out</label><input @required(empty($proposal['log_out'])) @readonly(!empty($proposal['log_out'])) type="datetime-local" class="form-control" name="proposed[log_out]" value="{{ old('proposed.log_out', str_replace(' ', 'T', substr((string)($proposal['log_out'] ?? ''),0,16))) }}"></div>
            <div class="col-md-2"><label class="form-label">Break treatment</label>
                @if(($proposal['break_policy'] ?? 'review') === 'review')
                    <select class="form-select" name="proposed[break_policy]" data-break-select required><option value="" @selected(!old('proposed.break_policy')) disabled>Choose</option><option value="recorded" @selected(old('proposed.break_policy') === 'recorded')>Recorded break</option><option value="none" @selected(old('proposed.break_policy') === 'none')>No break</option></select>
                @else
                    <input type="hidden" name="proposed[break_policy]" value="{{ $proposal['break_policy'] }}"><select class="form-select" data-break-select disabled><option selected>{{ $proposal['break_policy'] === 'none' ? 'No break' : 'Recorded break' }}</option></select>
                @endif
            </div>
            <div class="col-md-2"><label class="form-label">Break out</label><input type="datetime-local" class="form-control" name="proposed[breaktime_start]" value="{{ old('proposed.breaktime_start', str_replace(' ', 'T', substr((string)($proposal['breaktime_start'] ?? ''),0,16))) }}" @readonly(!empty($proposal['breaktime_start'])) @required(($proposal['break_policy'] ?? '') === 'recorded' && empty($proposal['breaktime_start'])) @disabled(($proposal['break_policy'] ?? '') === 'none')></div>
            <div class="col-md-2"><label class="form-label">Break in</label><input type="datetime-local" class="form-control" name="proposed[breaktime_end]" value="{{ old('proposed.breaktime_end', str_replace(' ', 'T', substr((string)($proposal['breaktime_end'] ?? ''),0,16))) }}" @readonly(!empty($proposal['breaktime_end'])) @required(($proposal['break_policy'] ?? '') === 'recorded' && empty($proposal['breaktime_end'])) @disabled(($proposal['break_policy'] ?? '') === 'none')></div>
            <div class="col-12"><label class="form-label">Note for payroll (optional)</label><textarea class="form-control" name="note" maxlength="1000" rows="2" placeholder="Explain any correction or missing punch">{{ old('note') }}</textarea></div>
            <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="acknowledged" value="1" required id="dtr-confirm-{{ $item['id'] }}"><label class="form-check-label" for="dtr-confirm-{{ $item['id'] }}">I confirm these are my actual attendance times.</label></div></div>
            <div class="col-12 text-end"><button type="submit" class="btn btn-primary">Submit DTR to payroll</button></div>
        </form>
        @endif
    </div></div>
@empty
    <div class="card"><div class="card-body text-center py-5"><h5>All caught up</h5><p class="text-muted mb-0">There are no DTR intervals waiting for your completion.</p></div></div>
@endforelse
<script>
document.querySelectorAll('[data-break-select]').forEach(select => select.addEventListener('change', () => {
    const form = select.form, noBreak = select.value === 'none';
    ['proposed[breaktime_start]','proposed[breaktime_end]'].forEach(name => { const input = form.querySelector(`[name="${name}"]`); input.disabled = noBreak; input.required = !noBreak && !input.readOnly; });
}));
</script>
@endsection
