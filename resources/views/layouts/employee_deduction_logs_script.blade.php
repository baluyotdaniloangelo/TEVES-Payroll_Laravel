
   <!-- Page level plugins -->
  <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script type="text/javascript">

	/*Load Branch*/
	LoadBranch();
	function LoadBranch() {		
	
		// $("#employee_list_logs span").remove();
		// $('<span style="display: none;"></span>').appendTo('#employee_list_logs');
		
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
		

	LoadDeductionType();
	function LoadDeductionType() {		

		$("#deduction_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#deduction_list_logs');

			  $.ajax({
				url: "{{ route('getDeductionTypeList_for_selection') }}",
				type:"POST",
				data:{
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){
							var deduction_id = response[i].deduction_id;			
							var deduction_description = response[i].deduction_description;
							$('#deduction_list_logs option:last').after("<option label='"+deduction_description+"' data-id='"+deduction_id+"' value='"+deduction_description+"'>");	
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
	//document.getElementById("employee_idx").value 		= "";
	
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
	
	<!--Load Table for Regular Logs-->
	$(function () {
				var EmployeeList = $('#EmployeeDeductionLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeDeductionLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'deduction_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'deduction_description', className: "text-left"},
							{data: 'deduction_amount', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},{data: 'branch_name', className: "text-left"},
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
	$("#submit_deduction_logs_details").click(function(event){
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_id_regular_logsError').text('');
					$('#deduction_dateError').text('');		
					$('#deduction_dateeError').text('');	
					$('#breaktime_endError').text('');		

			document.getElementById('EmployeeDeductionLogsForm').className = "g-2 needs-validation was-validated";
			
			let deduction_logs_id 			= document.getElementById("submit_deduction_logs_details").value;
			let branch_idx 					= $('#branch_list_logs option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
			let employee_idx 				= $('#employee_list_logs option[value="' + $('#employee_idx').val() + '"]').attr('data-id');
			let deduction_idx 				= $('#deduction_list_logs option[value="' + $('#deduction_idx').val() + '"]').attr('data-id');
			let deduction_date 				= $("input[name=deduction_date]").val();
			let deduction_amount			= $("input[name=deduction_amount]").val();
				  
			  $.ajax({
				url: "/submit_employee_deduction_logs_information",
				type:"POST",
				data:{
						deduction_logs_id:deduction_logs_id,
						employee_idx:employee_idx,
						branch_idx:branch_idx,
						deduction_idx:deduction_idx,
						deduction_date:deduction_date,
						deduction_amount:deduction_amount,
						_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#deduction_dateError').text('');	

					var table = $("#EmployeeDeductionLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Deduction Logs successfully "+response.success);
					$('#success-alert-modal').modal('show');
					
					$("#employee_list_logs option").remove();
			
					ResetEmployeeDeductionLogsForm();
					
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

					
					let deduction_name = $("input[name=deduction_idx]").val();					
					if(error.responseJSON.errors.deduction_idx=='validation.required'){
							
							if(deduction_name==''){
								$('#deduction_idxError').text('Please select Deduction');
							}else{
								
								$('#deduction_idxError').html("Incorrect Deduction <b>"+deduction_name+"</b>");
							}
							
							document.getElementById("deduction_idx").value = "";
							document.getElementById('deduction_idxError').className = "invalid-feedback";
					
					}				
				
				if(error.responseJSON.errors.deduction_date=="validation.required"){
							  
				  $('#deduction_dateError').html("Date is Required");
				  document.getElementById('deduction_dateError').className = "invalid-feedback";
				  
				}else if(error.responseJSON.errors.deduction_date=="validation.unique"){
				
					$('#deduction_dateError').html("Employee Logs already exist on the selected Date");
					document.getElementById('deduction_dateError').className = "invalid-feedback";
				
				}
				
				if(error.responseJSON.errors.deduction_amount=="validation.required"){
							  
				  $('#deduction_amountError').html("Amount is Required");
				  document.getElementById('deduction_amountError').className = "invalid-feedback";
				  
				}

				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','#edit_employee_deduction_logs',function(){
			
			event.preventDefault();
			let employeedeductionlogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeDeductionLogsInformation') }}",
				type:"POST",
				data:{
				  employeedeductionlogsID:employeedeductionlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("submit_deduction_logs_details").value = employeedeductionlogsID;

					/*Set Details*/
					document.getElementById("branch_idx").value 		= response[0].branch_code+" - "+response[0].branch_name;
					LoadEmployee();
					document.getElementById("employee_idx").value 		= response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name;
					LoadDeductionType();
					document.getElementById("deduction_idx").value 		= response[0].deduction_description;
					document.getElementById("deduction_amount").value 	= response[0].deduction_amount;	
					
					$('#modal_title_action_employee_logs').html("Edit Employee's Deduction");
					$('#employee_deduction_logs_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','#delete_employee_deduction_logs',function(){
			
			event.preventDefault();
			let employeedeductionlogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeDeductionLogsInformation') }}",
				type:"POST",
				data:{
				  employeedeductionlogsID:employeedeductionlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteEmployeeDeductionLogConfirmed").value = employeedeductionlogsID;
	
					$('#delete_deduction_date').html(response[0].deduction_date);
					$('#delete_deduction_amount').html(response[0].deduction_amount);
					$('#delete_deduction_description').html(response[0].deduction_description);
					$('#delete_employee_deduction_logs_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					
					$('#EmployeeDeductionLogDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteEmployeeDeductionLogConfirmed',function(){
			
			event.preventDefault();
			
			let employeedeductionlogsID = document.getElementById("deleteEmployeeDeductionLogConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteEmployeeDeductionLog') }}",
				type:"POST",
				data:{
				  employeedeductionlogsID:employeedeductionlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					var table = $("#EmployeeDeductionLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Deduction Log Information successfully Deleted");
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
  
  	function ResetEmployeeDeductionLogsForm(){
			
			event.preventDefault();
			$('#EmployeeDeductionLogsForm')[0].reset();
			
			$('#modal_title_action_employee').html("Add Employee's Deduction");
			
			document.getElementById('EmployeeDeductionLogsForm').className = "g-3 needs-validation";
			
			document.getElementById("submit_deduction_logs_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#deduction_dateError').text('');
			document.getElementById('deduction_dateError').className = "valid-feedback";
			document.getElementById('deduction_date').className = "form-control";
			
			$('#deduction_amountError').text('');	
			document.getElementById('deduction_amountError').className = "valid-feedback";
			document.getElementById('deduction_amount').className = "form-control";
			
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
	document.getElementById("update_deduction_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_deduction_date").addEventListener('change', doThing_site_management);
	document.getElementById("update_branch_idx").addEventListener('change', doThing_site_management);
	document.getElementById("update_department_idx").addEventListener('change', doThing_site_management);
	
	document.getElementById("update_deduction_amountError").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_start").addEventListener('change', doThing_site_management);
	document.getElementById("update_breaktime_end").addEventListener('change', doThing_site_management);
	document.getElementById("update_log_out").addEventListener('change', doThing_site_management);
*/	
	function doThing_employee_management(){

			let siteID = document.getElementById("update-site").value;
			
			//let deduction_date 			= $("input[name=update_deduction_date]").val();
			let deduction_date 	= $("input[name=update_deduction_date]").val();
			let branch_idx 			= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			let deduction_amountError 		= $("input[name=update_deduction_amountError]").val();
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
						deduction_amountError_value 	= response[0].deduction_amountError || '';
						breaktime_start_value 		= response[0].breaktime_start || '';
						breaktime_end_value 		= response[0].breaktime_end || '';
						log_out_value 		= response[0].log_out || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].deduction_date===deduction_date &&
						  response[0].deduction_date===deduction_date &&
						  response[0].branch_idx==branch_idx &&
						  response[0].department_idx==department_idx  &&
						  response[0].department_idx==department_idx  &&
						  deduction_amountError_value===deduction_amountError  &&
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