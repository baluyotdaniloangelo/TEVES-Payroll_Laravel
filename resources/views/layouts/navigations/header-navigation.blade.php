@php
    $fsmAccount = $user ?? $data ?? null;
    $fsmName = trim((string) ($fsmAccount->user_real_name ?? 'Admin'));
    $fsmType = trim((string) ($fsmAccount->user_type ?? 'Administrator'));
    $fsmIsEmployee = ($fsmEmployee ?? false) || strcasecmp($fsmType, 'Employee') === 0;
@endphp
<header id="app-header" class="fsm-topbar">
    <div class="fsm-topbar-start">
        <button id="mobile-menu" class="fsm-mobile-menu" type="button" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
            <i class="fsm-icon-menu" aria-hidden="true"></i>
        </button>
        <a class="fsm-mobile-logo" href="{{ $fsmIsEmployee ? route('my-dtr.index') : route('employee') }}" aria-label="Payroll home">
            <img src="{{ asset('client_logo/logo.png') }}" alt="Teves Petroleum">
        </a>
        <div class="fsm-topbar-title">
            <strong>{{ $title ?? 'Payroll' }}</strong>
            <span>{{ $fsmIsEmployee ? 'Employee portal' : 'Payroll workspace' }}</span>
        </div>
    </div>
    <div class="fsm-topbar-end">
        <div class="dropdown fsm-profile">
            <button class="fsm-profile-trigger" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open profile menu">
                <span class="fsm-profile-avatar">{{ strtoupper(substr($fsmName ?: 'A', 0, 1)) }}</span>
                <span class="fsm-profile-name">{{ $fsmName ?: 'Admin' }}</span>
                <i class="fsm-icon-chevron-down" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end fsm-profile-menu">
                <div class="fsm-profile-summary">
                    <strong>{{ $fsmName ?: 'Admin' }}</strong>
                    <small>{{ $fsmType }}</small>
                </div>
                @if($fsmIsEmployee)
                    <a class="dropdown-item" href="{{ route('my-profile.index') }}"><i class="fsm-icon-user-round" aria-hidden="true"></i> My Profile</a>
                @endif
                <a class="dropdown-item" href="{{ route('logout-user') }}"><i class="fsm-icon-log-out" aria-hidden="true"></i> Logout</a>
            </div>
        </div>
    </div>
</header>
