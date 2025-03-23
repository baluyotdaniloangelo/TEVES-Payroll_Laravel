@include('layouts.header');
    <body>
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			@include('layouts.navigations.header-navigation')     
			@include('layouts.navigations.sidebar-navigation') 
			@include('layouts.navigations.two-col-bar')		
				@yield('content')
			@include('layouts.navigations.layout-settings')	
       </div>
		<!-- /Main Wrapper --> 
		
		
@include('layouts.footer')
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
else if (Request::is('holiday')){
?>
@include('layouts.holiday_script')
<?php
}else if (Request::is('deduction_type')){
?>
@include('layouts.deduction_type_script')
<?php
}
else if (Request::is('employee-attendance-logs')){
?>
@include('layouts.employee_attendance_logs_script')
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
?>
	</body>
</html>