@php
    $fsmEmployee = $fsmEmployee ?? (isset($user) && strcasecmp(trim((string) $user->user_type), 'Employee') === 0);
    $fsmGroups = $fsmEmployee ? [
        'Employee' => [
            ['my_dtr', 'my-dtr.index', 'My DTR', 'fsm-icon-calendar-days'],
            ['my_payroll', 'my-payroll.index', 'My Payroll', 'fsm-icon-wallet'],
            ['my_updates', 'my-updates.index', 'My Updates', 'fsm-icon-bell'],
            ['my_profile', 'my-profile.index', 'My Profile', 'fsm-icon-user-round'],
        ],
    ] : [
        'Attendance' => [
            ['employee_attendance_logs', 'employee_attendance_logs', 'Employee Attendance Logs', 'fsm-icon-clock-3'],
            ['attendance_dtr_plotting', 'attendance-import.index', 'DTR Plotting', 'fsm-icon-calendar-days'],
            ['saved_dtr_entries', 'attendance-import.saved', 'Saved DTR Entries', 'fsm-icon-book-open-check'],
            ['drivers_attendance_logs', 'drivers_attendance_logs', 'Drivers Attendance Logs', 'fsm-icon-truck'],
        ],
        'Compensation' => [
            ['employee_allowance_logs', 'employee_allowance_logs', 'Allowance Logs', 'fsm-icon-wallet'],
            ['employee_deduction_logs', 'employee_deduction_logs', 'Deduction Logs', 'fsm-icon-receipt-text'],
            ['cut_off', 'CutOff', 'Cut-Off', 'fsm-icon-calendar-clock'],
        ],
        'Administration' => [
            ['employee', 'employee', 'Employees', 'fsm-icon-users'],
            ['branch', 'branch', 'Branches', 'fsm-icon-building-2'],
            ['user', 'user', 'Users', 'fsm-icon-user-round'],
            ['holiday', 'holiday', 'Holidays', 'fsm-icon-calendar-days'],
            ['deduction_type', 'deduction_type', 'Deduction Types', 'fsm-icon-list'],
        ],
        'Payroll' => [
            ['create_payroll', 'CreatePayroll', 'Create Payroll', 'fsm-icon-calculator'],
            ['drivers_payroll', 'drivers_payroll.index', 'Drivers Payroll', 'fsm-icon-banknote'],
        ],
    ];
@endphp
<aside id="sidebar" class="fsm-sidenav" aria-label="Main navigation">
    <div class="sidebar-brand">
        <a class="fsm-sidebar-logo" href="{{ $fsmEmployee ? route('my-dtr.index') : route('employee') }}" aria-label="Payroll home">
            <img src="{{ asset('client_logo/logo.png') }}" alt="Teves Petroleum">
            <span class="sidebar-label">PAYROLL</span>
        </a>
        <button id="desktop-collapse" class="fsm-collapse" type="button" aria-label="Collapse sidebar" aria-expanded="true">
            <i class="fsm-icon-chevron-right" aria-hidden="true"></i>
        </button>
    </div>
    <nav id="main-nav" class="fsm-main-nav" aria-label="Payroll navigation">
        @foreach($fsmGroups as $groupName => $links)
            @php $groupActive = collect($links)->contains(fn ($link) => ($active_link ?? '') === $link[0] || request()->routeIs($link[1])); @endphp
            <section class="fsm-nav-group" data-nav-group>
                <button class="sidebar-group-toggle" type="button" aria-expanded="{{ $groupActive ? 'true' : 'false' }}">
                    <span class="sidebar-group-label">{{ $groupName }}</span>
                    <i class="fsm-icon-chevron-down sidebar-chevron" aria-hidden="true"></i>
                </button>
                <ul class="fsm-nav-items" data-nav-items @if(!$groupActive) hidden @endif>
                    @foreach($links as $link)
                        @php $isActive = ($active_link ?? '') === $link[0] || request()->routeIs($link[1]); @endphp
                        <li>
                            <a href="{{ route($link[1]) }}" class="sidebar-item {{ $isActive ? 'active' : '' }}" @if($isActive) aria-current="page" @endif>
                                <i class="{{ $link[3] }}" aria-hidden="true"></i>
                                <span class="sidebar-label">{{ $link[2] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </nav>
</aside>
<div class="fsm-sidebar-backdrop" data-fsm-close></div>
