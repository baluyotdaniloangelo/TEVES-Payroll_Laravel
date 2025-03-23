
   <!-- Page level plugins -->
	<script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
	<script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
	<script type="text/javascript">

	/*Load Branch*/
	LoadBranch();
	function LoadBranch() {		
			
		$("#branch_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#branch_list_logs');
		
		/*Clear Employee Selection List*/
		//$("#employee_list_logs option").remove();
		
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
	
	<!--Load Table for Regular Logs-->
	$(function () {
				var EmployeeList = $('#EmployeeAllowanceLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeAllowanceLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'allowance_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'allowance_description', className: "text-left"},
							{data: 'allowance_type', className: "text-left"},
							{data: 'allowance_amount', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
							{data: 'branch_name', className: "text-left"},
							{data: 'department_name', className: "text-left"},
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
	$("#submit_allowance_logs_details").click(function(event){
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_id_regular_logsError').text('');
					$('#allowance_dateError').text('');		
					$('#allowance_dateeError').text('');	
					$('#breaktime_endError').text('');		

			document.getElementById('EmployeeAllowanceLogsForm').className = "g-2 needs-validation was-validated";
			
			let allowance_logs_id 			= document.getElementById("submit_allowance_logs_details").value;
			let branch_idx 					= $('#branch_list_logs option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
			let employee_idx 				= $('#employee_list_logs option[value="' + $('#employee_idx').val() + '"]').attr('data-id');
			let allowance_date 				= $("input[name=allowance_date]").val();
			let allowance_description				= $("input[name=allowance_description]").val();
			let allowance_type 				= $("#allowance_type").val();	  
			let allowance_amount			= $("input[name=allowance_amount]").val();
				  
			  $.ajax({
				url: "/submit_employee_allowance_logs_information",
				type:"POST",
				data:{
						allowance_logs_id:allowance_logs_id,
						employee_idx:employee_idx,
						branch_idx:branch_idx,
						allowance_date:allowance_date,
						allowance_description:allowance_description,
						allowance_type:allowance_type,
						allowance_amount:allowance_amount,
						_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#allowance_dateError').text('');	

					var table = $("#EmployeeAllowanceLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Allowance Logs successfully "+response.success);
					$('#success-alert-modal').modal('show');
					
					$("#employee_list_logs option").remove();
			
					ResetEmployeeAllowanceLogsForm();
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				/*Required Item Show Status*/
					let branch_name = $("input[name=branch_idx]").val();					
					if(error.responseJSON.errors.branch_idx=='validation.required'){
							
							if(branch_name==''){
								$('#branch_idxError').text('Please select Branch');
							}else{
								
								$('#branch_idxError').html("Incorrect Branch <b>"+branch_name+"</b>");
							}
							
							document.getElementById("branch_idx").value = "";
							document.getElementById('branch_idxError').className = "invalid-feedback";
					
					}						

					let employee_name = $("input[name=employee_idx]").val();					
					if(error.responseJSON.errors.employee_idx=='validation.required'){
							
							if(employee_name==''){
								$('#employee_idxError').text('Please select Employee');
							}else{
								
								$('#employee_idxError').html("Incorrect Employee <b>"+employee_name+"</b>");
							}
							
							document.getElementById("employee_idx").value = "";
							document.getElementById('employee_idxError').className = "invalid-feedback";
					
					}	

					
					/*let allowance_name = $("input[name=allowance_idx]").val();					
					if(error.responseJSON.errors.allowance_idx=='validation.required'){
							
							if(allowance_name==''){
								$('#allowance_idxError').text('Please select Allowance');
							}else{
								
								$('#allowance_idxError').html("Incorrect Allowance <b>"+allowance_name+"</b>");
							}
							
							document.getElementById("allowance_idx").value = "";
							document.getElementById('allowance_idxError').className = "invalid-feedback";
					
					}*/				
				
				if(error.responseJSON.errors.allowance_date=="validation.required"){
							  
				  $('#allowance_dateError').html("Date is Required");
				  document.getElementById('allowance_dateError').className = "invalid-feedback";
				  
				}else if(error.responseJSON.errors.allowance_date=="validation.unique"){
				
					$('#allowance_dateError').html("Employee Logs already exist on the selected Date");
					document.getElementById('allowance_dateError').className = "invalid-feedback";
				
				}
				
				if(error.responseJSON.errors.allowance_amount=="validation.required"){
							  
				  $('#allowance_amountError').html("Amount is Required");
				  document.getElementById('allowance_amountError').className = "invalid-feedback";
				  
				}

				//$('#InvalidModal').modal('toggle');				
					alert("Invalid Input");
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','#edit_employee_allowance_logs',function(){
			
			event.preventDefault();
			let employeeallowancelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeAllowanceLogsInformation') }}",
				type:"POST",
				data:{
				  employeeallowancelogsID:employeeallowancelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("submit_allowance_logs_details").value = employeeallowancelogsID;

					/*Set Details*/
					document.getElementById("branch_idx").value 			= response[0].branch_code+" - "+response[0].branch_name;
					LoadEmployee();
					document.getElementById("employee_idx").value 			= response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name;
					//LoadAllowanceType();
					document.getElementById("allowance_type").value 		= response[0].allowance_type;
					document.getElementById("allowance_description").value 	= response[0].allowance_description;
					document.getElementById("allowance_amount").value 		= response[0].allowance_amount;	
					
					$('#modal_title_action_employee_logs').html("Edit Employee's Allowance");
					$('#employee_allowance_logs_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','#delete_employee_allowance_logs',function(){
			
			event.preventDefault();
			let employeeallowancelogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeAllowanceLogsInformation') }}",
				type:"POST",
				data:{
				  employeeallowancelogsID:employeeallowancelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteEmployeeAllowanceLogConfirmed").value = employeeallowancelogsID;
	
					$('#delete_allowance_date').html(response[0].allowance_date);
					$('#delete_allowance_amount').html(response[0].allowance_amount);
					$('#delete_allowance_description').html(response[0].allowance_description);
					$('#delete_employee_allowance_logs_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					
					$('#EmployeeAllowanceLogDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteEmployeeAllowanceLogConfirmed',function(){
			
			event.preventDefault();
			
			let employeeallowancelogsID = document.getElementById("deleteEmployeeAllowanceLogConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteEmployeeAllowanceLog') }}",
				type:"POST",
				data:{
				  employeeallowancelogsID:employeeallowancelogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					var table = $("#EmployeeAllowanceLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Allowance Log Information successfully Deleted");
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
  
  	function ResetEmployeeAllowanceLogsForm(){
			
			event.preventDefault();
			$('#EmployeeAllowanceLogsForm')[0].reset();
			
			$('#modal_title_action_employee').html("Add Employee's Allowance");
			
			document.getElementById('EmployeeAllowanceLogsForm').className = "g-3 needs-validation";
			
			document.getElementById("submit_allowance_logs_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#allowance_dateError').text('');
			document.getElementById('allowance_dateError').className = "valid-feedback";
			document.getElementById('allowance_date').className = "form-control";
			
			$('#allowance_amountError').text('');	
			document.getElementById('allowance_amountError').className = "valid-feedback";
			document.getElementById('allowance_amount').className = "form-control";
			
			$('#branch_idxError').text('');	
			document.getElementById('branch_idxError').className = "valid-feedback";
			document.getElementById('branch_idx').className = "form-control";
			
			$('#employee_idxError').text('');	
			document.getElementById('employee_idxError').className = "valid-feedback";
			document.getElementById('employee_idx').className = "form-control";
			
			//$("#department_list_regular_logs span").remove();
			//$("#employee_list_logs span").remove();
			
	}	
  
  
/*
	document.getElementById("update_allowance_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_allowance_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_branch_idx").addEventListener('change', doThing_site_management);
	document.getElementById("update_department_idx").addEventListener('change', doThing_site_management);
	
	document.getElementById("update_allowance_amountError").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_start").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_end").addEventListener('change', doThing_site_management);
	document.getElementById("update_log_out").addEventListener('change', doThing_site_management);
*/	
	function doThing_employee_management(){

			let siteID = document.getElementById("update-site").value;
			
			//let allowance_date 			= $("input[name=update_allowance_date]").val();
			let allowance_date 	= $("input[name=update_allowance_date]").val();
			let branch_idx 			= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			let allowance_amountError 		= $("input[name=update_allowance_amountError]").val();
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
						allowance_amountError_value 	= response[0].allowance_amountError || '';
						breaktime_start_value 		= response[0].breaktime_start || '';
						breaktime_end_value 		= response[0].breaktime_end || '';
						log_out_value 		= response[0].log_out || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].allowance_date===allowance_date &&
						  response[0].allowance_date===allowance_date &&
						  response[0].branch_idx==branch_idx &&
						  response[0].department_idx==department_idx  &&
						  response[0].department_idx==department_idx  &&
						  allowance_amountError_value===allowance_amountError  &&
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