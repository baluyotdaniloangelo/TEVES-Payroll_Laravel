
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
					stateSave: false,
					layout: { topStart: 'pageLength', topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
					language: { emptyTable: 'No employees match the selected filters.', processing: 'Loading employees...' },
					//scrollCollapse: true,
					//scrollY: '500px',
					//scrollX: '100%',
					ajax: {
						url: "{{ route('getEmployeeList') }}",
						data: function (data) {
							data.branch_filter = $('#employee_branch_filter').val();
							data.department_filter = $('#employee_department_filter').val();
							data.status_filter = $('#employee_status_filter').val();
							data.type_filter = $('#employee_type_filter').val();
						}
					},
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},       
							{data: 'employee_number', className: "text-left"},
							{data: 'employee_full_name'},
							{data: 'branch_name', name: 'teves_branch_table.branch_name'},
							{data: 'department_name', name: 'teves_payroll_department_table.department_name'},
							{data: 'employee_position'},
							{data: 'employee_rate'},
							{data: 'employee_status'},
							{data: 'employment_type'},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});

				let employeeSearchTimer;
				$('#employee_search').on('input', function () {
					clearTimeout(employeeSearchTimer);
					const value = this.value;
					employeeSearchTimer = setTimeout(function () { EmployeeList.search(value).draw(); }, 250);
				});
				$('#employee_branch_filter, #employee_department_filter, #employee_status_filter, #employee_type_filter').on('change', function () { EmployeeList.draw(); });
				$('#clear_employee_filters').on('click', function () {
					$('#employee_search').val('');
					$('#employee_branch_filter, #employee_department_filter, #employee_status_filter, #employee_type_filter').val('');
					EmployeeList.search('').draw();
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

		
	//LoadDepartment_regular_logs();
	function LoadDepartment(selectedDepartment) {
	
		var branchID 			= $('#branch_list option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
		$('#department_idx').val('');
		$("#department_list option").remove();
		$('<option style="display: none;"></option>').appendTo('#department_list');
		if (!branchID) return;

			  $.ajax({
				url: "{{ route('getDepartmentList_for_selection') }}",
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
						
							var department_id = response[i].department_id;						
							var department_name = response[i].department_name;
	
							var departmentOption = new Option(department_name, department_name);
							departmentOption.dataset.id = department_id;
							$('#department_list').append(departmentOption);
							if (selectedDepartment && selectedDepartment === department_name) $('#department_idx').val(department_name);
							
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
	
	
	<!--Save New Site-->
	$("#submit_employee_details").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#employee_numberError').text('');
					$('#employee_last_nameError').text('');		
					$('#employee_first_nameeError').text('');	
					$('#employee_birthdayError').text('');		
					$('#employee_form_error_summary').addClass('d-none').empty();
					$('#Employeeform .is-invalid').removeClass('is-invalid');
					['employee_tin', 'employee_tax_status'].forEach(function(field) {
						$('#' + field + 'Error').text('');
						$('#' + field).removeClass('is-invalid');
					});

			document.getElementById('Employeeform').className = "g-2 needs-validation was-validated";
			
			/*#0*/let employee_id 						= document.getElementById("submit_employee_details").value;
			const employeeForm = document.getElementById('Employeeform');
			if (!employeeForm.checkValidity()) {
				employeeForm.classList.add('was-validated');
				employeeForm.reportValidity();
				return;
			}

			/*#1*/let employee_number 					= $("input[name=employee_number]").val();

			/*#2*/let employee_last_name 				= $("input[name=employee_last_name]").val();
			/*#3*/let employee_first_name 				= $("input[name=employee_first_name]").val();
			/*#4*/let employee_middle_name 				= $("input[name=employee_middle_name]").val();
			/*#5*/let employee_extension_name 			= $("input[name=employee_extension_name]").val();
			
			/*#6*/let employee_birthday 				= $("input[name=employee_birthday]").val();
			/*#7*/let employee_position 				= $("input[name=employee_position]").val();
				  let employee_status					= $("#employee_status").val();
				  let employment_type					= $("#employment_type").val();
				  let employee_night_diff_pay			= $("#employee_night_diff_pay").val();
				  let employee_rate						= $("#employee_rate").val();
			/*#8*/let employee_phone 					= $("input[name=employee_phone]").val();
			/*#9*/let employee_email 					= $("input[name=employee_email]").val();
			
			/*#10*///let branch_idx 						= $("#branch_idx").val();
				   var branch_idx 						= $('#branch_list option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
					
			/*#11*///let department_idx					= $("#department_idx").val();
				   var department_idx 					= $('#department_list option[value="' + $('#department_idx').val() + '"]').attr('data-id');
				if (!branch_idx || !department_idx) {
					const missing = !branch_idx ? 'Pumili ng branch mula sa listahan.' : 'Pumili ng department mula sa listahan.';
					$('#employee_form_error_summary').removeClass('d-none').text(missing);
					$('#' + (!branch_idx ? 'branch_idx' : 'department_idx')).addClass('is-invalid').trigger('focus');
					return;
				}
					
			/*#12*/let time_in 							= $("input[name=time_in]").val();
			/*#13*/let break_time_in 					= $("input[name=break_time_in]").val();
			/*#13*/let break_time_out 					= $("input[name=break_time_out]").val();
			
			/*#14*/let time_out 						= $("input[name=time_out]").val();
			if (break_time_out <= break_time_in) {
				$('#employee_form_error_summary').removeClass('d-none').text('Dapat mas huli ang break end kaysa break start.');
				$('#break_time_out').addClass('is-invalid').trigger('focus');
				return;
			}
			
			 /*#15*/var _restday_monday 				= $('.restday_monday:checked').val() || 'off';
					var restday_monday 					= (_restday_monday ==="on") ? "1":"0";
					
			 /*#16*/var _restday_tuesday 				= $('.restday_tuesday:checked').val() || 'off';
					var restday_tuesday 				= (_restday_tuesday ==="on") ? "1":"0";
					
			 /*#17*/var _restday_wednesday 				= $('.restday_wednesday:checked').val() || 'off';
					var restday_wednesday 				= (_restday_wednesday ==="on") ? "1":"0";
					
			 /*#18*/var _restday_thursday 				= $('.restday_thursday:checked').val() || 'off';
					var restday_thursday 				= (_restday_thursday ==="on") ? "1":"0";
					
			 /*#19*/var _restday_friday 				= $('.restday_friday:checked').val() || 'off';
					var restday_friday 					= (_restday_friday ==="on") ? "1":"0";
					
			 /*#20*/var _restday_saturday 				= $('.restday_saturday:checked').val() || 'off';
					var restday_saturday 				= (_restday_saturday ==="on") ? "1":"0";
					
			 /*#21*/var _restday_sunday 				= $('.restday_sunday:checked').val() || 'off';
					var restday_sunday 					= (_restday_sunday ==="on") ? "1":"0";
			
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
				  /*#7*/employee_status:employee_status,
				  /*#7*/employment_type:employment_type,
						employee_night_diff_pay:employee_night_diff_pay,
						employee_rate:employee_rate,
				  /*#8*/employee_phone:employee_phone,
				  /*#9*/employee_email:employee_email,
				  employee_tin:$('#employee_tin').val(),
				  employee_tax_status:$('#employee_tax_status').val(),
				  /*#10*/branch_idx:branch_idx,
				  /*#11*/department_idx:department_idx,
				  /*#12*/time_in:time_in,
				  /*#13*/break_time_in:break_time_in,
						break_time_out:break_time_out,
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
				  if(response) {
					  
					$('#employee_last_nameError').text('');	
					$('#employee_first_nameError').text('');		

					var table = $("#EmployeeListDatatable").DataTable();
				    table.ajax.reload(null, false);					
					
					$('#modal_success_details').html("Employee Information successfully "+response.success);
					$('#success-alert-modal').modal('show');
					$('#employee_details_modal').modal('hide');
					
					ResetEmployeeForm();
					
					//$('#branch_idxError').text('');
					//document.getElementById('branch_idxError').className = "invalid-tooltip";	
					//document.getElementById('branch_idx').className = "form-control";
					
					document.getElementById('Employeeform').className = "g-3 needs-validation";
					
				  }
				},
				error: function(error) {
					const errors = error.responseJSON && error.responseJSON.errors ? error.responseJSON.errors : {};
					const summary = $('#employee_form_error_summary').empty().removeClass('d-none');
					const messages = [];
					$('#Employeeform .is-invalid').removeClass('is-invalid');
					Object.keys(errors).forEach(function (field) {
						const input = $('#' + field);
						const message = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
						messages.push(message);
						if (input.length) {
							input.addClass('is-invalid');
							let feedback = $('#' + field + 'Error');
							if (!feedback.length) feedback = $('<div class="invalid-feedback"></div>').attr('id', field + 'Error').insertAfter(input);
							feedback.text(message).addClass('invalid-feedback').removeClass('valid-feedback');
						}
					});
					if (!messages.length) messages.push('Hindi na-save ang employee. Pakisubukan muli.');
					$('<strong>').text('Pakisuri ang mga sumusunod:').appendTo(summary);
					const list = $('<ul class="mb-0 mt-1"></ul>').appendTo(summary);
					messages.forEach(function (message) { $('<li>').text(message).appendTo(list); });
					$('#employee_details_modal .modal-body').scrollTop(0);
				  
				}
			   });
		
	  });

	<!--Select Employee For Update-->
	$('body').on('click','.js-edit-employee',function(event){
			
			event.preventDefault();
			ResetEmployeeForm();
			let employeeID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeInformation') }}",
				type:"POST",
				data:{
				  employeeID:employeeID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  if(response) {
					
					document.getElementById("submit_employee_details").value = employeeID;

					/*Set Details*/
					document.getElementById("employee_number").value 			= response[0].employee_number;
					
					document.getElementById("employee_last_name").value 		= response[0].employee_last_name;
					document.getElementById("employee_first_name").value 		= response[0].employee_first_name;
					document.getElementById("employee_middle_name").value 		= response[0].employee_middle_name;
					document.getElementById("employee_extension_name").value 	= response[0].employee_extension_name;
					document.getElementById("employee_birthday").value 			= response[0].employee_birthday;
					document.getElementById("employee_position").value 			= response[0].employee_position;
					document.getElementById("employee_status").value 			= response[0].employee_status;
					document.getElementById("employment_type").value 			= response[0].employment_type;
					document.getElementById("employee_night_diff_pay").value 	= response[0].employee_night_diff_pay;
					document.getElementById("employee_rate").value 				= response[0].employee_rate;
					document.getElementById("employee_phone").value 			= response[0].employee_phone;
					document.getElementById("employee_email").value 			= response[0].employee_email;
					document.getElementById("employee_tin").value = response[0].employee_tin || '';
					document.getElementById("employee_tax_status").value = response[0].employee_tax_status || '';
					
					document.getElementById("branch_idx").value					= response[0].branch_code+" - "+response[0].branch_name;
					LoadDepartment(response[0].department_name);
					document.getElementById("time_in").value 					= response[0].time_in;
					document.getElementById("break_time_in").value 				= response[0].break_time_in;
					document.getElementById("break_time_out").value 			= response[0].break_time_out;
					document.getElementById("time_out").value 					= response[0].time_out;
					
					if(response[0].restday_monday ==1){
						$( ".restday_monday" ).prop( "checked", true );
					}else{
						$( ".restday_monday" ).prop( "checked", false );
					}
					
					if(response[0].restday_tuesday ==1){
						$( ".restday_tuesday" ).prop( "checked", true );
					}else{
						$( ".restday_tuesday" ).prop( "checked", false );
					}
					
					if(response[0].restday_wednesday ==1){
						$( ".restday_wednesday" ).prop( "checked", true );
					}else{
						$( ".restday_wednesday" ).prop( "checked", false );
					}
					
					if(response[0].restday_thursday ==1){
						$( ".restday_thursday" ).prop( "checked", true );
					}else{
						$( ".restday_thursday" ).prop( "checked", false );
					}
					
					if(response[0].restday_friday ==1){
						$( ".restday_friday" ).prop( "checked", true );
					}else{
						$( ".restday_friday" ).prop( "checked", false );
					}
					
					if(response[0].restday_saturday ==1){
						$( ".restday_saturday" ).prop( "checked", true );
					}else{
						$( ".restday_saturday" ).prop( "checked", false );
					}
					
					if(response[0].restday_sunday ==1){
						$( ".restday_sunday" ).prop( "checked", true );
					}else{
						$( ".restday_sunday" ).prop( "checked", false );
					}
					
					$('#modal_title_action_employee').html('Edit Employee');
					$('#employee_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });
	  
	<!--Employee Deletion Confirmation-->
	$('body').on('click','.js-delete-employee',function(event){
			
			event.preventDefault();
			let employeeID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('EmployeeInformation') }}",
				type:"POST",
				data:{
				  employeeID:employeeID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					document.getElementById("deleteEmployeeConfirmed").value = employeeID;
					
					$('#delete_employee_position').html(response[0].employee_position);
					$('#delete_employee_complete_name').html(response[0].employee_last_name+", "+response[0].employee_first_name+" "+response[0].employee_middle_name+" "+response[0].employee_extension_name);
					
					$('#EmployeeDeleteModal').modal('show');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
	  });

	<!--Employee Confirmed For Deletion-->
	$('body').on('click','#deleteEmployeeConfirmed',function(event){
			
			event.preventDefault();
			
			let employeeID = document.getElementById("deleteEmployeeConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteEmployee') }}",
				type:"POST",
				data:{
				  employeeID:employeeID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					var table = $("#EmployeeListDatatable").DataTable();
				    table.ajax.reload(null, false);
					
					$('#modal_success_details').html("Employee Information Succefully Deleted");
					$('#EmployeeDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					}
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });
				
		
	  });
  
  	function ResetEmployeeForm(){
			
			$('#Employeeform')[0].reset();
			$('#employee_form_error_summary').addClass('d-none').empty();
			$('#Employeeform .is-invalid').removeClass('is-invalid');
			$('#department_list').empty().append('<option value=""></option>');
			['employee_tin', 'employee_tax_status'].forEach(function(field) {
				$('#' + field + 'Error').text('');
				$('#' + field).removeClass('is-invalid');
			});
			
			$('#modal_title_action_employee').html('Add Employee');
			
			document.getElementById('Employeeform').className = "g-3 needs-validation";
			
			document.getElementById("submit_employee_details").value = 0;
			
			/*Hide the Clear Button*/
			
			$('#employee_last_nameError').text('');
			document.getElementById('employee_last_nameError').className = "valid-feedback";
			document.getElementById('employee_last_name').className = "form-control";
			
			$('#employee_first_nameError').text('');	
			document.getElementById('employee_first_nameError').className = "valid-feedback";
			document.getElementById('employee_first_name').className = "form-control";
			
			$('#employee_numberError').text('');	
			document.getElementById('employee_numberError').className = "valid-feedback";
			document.getElementById('employee_number').className = "form-control";
			
			$('#employee_rateError').text('');	
			document.getElementById('employee_rateError').className = "valid-feedback";
			document.getElementById('employee_rate').className = "form-control";
			
			
			
	}	
  
  
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
			let employee_first_name 		= $("input[name=update_employee_first_name]").val();
			let branch_idx 					= $("#update_division_list option[value='" + $('#update_branch_idx').val() + "']").attr('data-id');
			let department_idx				= $("#update_company_list option[value='" + $('#update_department_idx').val() + "']").attr('data-id');
			let employee_middle_name 		= $("input[name=update_employee_middle_name]").val();
			let employee_extension_name 	= $("input[name=update_employee_extension_name]").val();
			let employee_birthday 			= $("input[name=update_employee_birthday]").val();
			let employee_position 			= $("input[name=update_employee_position]").val();
	
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
	
  </script>
