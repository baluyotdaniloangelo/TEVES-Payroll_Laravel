   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
<style>
#divisionList {
overflow: hidden;
max-height: 0;
transition: max-height 0.5s ease-in-out;
}
</style>
   <script type="text/javascript">
   // $(window).resize(function(){location.reload();});
	<!--Load Table-->				
	$(function () {
				
		var switchTable = $('#divisionList').DataTable({
			processing: true,
			responsive: true,
			serverSide: true,
			stateSave: true,/*Remember Searches*/
			scrollCollapse: true,
			scrollY: '500px',
			scrollX: '100%',
			ajax: {
				url : "{{ route('DivisionList') }}",
				method : 'POST',
				data: { _token: "{{ csrf_token() }}" },
			},
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},    
					{data: 'division_code'},  
					{data: 'division_name'},  					
					{data: 'created_at_dt_format', name: 'switch_status', orderable: true, searchable: false},
					{data: 'updated_at_dt_format', name: 'switch_status', orderable: true, searchable: false},
					{data: 'action', name: 'action', orderable: false, searchable: false},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0, 3, 4, 5] },
			],
			
		});
		  /*Add Options*/
		  $('<div class="btn-group" role="group" aria-label="Basic outlined example"style="margin-top: -50px; position: absolute;">'+
		  '<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateDivisionModal" onclick="ResetFormDivision();"> Division</button>'+
		  '</div>').appendTo('#division_option');

		autoAdjustColumns(switchTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	

	});
	
	
	<!--Save New Site-->
	$("#save-division").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/							  
					$('#division_nameError').text('');
					$('#division_codeError').text('');

			document.getElementById('CreateDivisionform').className = "g-3 needs-validation was-validated";
			
			let division_name 			= $("input[name=division_name]").val();	
			let division_code 			= $("input[name=division_code]").val();
			
			  $.ajax({
				url: "/create_division_post",
				type:"POST",
				data:{
				  division_code:division_code,
				  division_name:division_name,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				 
				  if(response) {
					  
					$('#division_nameError').text('');
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
				
					//document.getElementById("CreateDivisionform").reset();
					ResetFormDivision();
					
					var table = $("#divisionList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				
				if(error.responseJSON.errors.division_name=="The division name has already been taken."){
							  
				  $('#division_nameError').html("<b>"+ division_name +"</b> has already been taken.");
				  document.getElementById('division_nameError').className = "invalid-feedback";
				  document.getElementById('division_name').className = "form-control is-invalid";
				  $('#division_name').val("");
				  
				}else{
					
				  $('#division_nameError').text(error.responseJSON.errors.division_name);
				  document.getElementById('division_nameError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.division_code=="The division code has already been taken."){
							  
				  $('#division_codeError').html("<b>"+ division_code +"</b> has already been taken.");
				  document.getElementById('division_codeError').className = "invalid-feedback";
				  document.getElementById('division_code').className = "form-control is-invalid";
				  $('#division_code').val("");
				  
				}else{
					
				  $('#division_codeError').text(error.responseJSON.errors.division_code);
				  document.getElementById('division_codeError').className = "invalid-feedback";		
				
				}
				
				  $('#InvalidModal').modal('toggle');				
				  
				}
			   });
		
	  });

	function ResetFormDivision(){
			
			event.preventDefault();
			$('#CreateDivisionform')[0].reset();

			$('#division_codeError').text('');
			document.getElementById('division_codeError').className = "valid-feedback";		
			
			document.getElementById('division_code').className = "form-control";
			
			$('#division_nameError').text('');
			document.getElementById('division_nameError').className = "valid-feedback";		
			
			document.getElementById('division_name').className = "form-control";

			document.getElementById('CreateDivisionform').className = "g-3 needs-validation";
			
	}
	
	<!--Select Site For Update-->
	$('body').on('click','#editDivision',function(){
			
			event.preventDefault();
			let DivisionID = $(this).data('id');
			
			  $.ajax({
				url: "/division_info",
				type:"POST",
				data:{
				  DivisionID:DivisionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-division").value = DivisionID;
					document.getElementById("update-division").disabled = true;
					
					/*Set Switch Details*/
					document.getElementById("update_division_code").value = response.division_code;
					document.getElementById("update_division_name").value = response.division_name;
					$('#UpdateDivisionModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });
	  
	document.getElementById("update_division_name").addEventListener('change', doThing_division_management);
	document.getElementById("update_division_code").addEventListener('change', doThing_division_management);
	
	function doThing_division_management(){

			let DivisionID 				= document.getElementById("update-division").value;	
			
			let division_name 			= $("input[name=update_division_name]").val();
			let division_code 			= $("input[name=update_division_code]").val();
		
		$.ajax({
				url: "/division_info",
				type:"POST",
				data:{
				  DivisionID:DivisionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
				  

					  if(response.division_name===division_name && response.division_code===division_code){
							
							document.getElementById("update-division").disabled = true;
							
						}else{
							
							document.getElementById("update-division").disabled = false;
							
						}
					  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		 
	   
    }

	$("#update-division").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
			let DivisionID = document.getElementById("update-division").value;	
			
			document.getElementById('UpdateDivisionform').className = "g-2 needs-validation was-validated";

			let division_name 			= $("input[name=update_division_name]").val();
			let division_code 			= $("input[name=update_division_code]").val();
			
			$.ajax({
				url: "/update_division_post",
				type:"POST",
				data:{
				  DivisionID:DivisionID,
				  division_code:division_code,
				  division_name:division_name,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#update_division_nameError').text('');				
					$('#update_division_codeError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
					
					$('#UpdateUserModal').modal('toggle');
					
					var table = $("#divisionList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.division_name=="The division name has already been taken."){
							  
				  $('#update_division_nameError').html("<b>"+ division_name +"</b> has already been taken.");
				  document.getElementById('update_division_nameError').className = "invalid-feedback";
				  document.getElementById('update_division_name').className = "form-control is-invalid";
				  $('#update_division_name').val("");
				  
				}else{
					
				  $('#update_division_nameError').text(error.responseJSON.errors.division_name);
				  document.getElementById('update_division_nameError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.division_code=="The division code has already been taken."){
							  
				  $('#update_division_codeError').html("<b>"+ division_code +"</b> has already been taken.");
				  document.getElementById('update_division_codeError').className = "invalid-feedback";
				  document.getElementById('update_division_code').className = "form-control is-invalid";
				  $('#update_division_code').val("");
				  
				}else{
					
				  $('#update_division_codeError').text(error.responseJSON.errors.division_code);
				  document.getElementById('update_division_codeError').className = "invalid-feedback";		
				
				}
				
				$('#InvalidModal').modal('toggle');
				  
				}
			   });
	  });
	  
	<!--Switch Deletion Confirmation-->
	$('body').on('click','#deleteDivision',function(){
			
			event.preventDefault();
			let DivisionID = $(this).data('id');
			
			  $.ajax({
				url: "/division_info",
				type:"POST",
				data:{
				  DivisionID:DivisionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteDivisionConfirmed").value = DivisionID;
					
					$('#division_name_info_confirm').html(response.division_name);
					
					$('#DivisionDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	<!--User Confirmed For Deletion-->
	$('body').on('click','#deleteDivisionConfirmed',function(){
			
			event.preventDefault();

			let DivisionID = document.getElementById("deleteDivisionConfirmed").value;
			
				$.ajax({
				url: "/delete_division_confirmed",
				type:"POST",
				data:{
				  DivisionID:DivisionID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {
					  
					$('.success_modal_bg').html("Division Deleted!");
					$('#SuccessModal').modal('toggle');	

					var table = $("#divisionList").DataTable();
				    table.ajax.reload(null, false);
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });	
		
	  });

</script>