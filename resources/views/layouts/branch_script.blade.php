   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
<script type="text/javascript">
	<!--Load Table-->
	$(function () {

		var BranchListTable = $('#getbranchList').DataTable({
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
			ajax: "{{ route('getBranchList') }}",
			responsive: true,
			scrollCollapse: true,
			scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'branch_code'},   
					{data: 'branch_name'},
					{data: 'branch_initial'},
					{data: 'branch_tin'},
					{data: 'branch_address'},
					{data: 'branch_contact_number'},
					{data: 'branch_owner'},
					{data: 'branch_owner_title'},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0, 3] },
			]
		});
	
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateBranchModal"></button>'+
				'</div>').appendTo('#branch_option');
				
		autoAdjustColumns(BranchListTable);

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
	$("#save-branch").click(function(event){
			
			event.preventDefault();
			
				/*Reset Warnings*/
				$('#branch_nameError').text('');
				$('#branch_codeError').text('');
				$('#branch_initialError').text('');
				$('#branch_tinError').text('');

			document.getElementById('BranchformNew').className = "g-3 needs-validation was-validated";

			let branch_code 			= $("input[name=branch_code]").val();
			let branch_name 			= $("input[name=branch_name]").val();
			let branch_initial 			= $("input[name=branch_initial]").val();
			let branch_tin 				= $("input[name=branch_tin]").val();
			let branch_address 			= $("input[name=branch_address]").val();
			let branch_contact_number 	= $("input[name=branch_contact_number]").val();
			let branch_owner 			= $("input[name=branch_owner]").val();
			let branch_owner_title 		= $("input[name=branch_owner_title]").val();
			
			  $.ajax({
				url: "{{ route('CreateBranch') }}",
				type:"POST",
				data:{
				  branch_code:branch_code,
				  branch_name:branch_name,
				  branch_initial:branch_initial,
				  branch_tin:branch_tin,
				  branch_address:branch_address,
				  branch_contact_number:branch_contact_number,
				  branch_owner:branch_owner,
				  branch_owner_title:branch_owner_title,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					/*Reset Warnings*/
					$('#branch_nameError').text('');
					$('#branch_codeError').text('');
					$('#branch_initialError').text('');
					$('#branch_tinError').text('');
					
					document.getElementById('branch_nameError').className = "";
					document.getElementById('branch_name').className = "form-control";
					
					document.getElementById('branch_codeError').className = "";
					document.getElementById('branch_code').className = "form-control";
					
					document.getElementById('branch_initialError').className = "";
					document.getElementById('branch_initial').className = "form-control";
					
					document.getElementById('branch_tinError').className = "";
					document.getElementById('branch_tin').className = "form-control";
					
					$('#BranchformNew')[0].reset();
					document.getElementById('BranchformNew').className = "g-3 needs-validation";
					
					/*Refresh Table*/
					var table = $("#getbranchList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
					/*Branch Name Error Notification*/
					 if(error.responseJSON.errors.branch_name=="The branch name has already been taken."){
								  
					  $('#branch_nameError').html("<b>"+ branch_name +"</b> has already been taken.");
					  document.getElementById('branch_nameError').className = "invalid-feedback";
					  document.getElementById('branch_name').className = "form-control is-invalid";
					  $('#branch_name').val("");
				  
					}else{
						
					  $('#branch_nameError').text(error.responseJSON.errors.branch_name);
					  document.getElementById('branch_nameError').className = "invalid-feedback";
					  
					}
				 
					/*Branch Code Error Notification*/
				 
					 if(error.responseJSON.errors.branch_code=="The branch code has already been taken."){
								  
					  $('#branch_codeError').html("<b>"+ branch_code +"</b> has already been taken.");
					  document.getElementById('branch_codeError').className = "invalid-feedback";
					  document.getElementById('branch_code').className = "form-control is-invalid";
					  $('#branch_code').val("");
					  
					}else{
						
					  $('#branch_codeError').text(error.responseJSON.errors.branch_code);
					  document.getElementById('branch_codeError').className = "invalid-feedback";
					
					}
					
					/*Branch Initial Error Notification*/
				
					if(error.responseJSON.errors.branch_initial=="The branch initial has already been taken."){
								  
					  $('#branch_initialError').html("<b>"+ branch_initial +"</b> has already been taken.");
					  document.getElementById('branch_initialError').className = "invalid-feedback";
					  document.getElementById('branch_initial').className = "form-control is-invalid";
					  $('#branch_initial').val("");
					  
					}else{
						
					  $('#branch_initialError').text(error.responseJSON.errors.branch_initial);
					  document.getElementById('branch_initialError').className = "invalid-feedback";
					
					}
				
					/*Branch TIN Error Notification*/
				
					if(error.responseJSON.errors.branch_tin=="The branch tin has already been taken."){
								  
					  $('#branch_tinError').html("<b>"+ branch_tin +"</b> has already been taken.");
					  document.getElementById('branch_tinError').className = "invalid-feedback";
					  document.getElementById('branch_tin').className = "form-control is-invalid";
					  $('#branch_tin').val("");
					  
					}else{
						
					  $('#branch_tinError').text(error.responseJSON.errors.branch_tin);
					  document.getElementById('branch_tinError').className = "invalid-feedback";
					
					}	

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Select Branch For Update-->
	$('body').on('click','#editbranch',function(){
			
			event.preventDefault();
			let branchID = $(this).data('id');
			
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
					
					document.getElementById("update-branch").value = branchID;
					
					/*Set Details*/
					document.getElementById("update_branch_code").value = response.branch_code;
					document.getElementById("update_branch_name").value = response.branch_name;
					document.getElementById("update_branch_address").value = response.branch_address;
					document.getElementById("update_branch_tin").value = response.branch_tin;
					document.getElementById("update_branch_initial").value = response.branch_initial;
					document.getElementById("update_branch_address").value = response.branch_address;
					document.getElementById("update_branch_contact_number").value = response.branch_contact_number;
					document.getElementById("update_branch_owner").value = response.branch_owner;
					document.getElementById("update_branch_owner_title").value = response.branch_owner_title;
										
					$('#UpdateBranchModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$("#update-branch").click(function(event){			
	
			event.preventDefault();
					
				/*Reset Warnings*/
				$('#update_branch_nameError').text('');
				$('#update_branch_codeError').text('');
				$('#update_branch_initialError').text('');
				$('#update_branch_tinError').text('');

			document.getElementById('BranchformEdit').className = "g-3 needs-validation was-validated";
			
			let branchID 				= document.getElementById("update-branch").value;
			let branch_code 			= $("input[name=update_branch_code]").val();
			let branch_name 			= $("input[name=update_branch_name]").val();
			let branch_initial 			= $("input[name=update_branch_initial]").val();
			let branch_tin 				= $("input[name=update_branch_tin]").val();
			
			let branch_address 			= $("input[name=update_branch_address]").val();
			let branch_contact_number 	= $("input[name=update_branch_contact_number]").val();
			let branch_owner 			= $("input[name=update_branch_owner]").val();
			let branch_owner_title 		= $("input[name=update_branch_owner_title]").val();
			
			  $.ajax({
				url: "{{ route('UpdateBranch') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  branch_code:branch_code,
				  branch_name:branch_name,
				  branch_initial:branch_initial,
				  branch_tin:branch_tin,
				  branch_address:branch_address,
				  branch_contact_number:branch_contact_number,
				  branch_owner:branch_owner,
				  branch_owner_title:branch_owner_title,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					/*Reset Warnings*/
					$('#update_branch_nameError').text('');
					$('#update_branch_codeError').text('');
					$('#update_branch_initialError').text('');
					$('#update_branch_tinError').text('');
					
					document.getElementById('update_branch_nameError').className = "";
					document.getElementById('update_branch_name').className = "form-control";
					
					document.getElementById('update_branch_codeError').className = "";
					document.getElementById('update_branch_code').className = "form-control";
					
					document.getElementById('update_branch_initialError').className = "";
					document.getElementById('update_branch_initial').className = "form-control";
					
					document.getElementById('update_branch_tinError').className = "";
					document.getElementById('update_branch_tin').className = "form-control";
					
					$('#BranchformEdit')[0].reset();
					document.getElementById('BranchformEdit').className = "g-3 needs-validation";
										
					/*Close form*/
					$('#UpdateBranchModal').modal('toggle');
							
					/*Refresh Table*/
					var table = $("#getbranchList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
					/*Branch Name Error Notification*/
					 if(error.responseJSON.errors.branch_name=="The branch name has already been taken."){
								  
					  $('#update_branch_nameError').html("<b>"+ branch_name +"</b> has already been taken.");
					  document.getElementById('update_branch_nameError').className = "invalid-feedback";
					  document.getElementById('update_branch_name').className = "form-control is-invalid";
					  $('#update_branch_name').val("");
				  
					}else{
						
					  $('#update_branch_nameError').text(error.responseJSON.errors.branch_name);
					  document.getElementById('update_branch_nameError').className = "invalid-feedback";
					  
					}
				 
					/*Branch Code Error Notification*/
				 
					 if(error.responseJSON.errors.branch_code=="The branch code has already been taken."){
								  
					  $('#update_branch_codeError').html("<b>"+ branch_code +"</b> has already been taken.");
					  document.getElementById('update_branch_codeError').className = "invalid-feedback";
					  document.getElementById('update_branch_code').className = "form-control is-invalid";
					  $('#update_branch_code').val("");
					  
					}else{
						
					  $('#update_branch_codeError').text(error.responseJSON.errors.branch_code);
					  document.getElementById('update_branch_codeError').className = "invalid-feedback";
					
					}
					
					/*Branch Initial Error Notification*/
				
					if(error.responseJSON.errors.branch_initial=="The branch initial has already been taken."){
								  
					  $('#update_branch_initialError').html("<b>"+ branch_initial +"</b> has already been taken.");
					  document.getElementById('update_branch_initialError').className = "invalid-feedback";
					  document.getElementById('update_branch_initial').className = "form-control is-invalid";
					  $('#update_branch_initial').val("");
					  
					}else{
						
					  $('#update_branch_initialError').text(error.responseJSON.errors.branch_initial);
					  document.getElementById('update_branch_initialError').className = "invalid-feedback";
					
					}
				
					/*Branch TIN Error Notification*/
				
					if(error.responseJSON.errors.branch_tin=="The branch tin has already been taken."){
								  
					  $('#update_branch_tinError').html("<b>"+ branch_tin +"</b> has already been taken.");
					  document.getElementById('update_branch_tinError').className = "invalid-feedback";
					  document.getElementById('update_branch_tin').className = "form-control is-invalid";
					  $('#update_branch_tin').val("");
					  
					}else{
						
					  $('#update_branch_tinError').text(error.responseJSON.errors.branch_tin);
					  document.getElementById('update_branch_tinError').className = "invalid-feedback";
					
					}		
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);		  	  
				  
				}
			   });
		
	  });
	  
	<!--client Deletion Confirmation-->
	$('body').on('click','#deletebranch',function(){
			
			event.preventDefault();
			let branchID = $(this).data('id');
			
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
					
					document.getElementById("deleteBranchConfirmed").value = branchID;
					
					/*Set Details*/
					$('#confirm_delete_branch_code').text(response.branch_code);
					$('#confirm_delete_branch_name').text(response.branch_name);	
					$('#confirm_delete_branch_tin').text(response.branch_tin);	
					
					$('#BranchDeleteModal').modal('toggle');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--client Confirmed For Deletion-->
	  $('body').on('click','#deleteBranchConfirmed',function(){
			
			event.preventDefault();

			let branchID = document.getElementById("deleteBranchConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteBranch') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Branch Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getbranchList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	  
</script>