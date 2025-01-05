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
}
else if (Request::is('employee-attendance-logs')){
?>
@include('layouts.employee_attendance_logs_script')
<?php
}
?>
	</body>
</html>