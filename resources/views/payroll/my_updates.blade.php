@extends('layouts.employee')

@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/my-payroll.css') }}">
@endpush

@section('content')
<main class="my-payroll">
    <header class="my-payroll-heading">
        <div><span class="my-payroll-eyebrow">Employee portal</span><h1>My Updates</h1><p>Your DTR tasks and recently saved payroll cutoffs.</p></div>
    </header>
    <div class="my-payroll-profile-grid">
        <section class="my-payroll-record my-payroll-profile-card">
            <h2>DTR updates</h2>
            @forelse($items as $item)
                <a class="my-payroll-update" href="{{ route('my-dtr.index') }}">
                    <span><strong>{{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</strong><small>{{ $item['branch_code'] }} · {{ $item['filename'] }}</small></span>
                    <span class="my-payroll-update-state">{{ $item['status']==='employee_submitted'?'Submitted to payroll':(!empty($item['allow_employee_correction'])?'Correction requested':'Needs completion') }}</span>
                </a>
            @empty
                <p class="my-payroll-profile-note">No DTR tasks at the moment.</p>
            @endforelse
        </section>
        <section class="my-payroll-record my-payroll-profile-card">
            <h2>Saved payroll</h2>
            @forelse($recentPayroll as $payroll)
                <a class="my-payroll-update" href="{{ route('my-payroll.index') }}">
                    <span><strong>{{ \Carbon\Carbon::parse($payroll['start'])->format('M d') }} – {{ \Carbon\Carbon::parse($payroll['end'])->format('M d, Y') }}</strong><small>{{ $payroll['type'] }} saved</small></span>
                    <strong>₱{{ number_format((float)$payroll['amount'],2) }}</strong>
                </a>
            @empty
                <p class="my-payroll-profile-note">No saved payroll yet.</p>
            @endforelse
        </section>
    </div>
</main>
@endsection
