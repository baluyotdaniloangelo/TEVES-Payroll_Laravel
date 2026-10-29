@extends('layouts.layout')
@section('content')
<div class="page-wrapper"><div class="content container-fluid">
    <div class="page-header"><div class="row align-items-center"><div class="col"><h3 class="page-title">Generate Driver Payroll</h3><p class="text-muted mb-0">Sariling cutoff at payroll record ng bawat driver.</p></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card"><div class="card-body"><form method="GET" action="{{ route('drivers_payroll.index') }}" class="row align-items-end g-3">
        <div class="col-md-4"><label class="form-label" for="employee_id">Driver</label><select class="form-select" id="employee_id" name="employee_id" required><option value="">Select driver</option>@foreach($drivers as $option)<option value="{{ $option->employee_id }}" @selected(request('employee_id') == $option->employee_id)>{{ $option->employee_full_name }} — {{ $option->branch_code }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="cutoff_start">Cutoff start</label><input class="form-control" type="date" id="cutoff_start" name="cutoff_start" value="{{ request('cutoff_start') }}" required></div>
        <div class="col-md-3"><label class="form-label" for="cutoff_end">Cutoff end</label><input class="form-control" type="date" id="cutoff_end" name="cutoff_end" value="{{ request('cutoff_end') }}" required></div>
        <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Generate &amp; Review</button></div>
    </form></div></div>

    @if($hasPeriod && $employee)
    <div class="card"><div class="card-body"><h4>{{ $employee->employee_full_name }} <small class="text-muted">{{ $employee->employee_number }} · {{ $employee->branch_name }} ({{ $employee->branch_code }})</small></h4><strong>Cutoff:</strong> {{ request('cutoff_start') }} to {{ request('cutoff_end') }}<div class="text-muted mt-1">Trip pay lang ang payroll computation para sa drivers.</div></div></div>

    @php($showSavedTab = request('tab') === 'saved')
    <ul class="nav nav-tabs mb-3" id="driver-payroll-tabs" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link {{ $showSavedTab ? '' : 'active' }}" id="driver-generate-tab" data-bs-toggle="tab" data-bs-target="#driver-generate-pane" type="button" role="tab" aria-selected="{{ $showSavedTab ? 'false' : 'true' }}">Generate / Review</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link {{ $showSavedTab ? 'active' : '' }}" id="driver-saved-tab" data-bs-toggle="tab" data-bs-target="#driver-saved-pane" type="button" role="tab" aria-selected="{{ $showSavedTab ? 'true' : 'false' }}">Saved Payrolls</button></li>
    </ul>
    <div class="tab-content">
    <div class="tab-pane fade {{ $showSavedTab ? '' : 'show active' }}" id="driver-generate-pane" role="tabpanel" aria-labelledby="driver-generate-tab">
    @if($saved)
        <div class="alert alert-success">Saved payroll: <strong>{{ $saved->payroll_no }}</strong> · Trip Ref. No. <strong>{{ $saved->trip_reference }}</strong> · Total trip pay <strong>₱{{ number_format((float)$saved->total_trip_pay, 2) }}</strong></div>
        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Saved trip details</h4></div><div class="table-responsive"><table class="table table-bordered mb-0"><thead><tr><th>Travel Date</th><th>Plate</th><th>Loading Terminal</th><th>Destination</th><th>Volume</th><th>Rate / L</th><th>Gross Amount</th><th>Trip Pay</th></tr></thead><tbody>
        @foreach($savedTrips as $trip)<tr><td>{{ $trip->travel_date }}</td><td>{{ $trip->plate_number }}</td><td>{{ $trip->loading_terminal }}</td><td>{{ $trip->destination }}</td><td>{{ number_format((float)$trip->volume,2) }}</td><td>{{ number_format((float)$trip->rate_per_liter,2) }}</td><td>₱{{ number_format((float)$trip->gross_amount,2) }}</td><td>₱{{ number_format((float)$trip->trip_pay,2) }}</td></tr>@endforeach
        </tbody></table></div><div class="card-body"><a class="btn btn-outline-primary" target="_blank" href="{{ route('drivers_payroll.print', [$employee->employee_id, 'cutoff_start'=>request('cutoff_start'), 'cutoff_end'=>request('cutoff_end')]) }}">Print driver's payroll</a></div></div>
    @elseif($trips->isNotEmpty())
        <div class="card"><div class="card-header"><h4 class="card-title mb-0">Review trip earnings</h4></div><div class="table-responsive"><table class="table table-bordered mb-0"><thead><tr><th>Travel Date</th><th>Plate</th><th>Loading Terminal</th><th>Destination</th><th>Volume</th><th>Rate / L</th><th>Gross Amount</th><th>Driver's Trip Pay</th></tr></thead><tbody>
        @foreach($trips as $trip)<tr><td>{{ $trip->travel_date }}</td><td>{{ $trip->plate_number }}</td><td>{{ $trip->loading_terminal }}</td><td>{{ $trip->destination }}</td><td>{{ number_format((float)$trip->volume, 2) }}</td><td>{{ number_format((float)$trip->rate_per_liter, 2) }}</td><td>₱{{ number_format((float)$trip->gross_amount, 2) }}</td><td>₱{{ number_format((float)$trip->trip_pay, 2) }}</td></tr>@endforeach
        </tbody><tfoot><tr><th colspan="4" class="text-end">Total</th><th>{{ number_format((float)$trips->sum('volume'), 2) }}</th><th></th><th>₱{{ number_format((float)$trips->sum('gross_amount'), 2) }}</th><th>₱{{ number_format((float)$trips->sum('trip_pay'), 2) }}</th></tr></tfoot></table></div>
        <div class="card-body"><form method="POST" action="{{ route('drivers_payroll.save', $employee->employee_id) }}" onsubmit="return this.querySelector('[name=reviewed]').checked || (alert('Please confirm that you reviewed the driver and trip details.'), false)">@csrf
            <input type="hidden" name="cutoff_start" value="{{ request('cutoff_start') }}"><input type="hidden" name="cutoff_end" value="{{ request('cutoff_end') }}">
            <div class="row g-3 align-items-end"><div class="col-md-5"><label class="form-label">Trip Ref. No. <span class="text-danger">*</span></label><input class="form-control" name="trip_reference" maxlength="100" required placeholder="Enter trip reference"></div>
                <div class="col-md-5"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" value="1" id="reviewed-{{ $employee->employee_id }}" name="reviewed" required><label class="form-check-label" for="reviewed-{{ $employee->employee_id }}">I reviewed this driver's trips, amounts, and cutoff.</label></div></div>
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Save driver payroll</button></div></div>
            <div class="small text-muted mt-2">Mase-save ang payroll record at trip details para sa driver at cutoff na ito.</div>
        </form></div></div>
    @else<div class="alert alert-info">Walang available, hindi pa na-pay na trip logs para sa driver at cutoff period na ito.</div>@endif
    </div>

    <div class="tab-pane fade {{ $showSavedTab ? 'show active' : '' }}" id="driver-saved-pane" role="tabpanel" aria-labelledby="driver-saved-tab">
    <div class="card mt-4"><div class="card-header d-flex justify-content-between align-items-center"><h4 class="card-title mb-0">Saved driver payrolls for this cutoff</h4><strong>Total payout: ₱{{ number_format($cutoffTotal, 2) }}</strong></div>
        <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Payroll No.</th><th>Driver</th><th>Trip Ref. No.</th><th>Trips</th><th>Total trip pay</th><th>Printable</th></tr></thead><tbody>
        @forelse($cutoffPayrolls as $payroll)<tr><td>{{ $payroll->payroll_no }}</td><td>{{ $payroll->employee_full_name }}</td><td>{{ $payroll->trip_reference }}</td><td>{{ $payroll->total_trip_count }}</td><td>₱{{ number_format($payroll->total_trip_pay, 2) }}</td><td><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('drivers_payroll.print', [$payroll->employee_idx, 'cutoff_start'=>request('cutoff_start'), 'cutoff_end'=>request('cutoff_end')]) }}">Print</a></td></tr>
        @empty<tr><td colspan="6" class="text-center text-muted py-3">Wala pang saved driver payroll para sa cutoff period na ito.</td></tr>@endforelse
        </tbody></table></div>
    </div>
    </div>
    </div>
    @endif
</div></div>
@endsection
