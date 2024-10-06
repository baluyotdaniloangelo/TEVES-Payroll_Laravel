   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   <script type="text/javascript">
	<!--Load Table-->				
	$(function () {
				
		var switchTable = $('#userList').DataTable({
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
			responsive: true,
			serverSide: true,
			stateSave: true,/*Remember Searches*/
			scrollCollapse: true,
			scrollCollapse: true,
			scrollY: '500px',
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
			columnDefs: [
					{ className: 'text-center', targets: [0, 4, 5, 6, 7] },
			],
			
		});
		  /*Add Options*/
		  $('<div class="btn-group" role="group" aria-label="Basic outlined example"style="margin-top: -50px; position: absolute;">'+
		  '<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateUserModal" onclick="ResetFormUser()"></button>'+
		  '</div>').appendTo('#user_option');
		  
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
	
	
	function ChangeAccessType_Add(){

		let user_type 				= $("#user_type").val();
		var user_type_selected 		= $('#user_type').find(":selected").val();
		

		if(user_type_selected=='Admin'){
			document.getElementById('user_access').value = 'ALL';
		}
		else{
			document.getElementById('user_access').value = 'BYBRANCH';
		}
		
	}
	

	function ChangeAccessType_Update(){

		let user_type 				= $("#update_user_type").val();
		var user_type_selected 		= $('#update_user_type').find(":selected").val();
		

		if(user_type_selected=='Admin'){
			document.getElementById('update_user_access').value = 'ALL';
		}
		else{
			document.getElementById('update_user_access').value = 'BYBRANCH';
		}
		
	}

	
	<!--Save New Site-->
	$("#save-user").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/			
					$('#user_real_nameError').text('');				  
					$('#user_nameError').text('');
					$('#user_passwordError').text('');
					$('#user_typeError').text('');

			document.getElementById('CreateUserform').className = "g-3 needs-validation was-validated";
			
			let user_real_name 		= $("input[name=user_real_name]").val();
			let user_name 			= $("input[name=user_name]").val();
			let user_password 		= $("input[name=user_password]").val();
			let user_type 			= $("#user_type").val();
			let user_email_address 	= $("input[name=user_email_address_management]").val();
			let user_access 		= $("#user_access").val();
			let user_job_title 		= $("input[name=user_job_title]").val();
			
			  $.ajax({
				url: "/create_user_post",
				type:"POST",
				data:{
				  user_real_name:user_real_name,
				  user_name:user_name,
				  user_password:user_password,
				  user_type:user_type,
				  user_email_address:user_email_address,
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
					
					$('#user_email_address_managementError').text('');
					document.getElementById('user_email_address_management').className = "form-control";
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					document.getElementById("CreateUserform").reset();
				
					if(user_access=='BYSITE'){
						UpdateUserAccess(response.user_id);
					}
				
					var table = $("#userList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.user_real_name=="The user real name has already been taken."){
							  
				  $('#user_real_nameError').html("<b>"+ user_real_name +"</b> has already been taken.");
				  document.getElementById('user_real_nameError').className = "invalid-feedback";
				  document.getElementById('user_real_name').className = "form-control is-invalid";
				  $('#user_real_name').val("");
				  
				}else{
					
				  $('#user_real_nameError').text(error.responseJSON.errors.user_real_name);
				  document.getElementById('user_real_nameError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.user_email_address=="The user email address has already been taken."){
							  
				  $('#user_email_address_managementError').html("<b>"+ user_email_address +"</b> has already been taken.");
				  document.getElementById('user_email_address_managementError').className = "invalid-feedback";
				  document.getElementById('user_email_address_management').className = "form-control is-invalid";
				  $('#user_email_address_management').val("");
				  
				}else{
					
				  $('#user_email_address_managementError').text(error.responseJSON.errors.user_email_address);
				  document.getElementById('user_email_address_managementError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.user_name=="The user name has already been taken."){
							  
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
				
				  $('#switch_notice_off').show();
				  $('#sw_off').html("Invalid Input");
				  setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
				  
				}
			   });
		
	  });
	  
	function ResetFormUser(){
			
			event.preventDefault();
			$('#CreateUserform')[0].reset();

			$('#user_email_address_managementError').html("");
			document.getElementById('user_email_address_managementError').className = "valid-feedback";
			document.getElementById('user_email_address_management').className = "form-control";

			document.getElementById('CreateUserform').className = "g-3 needs-validation";
			
	}		  

	<!--Select Site For Update-->
	$('body').on('click','#editUser',function(){
			
			$('#UpdateUserform')[0].reset();
			$('#update_user_real_nameError').text('');				  
			$('#update_user_nameError').text('');
			$('#update_user_passwordError').text('');
			$('#update_user_typeError').text('');
			$('#update_user_job_titleError').text('');
			
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
					
					document.getElementById("update-user").value = UserID;
					document.getElementById("update-user").disabled = true;
					
					/*Set Switch Details*/
					document.getElementById("update_user_real_name").value = response.user_real_name;
					document.getElementById("update_user_name").value = response.user_name;
					document.getElementById("update_user_type").value = response.user_type;
					document.getElementById("update_user_email_address_management").value = response.user_email_address;
					document.getElementById("update_user_job_title").value = response.user_job_title;
					$('#UpdateUserModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	document.getElementById("update_user_real_name").addEventListener('change', doThing_account_management);
	document.getElementById("update_user_name").addEventListener('change', doThing_account_management);
	document.getElementById("update_user_email_address_management").addEventListener('change', doThing_account_management);

	document.getElementById("update_user_password").addEventListener('change', doThing_account_management);
	document.getElementById("update_user_type").addEventListener('change', doThing_account_management);
	document.getElementById("update_user_access").addEventListener('change', doThing_account_management);
	document.getElementById("update_user_job_title").addEventListener('change', doThing_account_management);
	
	function doThing_account_management(){

			let userID = document.getElementById("update-user").value;
		
			let user_real_name 		= $("input[name=update_user_real_name]").val();
			let user_name 			= $("input[name=update_user_name]").val();
			let user_email_address 	= $("input[name=update_user_email_address_management]").val();
			let user_password 		= $("input[name=update_user_password]").val();
			let user_type 			= $("#update_user_type").val();	
			let user_access 		= $("#update_user_access").val();
			let user_job_title 		= $("input[name=update_user_job_title]").val();
		
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
						
						 //alert('S1');
						if(response.user_real_name===user_real_name && response.user_name===user_name && response.user_email_address===user_email_address && response.user_type===user_type && response.user_branch_access_type===user_access && response.user_job_title===user_job_title){
							
							document.getElementById("update-user").disabled = false;
							//alert('b');
							
						}else{
							
							document.getElementById("update-user").disabled = false;
							//alert('c');
						}
					
				  }else{
					  
					  // alert('S2');
					  if(response.user_real_name===user_real_name && response.user_name===user_name && response.user_email_address===user_email_address && response.user_type===user_type && response.user_branch_access_type===user_access && response.user_job_title===user_job_title){
							
							document.getElementById("update-user").disabled = true;
							//alert('d')
							
						}else{
							
							document.getElementById("update-user").disabled = false;
							//alert('e')
							
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
	  

	$("#update-user").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					let userID = document.getElementById("update-user").value;
					$('#update_user_real_nameError').text('');				  
					$('#update_user_nameError').text('');
					$('#update_user_passwordError').text('');
					$('#update_user_typeError').text('');
					$('#update_user_job_titleError').text('');
			
			document.getElementById('UpdateUserform').className = "g-2 needs-validation was-validated";

			let user_real_name 		= $("input[name=update_user_real_name]").val();
			let user_name 			= $("input[name=update_user_name]").val();
			let user_password 		= $("input[name=update_user_password]").val();
			let user_type 			= $("#update_user_type").val();		
			let user_email_address 	= $("input[name=update_user_email_address_management]").val();
			let user_access 		= $("#update_user_access").val();
			let user_job_title 		= $("input[name=update_user_job_title]").val();
			
			$.ajax({
				url: "/update_user_post",
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
					  
					$('#update_user_real_nameError').text('');
					$('#update_switch_timerError').text('');					
					$('#update_user_typeError').text('');
					$('#update_user_email_address_managementError').text('');
					document.getElementById('update_user_email_address_management').className = "form-control";
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#UpdateUserModal').modal('toggle');
					
					var table = $("#userList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.user_real_name=="The user real name has already been taken."){
							  
				  $('#update_user_real_nameError').html("<b>"+ user_real_name +"</b> has already been taken.");
				  document.getElementById('update_user_real_nameError').className = "invalid-feedback";
				  document.getElementById('update_user_real_name').className = "form-control is-invalid";
				  $('#user_real_name').val("");
				  
				}else{
					
				  $('#update_user_real_nameError').text(error.responseJSON.errors.user_real_name);
				  document.getElementById('update_user_real_nameError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.user_name=="The user name has already been taken."){
							  
				  $('#update_user_nameError').html("<b>"+ user_name +"</b> has already been taken.");
				  document.getElementById('update_user_nameError').className = "invalid-feedback";
				  document.getElementById('update_user_name').className = "form-control is-invalid";
				  $('#update_user_name').val("");
				  
				}else{
					
				  $('#update_user_nameError').text(error.responseJSON.errors.user_real_name);
				  document.getElementById('update_user_nameError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.user_email_address=="The user email address has already been taken."){
							  
				  $('#update_user_email_address_managementError').html("<b>"+ user_email_address +"</b> has already been taken.");
				  document.getElementById('update_user_email_address_managementError').className = "invalid-feedback";
				  document.getElementById('update_user_email_address_management').className = "form-control is-invalid";
				  $('#update_user_email_address_management').val("");
				  
				}else{
					
				  $('#update_user_email_address_managementError').text(error.responseJSON.errors.user_email_address);
				  document.getElementById('update_user_email_address_managementError').className = "invalid-feedback";		
				
				}
					
				  $('#update_user_passwordError').text(error.responseJSON.errors.user_password);
				  document.getElementById('user_passwordError').className = "invalid-feedback";		
				  
				  $('#update_user_typeError').text(error.responseJSON.errors.user_type);
				  document.getElementById('update_user_typeError').className = "invalid-feedback";	

			      $('#update_user_job_titleError').text(error.responseJSON.errors.user_job_title);
				  document.getElementById('update_user_job_titleError').className = "invalid-feedback";					  
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
				  
				}
			   });
	  });
	<!--Switch Deletion Confirmation-->
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
					
					$('#user_real_name_info_confirm').html(response.user_real_name);
					$('#user_name_info_confirm').html(response.user_real_name);
					$('#user_type_info_confirm').html(response.user_type);
					
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
					
					$('#switch_notice_off').show();
					$('#sw_off').html("User Account Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	

					var table = $("#userList").DataTable();
				    table.ajax.reload(null, false);
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });	
		
	  });
	  

	<!--Update User Site Access-->
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
					
					document.getElementById("update-user-site-access").value = UserID;
					LoadSiteList.clear().draw();
					LoadSiteList.rows.add(result.data).draw();
					
					/*Get User Info*/
					UserSiteInfo(UserID);
					
					$('#SiteUserAccessModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	}

	let LoadSiteList = $('#UserSiteAccessList').DataTable( {
				"language": {
						"lengthMenu":'<select class="form-select form-control form-control-sm">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> '
			    }, 
				//processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: true,
				paging: true,
				searching: true,
				info: true,
				data: [],
				"columns": [
					{data: 'action', name: 'action', orderable: false, searchable: false},   
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'branch_code'},
					{data: 'branch_name'}
				]
	} );
  
	$('body').on('click','#update-user-site-access',function(){
			
			event.preventDefault();

			let userID = document.getElementById("update-user-site-access").value;

			var site_checklist_item = [];		
			$.each($("input[name='site_checklist']:checked"), function(){
			site_checklist_item.push($(this).val());
			});
			var site_checklist_item_checked = site_checklist_item.join(",");
			
				$.ajax({
				url: "/add_user_access_post",
				type:"POST",
				data:{
				  userID:userID,
				  site_items:site_checklist_item_checked,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
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
					
					document.getElementById("update-user").value = UserID;
					
					/*Set User Details*/
					$('#user_real_name_info_site_access').html(response.user_real_name);
					$('#user_name_info_site_access').html(response.user_name);
					$('#user_type_info_site_access').html(response.user_type);			
				  
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
			$('#CreateUserform')[0].reset();

			document.getElementById('CreateUserform').className = "g-3 needs-validation";
	}		  
	  
</script>