
   <!-- Page level plugins -->
  <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
<script type="text/javascript">
	<!--Load Table for Branches-->
	$(function () {

		var HolidayListTable = $('#getholidayList').DataTable({
			"language": {
						"lengthMenu":'<select class="dt-input">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> '
		    },
			/*processing: true,*/
			serverSide: true,
			stateSave: true,/*Remember Searches*/
			ajax: "{{ route('getholidayList') }}",
			responsive: true,
			//scrollCollapse: true,
			//scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'holiday_date'},   
					{data: 'holiday_description'},
					{data: 'holiday_type'},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1] },
			]
		});
	
		autoAdjustColumns(HolidayListTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }		
				
		$('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
		
		});					
	});
	

	<!--Save New Branch->
	$("#submit_holiday_details").click(function(event){
			
			event.preventDefault();
			
				/*Reset Warnings*/
				$('#holiday_descriptionError').text('');
				$('#holiday_dateError').text('');
				$('#holiday_typeError').text('');
				$('#holiday_tinError').text('');

			document.getElementById('HolidayForm').className = "g-3 needs-validation was-validated";

			let holiday_id 				= document.getElementById("submit_holiday_details").value;
			
			let holiday_date 			= $("input[name=holiday_date]").val();
			let holiday_description 	= $("input[name=holiday_description]").val();
			let holiday_type 			= $("#holiday_type").val();
		
			  $.ajax({
				url: "{{ route('SubmitHoliday') }}",
				type:"POST",
				data:{
				  holiday_id:holiday_id,
				  holiday_date:holiday_date,
				  holiday_description:holiday_description,
				  holiday_type:holiday_type,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#modal_success_details').html("Holiday Information successfully "+response.success);
					//$('#holiday_details_modal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					ResetHolidayForm();
					
					/*Refresh Table*/
					var table = $("#getholidayList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
					/*Branch Name Error Notification*/
					 if(error.responseJSON.errors.holiday_description=="validation.unique"){
								  
					  $('#holiday_descriptionError').html("<b>"+ holiday_description +"</b> has already been taken.");
					  document.getElementById('holiday_descriptionError').className = "invalid-feedback";
					  document.getElementById('holiday_description').className = "form-control is-invalid";
					  $('#holiday_description').val("");
				  
					}else{
						
					  $('#holiday_descriptionError').text('Description Required');
					  document.getElementById('holiday_descriptionError').className = "invalid-feedback";
					  
					}
				 
					/*Branch Code Error Notification*/
				 
					 if(error.responseJSON.errors.holiday_date=="validation.unique"){
								  
					  $('#holiday_dateError').html("<b>"+ holiday_date +"</b> has already been taken.");
					  document.getElementById('holiday_dateError').className = "invalid-feedback";
					  document.getElementById('holiday_date').className = "form-control is-invalid";
					  $('#holiday_date').val("");
					  
					}else{
						
					  $('#holiday_dateError').text('Date Required');
					  document.getElementById('holiday_dateError').className = "invalid-feedback";
					
					}
					
					/*Branch Initial Error Notification*/
				
					if(error.responseJSON.errors.holiday_type=="validation.unique"){
								  
					  $('#holiday_typeError').html("<b>"+ holiday_type +"</b> has already been taken.");
					  document.getElementById('holiday_typeError').className = "invalid-feedback";
					  document.getElementById('holiday_type').className = "form-control form-select is-invalid";
					  $('#holiday_type').val("");
					  
					}else{
						
					  $('#holiday_typeError').text('Type Required');
					  document.getElementById('holiday_typeError').className = "invalid-feedback";
					
					}

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Select Branch For Update-->
	$('body').on('click','#edit_holiday',function(){
			
			event.preventDefault();
			let holidayID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('HolidayInfo') }}",
				type:"POST",
				data:{
				  holidayID:holidayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#modal_title_action_holiday').html('Edit Holiday');
					
					document.getElementById("submit_holiday_details").value = holidayID;
					
					/*Set Details*/
					document.getElementById("holiday_date").value = response.holiday_date;
					document.getElementById("holiday_description").value = response.holiday_description;
					document.getElementById("holiday_type").value = response.holiday_type;
										
					$('#holiday_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	<!--Branch Deletion Confirmation-->
	$('body').on('click','#delete_holiday',function(){
			
			event.preventDefault();
			let holidayID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('HolidayInfo') }}",
				type:"POST",
				data:{
				  holidayID:holidayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteHolidayConfirmed").value = holidayID;
					
					/*Set Details*/
					$('#delete_holiday_complete_name').html(response.holiday_date+" - "+response.holiday_description);
				
					$('#HolidayDeleteModal').modal('show');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--Branch Confirmed For Deletion-->
	  $('body').on('click','#deleteHolidayConfirmed',function(){
			
			event.preventDefault();

			let holidayID = document.getElementById("deleteHolidayConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteHoliday') }}",
				type:"POST",
				data:{
				  holidayID:holidayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getholidayList").DataTable();
				    table.ajax.reload(null, false);
					
					$('#modal_success_details').html("Holiday Information Succefully Deleted");
					$('#HolidayDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	
  	function ResetHolidayForm(){
			
			event.preventDefault();
					
					$('#modal_title_action_holiday').html('Add Holiday');
			
					/*Reset Warnings*/
					$('#holiday_descriptionError').text('');
					$('#holiday_dateError').text('');
					$('#holiday_typeError').text('');
					
					document.getElementById('holiday_descriptionError').className = "";
					document.getElementById('holiday_description').className = "form-control";
					
					document.getElementById('holiday_dateError').className = "";
					document.getElementById('holiday_date').className = "form-control";
					
					document.getElementById('holiday_typeError').className = "";
					document.getElementById('holiday_type').className = "form-control form-select";
					
					$('#HolidayForm')[0].reset();
					document.getElementById('HolidayForm').className = "g-3 needs-validation";
					
					document.getElementById("submit_holiday_details").value = 0;
				
	}	
</script>