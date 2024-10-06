 <script>   
                
	/*Load Offline Meter List*/
	$(document).ready(function() {
			var table_offline_meter = $('#meterofflinelist').DataTable( {
				"language": {
						"lengthMenu":'<select class="form-select">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> ',
						 "emptyTable": 'No Offline Meter',
						 
			    },
				scrollCollapse: true,
				scrollY: '500px',
				scrollX: '100%',
				responsive: true,
				ajax: {				
					url: "{{ route('getOfflineMeter') }}",
					type: 'get',
					data:{
						siteID:{{ $SiteData[0]->site_id }},
						_token: "{{ csrf_token() }}"
					},
					},
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false}, 
					{data: 'meter_name', className: "text-center"},
					{data: 'customer_name', className: "text-left"},
					{data: 'last_log_update'},
					{data: 'meter_status'},				
					{data: 'gateway_sn'},
					{data: 'location_code'},
					{data: 'location_description'},
					{data: 'meter_role'},
					{data: 'config_file'},
					{data: 'meter_default_name'},
					{data: 'meter_remarks'}

				],
				columnDefs: [
						{ className: 'text-center', targets: [0, 3, 4] },
				]
			} );
				
		autoAdjustColumns_offlinegatewaylist(table_offline_meter);

		/*Adjust Table Column*/
		function autoAdjustColumns_offlinegatewaylist(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
			
	} );	

	/*Load Offline Meter List*/
	$(document).ready(function() {
			var LoadMeterPerBuildingList = $('#meterlist').DataTable( {
				
				
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
					url: "{{ route('getMeter') }}",
					type: 'get',
					data:{
						siteID:{{ $SiteData[0]->site_id }},
						_token: "{{ csrf_token() }}"
					},
					},
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , searchable: false, className: "text-right"},
					/*{data: 'measuring_point'},*/           
					{data: 'meter_name', className: "text-center"},
					{data: 'customer_name', className: "text-left"},
					{data: 'last_log_update', className: "text-center"},
					{data: 'meter_status', className: "text-center"},	
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},					
					{data: 'gateway_sn'},
					{data: 'location_code'},
					{data: 'location_description'},
					{data: 'meter_role'},
					{data: 'config_file'},
					{data: 'meter_default_name'},
					{data: 'meter_remarks'},
				],
				columnDefs: [
						{ className: 'text-center', targets: [0, 3, 4] },
				]
			} );
				
      $("#meterlist_filter.dataTables_filter").append($("#EERoomFilter_meter"));
      
      //Get the column index for the Category column to be used in the method below ($.fn.dataTable.ext.search.push)
      //This tells datatables what column to filter on when a user selects a value from the dropdown.
      //It's important that the text used here (Category) is the same for used in the header of the column to filter
	 var EERoomIndex = 0;
      $("#meterlist th").each(function (i) {
        if ( ($($(this)).text() == "Location Code: ")) {
			EERoomIndex = i;	  
			return false;
        }
      });

      //Use the built in datatables API to filter the existing rows by the Category column
      $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
          var selectedEEROOM = $('#EERoomFilter_meter').val();
          var EERoom = data[EERoomIndex];
          if ( selectedEEROOM === "" || EERoom.includes(selectedEEROOM) ) {
            return true;
          }
          return false;
        }
      );

      //Set the change event for the Category Filter dropdown to redraw the datatable each time
      //a user selects a new filter.
      $("#EERoomFilter_meter").change(function (e) {
        LoadMeterPerBuildingList.draw();
      });
		
		autoAdjustColumns_LoadMeterPerBuildingList(LoadMeterPerBuildingList);

		/*Adjust Table Column*/
		function autoAdjustColumns_LoadMeterPerBuildingList(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
			
	} );	


	document.getElementById("meter_name_addressable").addEventListener('change', doThing_meter_management_new);
	document.getElementById("meter_default_name").addEventListener('change', doThing_meter_management_new);
	
	function doThing_meter_management_new(){
			
			let meter_name_addressable_chk = document.getElementById("meter_name_addressable").checked;	
			
			if(meter_name_addressable_chk==true){ 
					meter_name_addressable_val = '0';
					/*enable alternate address field*/
					document.getElementById("meter_default_name").disabled = false;
					meter_default_name_val 		= $("input[name=meter_default_name]").val();
					
					$('#meter_default_nameError').hide();
					$('#meter_default_nameError').text('');
					//alert(meter_name_addressable_chk+" g g "+meter_default_name_val);
					//document.getElementById('createmeterform').className = "g-2 needs-validation";
					document.getElementById('meter_default_nameError').className = "valid-tooltip";
					document.getElementById('meter_name').className = "form-control";
			} else{ 
					meter_name_addressable_val = '1';
					document.getElementById("meter_default_name").disabled = true;
					document.getElementById("meter_default_name").value = '';
					meter_default_name_val 		= $("input[name=meter_name]").val();
					
					$('#meter_default_nameError').hide();
					$('#meter_default_nameError').text('');
					//document.getElementById('createmeterform').className = "g-2 needs-validation";
					document.getElementById('meter_default_nameError').className = "valid-tooltip";
					document.getElementById('meter_name').className = "form-control";
			}
				
    }



	<!--Save New Gateway-->
	$("#save-meter").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#meter_nameError').text('');
					$('#customer_nameError').text('');
					$('#meter_model_idError').text('');				  
					$('#meter_default_nameError').text('');
					$('#meter_typeError').text('');
					$('#meter_multiplierError').text('');
					$('#rtu_sn_number_idError').text('');
					
					$('#location_meterError').text('');
					$('#meter_statusError').text('');
					$('#meter_remarksError').text('');

			document.getElementById('createmeterform').className = "g-2 needs-validation was-validated";	
		
			let meter_name_addressable_chk = document.getElementById("meter_name_addressable").checked;	

			if(meter_name_addressable_chk==true){ 
					meter_name_addressable_val = '0';
					meter_default_name_val 		= $("input[name=meter_default_name]").val();
			} else{ 
					meter_name_addressable_val = '1';
					meter_default_name_val 		= $("input[name=meter_name]").val() + 0;
			}
			
			let meter_name 				= $("input[name=meter_name]").val();
			let customer_name 			= $("#customer_name").val();
			let meter_role 				= $("#meter_role").val();			
			let meter_model_id 			= $("#meter_model option[value='" + $('#meter_model_id').val() + "']").attr('data-id');
			
			let meter_name_addressable 	= meter_name_addressable_val;
			let meter_default_name 		= meter_default_name_val;
			
			let meter_type 				= $("input[name=meter_type]").val();
			let meter_brand 			= $("input[name=meter_brand]").val();
			let meter_multiplier 		= $("input[name=meter_multiplier]").val();		
			let rtu_sn_number_id 		= $("#rtu_sn_number option[value='" + $('#rtu_sn_number_id').val() + "']").attr('data-id');		
			
			let location_id 			= $("#location option[value='" + $('#location_id').val() + "']").attr('data-id');
			
			let meter_status 			= $("#meter_status").val();
			let meter_remarks 			= $("input[name=meter_remarks]").val();
			
			$.ajax({
				url: "{{ route('CREATE_METER_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				  site_code:'{{ $SiteData[0]->building_code }}',
				  meter_name:meter_name,
				  customer_name:customer_name,
				  meter_role:meter_role,
				  meter_model_id:meter_model_id,
				  meter_name_addressable:meter_name_addressable,
				  meter_default_name:meter_default_name,
				  meter_type:meter_type,
				  meter_brand:meter_brand,
				  meter_multiplier:meter_multiplier,
				  rtu_sn_number_id:rtu_sn_number_id,
				  location_id:location_id,
				  meter_status:meter_status,
				  meter_remarks:meter_remarks,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					
					$('#SuccessModal').modal('toggle');
					setTimeout(function() {
							$('#SuccessModal').modal('hide');
					}, 2000);		
					
					$('#createmeterform')[0].reset();
					document.getElementById('createmeterform').className = "g-3 needs-validation";
					
					var LoadMeterPerBuildingList = $("#meterlist").DataTable();
				    LoadMeterPerBuildingList.ajax.reload(null, false);					
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
					if(error.responseJSON.errors.meter_name=="The meter name has already been taken."){
								  
						meter_error = "<b>"+ meter_name +"</b> has already been taken.";
						$('#meter_descriptionError').html(meter_error);
						document.getElementById('meter_descriptionError').className = "invalid-tooltip";
						
						$('#meter_name').val("");
						  
					}else {
						
						meter_error = error.responseJSON.errors.meter_name;
						$('#meter_descriptionError').html(meter_error);
						document.getElementById('meter_descriptionError').className = "invalid-tooltip";		
						
					}
					
					let location = $("input[name=location]").val();					
					if(error.responseJSON.errors.location_id=='Area/EE Room is Required'){
							
							if(location==''){
								$('#location_meterError').text('Please Select Area/EE Room');
							}else{
								
								$('#location_meterError').html("Incorrect Area/EE Room <b>"+location+"</b>");
							}
							
							document.getElementById("location_id").value = "";
							document.getElementById('location_meterError').className = "invalid-tooltip";
					
					}
					
					let rtu_sn_number = $("input[name=rtu_sn_number]").val();	
			
					if(error.responseJSON.errors.rtu_sn_number_id=='Gateway is Required'){
							
							if(rtu_sn_number==''){	
								$('#rtu_sn_number_idError').html('Please Select Gateway Serial Number');
							}else{
								$('#rtu_sn_number_idError').html("Incorrect Gateway Serial Number <b>"+rtu_sn_number+"</b>");
							}
							
							document.getElementById("rtu_sn_number_id").value = "";
							document.getElementById('rtu_sn_number_idError').className = "invalid-tooltip";
					
					}	
					
					let meter_model = $("input[name=meter_model]").val();					
					if(error.responseJSON.errors.meter_model_id=='Configuration file is Required'){
							
							if(meter_model==''){
								$('#meter_model_idError').html('Please Select Meter Configuration File');
							}else{
								$('#meter_model_idError').html("Incorrect Meter Configuration File <b>"+meter_model+"</b>");
							}
							
							document.getElementById("meter_model_id").value = "";
							document.getElementById('meter_model_idError').className = "invalid-tooltip";
					
					}
				 		 
					meter_default_name = error.responseJSON.errors.meter_default_name;
					if(meter_default_name!=='' && meter_name_addressable==0){
						$('#meter_default_nameError').html(meter_default_name);
						document.getElementById('meter_default_nameError').className = "invalid-tooltip";	 
						$('#meter_default_nameError').show();
					}else{
						$('#meter_default_nameError').hide();
					}	 
						 
					$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });

	<!--Select Meter For Update-->
	$('body').on('click','#EditMeter',function(){
			
			event.preventDefault();
			let meterID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('MeterInfo') }}",
				type:"POST",
				data:{
				  meterID:meterID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					document.getElementById("update_meter_name").value = response[0].meter_name;
					document.getElementById("update_meter_role").value = response[0].meter_role; 
					document.getElementById("update_customer_name").value = response[0].customer_name;
					document.getElementById("update_meter_model_id").value = response[0].config_file;
					
					if(response[0].meter_name_addressable==0){
						document.getElementById("update_meter_default_name").disabled = false;
						document.getElementById("update_meter_name_addressable").checked = true;
						document.getElementById("update_meter_default_name").value = response[0].meter_default_name;
					}else{
						document.getElementById("update_meter_default_name").disabled = true;
						document.getElementById("update_meter_name_addressable").checked = false;
						document.getElementById("update_meter_default_name").value = '';
					}
					
					document.getElementById("update_meter_type").value = response[0].meter_type;
					document.getElementById("update_meter_brand").value = response[0].meter_brand;
					
					document.getElementById("update_meter_multiplier").value = response[0].meter_multiplier;
					document.getElementById("update_rtu_sn_number_id").value = response[0].gateway_sn;
					
					document.getElementById("update_location_id").value = response[0].location_code + " - " + response[0].location_description;	
					
					document.getElementById("update_meter_status").value = response[0].meter_status.toUpperCase();
					document.getElementById("update_meter_remarks").value = response[0].meter_remarks;
					
					document.getElementById("update-meter").value = meterID;
					document.getElementById("update-meter").disabled = true;
					
					$('#UpdateMeterModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	document.getElementById("update_meter_name").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_role").addEventListener('change', doThing_meter_management);
	document.getElementById("update_customer_name").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_model_id").addEventListener('change', doThing_meter_management);
	
	document.getElementById("update_meter_name_addressable").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_default_name").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_type").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_brand").addEventListener('change', doThing_meter_management);
	document.getElementById("update_rtu_sn_number_id").addEventListener('change', doThing_meter_management);
	
	document.getElementById("update_meter_multiplier").addEventListener('change', doThing_meter_management);
	document.getElementById("update_location_id").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_status").addEventListener('change', doThing_meter_management);
	document.getElementById("update_meter_remarks").addEventListener('change', doThing_meter_management);
	
	function doThing_meter_management(){
			
			let meter_name_addressable_chk = document.getElementById("update_meter_name_addressable").checked;	
			
			if(meter_name_addressable_chk==true){ 
					meter_name_addressable_val = '0';
					/*enable alternate address field*/
					document.getElementById("update_meter_default_name").disabled = false;
					meter_default_name_val 		= $("input[name=update_meter_default_name]").val();
					
					$('#update_meter_default_nameError').hide();
					$('#update_meter_default_nameError').text('');
					
					document.getElementById('updatemeterform').className = "g-2 needs-validation";
					document.getElementById('update_meter_default_nameError').className = "valid-tooltip";
					document.getElementById('update_meter_name').className = "form-control";
			} else{ 
					meter_name_addressable_val = '1';
					document.getElementById("update_meter_default_name").disabled = true;
					document.getElementById("update_meter_default_name").value = '';
					meter_default_name_val 		= $("input[name=update_meter_name]").val();
					
					$('#update_meter_default_nameError').hide();
					$('#update_meter_default_nameError').text('');
					document.getElementById('updatemeterform').className = "g-2 needs-validation";
					document.getElementById('update_meter_default_nameError').className = "valid-tooltip";
					document.getElementById('update_meter_name').className = "form-control";
			}
			
			let meterID					= document.getElementById("update-meter").value;
			let meter_name 				= $("input[name=update_meter_name]").val();
			let customer_name 			= $("input[name=update_customer_name]").val();
			let meter_role 				= $("#update_meter_role").val();			
			let meter_model_id 			= $("#update_meter_model option[value='" + $('#update_meter_model_id').val() + "']").attr('data-id');
			let meter_name_addressable 	= meter_name_addressable_val;
			let meter_default_name 		= meter_default_name_val;
			let meter_type 				= $("input[name=update_meter_type]").val();
			let meter_brand 			= $("input[name=update_meter_brand]").val();
			let meter_multiplier 		= $("input[name=update_meter_multiplier]").val();		
			let rtu_sn_number_id 		= $("#update_rtu_sn_number option[value='" + $('#update_rtu_sn_number_id').val() + "']").attr('data-id');		
			
			let location_id 			= $("#update_location option[value='" + $('#update_location_id').val() + "']").attr('data-id');
			
			let meter_status 			= $("#update_meter_status").val();
			let meter_remarks 			= $("input[name=update_meter_remarks]").val();
	
				$.ajax({
				url: "{{ route('MeterInfo') }}",
				type:"POST",
				data:{
				  meterID:meterID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
						
						/*Below items to Convert to Empty instead of NULL*/
						 customer_name_value 	= response[0].customer_name || '';
						 meter_brand_value 		= response[0].meter_brand || '';
						 meter_type_value 		= response[0].meter_type || '';
						 meter_remarks_value 	= response[0].meter_remarks || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( response[0].meter_name===meter_name &&
						  response[0].rtu_idx==rtu_sn_number_id &&
						  customer_name_value===customer_name &&
						  response[0].meter_role==meter_role &&
						  response[0].config_idx==meter_model_id &&
						  response[0].meter_name_addressable==meter_name_addressable &&
						  response[0].meter_default_name===meter_default_name &&
						  response[0].meter_multiplier==meter_multiplier &&
						  meter_brand_value===meter_brand &&
						  meter_type_value===meter_type &&
						  response[0].location_idx==location_id &&
						  response[0].meter_status==meter_status &&
						  meter_remarks_value==meter_remarks  
					  ){
							document.getElementById("update-meter").disabled = true;
							$('#loading_data_updatemeter').hide();
							
						}else{
							document.getElementById("update-meter").disabled = false;
							$('#loading_data_updatemeter').hide();
							
						}
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				},
				beforeSend:function()
				{
					$('#loading_data_updatemeter').show();
				}
			   });	
    }

	<!--Save New Gateway-->
	$("#update-meter").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#update_meter_nameError').text('');
					$('#update_customer_nameError').text('');
					$('#update_meter_model_idError').text('');				  
					$('#update_meter_default_nameError').text('');
					$('#update_meter_typeError').text('');
					$('#update_meter_multiplierError').text('');
					$('#update_rtu_sn_number_idError').text('');
					
					$('#location_meterError').text('');
					$('#update_meter_statusError').text('');
					$('#update_meter_remarksError').text('');

			document.getElementById('updatemeterform').className = "g-2 needs-validation was-validated";	
		
			let meter_name_addressable_chk = document.getElementById("update_meter_name_addressable").checked;	
		
			if(meter_name_addressable_chk==true){ 
					meter_name_addressable_val = '0';
					meter_default_name_val 		= $("input[name=update_meter_default_name]").val();
			} else{ 
					meter_name_addressable_val = '1';
					meter_default_name_val 		= $("input[name=update_meter_name]").val();
			}
			
			let meterID					= document.getElementById("update-meter").value;
			let meter_name 				= $("input[name=update_meter_name]").val();
			let customer_name 			= $("#update_customer_name").val();
			let meter_role 				= $("#update_meter_role").val();			
			let meter_model_id 			= $("#update_meter_model option[value='" + $('#update_meter_model_id').val() + "']").attr('data-id');
			let meter_name_addressable 	= meter_name_addressable_val;
			let meter_default_name 		= meter_default_name_val;
			let meter_type 				= $("input[name=update_meter_type]").val();
			let meter_brand 			= $("input[name=update_meter_brand]").val();
			let meter_multiplier 		= $("input[name=update_meter_multiplier]").val();		
			let rtu_sn_number_id 		= $("#update_rtu_sn_number option[value='" + $('#update_rtu_sn_number_id').val() + "']").attr('data-id');		
			
			let location_id 			= $("#update_location option[value='" + $('#update_location_id').val() + "']").attr('data-id');
			
			let meter_status 			= $("#update_meter_status").val();
			let meter_remarks 			= $("input[name=update_meter_remarks]").val();
			
			$.ajax({
				url: "{{ route('UPDATE_METER_INFO') }}",
				type:"POST",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				  site_code:'{{ $SiteData[0]->building_code }}',
				  meterID:meterID,
				  meter_name:meter_name,
				  customer_name:customer_name,
				  meter_role:meter_role,
				  meter_model_id:meter_model_id,
				  meter_name_addressable:meter_name_addressable,
				  meter_default_name:meter_default_name,
				  meter_type:meter_type,
				  meter_brand:meter_brand,
				  meter_multiplier:meter_multiplier,
				  rtu_sn_number_id:rtu_sn_number_id,
				  location_id:location_id,
				  meter_status:meter_status,
				  meter_remarks:meter_remarks,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#UpdateMeterModal').modal('toggle');	
					
					$('.success_modal_bg').html(response.success);
					
					$('#SuccessModal').modal('toggle');
					setTimeout(function() {
							$('#SuccessModal').modal('hide');
					}, 2000);		
	
					var LoadMeterPerBuildingList = $("#meterlist").DataTable();
				    LoadMeterPerBuildingList.ajax.reload(null, false);
					
					$('#update_meter_default_nameError').hide();
				  
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.meter_name=="The meter name has already been taken."){
							  
					meter_error = "<b>"+ meter_name +"</b> has already been taken.";
					$('#update_meter_descriptionError').html(meter_error);
					document.getElementById('update_meter_descriptionError').className = "invalid-tooltip";
					
					$('#update_meter_name').val("");
					  
				}else {
					
					meter_error = error.responseJSON.errors.meter_name;
					$('#update_meter_descriptionError').html(meter_error);
					document.getElementById('update_meter_descriptionError').className = "invalid-tooltip";		
					
				}
					
					
					let location = $("input[name=update_location]").val();					
					if(error.responseJSON.errors.location_id=='Area/EE Room is Required'){
							
							if(location==''){
								$('#update_location_meterError').text('Please Select Area/EE Room');
							}else{
								
								$('#update_location_meterError').html("Incorrect Area/EE Room <b>"+location+"</b>");
							}
							
							document.getElementById("update_location_id").value = "";
							document.getElementById('update_location_meterError').className = "invalid-tooltip";
					
					}
					
					let rtu_sn_number = $("input[name=update_rtu_sn_number]").val();	
			
					if(error.responseJSON.errors.rtu_sn_number_id=='Gateway is Required'){
							
							if(rtu_sn_number==''){	
								$('#update_rtu_sn_number_idError').html('Please Select Gateway Serial Number');
							}else{
								$('#update_rtu_sn_number_idError').html("Incorrect Gateway Serial Number <b>"+rtu_sn_number+"</b>");
							}
							
							document.getElementById("update_rtu_sn_number_id").value = "";
							document.getElementById('update_rtu_sn_number_idError').className = "invalid-tooltip";
					
					}	
					
					let meter_model = $("input[name=update_meter_model]").val();					
					if(error.responseJSON.errors.meter_model_id=='Configuration file is Required'){
							
							if(meter_model==''){
								$('#update_meter_model_idError').html('Please Select Meter Configuration File');
							}else{
								$('#update_meter_model_idError').html("Incorrect Meter Configuration File <b>"+meter_model+"</b>");
							}
							
							document.getElementById("update_meter_model_id").value = "";
							document.getElementById('update_meter_model_idError').className = "invalid-tooltip";
					
					}
						 
					meter_default_name = error.responseJSON.errors.meter_default_name;
					if(meter_default_name!=='' && meter_name_addressable==0){
						$('#update_meter_default_nameError').html(meter_default_name);
						document.getElementById('update_meter_default_nameError').className = "invalid-tooltip";	 
						$('#update_meter_default_nameError').show();
					}else{
						$('#update_meter_default_nameError').hide();
					}
					
					$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		 
	  });


	<!--Meter Deletion Confirmation-->
	$('body').on('click','#DeleteMeter',function(){
			
			event.preventDefault();
			let meterID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('MeterInfo') }}",
				type:"POST",
				data:{
				  meterID:meterID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteMeterConfirmed").value = meterID;
					
					$('#meter_name_delete').html(response[0].meter_name);
					$('#customer_name_delete').html(response[0].customer_name);
					$('#meter_status_delete').html(response[0].meter_status);
					$('#meter_location_delete').html(response[0].location_description);
					
					
					$('#meter_name_delete_confirmed').html(response[0].meter_name);
					$('#customer_name_delete_confirmed').html(response[0].customer_name);
					$('#meter_status_delete_confirmed').html(response[0].meter_status);
					$('#meter_location_delete_confirmed').html(response[0].location_description);
					
					$('#MeterDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		  
	  });

	<!--Meter Confirmed For Deletion-->
	$('body').on('click','#deleteMeterConfirmed',function(){
			
			event.preventDefault();

			let meterID = document.getElementById("deleteMeterConfirmed").value;
			
			  $.ajax({
				url: "{{ route('DeleteMeterInfo') }}",
				type:"POST",
				data:{
				  meterID:meterID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#MeterDeleteModalConfirmed').modal('toggle');
					setTimeout(function() {
							$('#MeterDeleteModalConfirmed').modal('hide');
					}, 2000);
					
					var LoadMeterPerBuildingList = $("#meterlist").DataTable();
				    LoadMeterPerBuildingList.ajax.reload(null, false);	
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });  
	});

	function downloadofflinemeter(){
		  	  
		var query = {
			 siteID:{{ $SiteData[0]->site_id }},
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('download_offline_meter')}}?" + $.param(query)
		window.open(url);
	  
	} 

  	function ResetFormAddMeter(){
			
			event.preventDefault();
			$('#createmeterform')[0].reset();
			
			document.getElementById('createmeterform').className = "g-2 needs-validation";
			document.getElementById("meter_default_name").disabled = true;
	}	

</script>
