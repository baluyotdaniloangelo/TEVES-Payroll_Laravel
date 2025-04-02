<script type="text/javascript">
	<!--Load Table for Leave Logs-->
	$(function () {
				var EmployeeList = $('#DriversLogsListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getDriversLogsList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"}, 	
							{data: 'travel_date', className: "text-left"},
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name', className: "text-left"},
							{data: 'volume', render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
							{data: 'rate_per_liter',  render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
							{data: 'gross_amount',  render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
							{data: 'trip_pay',  render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
							{data: 'plate_number', className: "text-left"},
							{data: 'loading_terminal', className: "text-left"},
							{data: 'destination', className: "text-left"}
							
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
	function Drivers_LoadList() {		
	
	/*Clear Employee Value*/
	
		var branchID 		= $('#drivers_branch_list_logs option[value="' + $('#drivers_branch_idx').val() + '"]').attr('data-id');
		
		$("#drivers_list_logs option").remove();
		$('<option style="display: none;"></option>').appendTo('#drivers_list_logs');

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
							
							$('#drivers_list_logs option:last').after(""+
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
	$("#submit_drivers_logs_details").click(function(event){
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_id_regular_logsError').text('');
					$('#travel_dateError').text('');	
					$('#plate_numberError').text('');		
					$('#loading_terminalError').text('');	
					$('#destinationError').text('');		
					$('#volumeError').text('');		
					$('#rate_per_literError').text('');	

				document.getElementById('DriversLogsForm').className = "g-2 needs-validation was-validated";
			
				let drivers_logs_id 			= document.getElementById("submit_drivers_logs_details").value;
				
				let branch_idx 					= $('#drivers_branch_list_logs option[value="' + $('#drivers_branch_idx').val() + '"]').attr('data-id');
				let employee_idx 				= $('#drivers_list_logs option[value="' + $('#drivers_idx').val() + '"]').attr('data-id');			
				let travel_date 				= $("input[name=travel_date]").val();
				let plate_number				= $("input[name=plate_number]").val();
				let loading_terminal			= $("input[name=loading_terminal]").val();
				let destination					= $("input[name=destination]").val();
				let volume						= $("input[name=volume]").val();
				let rate_per_liter				= $("input[name=rate_per_liter]").val();

			  $.ajax({
				url: "/submit_drivers_logs_information",
				type:"POST",
				data:{
						branch_idx:branch_idx,
						employee_idx:employee_idx,
						drivers_logs_id:drivers_logs_id,
						travel_date:travel_date,
						plate_number:plate_number,
						loading_terminal:loading_terminal,
						destination:destination,
						volume:volume,
						rate_per_liter:rate_per_liter,
						_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#employee_id_regular_logsError').text('');
					$('#travel_dateError').text('');	
					$('#plate_numberError').text('');		
					$('#loading_terminalError').text('');	
					$('#destinationError').text('');		
					$('#volumeError').text('');			
					$('#rate_per_literError').text('');		

					var table = $("#DriversLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Driver's Logs successfully "+response.success);
					$('#success-alert-modal').modal('show');
					
					$("#drivers_list_logs option").remove();
			
					ResetDriversLogsForm();
					
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
							  
				  $('#drivers_idxError').html("Employee is Required");
				  document.getElementById('drivers_idxError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.travel_date=="validation.required"){
							  
				  $('#travel_dateError').html("Date is Required");
				  document.getElementById('travel_dateError').className = "invalid-feedback";
				  
				}else if(error.responseJSON.errors.travel_date=="validation.unique"){
				
					document.getElementById("travel_date").value = '';
					$('#travel_dateError').html("Employee Leave already exist on the selected Date");
					document.getElementById('travel_dateError').className = "invalid-feedback";
				
				}
				
				if(error.responseJSON.errors.plate_number=="validation.required"){
							  
				  $('#plate_numberError').html("Plate Number is Required");
				  document.getElementById('plate_numberError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.loading_terminal=="validation.required"){
							  
				  $('#loading_terminalError').html("Loading Terminal is Required");
				  document.getElementById('loading_terminalError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.destination=="validation.required"){
							  
				  $('#destinationError').html("Destination is Required");
				  document.getElementById('destinationError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.volume=="validation.required"){
							  
				  $('#volumeError').html("Volume is Required");
				  document.getElementById('volumeError').className = "invalid-feedback";
				  
				}
				
				if(error.responseJSON.errors.rate_per_liter=="validation.required"){
							  
				  $('#rate_per_literError').html("Rate is Required");
				  document.getElementById('rate_per_literError').className = "invalid-feedback";
				  
				}
				
				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','#edit_driver_logs',function(){
			
			event.preventDefault();
			let DriverlogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DriversLogsInformation') }}",
				type:"POST",
				data:{
				  DriverlogsID:DriverlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("submit_drivers_logs_details").value = DriverlogsID;

					/*Set Details*/
					document.getElementById("drivers_branch_idx").value 	= response[0].branch_code+" - "+response[0].branch_name;
					Drivers_LoadList();
					document.getElementById("drivers_idx").value 			= response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name;		
					document.getElementById("travel_date").value 			= response[0].travel_date;
					document.getElementById("plate_number").value 			= response[0].plate_number;	
					document.getElementById("loading_terminal").value 		= response[0].loading_terminal;
					document.getElementById("destination").value 			= response[0].destination;
					document.getElementById("volume").value 				= response[0].volume;
					document.getElementById("rate_per_liter").value 		= response[0].rate_per_liter;
					
					$('#modal_title_action_drivers_logs').html('Edit Drivers Logs');
					$('#drivers_logs_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','#delete_driver_logs',function(){
			
			event.preventDefault();
			let DriverlogsID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DriversLogsInformation') }}",
				type:"POST",
				data:{
				  DriverlogsID:DriverlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteDriversLogConfirmed").value = DriverlogsID;
					
					$('#delete_drivers_logs_complete_name').html(response[0].travel_date);
					$('#delete_drivers_logs_date').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
						
					$('#DriversLogDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteDriversLogConfirmed',function(){
			
			event.preventDefault();
			
			let DriverlogsID = document.getElementById("deleteDriversLogConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteDriversLog') }}",
				type:"POST",
				data:{
				  DriverlogsID:DriverlogsID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#DriversLogsListDatatable").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#modal_success_details').html("Employee Leave Succefully Deleted");
					$('#EmployeeLeaveLogDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					document.getElementById("deleteDriversLogConfirmed").value = 0;
					
					}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
				
		
	  });
  
  	function ResetDriversLogsForm(){
			
			event.preventDefault();
			$('#DriversLogsForm')[0].reset();
			
			$('#modal_title_action_drivers_logs').html("Add Driver's Logs");
			
			document.getElementById('DriversLogsForm').className = "g-3 needs-validation";
			
			document.getElementById("submit_drivers_logs_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#travel_dateError').text('');
			document.getElementById('travel_dateError').className = "valid-feedback";
			document.getElementById('travel_date').className = "form-control";
			
			$('#plate_numberError').text('');	
			document.getElementById('plate_numberError').className = "valid-feedback";
			document.getElementById('plate_number').className = "form-control";
			

			$("#drivers_list_logs span").remove();
			
	}	
</script>