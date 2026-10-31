<!-- Sidebar -->
<div class="sidebar payroll-sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="sidebar-vertical">
                <li class="menu-title"><span>Payroll Operations</span></li>
                <li class="submenu">
                    <a href="#"><i class="la la-clock-o"></i><span>Attendance</span><span class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ ($active_link ?? '') === 'employee_attendance_logs' ? 'active' : '' }}" href="{{ route('employee_attendance_logs') }}">Employee Attendance Logs</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'attendance_dtr_plotting' ? 'active' : '' }}" href="{{ route('attendance-import.index') }}">DTR Plotting</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'saved_dtr_entries' ? 'active' : '' }}" href="{{ route('attendance-import.saved') }}">Saved DTR Entries</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'drivers_attendance_logs' ? 'active' : '' }}" href="{{ route('drivers_attendance_logs') }}">Drivers Attendance Logs</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="#"><i class="la la-money"></i><span>Compensation</span><span class="menu-arrow"></span></a>
                    <ul>
                        <li><a class="{{ ($active_link ?? '') === 'employee_allowance_logs' ? 'active' : '' }}" href="{{ route('employee_allowance_logs') }}">Allowance Logs</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'employee_deduction_logs' ? 'active' : '' }}" href="{{ route('employee_deduction_logs') }}">Deduction Logs</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'cut_off' ? 'active' : '' }}" href="{{ route('CutOff') }}">Cut-Off</a></li>
                    </ul>
                </li>

                <li class="menu-title"><span>Administration</span></li>
                <li><a class="{{ ($active_link ?? '') === 'employee' ? 'active' : '' }}" href="{{ route('employee') }}"><i class="la la-users"></i><span>Employees</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'branch' ? 'active' : '' }}" href="{{ route('branch') }}"><i class="la la-building"></i><span>Branches</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'user' ? 'active' : '' }}" href="{{ route('user') }}"><i class="la la-user"></i><span>Users</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'holiday' ? 'active' : '' }}" href="{{ route('holiday') }}"><i class="la la-calendar"></i><span>Holidays</span></a></li>
                <li><a class="{{ ($active_link ?? '') === 'deduction_type' ? 'active' : '' }}" href="{{ route('deduction_type') }}"><i class="la la-list"></i><span>Deduction Types</span></a></li>

                <li class="menu-title"><span>Payroll</span></li>
                <li class="submenu">
                    <a href="#"><i class="la la-calculator"></i><span>Payroll</span><span class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('CreatePayroll') }}">Create Payroll</a></li>
                        <li><a class="{{ ($active_link ?? '') === 'drivers_payroll' ? 'active' : '' }}" href="{{ route('drivers_payroll.index') }}">Drivers Payroll</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
