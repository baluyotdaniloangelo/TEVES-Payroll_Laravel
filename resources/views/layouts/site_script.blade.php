
   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script type="text/javascript">
   // $(window).resize(function(){location.reload();});

	<?php 
			
	if($data->user_type=="Admin"){ 
		$route_ctr = 'AdminSiteList'; 
		?>
			<!--Load Table-->
			$(function () {
			
				var siteTable = $('#siteList').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					scrollCollapse: true,
					scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('AdminSiteList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},       
							{data: 'building_code', className: "text-left"},
							{data: 'building_description', className: "text-left"},
							{data: 'company_name', className: "text-left"},
							{data: 'division_code', className: "text-left"},
							{data: 'cut_off', className: "text-center"},
							{data: 'status', name: 'status', orderable: true, searchable: true, className: "text-center"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});


		autoAdjustColumns(siteTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	
			});	
		<?php
	} 
	else{
		$route_ctr = 'UserSiteList';
		?>
			<!--Load Table-->
			$(function () {
			
				var siteTable = $('#siteList').DataTable({
					processing: true,
					responsive: true,
					serverSide: true,
					stateSave: true,/*Remember Searches*/
					scrollCollapse: true,
					scrollY: '500px',
					//scrollX: '100%',
					ajax: "{{ route('UserSiteList') }}",
					columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},       
							{data: 'building_code', className: "text-left"},
							{data: 'building_description', className: "text-left"},
							{data: 'company_name', className: "text-left"},
							{data: 'division_code', className: "text-left"},
							{data: 'cut_off', className: "text-center"},
							{data: 'status', name: 'status', orderable: true, searchable: true, className: "text-center"},
							{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					]
				});
				
autoAdjustColumns(siteTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	

			});	
		<?php
	} 
			
	?>

		

	<!--Save New Site-->
	$("#save-site").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#building_codeError').text('');
					$('#building_descriptionError').text('');				  
					//$('#site_cut_offError').text('');
					$('#device_ip_rangeError').text('');
					$('#ip_netmaskError').text('');
					$('#ip_networkError').text('');
					$('#ip_gatewayError').text('');

			document.getElementById('siteform').className = "g-2 needs-validation was-validated";

			let building_code 			= $("input[name=building_code]").val();
			let building_description 	= $("input[name=building_description]").val();
			
			let division_id 			= $("#division_list option[value='" + $('#division_id').val() + "']").attr('data-id');
			let company_id				= $("#company_list option[value='" + $('#company_id').val() + "']").attr('data-id');
			
			//let site_cut_off 		= $("input[name=site_cut_off]").val();
			let device_ip_range 	= $("input[name=device_ip_range]").val();
			let ip_netmask 			= $("input[name=ip_netmask]").val();
			let ip_network 			= $("input[name=ip_network]").val();
			let ip_gateway 			= $("input[name=ip_gateway]").val();
			
			  $.ajax({
				url: "/create_site_post",
				type:"POST",
				data:{
				  building_code:building_code,
				  building_description:building_description,
				  division_id:division_id,
				  company_id:company_id,
				  //site_cut_off:site_cut_off,
				  device_ip_range:device_ip_range,
				  ip_netmask:ip_netmask,
				  ip_network:ip_network,
				  ip_gateway:ip_gateway,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#building_codeError').text('');	
					$('#building_descriptionError').text('');		

					var table = $("#siteList").DataTable();
				    table.ajax.reload(null, false);					
					
					ResetFormSite();
					
					$('#division_idError').text('');
					document.getElementById('division_idError').className = "invalid-tooltip";	
					document.getElementById('division_id').className = "form-control";
					
					$('#company_idError').text('');
					document.getElementById('company_idError').className = "invalid-tooltip";	
					document.getElementById('company_id').className = "form-control";
					
					document.getElementById('siteform').className = "g-3 needs-validation";
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				if(error.responseJSON.errors.building_code=="The building code has already been taken."){
							  
				  $('#building_codeError').html("<b>"+ building_code +"</b> has already been taken.");
				  document.getElementById('building_codeError').className = "invalid-tooltip";
				  document.getElementById('building_code').className = "form-control is-invalid";
				  $('#building_code').val("");
				  
				}else{
					
				  $('#building_codeError').text(error.responseJSON.errors.building_code);
				  document.getElementById('building_codeError').className = "invalid-tooltip";		
				
				}
				
				if(error.responseJSON.errors.building_description=="The building description has already been taken."){
							  
				  $('#building_descriptionError').html("<b>"+ building_description +"</b> has already been taken.");
				  document.getElementById('building_descriptionError').className = "invalid-tooltip";
				  document.getElementById('building_description').className = "form-control is-invalid";
				  $('#building_description').val("");
				  
				}else{
					
				  $('#building_descriptionError').text(error.responseJSON.errors.building_description);
				  document.getElementById('building_descriptionError').className = "invalid-tooltip";		
				
				}
				
				if(division_id==0||division_id==undefined){
					
					$('#division_idError').text('Please Select a Division');
					document.getElementById('division_idError').className = "invalid-tooltip";	
					document.getElementById('division_id').className = "form-control is-invalid";
				
				}else{
					
					$('#division_idError').text('');
					document.getElementById('division_idError').className = "invalid-tooltip";	
					document.getElementById('division_id').className = "form-control";
					
				}
				
				if(company_id==0||company_id==undefined){
					
					$('#company_idError').text('Please Select a Company');
					document.getElementById('company_idError').className = "invalid-tooltip";	
					document.getElementById('company_id').className = "form-control is-invalid";
				
				}else{
					
					$('#company_idError').text('');
					document.getElementById('company_idError').className = "invalid-tooltip";	
					document.getElementById('company_id').className = "form-control";
					
				}
			
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
					document.getElementById("update_building_code").value = response[0].building_code;
					document.getElementById("update_building_description").value = response[0].building_description;

					document.getElementById("update_division_id").value = response[0].division_code + " - " + response[0].division_name;
					document.getElementById("update_company_id").value = response[0].company_name;
					
					//document.getElementById("update_site_cut_off").value = response[0].site_cut_off;
					
					document.getElementById("update_device_ip_range").value = response[0].device_ip_range;
					document.getElementById("update_ip_network").value = response[0].ip_network;
					document.getElementById("update_ip_netmask").value = response[0].ip_netmask;
					document.getElementById("update_ip_gateway").value = response[0].ip_gateway;
					
					/*Reset Warnings*/
					$('#update_division_idError').text('');
					$('#update_company_idError').text('');
					
					$('#UpdateSiteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });
				  
	  });

	document.getElementById("update_building_code").addEventListener('change', doThing_site_management);
	document.getElementById("update_building_description").addEventListener('change', doThing_site_management);
	document.getElementById("update_division_id").addEventListener('change', doThing_site_management);
	document.getElementById("update_company_id").addEventListener('change', doThing_site_management);
	
	document.getElementById("update_device_ip_range").addEventListener('change', doThing_site_management);
	document.getElementById("update_ip_netmask").addEventListener('change', doThing_site_management);
	document.getElementById("update_ip_network").addEventListener('change', doThing_site_management);
	document.getElementById("update_ip_gateway").addEventListener('change', doThing_site_management);
	
	function doThing_site_management(){

			let siteID = document.getElementById("update-site").value;
			
			let building_code 			= $("input[name=update_building_code]").val();
			let building_description 	= $("input[name=update_building_description]").val();
			let division_id 			= $("#update_division_list option[value='" + $('#update_division_id').val() + "']").attr('data-id');
			let company_id				= $("#update_company_list option[value='" + $('#update_company_id').val() + "']").attr('data-id');
			let device_ip_range 		= $("input[name=update_device_ip_range]").val();
			let ip_netmask 				= $("input[name=update_ip_netmask]").val();
			let ip_network 				= $("input[name=update_ip_network]").val();
			let ip_gateway 				= $("input[name=update_ip_gateway]").val();
	
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
						device_ip_range_value 	= response[0].device_ip_range || '';
						ip_netmask_value 		= response[0].ip_netmask || '';
						ip_network_value 		= response[0].ip_network || '';
						ip_gateway_value 		= response[0].ip_gateway || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].building_code===building_code &&
						  response[0].building_description===building_description &&
						  response[0].division_id==division_id &&
						  response[0].company_id==company_id  &&
						  response[0].company_id==company_id  &&
						  device_ip_range_value===device_ip_range  &&
						  ip_netmask_value===ip_netmask  &&
						  ip_network_value===ip_network  &&
						  ip_gateway_value===ip_gateway 
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
					$('#update_building_codeError').text('');
					$('#update_building_codeError').text('');
					$('#update_building_descriptionError').text('');				  
					//$('#update_site_cut_offError').text('');
					$('#update_device_ip_rangeError').text('');
					$('#update_ip_netmaskError').text('');
					$('#update_ip_networkError').text('');
					$('#update_ip_gatewayError').text('');

			document.getElementById('updatesiteform').className = " g-2 needs-validation was-validated";
			
			let building_id				= $("input[name=update_building_id]").val();
			
			let building_code 			= $("input[name=update_building_code]").val();
			let building_description 	= $("input[name=update_building_description]").val();
			
			let division_id 			= $("#update_division_list option[value='" + $('#update_division_id').val() + "']").attr('data-id');
			let company_id				= $("#update_company_list option[value='" + $('#update_company_id').val() + "']").attr('data-id');
			
			//let site_cut_off 			= $("input[name=update_site_cut_off]").val();
			let device_ip_range 		= $("input[name=update_device_ip_range]").val();
			let ip_netmask 				= $("input[name=update_ip_netmask]").val();
			let ip_network 				= $("input[name=update_ip_network]").val();
			let ip_gateway 				= $("input[name=update_ip_gateway]").val();
			
			  $.ajax({
				url: "/update_site_post",
				type:"POST",
				data:{
				  SiteID:siteID,
				  building_id:building_id,
				  building_code:building_code,
				  building_description:building_description,
				  division_id:division_id,
				  company_id:company_id,
				  //site_cut_off:site_cut_off,
				  device_ip_range:device_ip_range,
				  ip_netmask:ip_netmask,
				  ip_network:ip_network,
				  ip_gateway:ip_gateway,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#building_codeError').text('');	
					$('#building_descriptionError').text('');				  
				  
					var table = $("#siteList").DataTable();
				    table.ajax.reload(null, false);
					
					$('#update_division_idError').text('');
					document.getElementById('update_division_idError').className = "invalid-tooltip";	
					document.getElementById('update_division_id').className = "form-control";
				  
					$('#update_company_idError').text('');
					document.getElementById('update_company_idError').className = "invalid-tooltip";	
					document.getElementById('update_company_id').className = "form-control";
					
					$('#updatesiteform')[0].reset();
					$('#UpdateSiteModal').modal('toggle');	
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				  			  
				if(error.responseJSON.errors.building_code=="The building code has already been taken."){
							  
				  $('#update_building_codeError').html("<b>"+ building_code +"</b> has already been taken.");
				  document.getElementById('update_building_codeError').className = "invalid-tooltip";
				  document.getElementById('update_building_code').className = "form-control is-invalid";
				  $('#update_building_code').val("");
				  
				}else{
					
				  $('#update_building_codeError').text(error.responseJSON.errors.building_code);
				  document.getElementById('update_building_codeError').className = "invalid-tooltip";		
				
				}
				
				if(error.responseJSON.errors.building_description=="The building description has already been taken."){
							  
				  $('#update_building_descriptionError').html("<b>"+ building_description +"</b> has already been taken.");
				  document.getElementById('update_building_descriptionError').className = "invalid-tooltip";
				  document.getElementById('update_building_description').className = "form-control is-invalid";
				  $('#update_building_description').val("");
				  
				}else{
					
				  $('#update_building_descriptionError').text(error.responseJSON.errors.building_description);
				  document.getElementById('update_building_descriptionError').className = "invalid-tooltip";		
				
				}
				
				
				if(division_id==0||division_id==undefined){
					
					$('#update_division_idError').text('Please Select a Division');
					document.getElementById('update_division_idError').className = "invalid-tooltip";	
					document.getElementById('update_division_id').className = "form-control is-invalid";
				
				}else{     
					
					$('#update_division_idError').text('');
					document.getElementById('update_division_idError').className = "invalid-tooltip";	
					document.getElementById('update_division_id').className = "form-control";
					
				}
				
				if(company_id==0||company_id==undefined){
					
					$('#update_company_idError').text('Please Select a Company');
					document.getElementById('update_company_idError').className = "invalid-tooltip";	
					document.getElementById('update_company_id').className = "form-control is-invalid";
				
				}else{
					
					$('#update_company_idError').text('');
					document.getElementById('update_company_idError').className = "invalid-tooltip";	
					document.getElementById('update_company_id').className = "form-control";
					
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
					
					$('#building_code_delete_info').html(response[0].building_code);
					$('#building_description_delete_info').html(response[0].building_description);
					
					$('#building_code_delete_confirmed_info').html(response[0].building_description);
					$('#building_description_delete_confirmed_info').html(response[0].building_code);
					
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
  
  	function ResetFormSite(){
			
			event.preventDefault();
			$('#siteform')[0].reset();
			
			document.getElementById('siteform').className = "g-3 needs-validation";
			
			$('#division_idError').text('');
			document.getElementById('division_idError').className = "valid-feedback";	
			document.getElementById('division_id').className = "form-control";
					
			$('#company_idError').text('');
			document.getElementById('company_idError').className = "valid-feedback";	
			document.getElementById('company_id').className = "form-control";
			
			$('#building_codeError').text('');
			document.getElementById('building_codeError').className = "valid-feedback";
			document.getElementById('building_code').className = "form-control";
			
			$('#building_descriptionError').text('');	
			document.getElementById('building_descriptionError').className = "valid-feedback";
			document.getElementById('building_description').className = "form-control";
			
	}	
  </script>