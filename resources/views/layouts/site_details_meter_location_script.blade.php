  <script>    
	/*Load Location List*/
	$(document).ready(function() {
		let  LoadLocationList = $('#meterlocationlist').DataTable( {
				// language: {
					// lengthMenu: 'Display _MENU_ records'
				// },
				processing: true,
				stateSave: true,/*Remember Searches*/
				scrollCollapse: true,
				scrollY: '500px',
				scrollX: '100%',
				responsive: true,
				paging: true,
				searching: true,
				info: true,
				ajax: {				
					url: "{{ route('getMeterLocation') }}",
					type:"POST",
					data:{
						siteID:{{ $SiteData[0]->site_id }},
						_token: "{{ csrf_token() }}"
					},
					},
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , searchable: false},
					{data: 'location_code', className: "text-left" },        
					{data: 'location_description', className: "text-left" },
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
				]
			} );
	
	
	
		//var table = $('#my-dt').DataTable().columns.adjust();
		autoAdjustColumns_LocationList(LoadLocationList);

		/*Adjust Table Column*/
		function autoAdjustColumns_LocationList(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
	
	} );
  
  
	<!--Save New Location-->
	$("#save-meterlocation").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#location_codeError').text('');
					$('#location_descriptionError').text('');

			document.getElementById('meterlocationform').className = "g-2 needs-validation was-validated";
			
			let location_code 				= $("input[name=location_code]").val();
			let location_description 		= $("input[name=location_description]").val();
			
			  $.ajax({
				url: "{{ route('CREATE_METER_LOCATION_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				 // building_id:building_id,
				  location_code:location_code,
				  location_description:location_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#location_codeError').text('');	
					document.getElementById('location_code').className = "form-control";
					
					$('#location_descriptionError').text('');
					document.getElementById('location_description').className = "form-control";

					LoadBuildingEERoomList();	
					
					var table = $("#meterlocationlist").DataTable();
				    table.ajax.reload(null, false);	
					
					$('#meterlocationform')[0].reset();
					document.getElementById('meterlocationform').className = "g-3 needs-validation";
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.location_code=="The location code has already been taken."){
							  
				  $('#location_codeError').html("<b>"+ location_code +"</b> has already been taken.");
				  document.getElementById('location_codeError').className = "invalid-feedback";
				  document.getElementById('location_code').className = "form-control is-invalid";
				  $('#location_code').val("");
				  
				}else{
					
				  $('#location_codeError').text(error.responseJSON.errors.location_code);
				  document.getElementById('location_codeError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.location_description=="The location description has already been taken."){
							  
				  $('#location_descriptionError').html("<b>"+ location_description +"</b> has already been taken.");
				  document.getElementById('location_descriptionError').className = "invalid-feedback";
				  document.getElementById('location_description').className = "form-control is-invalid";
				  $('#location_description').val("");
				  
				}else{
					
				  $('#location_descriptionError').text(error.responseJSON.errors.location_description);
				  document.getElementById('location_descriptionError').className = "invalid-feedback";		
				
				}
				 
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });		

	<!--Select Building For Update-->
	$('body').on('click','#editMeterLocation',function(){
			
			event.preventDefault();
			let meterlocationID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('MeterLocationInfo') }}",
				type:"POST",
				data:{
				  meterlocationID:meterlocationID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-meterlocation").value = meterlocationID;
					document.getElementById("update-meterlocation").disabled = true;
					
					document.getElementById("update_location_code").value = response[0].location_code;
					document.getElementById("update_location_description").value = response[0].location_description;
				
					$('#UpdateMeterLocationModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });


	document.getElementById("update_location_code").addEventListener('change', doThing_meterlocation_management);
	document.getElementById("update_location_description").addEventListener('change', doThing_meterlocation_management);
	
	function doThing_meterlocation_management(){

			let meterlocationID = document.getElementById("update-meterlocation").value;

			let location_code 				= $("input[name=update_location_code]").val();
			let location_description 		= $("input[name=update_location_description]").val();
	
				$.ajax({
					url: "{{ route('MeterLocationInfo') }}",
					type:"POST",
					data:{
					  meterlocationID:meterlocationID,
					  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
						
						/*Below items to Convert to Empty instead of NULL*/
						// device_ip_range_value = response[0].device_ip_range || '';
						// ip_netmask_value 		= response[0].ip_netmask || '';
						// ip_network_value 		= response[0].ip_network || '';
						// ip_gateway_value 		= response[0].ip_gateway || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].location_code===location_code &&
						  response[0].location_description===location_description
					  ){
							
							document.getElementById("update-meterlocation").disabled = true;
							$('#loading_data_updateLocation').hide();
							
						}else{
							
							document.getElementById("update-meterlocation").disabled = false;
							$('#loading_data_updateLocation').hide();
							
						}
						
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				},
				beforeSend:function()
				{
					$('#loading_data_updateLocation').show();
				}
			   });	
    }


	<!--Save New Location-->
	$("#update-meterlocation").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#location_codeError').text('');
					$('#location_descriptionError').text('');

			document.getElementById('updatemeterlocationform').className = "g-2 needs-validation was-validated";
			
			let meterlocationID = document.getElementById("update-meterlocation").value;

			let location_code 				= $("input[name=update_location_code]").val();
			let location_description 		= $("input[name=update_location_description]").val();
			
			  $.ajax({
				url: "{{ route('UPDATE_METER_LOCATION_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				  meterlocationID:meterlocationID,
				  location_code:location_code,
				  location_description:location_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#update_location_codeError').text('');					
					$('#update_location_descriptionError').text('');	

					LoadBuildingEERoomList();	

					var table = $("#meterlocationlist").DataTable();
				    table.ajax.reload(null, false);	
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.location_code=="The location code has already been taken."){
							  
				  $('#update_location_codeError').html("<b>"+ location_code +"</b> has already been taken.");
				  document.getElementById('update_location_codeError').className = "invalid-feedback";
				  document.getElementById('update_location_code').className = "form-control is-invalid";
				  $('#update_location_code').val("");
				  
				}else{
					
				  $('#update_location_codeError').text(error.responseJSON.errors.location_code);
				  document.getElementById('update_location_codeError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.location_description=="The location description has already been taken."){
							  
				  $('#update_location_descriptionError').html("<b>"+ location_description +"</b> has already been taken.");
				  document.getElementById('update_location_descriptionError').className = "invalid-feedback";
				  document.getElementById('update_location_description').className = "form-control is-invalid";
				  $('#update_location_description').val("");
				  
				}else{
					
				  $('#update_location_descriptionError').text(error.responseJSON.errors.location_description);
				  document.getElementById('update_location_descriptionError').className = "invalid-feedback";		
				
				}
				 
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });		

	<!--Meter Location Deletion Confirmation-->
	$('body').on('click','#deleteMeterLocation',function(){
			
			event.preventDefault();
			let meterlocationID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('MeterLocationInfo') }}",
				type:"POST",
				data:{
				  meterlocationID:meterlocationID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deletemeterlocationConfirmed").value = meterlocationID;
					
					$('#meter_location_code_delete').html(response[0].location_code);
					$('#meter_location_description_delete').html(response[0].location_description);
					
					$('#meter_location_code_delete_confirmed').html(response[0].location_code);
					$('#meter_location_description_delete_confirmed').html(response[0].location_description);
					
					$('#meterlocationDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		  
	  });

	<!--location Confirmed For Deletion-->
	$('body').on('click','#deletemeterlocationConfirmed',function(){
			
			event.preventDefault();

			let meterlocationID = document.getElementById("deletemeterlocationConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteMeterLocationInfo') }}",
				type:"POST",
				data:{
				  meterlocationID:meterlocationID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#meterlocationDeleteModalConfirmed').modal('toggle');
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					LoadBuildingEERoomList();	
					
					var table = $("#meterlocationlist").DataTable();
				    table.ajax.reload(null, false);	
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });  
	});

	
  	function ResetFormEEroom(){
			
			$('#location_codeError').text('');	
			document.getElementById('location_code').className = "form-control";
					
			$('#location_descriptionError').text('');
			document.getElementById('location_description').className = "form-control";

			$('#meterlocationform')[0].reset();
			document.getElementById('meterlocationform').className = "g-3 needs-validation";
			
	}	
	  

	LoadBuildingEERoomList();
	/*Load Building EE Room List on Accordion*/
	function LoadBuildingEERoomList() {	
			
			/*Add Gateway*/
			$("#gateway_location_list option").remove();
			$('<option style="display: none;"></option>').appendTo('#gateway_location_list');
			/*Update Gateway*/
			$("#update_gateway_location_list option").remove();
			$('<option style="display: none;"></option>').appendTo('#update_gateway_location_list');
			
			/*Add Meter*/
			$("#location option").remove();
			$('<option style="display: none;"></option>').appendTo('#location');
			
			/*Update Meter */
			$("#update_location option").remove();
			$('<option style="display: none;"></option>').appendTo('#update_location');
			
		
		
			
			/*Location Meter Filter */
			$("#EERoomFilter_meter option").remove();
			$('<option value="">Show All</option>').appendTo('#EERoomFilter_meter');
			
			/*Location Gateway Filter */
			$("#EERoomFilter_gateway option").remove();
			$('<option value="">Show All</option>').appendTo('#EERoomFilter_gateway');
			
			  $.ajax({
				url: "/get_ee_room_location_accordion",
				type:"POST",
				data:{
					siteID:{{ $SiteData[0]->site_id }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						//$('.dt-length').html('');
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var id = response[i].location_id;
							var location_code = response[i].location_code;
							var location_description = response[i].location_description;

							$('#gateway_location_list option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#update_gateway_location_list option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#location option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#update_location option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#EERoomFilter_meter option:last').after("<option label='"+location_code+" - "+location_description+"' data-id='"+id+"' value='"+location_code+"'>");
							$('#EERoomFilter_gateway option:last').after("<option label='"+location_code+" - "+location_description+"' data-id='"+id+"' value='"+location_code+"'>");
							
					}			
				  }else{
					  
							/*No Result Found or Error*/	
							/*Add Gateway*/
							$("#gateway_location_list option").remove();
							$('<option style="display: none;"></option>').appendTo('#gateway_location_list');
							/*Update Gateway*/
							$("#update_gateway_location_list option").remove();
							$('<option style="display: none;"></option>').appendTo('#update_gateway_location_list');
							
							/*Add Meter*/
							$("#location option").remove();
							$('<option style="display: none;"></option>').appendTo('#location');
							/*Update Meter */
							$("#update_location option").remove();
							$('<option style="display: none;"></option>').appendTo('#update_location');
							/*Location Meter Filter */
							$("#EERoomFilter_meter option").remove();
							$('<option value="">Show All</option>').appendTo('#EERoomFilter_meter');
							/*Location Gateway Filter */
							$("#EERoomFilter_gateway option").remove();
							$('<option value="">Show All</option>').appendTo('#EERoomFilter_gateway');
			
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	} 	

</script>
