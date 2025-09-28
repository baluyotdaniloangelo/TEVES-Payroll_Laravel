   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   
<script type="text/javascript">

	setMaxonEndDate();
	
	function setMaxonEndDate(){
	
		let start_date 			= $("input[name=start_date]").val();
		
		var myDate = new Date(start_date);
		var result1 = myDate.setMonth(myDate.getMonth()+1);
		
		const date_new = new Date(result1);
		
		const max_date = document.getElementById('end_date');
		
		document.getElementById("end_date").min = start_date;
		document.getElementById("end_date").max = date_new.toISOString("en-US").substring(0, 10);
		
		document.getElementById("end_date").value = start_date;
		
	}
	
	function CheckEndDateValidity(){
		
		let start_date 			= $("input[name=start_date]").val();
		let end_date 			= $("input[name=end_date]").val();
		
		let end_date_max 		= document.getElementById("end_date").max;
		
		const x = new Date(start_date);
		const y = new Date(end_date);
		
		const edt = new Date(end_date_max);
		
			if(x > y){
					
					/*Set The End Date same with Start Date*/
					document.getElementById("end_date").value = start_date;
				
			}
			else if(edt < y){
					
					/*Set The End Date same with Start Date*/
					document.getElementById("end_date").value = start_date;
					
			}else{
					$('#end_dateError').html('');
					document.getElementById('end_dateError').className = "valid-feedback";
			}
	
	}
	
	/*Load Branch*/
	LoadBranch();
	function LoadBranch() {		
		
		$("#branch_list option").remove();
		$('<option style="display: none;"></option>').appendTo('#branch_list');
	
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
							$('#branch_list option:last').after(""+
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
	
	<!--Load Table-->
	$("#generate_payroll").click(function(event){
		
			event.preventDefault();
	
					/*Reset Warnings*/
					$('#branchIDError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/					
					$("#EmployeesPayrollResult tbody").html("");					
					
			document.getElementById('generate_payroll_form').className = "g-3 needs-validation was-validated";

			let branchID 			= $('#branch_list option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
				
			  $.ajax({
				url: "{{ route('ReviewPayroll') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  start_date:start_date,
				  end_date:end_date,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#generate_payroll_modal').modal('toggle');
				
				/*Call Function to Get the Branch Details*/
				get_branch_details(branchID);
				
				var start_date_new  = new Date(start_date);
				start_date_new_format = (start_date_new.toLocaleDateString("en-PH")); // 9/17/2016
							
				var end_date_new  = new Date(end_date);
				end_date_new_format = (end_date_new.toLocaleDateString("en-PH")); // 9/17/2016

				$('#covered_period_details').text(start_date_new_format + ' - ' +end_date_new_format);			
				$('#covered_period_details_save').text(start_date_new_format + ' - ' +end_date_new_format);			
							
							
				  console.log(response);
				  if(response!='') {
					
					$('#branchIDError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					
						var gross_salary = 0;
						var net_salary = 0;
						
						var len = response['data'].length;
						for(var i=0; i<len; i++){
							
							gross_salary += response['data'][i].gross_salary;
							net_salary += response['data'][i].net_salary;
							
						}			
						
						LoadPayrollData.clear().draw();
						LoadPayrollData.rows.add(response.data).draw();	
						
						if(net_salary!=0){
							
							$("#save_options").html('<a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#SaveCutOffModal"><i class="fa fa-save"></i> Save</a>');
							$("#print_options").html('<a href="#" class="btn add-btn" onclick="print_employees_payroll_draft()"><i class="fa fa-save"></i> Print</a>');
	
						}
							
						/*Set Details*/
						/*Save Cut Off must be visible*/
						
				  }else{
							/*Close Form*/
							$('#generate_payroll_modal').modal('toggle');
							/*No Result Found*/
					}
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					//document.getElementById("generate_payroll").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
				},
				complete: function(){
					
					/*Enable Submit Button*/
					//document.getElementById("generate_payroll").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();
					
				},
				error: function(error) {
				 console.log(error);	
				 
				  $('#branchIDError').text(error.responseJSON.errors.branchID);
				  document.getElementById('branchIDError').className = "invalid-feedback";
				  			  
				  $('#start_dateError').text(error.responseJSON.errors.start_date);
				  document.getElementById('start_dateError').className = "invalid-feedback";		

				  $('#end_dateError').text(error.responseJSON.errors.end_date);
				  document.getElementById('end_dateError').className = "invalid-feedback";		
				
				$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });

		/*Load to Datatables*/	
		let LoadPayrollData = $('#EmployeesPayrollResult').DataTable( {
				"language": {
						"emptyTable": "No Result Found",
						"infoEmpty": "No entries to show"
			    }, 
				//processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
					$('td:eq(0)', nRow).html(iDisplayIndexFull +1);
				},
				responsive: true,
				paging: true,
				searching: false,
				info: false,
				data: [],
				scrollCollapse: true,
				scrollY: '500px',
				scrollx: false,

                /*

                DT_RowIndex
: 
14
allowance_amount_total
: 
0
count_days
: 
15
daily_rate
: 
1400
deduction_amount_total
: 
0
employee_full_name
: 
"BALUYOT, DANILO ANGELO RELINGADO "
employee_number
: 
"TEST-EMP"
employment_type
: 
"Regular"
gross_salary
: 
26838.29
leave_amount_pay_total
: 
0
leave_logs_count
: 
0
net_salary
: 
26838.29
night_differential_pay_total
: 
0
regular_holiday_pay_total
: 
4200
regular_overtime_pay
: 
751.04
regular_pay
: 
751.04
restday_overtime_pay
: 
887.25
restday_pay
: 
7280
special_holiday_pay_total
: 
420
                */
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: true, searchable: false, className: "text-center"},  
					{data: 'employee_number', className: "text-left", orderable: false },
					{data: 'employee_full_name', className: "text-left", orderable: false },
					{data: 'employment_type', className: "text-left", orderable: false },
					{data: 'daily_rate', className: "text-left", orderable: false },	
					{data: 'count_days', className: "text-left", orderable: false },		
					{data: 'leave_logs_count', className: "text-left", orderable: false },	
					{data: 'regular_pay', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'regular_overtime_pay', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'restday_pay', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'restday_overtime_pay', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'night_differential_pay_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'special_holiday_pay_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'regular_holiday_pay_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'allowance_amount_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'deduction_amount_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2 , '' ) },
					{data: 'gross_salary', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2 , '' ) },
					{data: 'net_salary', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2 , '' ) }
				],
				
		} );
		
	autoAdjustColumns(LoadPayrollData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }

	$("#saveCutOffConfirmed").click(function(event){
		
			event.preventDefault();
	
					/*Reset Warnings*/
					$('#branchIDError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/					
					$("#EmployeesPayrollResult tbody").html("");					
					
			document.getElementById('generate_payroll_form').className = "g-3 needs-validation was-validated";

			let branchID 			= $('#branch_list option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
				
			  $.ajax({
				url: "{{ route('SavePayroll') }}",
				type:"POST",
				data:{
				  branch_idx:branchID,
				  cut_off_period_start:start_date,
				  cut_off_period_end:end_date,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#SaveCutOffModal').modal('toggle');
				
				/*Call Function to Get the Branch Details*/
				get_branch_details(branchID);
				
				var start_date_new  = new Date(start_date);
				start_date_new_format = (start_date_new.toLocaleDateString("en-PH")); // 9/17/2016
							
				var end_date_new  = new Date(end_date);
				end_date_new_format = (end_date_new.toLocaleDateString("en-PH")); // 9/17/2016

				$('#covered_period_details').text(start_date_new_format + ' - ' +end_date_new_format);			
				$('#covered_period_details_save').text(start_date_new_format + ' - ' +end_date_new_format);			
					
				  console.log(response);
				  if(response!='') {
					
					$('#branchIDError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					
						var gross_salary = 0;
						var net_salary = 0;
						
						var len = response['data']['original']['data'].length;
						var cutoff_idx = response['cutoff_idx'];
						
						for(var i=0; i<len; i++){
							
							gross_salary += response['data']['original']['data'].gross_salary;
							net_salary += response['data']['original']['data'].net_salary;
							
							
						}			
						
						LoadPayrollData.clear().draw();
						LoadPayrollData.rows.add(response['data']['original']['data']).draw();	
						
						if(cutoff_idx!=0){ 
							//$("#save_options").html('<span></span>');
							$("#save_options").html('<a href="#" class="btn add-btn" title><i class="fa fa-save"></i> Saved</a>');
							
							$("#print_options").html('<a href="#" class="btn add-btn" onclick="print_payroll_after_save('+cutoff_idx+')"><i class="fa fa-save"></i> Print</a>');
						}
							
						/*Set Details*/
						/*Save Cut Off must be invisible*/
						
				  }else{
							/*Close Form*/
							//$('#generate_payroll_modal').modal('toggle');
							/*No Result Found*/
					}
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					//document.getElementById("generate_payroll").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
				},
				complete: function(){
					
					/*Enable Submit Button*/
					//document.getElementById("generate_payroll").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();
					
				},
				error: function(error) {
				 console.log(error);	
				 
				 
				 if(error.responseJSON.errors.branch_idx="validation.unique"){
					 
					 alert('The selected branch and period already exist!');
					 
				 }
				 
					LoadPayrollData.clear().draw();
					LoadPayrollData.rows.add(response.data).draw();	
					
					$("#save_options").html('&nbsp;');
					$("#print_options").html('&nbsp;');
					  //$('#branchIDError').text(error.responseJSON.errors.branch_idx);
					  //document.getElementById('branchIDError').className = "invalid-feedback";
								  
					 //$('#start_dateError').text(error.responseJSON.errors.cut_off_period_end);
					 //document.getElementById('start_dateError').className = "invalid-feedback";		

					 //$('#end_dateError').text(error.responseJSON.errors.cut_off_period_start);
					 //document.getElementById('end_dateError').className = "invalid-feedback";		
					
					//$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });


	<!--Select Branch For Update-->
	function get_branch_details(branchID){
			
			event.preventDefault();
			//let branchID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('BranchInfo') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
						
					$('#branch_name_details').text(response.branch_name);	
					$('#branch_code_details').text(response.branch_code);	
					$('#branch_tin_details').text(response.branch_tin);	

					$('#branch_name_details_save').text(response.branch_name);	
					$('#branch_code_details_save').text(response.branch_code);	
					$('#branch_tin_details_save').text(response.branch_tin);		
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  }
	
	/*Print Drafts*/
	function print_employees_payroll_draft(){
			
			let branchID 			= $('#branch_list option[value="' + $('#branch_idx').val() + '"]').attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			
		var query = {
			branchID:branchID,
			start_date:start_date,
			end_date:end_date,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_employees_payroll_draft_pdf')}}?" + $.param(query)
		window.open(url);
	  
	}
	    

	function print_payroll_after_save(cutoff_idx){
	  
		var query = {
			cutoff_idx:cutoff_idx,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_employees_saved_payroll_pdf')}}?" + $.param(query)
		window.open(url);
	  
	}
</script>
