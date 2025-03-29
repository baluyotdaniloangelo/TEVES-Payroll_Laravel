<script type="text/javascript">
	<!--Load Table for Leave Logs-->
	$(function () {
				var EmployeeList = $('#LeaveLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeLeaveLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'date_of_leave', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'reason_of_leave', className: "text-left"},
							//{data: 'leave_amount', className: "text-left"},
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
	
	/*Load Employee for Leave Input*/
	function leave_LoadEmployee() {		
	
	/*Clear Employee Value*/
	
		var branchID 		= $('#branch_list_logs option[value="' + $('#leave_branch_idx').val() + '"]').attr('data-id');
		
		$("#leave_employee_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#leave_employee_list_logs');

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
							
							$('#leave_employee_list_logs option:last').after(""+
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
	
	<!--Save New Logs-->
	$("#submit_leave_details").click(function(event){
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_id_regular_logsError').text('');
					$('#date_of_leaveError').text('');		
					$('#breaktime_endError').text('');		

				document.getElementById('EmployeeLeavesLogsForm').className = "g-2 needs-validation was-validated";
			
				let employee_leave_logs_id 			= document.getElementById("submit_leave_details").value;
				let branch_idx 						= $('#leave_branch_list_logs option[value="' + $('#leave_branch_idx').val() + '"]').attr('data-id');
				let employee_idx 					= $('#leave_employee_list_logs option[value="' + $('#leave_employee_idx').val() + '"]').attr('data-id');
				let date_of_leave 					= $("input[name=date_of_leave]").val();
				let reason_of_leave					= $("input[name=reason_of_leave]").val();

			  $.ajax({
				url: "/submit_employee_leave_logs_information",
				type:"POST",
				data:{
						branch_idx:branch_idx,
						employee_idx:employee_idx,
						employee_leave_logs_id:employee_leave_logs_id,
						date_of_leave:date_of_leave,
						reason_of_leave:reason_of_leave,
						_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#date_of_leaveError').text('');	

					var table = $("#LeaveLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Logs successfully "+response.success);
					$('#success-alert-modal').modal('show');
					
					$("#leave_employee_list_logs option").remove();
			
					ResetEmployeeLeavesLogsForm();
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				/*Required Item Show Status*/
				
				if(error.responseJSON.errors.branch_idx=="validation.required"){
							  
				  $('#leave_branch_idxError').html("Branch is Required");
				  document.getElementById('leave_branch_idxError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.employee_idx=="validation.required"){
							  
				  $('#leave_employee_idxError').html("Employee is Required");
				  document.getElementById('leave_employee_idxError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.date_of_leave=="validation.required"){
							  
				  $('#date_of_leaveError').html("Date is Required");
				  document.getElementById('date_of_leaveError').className = "invalid-feedback";
				  
				}else if(error.responseJSON.errors.date_of_leave=="validation.unique"){
				
					document.getElementById("date_of_leave").value = '';
					$('#date_of_leaveError').html("Employee Leave already exist on the selected Date");
					document.getElementById('date_of_leaveError').className = "invalid-feedback";
				
				}
				
				if(error.responseJSON.errors.reason_of_leave=="validation.required"){
							  
				  $('#reason_of_leaveError').html("Reason of Leave is Required");
				  document.getElementById('reason_of_leaveError').className = "invalid-feedback";
				  
				}
				
				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','#edit_employee_leave_logs',function(){
			
			event.preventDefault();
			let employeeLeavelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeLeaveLogsInformation') }}",
				type:"POST",
				data:{
				  employeeLeavelogsID:employeeLeavelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("submit_leave_details").value = employeeLeavelogsID;

					/*Set Details*/
					document.getElementById("leave_branch_idx").value 		= response[0].branch_code+" - "+response[0].branch_name;
					leave_LoadEmployee();
					document.getElementById("leave_employee_idx").value 		= response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name;
					
					//document.getElementById("deleteEmployeeLogConfirmed").value = employeeLeavelogsID;
					
					document.getElementById("date_of_leave").value 	= response[0].date_of_leave;
					document.getElementById("reason_of_leave").value 			= response[0].reason_of_leave;
					
					$('#modal_title_action_employee_leave_logs').html('Edit Leave Logs');
					$('#employee_leave_logs_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','#delete_employee_leave_logs',function(){
			
			event.preventDefault();
			let employeeLeavelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeLeaveLogsInformation') }}",
				type:"POST",
				data:{
				  employeeLeavelogsID:employeeLeavelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteEmployeeLeaveLogConfirmed").value = employeeLeavelogsID;
					
	
					$('#delete_employee_leave_logs_date').html(response[0].date_of_leave);
					$('#delete_employee_leave_logs_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					$('#delete_employee_leave_logs_reason').html(response[0].reason_of_leave);
					
					$('#EmployeeLeaveLogDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteEmployeeLeaveLogConfirmed',function(){
			
			event.preventDefault();
			
			let employeelogID = document.getElementById("deleteEmployeeLeaveLogConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteEmployeeLeaveLog') }}",
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
					
					var table = $("#LeaveLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Leave Succefully Deleted");
					$('#EmployeeLeaveLogDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					}
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });
				
		
	  });
  
  	function ResetEmployeeLeavesLogsForm(){
			
			event.preventDefault();
			$('#EmployeeLeavesLogsForm')[0].reset();
			
			$('#modal_title_action_employee').html('Add Employee');
			
			document.getElementById('EmployeeLeavesLogsForm').className = "g-3 needs-validation";
			
			document.getElementById("submit_leave_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#date_of_leaveError').text('');
			document.getElementById('date_of_leaveError').className = "valid-feedback";
			document.getElementById('date_of_leave').className = "form-control";
			
			$('#reason_of_leaveError').text('');	
			document.getElementById('reason_of_leaveError').className = "valid-feedback";
			document.getElementById('reason_of_leave').className = "form-control";
			

			$("#leave_employee_list_logs span").remove();
			
	}	


</script>