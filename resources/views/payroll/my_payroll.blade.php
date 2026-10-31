@extends('layouts.employee')

@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/my-payroll.css') }}">
@endpush

@section('content')
@php
    $money = static fn ($amount) => '₱'.number_format((float) $amount, 2);
    $date = static fn ($value) => \Carbon\Carbon::parse($value)->format('M d, Y');
@endphp
<main class="my-payroll">
    <header class="my-payroll-heading">
        <div>
            <span class="my-payroll-eyebrow">Employee portal</span>
            <h1>My Payroll</h1>
            <p>Saved payroll records for each cutoff. These amounts are read only.</p>
        </div>
        <span class="my-payroll-count">{{ $records->count() }} saved {{ \Illuminate\Support\Str::plural('record', $records->count()) }}</span>
    </header>

    @if($records->isEmpty())
        <section class="my-payroll-empty">
            <i class="fa-regular fa-file-lines" aria-hidden="true"></i>
            <h2>No saved payroll yet</h2>
            <p>Your cutoff payroll records will appear here after payroll saves them.</p>
        </section>
    @else
        <div class="my-payroll-list">
            @foreach($records as $record)
                <details class="my-payroll-record">
                    <summary>
                        <span class="my-payroll-period">
                            <strong>{{ $date($record['start']) }} – {{ $date($record['end']) }}</strong>
                            <small>{{ $record['branch_code'] ?: 'Branch' }} · {{ $record['reference'] }}</small>
                        </span>
                        <span class="my-payroll-kind">{{ $record['type'] === 'driver' ? 'Driver trips' : 'Employee payroll' }}</span>
                        <span class="my-payroll-payout">
                            <small>{{ $record['type'] === 'driver' ? 'Total trip pay' : 'Net pay' }}</small>
                            <strong>{{ $money($record['net']) }}</strong>
                        </span>
                        <i class="fa-solid fa-chevron-down my-payroll-chevron" aria-hidden="true"></i>
                    </summary>
                    <div class="my-payroll-detail">
                        @if($record['type'] === 'regular')
                            @php $pay = $record['detail']; @endphp
                            <div class="my-payroll-breakdown">
                                <div><span>Regular pay</span><strong>{{ $money($pay->regular_pay_total ?? 0) }}</strong></div>
                                <div><span>Regular overtime</span><strong>{{ $money($pay->regular_overtime_pay_total ?? 0) }}</strong></div>
                                <div><span>Rest day pay</span><strong>{{ $money($pay->restday_pay_total ?? 0) }}</strong></div>
                                <div><span>Rest day overtime</span><strong>{{ $money($pay->restday_overtime_pay_total ?? 0) }}</strong></div>
                                <div><span>Night differential</span><strong>{{ $money($pay->night_differential_pay_total ?? 0) }}</strong></div>
                                <div><span>Regular holiday</span><strong>{{ $money($pay->regular_holiday_pay_total ?? 0) }}</strong></div>
                                <div><span>Special holiday</span><strong>{{ $money($pay->special_holiday_pay_total ?? 0) }}</strong></div>
                                <div><span>Paid leave</span><strong>{{ $money($pay->leave_amount_pay_total ?? 0) }}</strong></div>
                                <div class="my-payroll-subtotal"><span>Gross salary</span><strong>{{ $money($record['gross']) }}</strong></div>
                                <div><span>Allowances</span><strong>+ {{ $money($record['allowances']) }}</strong></div>
                                <div><span>Deductions</span><strong>− {{ $money($record['deductions']) }}</strong></div>
                                <div class="my-payroll-total"><span>Net pay</span><strong>{{ $money($record['net']) }}</strong></div>
                            </div>
                        @else
                            @php $pay = $record['detail']; @endphp
                            <div class="my-payroll-breakdown">
                                <div><span>Trip reference</span><strong>{{ $pay->trip_reference }}</strong></div>
                                <div><span>Payroll date</span><strong>{{ $date($pay->payroll_date) }}</strong></div>
                                <div><span>Trips</span><strong>{{ $record['items']->count() }}</strong></div>
                                <div><span>Total volume</span><strong>{{ number_format((float) $pay->total_volume, 2) }}</strong></div>
                                <div><span>Gross trip amount</span><strong>{{ $money($pay->total_gross_amount) }}</strong></div>
                                <div class="my-payroll-total"><span>Total trip pay</span><strong>{{ $money($record['net']) }}</strong></div>
                            </div>
                            @if($record['items']->isNotEmpty())
                                <div class="my-payroll-trips">
                                    <h2>Trip details</h2>
                                    @foreach($record['items'] as $trip)
                                        <div class="my-payroll-trip">
                                            <span><strong>{{ $date($trip->travel_date) }}</strong><small>{{ $trip->plate_number }} · {{ $trip->loading_terminal }} → {{ $trip->destination }}</small></span>
                                            <strong>{{ $money($trip->trip_pay) }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    @endif
</main>
@endsection
