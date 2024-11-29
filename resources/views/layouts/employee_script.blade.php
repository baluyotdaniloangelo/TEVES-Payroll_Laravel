
   <!-- Page level plugins -->
  <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script type="text/javascript">

			<!--Load Table-->
			$(function () {
			
				var EmployeeList = $('#EmployeeListDatatable').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					scrollCollapse: true,
					scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('getEmployeeList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},       
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_last_name', className: "text-left"},
							{data: 'employee_first_name', className: "text-left"},
							{data: 'employee_middle_name', className: "text-left"},
							{data: 'employee_extension_name', className: "text-left"},
							{data: 'branch_name', className: "text-left"},
							{data: 'department_name', className: "text-left"},
							{data: 'employee_position', className: "text-left"},
							{data: 'employee_status', className: "text-left"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});


		//autoAdjustColumns(EmployeeList);

		 /*Adjust Table Column
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	*/

			});	

		

	<!--Save New Site-->
	$("#submit_employee_details").click(function(event){
			
			event.preventDefault();
			
								/*'employee_number',
								'employee_last_name',
								'employee_first_name',
								'employee_middle_name',
								'employee_extension_name',
								'employee_birthday',
								'employee_position',
								'employee_picture',
								'employee_phone',
								'employee_email',
								'branch_idx',
								'department_idx',
								'time_in',
								'break_time',
								'time_out',
								'restday_monday',
								'restday_tuesday',
								'restday_wednesday',
								'restday_thursday',
								'restday_friday',
								'restday_saturday',
								'restday_sunday',
								*/
			
					/*Reset Warnings*/
					//$('#employee_last_nameError').text('');
					//$('#employee_first_nameError').text('');				  
					//$('#site_cut_offError').text('');
					//$('#employee_middle_nameError').text('');
					//$('#employee_extension_nameError').text('');
					//$('#employee_birthdayError').text('');
					//$('#employee_positionError').text('');

			document.getElementById('Employeeform').className = "g-2 needs-validation was-validated";
			
			/*#0*/let employee_id 						= document.getElementById("submit_employee_details").value;

			/*#1*/let employee_number 					= $("input[name=employee_number]").val();

			/*#2*/let employee_last_name 				= $("input[name=employee_last_name]").val();
			/*#3*/let employee_first_name 				= $("input[name=employee_first_name]").val();
			/*#4*/let employee_middle_name 				= $("input[name=employee_middle_name]").val();
			/*#5*/let employee_extension_name 			= $("input[name=employee_extension_name]").val();
			
			/*#6*/let employee_birthday 				= $("input[name=employee_birthday]").val();
			/*#7*/let employee_position 				= $("input[name=employee_position]").val();
			/*#8*/let employee_phone 					= $("input[name=employee_phone]").val();
			/*#9*/let employee_email 					= $("input[name=employee_email]").val();
			
			/*#10*/let branch_idx 						= $("#division_list option[value='" + $('#branch_idx').val() + "']").attr('data-id');
			/*#11*/let department_idx					= $("#company_list option[value='" + $('#department_idx').val() + "']").attr('data-id');
			
			/*#12*/let time_in 							= $("input[name=time_in]").val();
			/*#13*/let break_time 						= $("input[name=break_time]").val();
			/*#14*/let time_out 						= $("input[name=time_out]").val();
			
			/*#15*/let restday_monday 					= $("input[name=restday_monday]").val();
			/*#16*/let restday_tuesday 					= $("input[name=restday_tuesday]").val();
			/*#17*/let restday_wednesday 				= $("input[name=restday_wednesday]").val();
			/*#18*/let restday_thursday 				= $("input[name=restday_thursday]").val();
			/*#19*/let restday_friday 					= $("input[name=restday_friday]").val();
			/*#20*/let restday_saturday 				= $("input[name=restday_saturday]").val();
			/*#21*/let restday_sunday 					= $("input[name=restday_sunday]").val();
			  alert(restday_friday);
			  $.ajax({
				url: "/submit_employee_information",
				type:"POST",
				data:{
				  /*#0*/employee_id:employee_id,
				  /*#1*/employee_number:employee_number,
				  /*#2*/employee_last_name:employee_last_name,
				  /*#3*/employee_first_name:employee_first_name,
				  /*#4*/employee_middle_name:employee_middle_name,
				  /*#5*/employee_extension_name:employee_extension_name,
				  /*#6*/employee_birthday:employee_birthday,
				  /*#7*/employee_position:employee_position,
				  /*#8*/employee_phone:employee_phone,
				  /*#9*/employee_email:employee_email,
				  /*#10*/branch_idx:branch_idx,
				  /*#11*/department_idx:department_idx,
				  /*#12*/time_in:time_in,
				  /*#13*/break_time:break_time,
				  /*#14*/time_out:time_out,
				  /*#15*/restday_monday:restday_monday,
				  /*#16*/restday_tuesday:restday_tuesday,
				  /*#17*/restday_wednesday:restday_wednesday,
				  /*#18*/restday_thursday:restday_thursday,
				  /*#19*/restday_friday:restday_friday,
				  /*#20*/restday_saturday:restday_saturday,
				  /*#21*/restday_sunday:restday_sunday,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#employee_last_nameError').text('');	
					$('#employee_first_nameError').text('');		

					var table = $("#siteList").DataTable();
				    table.ajax.reload(null, false);					
					
					ResetEmployeeForm();
					
					$('#branch_idxError').text('');
					document.getElementById('branch_idxError').className = "invalid-tooltip";	
					document.getElementById('branch_idx').className = "form-control";
					
					$('#department_idxError').text('');
					document.getElementById('department_idxError').className = "invalid-tooltip";	
					document.getElementById('department_idx').className = "form-control";
					
					document.getElementById('Employeeform').className = "g-3 needs-validation";
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				
				/*Required Item Show Status*/
				
				
				
				if(error.responseJSON.errors.employee_last_name=="validation.required"){
							  
				  $('#employee_last_nameError').html("Last Name is Required");
				  document.getElementById('employee_last_nameError').className = "invalid-tooltip";
				  
				}
				
				if(error.responseJSON.errors.employee_first_name=="validation.required"){
							  
				  $('#employee_first_nameError').html("First Name is Required");
				  document.getElementById('employee_first_nameError').className = "invalid-tooltip";
				  
				}
				
				if(error.responseJSON.errors.time_in=="validation.required"){
							  
				  $('#time_inError').html("Time In is Required");
				  document.getElementById('time_inError').className = "invalid-tooltip";
				  
				}
				
				if(error.responseJSON.errors.break_time=="validation.required"){
							  
				  $('#break_timeError').html("Break Time is Required");
				  document.getElementById('break_timeError').className = "invalid-tooltip";
				  
				}
				/*
				if(branch_idx==0||branch_idx==undefined){
					
					$('#branch_idxError').text('Please Select a Division');
					document.getElementById('branch_idxError').className = "invalid-tooltip";	
					document.getElementById('branch_idx').className = "form-control is-invalid";
				
				}else{
					
					$('#branch_idxError').text('');
					document.getElementById('branch_idxError').className = "invalid-tooltip";	
					document.getElementById('branch_idx').className = "form-control";
					
				}
				
				if(department_idx==0||department_idx==undefined){
					
					$('#department_idxError').text('Please Select a Company');
					document.getElementById('department_idxError').className = "invalid-tooltip";	
					document.getElementById('department_idx').className = "form-control is-invalid";
				
				}else{
					
					$('#department_idxError').text('');
					document.getElementById('department_idxError').className = "invalid-tooltip";	
					document.getElementById('department_idx').className = "form-control";
					
				}*/
			
				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

	<!--Select Site For Update-->
	$('body').on('click','#editSite',function(){
			
			event.preventDefault();
			let siteID = $(this).data('id');
			
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
					
					document.getElementById("update-site").value = siteID;
					document.getElementById("update-site").disabled = true;
					
					$('#CloseManual').attr('data-bs-target','#UpdateSiteModal');

					/*Set Details*/
					document.getElementById("update_building_id").value = response[0].building_id;
					document.getElementById("update_employee_last_name").value = response[0].employee_last_name;
					document.getElementById("update_employee_first_name").value = response[0].employee_first_name;

					document.getElementById("update_branch_idx").value = response[0].division_code + " - " + response[0].division_name;
					document.getElementById("update_department_idx").value = response[0].company_name;
					
					//document.getElementById("update_site_cut_off").value = response[0].site_cut_off;
					
					document.getElementById("update_employee_middle_name").value = response[0].employee_middle_name;
					document.getElementById("update_employee_birthday").value = response[0].employee_birthday;
					document.getElementById("update_employee_extension_name").value = response[0].employee_extension_name;
					document.getElementById("update_employee_position").value = response[0].employee_position;
					
					/*Reset Warnings*/
					$('#update_branch_idxError').text('');
					$('#update_department_idxError').text('');
					
					$('#UpdateSiteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
/*
	document.getElementById("update_employee_last_name").addEventListener('change', doThing_site_management);
	document.getElementById("update_employee_first_name").addEventListener('change', doThing_site_management);
	document.getElementById("update_branch_idx").addEventListener('change', doThing_site_management);
	document.getElementById("update_department_idx").addEventListener('change', doThing_site_management);
	
	document.getElementById("update_employee_middle_name").addEventListener('change', doThing_site_management);
	document.getElementById("update_employee_extension_name").addEventListener('change', doThing_site_management);
	document.getElementById("update_employee_birthday").addEventListener('change', doThing_site_management);
	document.getElementById("update_employee_position").addEventListener('change', doThing_site_management);
*/	
	function doThing_employee_management(){

			let siteID = document.getElementById("update-site").value;
			
			let employee_last_name 			= $("input[name=update_employee_last_name]").val();
			let employee_first_name 	= $("input[name=update_employee_first_name]").val();
			let branch_idx 			= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			let employee_middle_name 		= $("input[name=update_employee_middle_name]").val();
			let employee_extension_name 				= $("input[name=update_employee_extension_name]").val();
			let employee_birthday 				= $("input[name=update_employee_birthday]").val();
			let employee_position 				= $("input[name=update_employee_position]").val();
	
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
						employee_middle_name_value 	= response[0].employee_middle_name || '';
						employee_extension_name_value 		= response[0].employee_extension_name || '';
						employee_birthday_value 		= response[0].employee_birthday || '';
						employee_position_value 		= response[0].employee_position || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].employee_last_name===employee_last_name &&
						  response[0].employee_first_name===employee_first_name &&
						  response[0].branch_idx==branch_idx &&
						  response[0].department_idx==department_idx  &&
						  response[0].department_idx==department_idx  &&
						  employee_middle_name_value===employee_middle_name  &&
						  employee_extension_name_value===employee_extension_name  &&
						  employee_birthday_value===employee_birthday  &&
						  employee_position_value===employee_position 
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
	
	$("#update-site").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					let siteID = document.getElementById("update-site").value;
					$('#update_employee_last_nameError').text('');
					$('#update_employee_last_nameError').text('');
					$('#update_employee_first_nameError').text('');				  
					//$('#update_site_cut_offError').text('');
					$('#update_employee_middle_nameError').text('');
					$('#update_employee_extension_nameError').text('');
					$('#update_employee_birthdayError').text('');
					$('#update_employee_positionError').text('');

			document.getElementById('updateEmployeeform').className = " g-2 needs-validation was-validated";
			
			let building_id				= $("input[name=update_building_id]").val();
			
			let employee_last_name 			= $("input[name=update_employee_last_name]").val();
			let employee_first_name 	= $("input[name=update_employee_first_name]").val();
			
			let branch_idx 			= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			
			//let site_cut_off 			= $("input[name=update_site_cut_off]").val();
			let employee_middle_name 		= $("input[name=update_employee_middle_name]").val();
			let employee_extension_name 				= $("input[name=update_employee_extension_name]").val();
			let employee_birthday 				= $("input[name=update_employee_birthday]").val();
			let employee_position 				= $("input[name=update_employee_position]").val();
			
			  $.ajax({
				url: "/update_site_post",
				type:"POST",
				data:{
				  SiteID:siteID,
				  building_id:building_id,
				  employee_last_name:employee_last_name,
				  employee_first_name:employee_first_name,
				  branch_idx:branch_idx,
				  department_idx:department_idx,
				  //site_cut_off:site_cut_off,
				  employee_middle_name:employee_middle_name,
				  employee_extension_name:employee_extension_name,
				  employee_birthday:employee_birthday,
				  employee_position:employee_position,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#employee_last_nameError').text('');	
					$('#employee_first_nameError').text('');				  
				  
					var table = $("#siteList").DataTable();
				    table.ajax.reload(null, false);
					
					$('#update_branch_idxError').text('');
					document.getElementById('update_branch_idxError').className = "invalid-tooltip";	
					document.getElementById('update_branch_idx').className = "form-control";
				  
					$('#update_department_idxError').text('');
					document.getElementById('update_department_idxError').className = "invalid-tooltip";	
					document.getElementById('update_department_idx').className = "form-control";
					
					$('#updateEmployeeform')[0].reset();
					$('#UpdateSiteModal').modal('toggle');	
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				if(error.responseJSON.errors.employee_last_name=="The building code has already been taken."){
							  
				  $('#update_employee_last_nameError').html("<b>"+ employee_last_name +"</b> has already been taken.");
				  document.getElementById('update_employee_last_nameError').className = "invalid-tooltip";
				  document.getElementById('update_employee_last_name').className = "form-control is-invalid";
				  $('#update_employee_last_name').val("");
				  
				}else{
					
				  $('#update_employee_last_nameError').text(error.responseJSON.errors.employee_last_name);
				  document.getElementById('update_employee_last_nameError').className = "invalid-tooltip";		
				
				}
				
				if(error.responseJSON.errors.employee_first_name=="The building description has already been taken."){
							  
				  $('#update_employee_first_nameError').html("<b>"+ employee_first_name +"</b> has already been taken.");
				  document.getElementById('update_employee_first_nameError').className = "invalid-tooltip";
				  document.getElementById('update_employee_first_name').className = "form-control is-invalid";
				  $('#update_employee_first_name').val("");
				  
				}else{
					
				  $('#update_employee_first_nameError').text(error.responseJSON.errors.employee_first_name);
				  document.getElementById('update_employee_first_nameError').className = "invalid-tooltip";		
				
				}
				
				
				if(branch_idx==0||branch_idx==undefined){
					
					$('#update_branch_idxError').text('Please Select a Division');
					document.getElementById('update_branch_idxError').className = "invalid-tooltip";	
					document.getElementById('update_branch_idx').className = "form-control is-invalid";
				
				}else{     
					
					$('#update_branch_idxError').text('');
					document.getElementById('update_branch_idxError').className = "invalid-tooltip";	
					document.getElementById('update_branch_idx').className = "form-control";
					
				}
				
				if(department_idx==0||department_idx==undefined){
					
					$('#update_department_idxError').text('Please Select a Company');
					document.getElementById('update_department_idxError').className = "invalid-tooltip";	
					document.getElementById('update_department_idx').className = "form-control is-invalid";
				
				}else{
					
					$('#update_department_idxError').text('');
					document.getElementById('update_department_idxError').className = "invalid-tooltip";	
					document.getElementById('update_department_idx').className = "form-control";
					
				}
				
				$('#InvalidModal').modal('toggle');				  
				  
				}
			   });
	  });
	  
	<!--Site Deletion Confirmation-->
	$('body').on('click','#deleteSite',function(){
			
			event.preventDefault();
			let siteID = $(this).data('id');
			
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
					
					document.getElementById("deleteSiteConfirmed").value = siteID;
					
					$('#employee_last_name_delete_info').html(response[0].employee_last_name);
					$('#employee_first_name_delete_info').html(response[0].employee_first_name);
					
					$('#employee_last_name_delete_confirmed_info').html(response[0].employee_first_name);
					$('#employee_first_name_delete_confirmed_info').html(response[0].employee_last_name);
					
					$('#SiteDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	  <!--Site Confirmed For Deletion-->
	$('body').on('click','#deleteSiteConfirmed',function(){
			
			event.preventDefault();

			let siteID = document.getElementById("deleteSiteConfirmed").value;
			
			  $.ajax({
				url: "/delete_site_confirmed",
				type:"POST",
				data:{
				  siteID:siteID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#SiteDeleteModalConfirmed').modal('toggle');
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#siteList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });
				
		
	  });
  
  	function ResetEmployeeForm(){
			
			event.preventDefault();
			$('#Employeeform')[0].reset();
			
			document.getElementById('Employeeform').className = "g-3 needs-validation";
			
			document.getElementById("submit_employee_details").value = 0;
			/*Hide the Clear Button*/
			
			$('#branch_idxError').text('');
			document.getElementById('branch_idxError').className = "valid-feedback";	
			document.getElementById('branch_idx').className = "form-control";
					
			$('#department_idxError').text('');
			document.getElementById('department_idxError').className = "valid-feedback";	
			document.getElementById('department_idx').className = "form-control";
			
			$('#employee_last_nameError').text('');
			document.getElementById('employee_last_nameError').className = "valid-feedback";
			document.getElementById('employee_last_name').className = "form-control";
			
			$('#employee_first_nameError').text('');	
			document.getElementById('employee_first_nameError').className = "valid-feedback";
			document.getElementById('employee_first_name').className = "form-control";
			
	}	
  </script>