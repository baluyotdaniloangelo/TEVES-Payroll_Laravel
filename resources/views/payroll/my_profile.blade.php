@extends('layouts.employee')

@push('page_styles')
<link rel="stylesheet" href="{{ asset('assets/css/my-payroll.css') }}">
@endpush

@section('content')
<main class="my-payroll">
    <header class="my-payroll-heading">
        <div><span class="my-payroll-eyebrow">Employee portal</span><h1>My Profile</h1><p>Your employment information and account security.</p></div>
    </header>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <div class="my-payroll-profile-grid">
        <section class="my-payroll-record my-payroll-profile-card">
            <h2>Employee information</h2>
            <p class="my-payroll-profile-note">Contact payroll if any employment detail needs correction.</p>
            <dl class="my-payroll-profile-fields">
                <div><dt>Name</dt><dd>{{ $employee->employee_full_name }}</dd></div>
                <div><dt>Employee number</dt><dd>{{ $employee->employee_number }}</dd></div>
                <div><dt>Branch</dt><dd>{{ $employee->branch_code }} · {{ $employee->branch_name }}</dd></div>
                <div><dt>Position</dt><dd>{{ $employee->employee_position ?: '—' }}</dd></div>
                <div><dt>Employment type</dt><dd>{{ $employee->employment_type ?: '—' }}</dd></div>
                <div><dt>Email</dt><dd>{{ $employee->employee_email ?: '—' }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $employee->employee_phone ?: '—' }}</dd></div>
                <div><dt>TIN</dt><dd>{{ $employee->employee_tin ?: '—' }}</dd></div>
                <div><dt>Tax status</dt><dd>{{ $employee->employee_tax_status ?: 'Not specified' }}</dd></div>
            </dl>
        </section>
        <section class="my-payroll-record my-payroll-profile-card">
            <h2>Change password</h2>
            <p class="my-payroll-profile-note">Use your current password to protect this account.</p>
            <form method="post" action="{{ route('my-profile.password') }}" class="my-payroll-password-form">
                @csrf
                <div><label for="current-password">Current password</label><input class="form-control @error('current_password') is-invalid @enderror" id="current-password" type="password" name="current_password" required autocomplete="current-password">@error('current_password')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div><label for="new-password">New password</label><input class="form-control @error('password') is-invalid @enderror" id="new-password" type="password" name="password" minlength="8" maxlength="72" required autocomplete="new-password">@error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div><label for="confirm-password">Confirm new password</label><input class="form-control" id="confirm-password" type="password" name="password_confirmation" minlength="8" maxlength="72" required autocomplete="new-password"></div>
                <button class="btn btn-primary" type="submit">Update password</button>
            </form>
        </section>
    </div>
</main>
@endsection
