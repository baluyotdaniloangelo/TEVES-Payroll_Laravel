   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script type="text/javascript">

	<!--Load Table-->				
	$(function () {
				
		var userTable = $('#userList').DataTable({
			processing: true,
			responsive: true,
			serverSide: true,
			stateSave: true,/*Remember Searches*/
			ajax: {
				url : "{{ route('UserList') }}",
				method : 'POST',
				data: { _token: "{{ csrf_token() }}" },
			},
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},    
					{data: 'user_real_name', className: "text-left"},   
					{data: 'user_job_title', className: "text-left"},	
					{data: 'user_name', className: "text-left"}, 					
					{data: 'user_type', className: "text-left"},
					{data: 'user_email_address', className: "text-left"},					
					{data: 'created_at_dt_format', name: 'switch_status', orderable: true, searchable: false, className: "text-left"},
					{data: 'updated_at_dt_format', name: 'switch_status', orderable: true, searchable: false, className: "text-left"},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			 ],
			// columnDefs: [
					 // { className: 'text-center', targets: [5, 6, 7] },
			// ],
			
		});
		  /*Add Options*/
		  $('<div class="btn-group" role="group" aria-label="Basic outlined example"style="margin-top: -50px; position: absolute;">'+
		  '<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateUserModal" onclick="ResetFormUser();";> User</button>'+
		  '</div>').appendTo('#user_option');

		autoAdjustColumns(userTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	

	});


	function ChangeAccessType_Add(){

		let user_type 			= $("#user_type").val();
		var user_type_selected = $('#user_type').find(":selected").val();
		
		if(user_type_selected=='Admin'){
			document.getElementById('user_access').value = 'ALL';
		}
		else{
			document.getElementById('user_access').value = 'BYSITE';
		}

	}
	
	function ChangeAccessType_Update(){

		let user_type 			= $("#update_user_type").val();
		var user_type_selected = $('#update_user_type').find(":selected").val();
		

		if(user_type_selected=='Admin'){
			document.getElementById('update_user_access').value = 'ALL';
		}
		else{
			document.getElementById('update_user_access').value = 'BYSITE';
		}
		
	}

	<!--Save New User-->
	$("#save-user").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/			
					$('#user_real_nameError').text('');				  
					$('#user_nameError').text('');
					$('#user_passwordError').text('');
					$('#user_typeError').text('');

			document.getElementById('UserForm').className = "g-3 needs-validation was-validated";

            let userID 				= document.getElementById("save-user").value;

			let user_real_name 		= $("input[name=user_real_name]").val();
			let user_name 			= $("input[name=user_name]").val();
			let user_email_address 	= $("input[name=user_email_address]").val();
			let user_password 		= $("input[name=user_password]").val();
			let user_type 			= $("#user_type").val();
			let user_access 		= $("#user_access").val();
			let user_job_title 		= $("input[name=user_job_title]").val();
			
			  $.ajax({
				url: "/create_user_post",
				type:"POST",
				data:{
                  userID:userID,
				  user_real_name:user_real_name,
				  user_name:user_name,
				  user_email_address:user_email_address,
				  user_password:user_password,
				  user_type:user_type,
				  user_access:user_access,
				  user_job_title:user_job_title,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				 
				  if(response) {

					$('#user_real_nameError').text('');				  
					$('#user_nameError').text('');
					$('#user_passwordError').text('');
					$('#user_typeError').text('');		
					$('#user_job_titleError').text('');
					$('#user_email_addressError').text('');
					document.getElementById('user_email_address').className = "form-control";
					
                    $('#modal_success_details').html("User Information successfully "+response.success);
                    $('#success-alert-modal').modal('toggle');	
				
					document.getElementById("UserForm").reset();
				
					if(user_access=='BYSITE'){
						UpdateUserAccess(response.user_id);
					}
				
					var table = $("#userList").DataTable();
				    table.ajax.reload(null, false);
					
					document.getElementById('UserForm').className = "g-3 needs-validation";
				
				  }
				},
				error: function(error) {
				 console.log(error);	

				if(error.responseJSON.errors.user_real_name=="validation.unique"){
							  
				  $('#user_real_nameError').html("<b>"+ user_real_name +"</b> has already been taken.");
				  document.getElementById('user_real_nameError').className = "invalid-feedback";
				  document.getElementById('user_real_name').className = "form-control is-invalid";
				  $('#user_real_name').val("");
				  
				}else{
					
				  $('#user_real_nameError').text(error.responseJSON.errors.user_real_name);
				  document.getElementById('user_real_nameError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.user_email_address=="validation.unique"){
							  
				  $('#user_email_addressError').html("<b>"+ user_email_address +"</b> has already been taken.");
				  document.getElementById('user_email_addressError').className = "invalid-feedback";
				  document.getElementById('user_email_address').className = "form-control is-invalid";
				  $('#user_email_address').val("");
				  
				}else{
					
				  $('#user_email_addressError').text(error.responseJSON.errors.user_email_address);
				  document.getElementById('user_email_addressError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.user_name=="validation.unique"){
							  
				  $('#user_nameError').html("<b>"+ user_name +"</b> has already been taken.");
				  document.getElementById('user_nameError').className = "invalid-feedback";
				  document.getElementById('user_name').className = "form-control is-invalid";
				  $('#user_name').val("");
				  
				}else{
					
				  $('#user_nameError').text(error.responseJSON.errors.user_name);
				  document.getElementById('user_nameError').className = "invalid-feedback";		
				
				}
					
				  $('#user_passwordError').text(error.responseJSON.errors.user_password);
				  document.getElementById('user_passwordError').className = "invalid-feedback";		
				  
				  $('#user_typeError').text(error.responseJSON.errors.user_type);
				  document.getElementById('user_typeError').className = "invalid-feedback";		
				  
				  $('#user_job_titleError').text(error.responseJSON.errors.user_job_title);
				  document.getElementById('user_job_titleError').className = "invalid-feedback";	
				  
				  $('#InvalidModal').modal('toggle');				
				  
				}
			   });
	  });

	function ResetFormUser(){
			
			event.preventDefault();
			$('#UserForm')[0].reset();

			$('#user_email_addressError').html("");
			document.getElementById('user_email_addressError').className = "valid-feedback";
			document.getElementById('user_email_address').className = "form-control";
			document.getElementById('UserForm').className = "g-3 needs-validation";
			
	}	

	<!--Select Site For Update-->
	$('body').on('click','#editUser',function(){
					
			event.preventDefault();
			let UserID = $(this).data('id');

            $('#modal_title_action_user').html('Edit User');
			
			  $.ajax({
				url: "/user_info",
				type:"POST",
				data:{
				  UserID:UserID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("save-user").value = UserID;
					document.getElementById("save-user").disabled = true;
					/*Set Switch Details*/
					document.getElementById("user_real_name").value = response.user_real_name;
					document.getElementById("user_name").value = response.user_name;
					document.getElementById("user_email_address").value = response.user_email_address;
					document.getElementById("user_type").value = response.user_type;
					document.getElementById("user_access").value = response.user_branch_access_type;
					document.getElementById("user_job_title").value = response.user_job_title;
					
					$('#user_email_addressError').html("");
					document.getElementById('user_email_addressError').className = "valid-feedback";
					document.getElementById('user_email_address').className = "form-control";
						
					$('#user_details_modal').modal('toggle');	
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

    document.getElementById("user_real_name").addEventListener('change', doThing_account_management);
	document.getElementById("user_name").addEventListener('change', doThing_account_management);
	document.getElementById("user_email_address").addEventListener('change', doThing_account_management);
	document.getElementById("user_password").addEventListener('change', doThing_account_management);
	document.getElementById("user_type").addEventListener('change', doThing_account_management);
	document.getElementById("user_access").addEventListener('change', doThing_account_management);
	document.getElementById("user_job_title").addEventListener('change', doThing_account_management);
	
	function doThing_account_management(){

			let userID = document.getElementById("save-user").value;
		
			let user_real_name 		= $("input[name=user_real_name]").val();
			let user_name 			= $("input[name=user_name]").val();
			let user_email_address 	= $("input[name=user_email_address]").val();
			let user_password 		= $("input[name=user_password]").val();
			let user_type 			= $("#user_type").val();	
			let user_access 		= $("#user_access").val();
			let user_job_title 		= $("input[name=user_job_title]").val();
		
		$.ajax({
				url: "/user_info",
				type:"POST",
				data:{
				  UserID:userID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
				  
				  
				  if(user_password!==''){
						
						if(response.user_real_name===user_real_name && response.user_name===user_name && response.user_email_address===user_email_address && response.user_type===user_type && response.user_branch_access_type===user_access && response.user_job_title===user_job_title){
							
							document.getElementById("save-user").disabled = false;
							
						}else{
							
							document.getElementById("save-user").disabled = false;
							
						}
					
				  }else{
					  
					  if(response.user_real_name===user_real_name && response.user_name===user_name && response.user_email_address===user_email_address && response.user_type===user_type && response.user_branch_access_type===user_access && response.user_job_title===user_job_title){
							
							document.getElementById("save-user").disabled = true;
							
						}else{
							
							document.getElementById("save-user").disabled = false;
							
						}
					  
				  }
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		 
	   
    }
	  

	<!--User Deletion Confirmation-->
	$('body').on('click','#deleteUser',function(){
			
			event.preventDefault();
			let UserID = $(this).data('id');
			
			  $.ajax({
				url: "/user_info",
				type:"POST",
				data:{
				  UserID:UserID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteUserConfirmed").value = UserID;
					
					$('#user_real_name_info_delete').html(response.user_real_name);
					$('#user_name_info_delete').html(response.user_real_name);
					$('#user_type_info_delete').html(response.user_type);
					
					$('#UserDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	<!--User Confirmed For Deletion-->
	$('body').on('click','#deleteUserConfirmed',function(){
			
			event.preventDefault();

			let userID = document.getElementById("deleteUserConfirmed").value;
			
				$.ajax({
				url: "/delete_user_confirmed",
				type:"POST",
				data:{
				  userID:userID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {
					  
                    $('#modal_success_details').html("User information successfully "+response.success);
                    $('#success-alert-modal').modal('toggle');

					var table = $("#userList").DataTable();
				    table.ajax.reload(null, false);
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });	
		
	});

	<!--Update User Branch Access-->
	function UpdateUserAccess(UserID){
			
			event.preventDefault();
			
			  $.ajax({
				url: "{{ route('getUserBranchAccess') }}",
				type:"GET",
				data:{
				  UserID:UserID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(result){
				  console.log(result);
				  if(result) {
					
					document.getElementById("update-user-branch-access").value = UserID;
					LoadBranchList.clear().draw();
					LoadBranchList.rows.add(result.data).draw();
					
					/*Get User Info*/
					UserSiteInfo(UserID);
					$('#BranchUserAccessModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	}

	let LoadBranchList = $('#UserBranchAccessList').DataTable( {
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
			    responsive: false,
			    paging: true,
			    searching: false,
			    info: false,
			    data: [],
			    //scrollCollapse: true,
			    //scrollY: '500px',
			    //scrollx: false,
				"columns": [
					{data: 'action', name: 'action', orderable: false, searchable: false},   
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'branch_code'},
					{data: 'branch_name'}
				]
	} );
  
	$('body').on('click','#update-user-branch-access',function(){
			
			event.preventDefault();

			let userID = document.getElementById("update-user-branch-access").value;

			var branch_checklist_item = [];		
			$.each($("input[name='branch_checklist']:checked"), function(){
			branch_checklist_item.push($(this).val());
			});
			var branch_checklist_item_checked = branch_checklist_item.join(",");
			
				$.ajax({
				url: "/add_user_access_post",
				type:"POST",
				data:{
				  userID:userID,
				  branch_items:branch_checklist_item_checked,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {
					
                    $('#modal_success_details').html("User branch access successfully "+response.success);
                    $('#success-alert-modal').modal('toggle');	

				  }
				},
				error: function(errors) {
				 console.log(errors);
				 
					$('#InvalidModal').modal('toggle');
				}
			   });	
	});
	  
	function UserSiteInfo(UserID){
			
			event.preventDefault();
			
			  $.ajax({
				url: "/user_info",
				type:"POST",
				data:{
				  UserID:UserID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					//document.getElementById("update-user").value = UserID;
					
					/*Set User Details*/
					$('#user_real_name_info_branch_access').html(response.user_real_name);
					$('#user_name_info_branch_access').html(response.user_name);
					$('#user_type_info_branch_access').html(response.user_type);			
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  };
	  
	function ResetFormUser(){
			
			event.preventDefault();
			$('#UserForm')[0].reset();

            $('#modal_title_action_user').html('Add User');

			document.getElementById('UserForm').className = "g-3 needs-validation";
	}		  
</script>
