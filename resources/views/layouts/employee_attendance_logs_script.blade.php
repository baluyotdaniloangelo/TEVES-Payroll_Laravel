
   <!-- Page level plugins -->
  <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script type="text/javascript">

	/*Load Branch*/
	LoadBranch();
	function LoadBranch() {		

		$("#branch_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#branch_list_logs');
		
		$("#leave_branch_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#leave_branch_list_logs');
		
		$("#drivers_branch_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#drivers_branch_list_logs');
			
			  $.ajax({
				url: "{{ route('getBranchList_for_selection') }}",
				type:"POST",
				data:{
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){
							var branch_id = response[i].branch_id;						
							var branch_name = response[i].branch_name;				
							var branch_code = response[i].branch_code;
							$('#branch_list_logs option:last').after(""+
							"<option label='"+branch_code+" - "+branch_name+"' data-id='"+branch_id+"' value='"+branch_code+" - "+branch_name+"'>" +
							"");	
							
							$('#leave_branch_list_logs option:last').after(""+
							"<option label='"+branch_code+" - "+branch_name+"' data-id='"+branch_id+"' value='"+branch_code+" - "+branch_name+"'>" +
							"");	
							
							$('#drivers_branch_list_logs option:last').after(""+
							"<option label='"+branch_code+" - "+branch_name+"' data-id='"+branch_id+"' value='"+branch_code+" - "+branch_name+"'>" +
							"");	
					}			
				  }else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	}
		
	/*Get List of Employee After Selecting Branch*/
	function LoadEmployee() {		
	
	/*Clear Employee Value*/
	
		var branchID 		= $('#branch_list_logs option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
		
		$("#employee_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#employee_list_logs');

			  $.ajax({
				url: "{{ route('getEmployeeList_for_selection') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){

							var employee_id = response[i].employee_id;	
							var employee_full_name = response[i].employee_full_name;				
							// var employee_rate = response[i].employee_rate;
							$('#employee_list_logs option:last').after(""+
							"<option label='"+employee_full_name+"' data-id='"+employee_id+"' value='"+employee_full_name+"'>" +
							"");	
							
					}			
				  }else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	}	
	

	
	/*Load Default Employee Logs*/
	function Load_Employee_Default_Logs(){

			event.preventDefault();
			let employee_idx = $('#employee_list_logs option[value="' + $('#employee_idx').val() + '"]').attr('data-id');

			  $.ajax({
				url: "{{ route('EmployeeInformation') }}",
				type:"POST",
				data:{
				  employeeID:employee_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					let attendance_date 			= $("input[name=attendance_date]").val();
					
					document.getElementById("log_in").value 			= attendance_date + ' ' + response[0].time_in;
					document.getElementById("breaktime_start").value 	= attendance_date + ' ' + response[0].break_time_in;
					document.getElementById("breaktime_end").value 		= attendance_date + ' ' + response[0].break_time_out;
					document.getElementById("log_out").value 			= attendance_date + ' ' + response[0].time_out;
			
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
			   
    }
	
	<!--Load Table for Regular Logs-->
	$(function () {
				var EmployeeList = $('#EmployeeRegularLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeRegularLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'attendance_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'total_regular_hours', className: "text-left"},
							{data: 'total_night_differential_hours', className: "text-left"},
							{data: 'branch_name', className: "text-left"},
							{data: 'department_name', className: "text-left"},
							{data: 'log_in', className: "text-left"},
							{data: 'log_out', className: "text-left"},
							{data: 'breaktime_start', className: "text-left"},
							{data: 'breaktime_end', className: "text-left"},
							{data: 'basic_pay', className: "text-left"},
							{data: 'night_differential_pay', className: "text-left"},
							{data: 'regular_holiday_pay', className: "text-left"},
							{data: 'special_holiday_pay', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});
		autoAdjustColumns(EmployeeList);
		// Adjust Table Column
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	
	});	

	<!--Load Table for Regular Overtime Logs-->
	$(function () {
				var EmployeeList = $('#EmployeeRegularOTLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeRegularOTLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'attendance_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'total_regular_hours', className: "text-left"},
							{data: 'total_night_differential_hours', className: "text-left"},
							{data: 'branch_name', className: "text-left"},
							{data: 'department_name', className: "text-left"},
							{data: 'log_in', className: "text-left"},
							{data: 'log_out', className: "text-left"},
							{data: 'breaktime_start', className: "text-left"},
							{data: 'breaktime_end', className: "text-left"},
							{data: 'overtime_pay', className: "text-left"},
							{data: 'night_differential_pay', className: "text-left"},
							{data: 'regular_holiday_pay', className: "text-left"},
							{data: 'special_holiday_pay', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});
		autoAdjustColumns(EmployeeList);
		// Adjust Table Column
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	
	});			

	<!--Load Table for Restday Logs-->
	$(function () {
				var EmployeeList = $('#EmployeeRestDayOTLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeRestDayOTLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'attendance_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'total_regular_hours', className: "text-left"},
							{data: 'total_night_differential_hours', className: "text-left"},
							{data: 'branch_name', className: "text-left"},
							{data: 'department_name', className: "text-left"},
							{data: 'log_in', className: "text-left"},
							{data: 'log_out', className: "text-left"},
							{data: 'breaktime_start', className: "text-left"},
							{data: 'breaktime_end', className: "text-left"},
							{data: 'day_off_pay', className: "text-left"},
							{data: 'night_differential_pay', className: "text-left"},
							{data: 'regular_holiday_pay', className: "text-left"},
							{data: 'special_holiday_pay', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});
		autoAdjustColumns(EmployeeList);
		// Adjust Table Column
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	
	});	

	<!--Save New Logs-->
	$("#submit_logs_details").click(function(event){
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_id_regular_logsError').text('');
					$('#attendance_dateError').text('');	
					$('#breaktime_endError').text('');		

			document.getElementById('EmployeeAttendanceLogsForm').className = "g-2 needs-validation was-validated";
			/*#0*/let employee_logs_id 					= document.getElementById("submit_logs_details").value;
			/*#1*/let employee_idx 						= $('#employee_list_logs option[value="' + $('#employee_idx').val() + '"]').attr('data-id');
			/*#2*/let attendance_date 					= $("input[name=attendance_date]").val();
			/*#3*/let log_in							= $("input[name=log_in]").val();
			/*#4*/let breaktime_start 					= $("input[name=breaktime_start]").val();
			/*#5*/let breaktime_end 					= $("input[name=breaktime_end]").val();
			/*#6*/let log_out 							= $("input[name=log_out]").val();		
			/*#7*/let override_default_shift			= $("#override_default_shift").val();
			/*#8*/let overtime_status			= $("#overtime_status").val();
				  let branch_idx 						= $('#branch_list_logs option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
				  //alert(employee_idx);
			  $.ajax({
				url: "/submit_employee_regular_logs_information",
				type:"POST",
				data:{
				  /*#0*/employee_idx:employee_idx,
				  /*#1*/employee_logs_id:employee_logs_id,
				  /*#2*/attendance_date:attendance_date,
				  /*#3*/log_in:log_in,
				  /*#4*/breaktime_start:breaktime_start,
				  /*#5*/breaktime_end:breaktime_end,
				  /*#6*/log_out:log_out,
				  /*#6*/override_default_shift:override_default_shift,
				  /*#6*/overtime_status:overtime_status,
						branch_idx:branch_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#attendance_dateError').text('');	

					var table = $("#EmployeeRegularLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	

					var table = $("#EmployeeRegularOTLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					var table = $("#EmployeeRestDayOTLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Logs successfully "+response.success);
					$('#success-alert-modal').modal('show');
					
					$("#employee_list_logs option").remove();
			
					ResetEmployeeAttendanceLogsForm();
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				/*Required Item Show Status*/
				
				
				if(error.responseJSON.errors.branch_idx=="validation.required"){
							  
				  $('#branch_idxError').html("Branch is Required");
				  document.getElementById('branch_idxError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.employee_idx=="validation.required"){
							  
				  $('#employee_idxError').html("Employee is Required");
				  document.getElementById('employee_idxError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.attendance_date=="validation.required"){
							  
				  $('#attendance_dateError').html("Date is Required");
				  document.getElementById('attendance_dateError').className = "invalid-feedback";
				  
				}else if(error.responseJSON.errors.attendance_date=="validation.unique"){
				
					$('#attendance_dateError').html("Employee Logs already exist on the selected Date");
					document.getElementById('attendance_dateError').className = "invalid-feedback";
				
				}
				
				if(error.responseJSON.errors.log_in=="validation.required"){
							  
				  $('#log_inError').html("Time In is Required");
				  document.getElementById('log_inError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.log_out=="validation.required"){
							  
				  $('#log_outError').html("Time Out is Required");
				  document.getElementById('log_outError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.breaktime_start=="validation.required"){
							  
				  $('#breaktime_startError').html("Break Time Start is Required");
				  document.getElementById('breaktime_startError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.breaktime_end=="validation.required"){
							  
				  $('#breaktime_endError').html("Break Time End is Required");
				  document.getElementById('breaktime_endError').className = "invalid-feedback";
				  
				}

				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','#edit_employee_logs',function(){
			
			event.preventDefault();
			let employeelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeLogsInformation') }}",
				type:"POST",
				data:{
				  employeelogsID:employeelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("submit_logs_details").value = employeelogsID;

					/*Set Details*/
					document.getElementById("branch_idx").value 		= response[0].branch_code+" - "+response[0].branch_name;
					LoadEmployee();
					document.getElementById("employee_idx").value 		= response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name;
					
					document.getElementById("deleteEmployeeLogConfirmed").value = employeelogsID;
					var _log_type = response[0].log_type;
					if(_log_type=='Regular'){
						//var log_type = 'Regular';
						document.getElementById("overtime_status").value 		= 'No';
					}else if(_log_type=='RegularOT'){
						//var log_type = 'Overtime';
						document.getElementById("overtime_status").value 		= 'Yes';
					}else{
						//var log_type = 'Day-off';
						document.getElementById("overtime_status").value 		= 'No';
					}
					
					document.getElementById("attendance_date").value 	= response[0].attendance_date;
					
					document.getElementById("log_in").value 			= response[0].log_in;
					document.getElementById("breaktime_start").value 	= response[0].breaktime_start
					document.getElementById("breaktime_end").value 		= response[0].breaktime_end;
					document.getElementById("log_out").value 			= response[0].log_out;
					
	// /*#0*/let employee_logs_id 					= document.getElementById("submit_logs_details").value;
			// /*#1*/let employee_idx 						= $('#employee_list_logs option[value="' + $('#employee_idx').val() + '"]').attr('data-id');
			// /*#2*/let attendance_date 					= $("input[name=attendance_date]").val();
			// /*#3*/let log_in							= $("input[name=log_in]").val();
			// /*#4*/let breaktime_start 					= $("input[name=breaktime_start]").val();
			// /*#5*/let breaktime_end 					= $("input[name=breaktime_end]").val();
			// /*#6*/let log_out 							= $("input[name=log_out]").val();		
			// /*#7*/let override_default_shift			= $("#override_default_shift").val();
			// /*#8*/let overtime_status			= $("#overtime_status").val();
				  // let branch_idx 							= $('#branch_list_logs option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
				  
					// $('#delete_employee_logs_date').html(response[0].attendance_date);
					// $('#delete_employee_logs_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					// $('#delete_log_type').html(log_type);
					
					
					$('#modal_title_action_employee_logs').html('Edit Attendance Logs');
					$('#employee_regular_logs_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','#delete_employee_logs',function(){
			
			event.preventDefault();
			let employeelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeLogsInformation') }}",
				type:"POST",
				data:{
				  employeelogsID:employeelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteEmployeeLogConfirmed").value = employeelogsID;
					var _log_type = response[0].log_type;
					if(_log_type=='Regular'){
						var log_type = 'Regular';
					}else if(_log_type=='RegularOT'){
						var log_type = 'Overtime';
					}else{
						var log_type = 'Day-off';
					}
	
					$('#delete_employee_logs_date').html(response[0].attendance_date);
					$('#delete_employee_logs_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					$('#delete_log_type').html(log_type);
					
					$('#EmployeeLogDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteEmployeeLogConfirmed',function(){
			
			event.preventDefault();
			
			let employeelogID = document.getElementById("deleteEmployeeLogConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteEmployeeLog') }}",
				type:"POST",
				data:{
				  employeelogID:employeelogID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					var table = $("#EmployeeRegularLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	

					var table = $("#EmployeeRegularOTLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					var table = $("#EmployeeRestDayOTLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Log Information Succefully Deleted");
					$('#EmployeeLogDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					}
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });
				
		
	  });
  
  	function ResetEmployeeAttendanceLogsForm(){
			
			event.preventDefault();
			$('#EmployeeAttendanceLogsForm')[0].reset();
			
			$('#modal_title_action_employee').html('Add Employee');
			
			document.getElementById('EmployeeAttendanceLogsForm').className = "g-3 needs-validation";
			
			document.getElementById("submit_logs_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#attendance_dateError').text('');
			document.getElementById('attendance_dateError').className = "valid-feedback";
			document.getElementById('attendance_date').className = "form-control";
			
			$('#attendance_dateError').text('');	
			document.getElementById('attendance_dateError').className = "valid-feedback";
			document.getElementById('attendance_date').className = "form-control";
			
			$('#employee_id_regular_logsError').text('');	
			document.getElementById('employee_id_regular_logsError').className = "valid-feedback";
			document.getElementById('employee_id_regular_logs').className = "form-control";
			
			$("#department_list_regular_logs span").remove();
			$("#employee_list_logs span").remove();
			
	}	

	
/*
	document.getElementById("update_attendance_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_attendance_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_branch_idx").addEventListener('change', doThing_site_management);
	document.getElementById("update_department_idx").addEventListener('change', doThing_site_management);
	
	document.getElementById("update_log_inError").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_start").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_end").addEventListener('change', doThing_site_management);
	document.getElementById("update_log_out").addEventListener('change', doThing_site_management);
*/	
	function doThing_employee_management(){

			let siteID = document.getElementById("update-site").value;
			
			//let attendance_date 			= $("input[name=update_attendance_date]").val();
			let attendance_date 	= $("input[name=update_attendance_date]").val();
			let branch_idx 			= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			let log_inError 		= $("input[name=update_log_inError]").val();
			let breaktime_start 				= $("input[name=update_breaktime_start]").val();
			let breaktime_end 				= $("input[name=update_breaktime_end]").val();
			let log_out 				= $("input[name=update_log_out]").val();
	
				$.ajax({
				url: "/site_info",
				type:"POST",
				data:{
				  siteID:siteID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
						
						/*Below items to Convert to Empty instead of NULL*/
						log_inError_value 	= response[0].log_inError || '';
						breaktime_start_value 		= response[0].breaktime_start || '';
						breaktime_end_value 		= response[0].breaktime_end || '';
						log_out_value 		= response[0].log_out || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].attendance_date===attendance_date &&
						  response[0].attendance_date===attendance_date &&
						  response[0].branch_idx==branch_idx &&
						  response[0].department_idx==department_idx  &&
						  response[0].department_idx==department_idx  &&
						  log_inError_value===log_inError  &&
						  breaktime_start_value===breaktime_start  &&
						  breaktime_end_value===breaktime_end  &&
						  log_out_value===log_out 
					  ){
							
							document.getElementById("update-site").disabled = true;
							$('#loading_data').hide();
							
						}else{
							
							document.getElementById("update-site").disabled = false;
							$('#loading_data').hide();
							
						}
						
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				},
				beforeSend:function()
				{
					$('#loading_data').show();
				}
			   });	
    }
	
  </script>