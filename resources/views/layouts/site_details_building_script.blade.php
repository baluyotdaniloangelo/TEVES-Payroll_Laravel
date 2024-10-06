  <script>    
	<!--Save New Building-->
	$("#save-building").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#building_codeError').text('');
					$('#building_descriptionError').text('');

			document.getElementById('buildingform').className = "g-2 needs-validation was-validated";

			let building_code 				= $("input[name=building_code]").val();
			let building_description 		= $("input[name=building_description]").val();
			
			  $.ajax({
				url: "{{ route('CREATE_BUILDING_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData->id }},
				  building_code:building_code,
				  building_description:building_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#building_codeError').text('');					
					$('#building_descriptionError').text('');	

					var table = $("#buildinglist").DataTable();
				    table.ajax.reload(null, false);					
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.building_code=="The building code has already been taken."){
							  
				  $('#building_codeError').html("<b>"+ building_code +"</b> has already been taken.");
				  document.getElementById('building_codeError').className = "invalid-feedback";
				  document.getElementById('building_code').className = "form-control is-invalid";
				  $('#building_code').val("");
				  
				}else{
					
				  $('#building_codeError').text(error.responseJSON.errors.building_code);
				  document.getElementById('building_codeError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.building_description=="The building description has already been taken."){
							  
				  $('#building_descriptionError').html("<b>"+ building_description +"</b> has already been taken.");
				  document.getElementById('building_descriptionError').className = "invalid-feedback";
				  document.getElementById('building_description').className = "form-control is-invalid";
				  $('#building_description').val("");
				  
				}else{
					
				  $('#building_descriptionError').text(error.responseJSON.errors.building_description);
				  document.getElementById('building_descriptionError').className = "invalid-feedback";		
				
				}
				 
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });	

	<!--Select Building For Update-->
	$('body').on('click','#editBuilding',function(){
			
			event.preventDefault();
			let buildingID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('BldgInfo') }}",
				type:"POST",
				data:{
				  buildingID:buildingID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-building").value = buildingID;
						
					document.getElementById("update_building_code").value = response.building_code;
					document.getElementById("update_building_description").value = response.building_description;
				
					$('#UpdateBuildingModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	<!--Update New Building-->
	$("#update-building").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#update_building_codeError').text('');
					$('#update_building_descriptionError').text('');

			document.getElementById('updatebuildingform').className = "g-2 needs-validation was-validated";
			
			let buildingID = document.getElementById("update-building").value;
			
			let building_code 				= $("input[name=update_building_code]").val();
			let building_description 		= $("input[name=update_building_description]").val();
			
			  $.ajax({
				url: "{{ route('UPDATE_BUILDING_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData->id }},
				  buildingID:buildingID,
				  building_code:building_code,
				  building_description:building_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#update_building_codeError').text('');					
					$('#update_building_descriptionError').text('');	

					var table = $("#buildinglist").DataTable();
				    table.ajax.reload(null, false);					
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.building_code=="The building code has already been taken."){
							  
				  $('#update_building_codeError').html("<b>"+ building_code +"</b> has already been taken.");
				  document.getElementById('update_building_codeError').className = "invalid-feedback";
				  document.getElementById('update_building_code').className = "form-control is-invalid";
				  $('#update_building_code').val("");
				  
				}else{
					
				  $('#update_building_codeError').text(error.responseJSON.errors.building_code);
				  document.getElementById('update_building_codeError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.building_description=="The building description has already been taken."){
							  
				  $('#update_building_descriptionError').html("<b>"+ building_description +"</b> has already been taken.");
				  document.getElementById('update_building_descriptionError').className = "invalid-feedback";
				  document.getElementById('update_building_description').className = "form-control is-invalid";
				  $('#update_building_description').val("");
				  
				}else{
					
				  $('#update_building_descriptionError').text(error.responseJSON.errors.building_description);
				  document.getElementById('building_descriptionError').className = "invalid-feedback";		
				
				}
				 
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });	

	<!--Building Deletion Confirmation-->
	$('body').on('click','#deleteBuilding',function(){
			
			event.preventDefault();
			let buildingID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('BldgInfo') }}",
				type:"POST",
				data:{
				  buildingID:buildingID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteBuildingConfirmed").value = buildingID;
					$('#building_info').html(response.building_code + "/" + response.building_description);
					$('#building_info_confirmed').html(response.building_code + "/" + response.building_description);
					$('#BuildingDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		  
	  });

	<!--Building Confirmed For Deletion-->
	$('body').on('click','#deleteBuildingConfirmed',function(){
			
			event.preventDefault();

			let buildingID = document.getElementById("deleteBuildingConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteBuildingInfo') }}",
				type:"POST",
				data:{
				  buildingID:buildingID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#BuildingDeleteModalConfirmed').modal('toggle');
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#buildinglist").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });  
	});
	
	LoadBuildingListOnAccordion();
	/*Load Building List on Accordion*/
	function LoadBuildingListOnAccordion() {	

			  $.ajax({
				url: "/get_building_accordion",
				type:"POST",
				data:{
					siteID:{{ $SiteData->id }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var id = response[i].id;
							var building_code = response[i].building_code;
							var building_description = response[i].building_description;
							
							$('.accordion-item').last().append(
							'<div align="right"><div class="btn-group" role="group" aria-label="Basic outlined example"  ">'+
							
							'<a href="#" data-id="'+id+'" class="btn btn-info new_item bi bi-eye-fill form_button_icon"  id="viewBuilding" onclick="GatewayPerBuilding('+id+','+0+')"  title="View Building Area/EE Room Location for '+building_code+'">'+  
							'<span title=""></span></a>'+
							
							'<a href="#" data-id="'+id+'" class="btn btn-warning new_item bi bi-pencil-fill form_button_icon"  id="editBuilding" title="Edit Building Details for '+building_code+'">'+  
							'<span title=""></span></a>'+
							
							'<a href="#" data-id="'+id+'" class="btn btn-danger new_item bi bi-trash3-fill form_button_icon"  id="deleteBuilding" title="Delete Building Details for '+building_code+'">'+  
							'<span title=""></span></a>'+
							
							'</div></div>'+
							'<h2 class="accordion-header" id="accordion_'+building_code+'" title="Click to Load EE Room, Meter and Gateway List">'+
							'<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion_target_'+building_code+'" aria-expanded="false" aria-controls="collapseOne" title="Click to View Area / EE Room">'+
							  'Code:'+building_code+'<br>'+
							  'Description:'+building_description+'<br>'+
							'</button>'+
							'</h2>'+
						  '<div id="accordion_target_'+building_code+'" class="accordion-collapse collapse" aria-labelledby="accordion_'+building_code+'" data-bs-parent="#accordionExample" style="">'+
							'<div class="accordion-body">'+
								'<ol class="list-group list-group-numbered" id="eeroomlist_'+id+'"> '+
								'</ol>'+
							'</div>'+
						  '</div>'+
						'');
							
							/*Load EE ROOM / AREA LOCATION*/
							LoadBuildingEERoomList(id,0);
					
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
	
	GatewayPerBuilding(0,0);
	
	function GatewayPerBuilding(building_id,location_id){
		
		/*Load Meter*/
		MeterPerBuilding(building_id,location_id);
						
			  $.ajax({
				url: "{{ route('getGatewayPerBLGandEEROOM') }}",
				type:"GET",
				data:{
				  siteID:{{ $SiteData->id }},
				  building_id:building_id,
				  location_id:location_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(result){
				  console.log(result);
				  if(result) {
					
					LoadGatewayPerBuildingList.clear().draw();
					LoadGatewayPerBuildingList.rows.add(result.data).draw();
						
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
		
	  };
	  
	let LoadGatewayPerBuildingList = $('#gatewaylist').DataTable( {
				/*
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
				*/
				//processing: true,
				//serverSide: true,
				stateSave: true,/*Remember Searches*/
				responsive: true,
				paging: true,
				searching: true,
				info: true,
				data: [],
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'rtu_sn_number'},           
					{data: 'phone_no_or_ip_address'},
					{data: 'mac_addr'},
					{data: 'building_code'},
					{data: 'location_code'},
					{data: 'idf_number'},
					{data: 'switch_name'},
					{data: 'idf_port'},
					{data: 'status', name: 'status', orderable: true, searchable: true},
					{data: 'update', name: 'status', orderable: false, searchable: true},
					{data: 'action', name: 'action', orderable: false, searchable: false},
				]
	} );
	
	
	//MeterPerBuilding(0,0);
	
	function MeterPerBuilding(building_id,location_id){
			
			  $.ajax({
				url: "{{ route('getMeter') }}",
				type:"GET",
				data:{
				  siteID:{{ $SiteData->id }},
				  building_id:building_id,
				  location_id:location_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(result){
				  console.log(result);
				  if(result) {
					
					LoadMeterPerBuildingList.clear().draw();
					LoadMeterPerBuildingList.rows.add(result.data).draw();				
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
		
	  };
	  
	let LoadMeterPerBuildingList = $('#meterlist').DataTable( {
				/*
				"language": {
						"lengthMenu":'<select class="form-select form-control form-control-sm">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> '
			    },*/
				//processing: true,
				//serverSide: true,
				stateSave: true,/*Remember Searches*/
				responsive: true,
				paging: true,
				searching: true,
				info: true,
				data: [],
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					/*{data: 'measuring_point'},*/           
					{data: 'meter_name'},
					{data: 'customer_name'},
					{data: 'last_log_update'},
					{data: 'meter_status'},				
					{data: 'rtu_sn_number'},
					{data: 'location_code'},
					{data: 'location_description'},
					{data: 'building_code'},
					{data: 'building_description'},
					{data: 'meter_role'},
					{data: 'meter_config_file'},
					{data: 'meter_default_name'},
					{data: 'meter_remarks'},
					{data: 'action', name: 'action', orderable: false, searchable: false},
					
				]
	} );	
	
	/*Load Building EE Room List on Accordion*/
	function LoadBuildingEERoomList(building_id,location_id) {	
	
			  $.ajax({
				url: "/get_ee_room_location_accordion",
				type:"POST",
				data:{
					siteID:{{ $SiteData->id }},
					building_id:building_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						$('#eeroomlist_'+building_id).html('');
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var id = response[i].id;
							var location_code = response[i].location_code;
							var location_description = response[i].location_description;
							
							$('#eeroomlist_'+building_id).prepend(
							'<li class="list-group-item d-flex justify-content-between align-items-start" title="">'+
							    '<div class="ms-2 me-auto">'+
								'<div class="fw-bold">Location Code:'+location_code+'</div>'+
								'Location Description:'+location_description+
								'</div>'+
							'<span class="badge">'+
							'<div align="right" class="action_table_menu_site">'+
							'<a href="#" data-id="'+id+'" style="cursor: pointer;" class="btn-info btn-circle bi bi-eye-fill btn_icon_accordion btn_icon_table_view" id="viewMeterLocation" onclick="GatewayPerBuilding('+building_id+','+id+')"></a>'+
							'<a href="#" data-id="'+id+'" style="cursor: pointer;" class="btn-warning btn-circle bi bi-pencil-fill btn_icon_accordion btn_icon_table_edit" id="editMeterLocation"></a>'+
							'<a href="#" data-id="'+id+'" style="cursor: pointer;" class="btn-danger btn-circle bi-trash3-fill btn_icon_accordion btn_icon_table_delete" id="deleteMeterLocation"></a>'+
							'</div>'+'</span>'+
							'</li>');
							
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
 </script>
