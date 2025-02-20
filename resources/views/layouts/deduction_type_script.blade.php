
   <!-- Page level plugins -->
  <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
<script type="text/javascript">
	<!--Load Table for Branches-->
	$(function () {

		var DeductionListTable = $('#getDeductionList').DataTable({
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
			ajax: "{{ route('getDeductionTypeList') }}",
			responsive: true,
			//scrollCollapse: true,
			//scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'deduction_description', className: "text-left"},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1] },
			]
		});
	
		autoAdjustColumns(DeductionListTable);

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
	$("#submit_deduction_type_details").click(function(event){
			
			event.preventDefault();
			
				/*Reset Warnings*/
				$('#deduction_descriptionError').text('');

			document.getElementById('DeductionTypeForm').className = "g-3 needs-validation was-validated";

			let deduction_id 				= document.getElementById("submit_deduction_type_details").value;
			
			let deduction_description 	= $("input[name=deduction_description]").val();
		
			  $.ajax({
				url: "{{ route('SubmitDeductionType') }}",
				type:"POST",
				data:{
				  deduction_id:deduction_id,
				  deduction_description:deduction_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#modal_success_details').html("Deduction Type successfully "+response.success);
					//$('#deduction_type_modal').modal('hide');
					$('#success-alert-modal').modal('show');
					
					ResetDeductionTypeForm();
					
					/*Refresh Table*/
					var table = $("#getDeductionList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
					/*Branch Name Error Notification*/
					 if(error.responseJSON.errors.deduction_description=="validation.unique"){
								  
					  $('#deduction_descriptionError').html("<b>"+ deduction_description +"</b> has already been taken.");
					  document.getElementById('deduction_descriptionError').className = "invalid-feedback";
					  document.getElementById('deduction_description').className = "form-control is-invalid";
					  $('#deduction_description').val("");
				  
					}else{
						
					  $('#deduction_descriptionError').text('Description Required');
					  document.getElementById('deduction_descriptionError').className = "invalid-feedback";
					  
					}
				 

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Select Branch For Update-->
	$('body').on('click','#edit_DeductionType',function(){
			
			event.preventDefault();
			let DeductionID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DeductionTypeInfo') }}",
				type:"POST",
				data:{
				  DeductionID:DeductionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#modal_title_action_deduction_type').html('Edit Deduction');
					
					document.getElementById("submit_deduction_type_details").value = DeductionID;
					
					/*Set Details*/
					document.getElementById("deduction_description").value = response.deduction_description;
										
					$('#deduction_details_modal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	<!--Branch Deletion Confirmation-->
	$('body').on('click','#delete_DeductionType',function(){
			
			event.preventDefault();
			let DeductionID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DeductionTypeInfo') }}",
				type:"POST",
				data:{
				  DeductionID:DeductionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteDeductionConfirmed").value = DeductionID;
					
					/*Set Details*/
					$('#delete_deduction_description').html(response.deduction_description);
				
					$('#DeductionDeleteModal').modal('show');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--Branch Confirmed For Deletion-->
	  $('body').on('click','#deleteDeductionConfirmed',function(){
			
			event.preventDefault();

			let DeductionID = document.getElementById("deleteDeductionConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteDeductionType') }}",
				type:"POST",
				data:{
				  DeductionID:DeductionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getDeductionList").DataTable();
				    table.ajax.reload(null, false);
					
					$('#modal_success_details').html("Deduction Information Succefully Deleted");
					$('#DeductionDeleteModal').modal('hide');
					$('#success-alert-modal').modal('show');
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	
  	function ResetDeductionTypeForm(){
			
			event.preventDefault();
					
					$('#modal_title_action_deduction_type').html('Add Deduction');
			
					/*Reset Warnings*/
					$('#deduction_descriptionError').text('');
					
					document.getElementById('deduction_descriptionError').className = "";
					document.getElementById('deduction_description').className = "form-control";
					$('#DeductionTypeForm')[0].reset();
					document.getElementById('DeductionTypeForm').className = "g-3 needs-validation";
					
					document.getElementById("submit_deduction_type_details").value = 0;
				
	}	
</script>