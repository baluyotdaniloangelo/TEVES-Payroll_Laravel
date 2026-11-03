@include('layouts.header');
    <body class="fsm-theme">
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
            @include('layouts.navigations.sidebar-navigation')
            <div id="main-shell">
                @include('layouts.navigations.header-navigation')
                @yield('content')
            </div>
       </div>
		<!-- /Main Wrapper --> 
		
		
@include('layouts.footer')
<script src="{{ asset('fsm/js/payroll-shell.js') }}" defer></script>
<?php
if (Request::is('employee')){
?>
@include('layouts.employee_script')
<?php
}
else if (Request::is('branch')){
?>
@include('layouts.branch_script')
@include('layouts.department_script')
<?php
}
else if (Request::is('cut-off')){
?>
@include('layouts.cut_off_script')
<?php
}
else if (Request::is('holiday')){
?>
@include('layouts.holiday_script')
<?php
}else if (Request::is('deduction_type')){
?>
@include('layouts.deduction_type_script')
<?php
}
else if (Request::is('drivers-attendance-logs')){
?>
@include('layouts.drivers_attendance_logs_script')
@include('layouts.employee_attendance_import_logs_script')
<?php
}
else if (Request::is('employee-attendance-logs')){
?>
@include('layouts.employee_attendance_logs_script')
@include('layouts.employee_attendance_leaves_script')
@include('layouts.employee_attendance_import_logs_script')
<?php
}
else if (Request::is('employee-deduction-logs')){
?>
@include('layouts.employee_deduction_logs_script')
<?php
}else if (Request::is('employee-allowance-logs')){
?>
@include('layouts.employee_allowance_logs_script')
<?php
}
else if (Request::is('create-payroll')){
?>
@include('layouts.create_payroll_script')
<?php
}else if (Request::is('user')){
?>
<body class="">
@include('layouts.user_script')
<?php
}
?>
	</body>
</html>
