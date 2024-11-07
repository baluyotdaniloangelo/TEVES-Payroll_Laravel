  <script>
               
	ReloadGatewayOption();
	/*Reload Gateway Option for Add/Update Meter*/
  	function ReloadGatewayOption(){
		
		
			/*For Add Meter*/
			$("#rtu_sn_number option").remove();
			$('<option style="display: none;"></option>').appendTo('#rtu_sn_number');
			/*For Update Meter */
			$("#update_rtu_sn_number option").remove();
			$('<option style="display: none;"></option>').appendTo('#update_rtu_sn_number');
			
			  $.ajax({
				url: "{{ route('ReloadGatewayOption') }}",
				type:"GET",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				  location_id:0,
				  _token: "{{ csrf_token() }}"
				},
				success:function(result){
				  console.log(result);
				  if(result) {
					
						var len = result.length;
					
						for(var i=0; i<len; i++){
							
							var rtu_id = result[i].rtu_id;
							var gateway_ip = result[i].gateway_ip;
							var gateway_sn = result[i].gateway_sn;
							
								$('#rtu_sn_number option:last').after("<option label='IP Address:"+gateway_ip+"' data-id='"+rtu_id+"' value='"+gateway_sn+"'>");
								$('#update_rtu_sn_number option:last').after("<option label='IP Address:"+gateway_ip+"' data-id='"+rtu_id+"' value='"+gateway_sn+"'>");
							
						}
						
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
		
	};			   
			   
			   
	/*Load Offline Gateway List*/
	$(document).ready(function() {
			let  table_offline_gw = $('#offlinegatewaylist').DataTable( {
				"language": {
						lengthMenu:' <select class="form-select">' +
            '<option value="10">10</option>' +
            '<option value="20">20</option>' +
            '<option value="30">30</option>' +
            '<option value="40">40</option>' +
            '<option value="50">50</option>' +
            '<option value="-1">All</option>' +
            '</select> ',
						 "emptyTable": 'No Offline Gateway'
			    },
				scrollCollapse: true,
				scrollY: '500px',
				scrollX: '100%',
				responsive: true,
				ajax: {				
					url: "{{ route('getOfflineGateway') }}",
					type: 'get',
					data:{
						siteID:{{ $SiteData[0]->site_id }},
						_token: "{{ csrf_token() }}"
					},
					},
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'gateway_sn', className: "text-left"},           
					{data: 'gateway_ip', className: "text-left"},
					{data: 'gateway_mac', className: "text-center"},
					{data: 'location_code', className: "text-left"},
					
					{data: 'status', name: 'status', orderable: true, searchable: true},
					{data: 'location_description'},
					{data: 'idf_number'},
					{data: 'switch_name'},
					{data: 'idf_port'},
				],
				columnDefs: [
						{ className: 'text-center', targets: [0, 1, 2, 5] },
				]
			} );
	
		//var table = $('#my-dt').DataTable().columns.adjust();
		autoAdjustColumns_offlinegatewaylist(table_offline_gw);

		/*Adjust Table Column*/
		function autoAdjustColumns_offlinegatewaylist(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
	
	} );		

	/*Load Gateway List*/
	$(document).ready(function() {
			let  LoadGatewayPerBuildingList = $('#gatewaylist').DataTable( {
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
					url: "{{ route('getGatewayPerBLGandEEROOM') }}",
					type: 'get',
					data:{
						siteID:{{ $SiteData[0]->site_id }},
						_token: "{{ csrf_token() }}"
					},
					},
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , searchable: false},
					{data: 'gateway_sn', className: "text-left" },        
					{data: 'gateway_ip', className: "text-left" },
					{data: 'gateway_mac', className: "text-left" },
					{data: 'status', name: 'status', orderable: true, searchable: true, className: "text-center"},
					{data: 'update', name: 'status', orderable: false, searchable: true},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					{data: 'location_code'},
					{data: 'location_description'},
					{data: 'idf_number'},
					{data: 'switch_name'},
					{data: 'idf_port'},
				],
				columnDefs: [
						{ className: 'text-center', targets: [0, 1, 2, 5] },
				]
			} );
	
      $("#meterlist_filter.dataTables_filter").append($("#EERoomFilter_gateway"));
      
      //Get the column index for the Category column to be used in the method below ($.fn.dataTable.ext.search.push)
      //This tells datatables what column to filter on when a user selects a value from the dropdown.
      //It's important that the text used here (Category) is the same for used in the header of the column to filter
	 var EERoomIndex = 0;
	  //var StatusIndex = 0;
      $("#meterlist th").each(function (i) {
        if ( ($($(this)).text() == "Location Code: ")) {
			EERoomIndex = i;	  
			return false;
        }
      });

      //Use the built in datatables API to filter the existing rows by the Category column
      $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
          var selectedEEROOM = $('#EERoomFilter_gateway').val();
          var EERoom = data[EERoomIndex];
          if ( selectedEEROOM === "" || EERoom.includes(selectedEEROOM) ) {
            return true;
          }
          return false;
        }
      );

      //Set the change event for the Category Filter dropdown to redraw the datatable each time
      //a user selects a new filter.
      $("#EERoomFilter_gateway").change(function (e) {
        LoadGatewayPerBuildingList.draw();
      });
			
	
		autoAdjustColumns_gatewaylist(LoadGatewayPerBuildingList);

		/*Adjust Table Column*/
		function autoAdjustColumns_gatewaylist(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
	
	} );

	<!--Save New Gateway-->
	$("#save-gateway").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#gateway_snError').text('');
					$('#gateway_macError').text('');
					$('#gateway_ipError').text('');				  
					$('#idf_nameError').text('');
					$('#idf_switchError').text('');
					$('#idf_portError').text('');
					$('#gateway_location_meterError').text('');
					$('#gateway_descriptionError').text('');
					$('#connection_typeError').text(''); 

			document.getElementById('gatewayform').className = "g-2 needs-validation was-validated";

			let gateway_sn 				= $("input[name=gateway_sn]").val();
			let gateway_mac 			= $("input[name=gateway_mac]").val();
			let gateway_ip 				= $("input[name=gateway_ip]").val();
			let idf_name 				= $("input[name=idf_name]").val();
			let idf_switch 				= $("input[name=idf_switch]").val();
			let idf_port 				= $("input[name=idf_port]").val();
			
			let location_id 			= $("#gateway_location_list option[value='" + $('#gateway_location').val() + "']").attr('data-id');

			let gateway_description 	= $("input[name=gateway_description]").val();
			let connection_type 		= $("#connection_type").val();
			
			  $.ajax({
				url: "/create_gateway_post",
				type:"POST",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
				  site_code:'{{ $SiteData[0]->building_code }}',
				  location_id:location_id,
				  gateway_sn:gateway_sn,
				  gateway_mac:gateway_mac,
				  gateway_ip:gateway_ip,
				  idf_name:idf_name,
				  idf_switch:idf_switch,
				  idf_port:idf_port,
				  gateway_description:gateway_description,
				  connection_type:connection_type,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#gateway_snError').text('');					
					$('#gateway_macError').text('');
					$('#site_descriptionError').text('');			
					
					ReloadGatewayOption();
					
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);	
					
					ResetFormAddGateway()
					
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.gateway_sn=="The gateway sn has already been taken."){
							  
				  $('#gateway_snError').html("<b>"+ gateway_sn +"</b> has already been taken.");
				  document.getElementById('gateway_snError').className = "invalid-feedback";
				  document.getElementById('gateway_sn').className = "form-control is-invalid";
				  $('#gateway_sn').val("");
				  
				}else{
					
				  $(gateway_snError).text(error.responseJSON.errors.gateway_sn);
				  document.getElementById('gateway_snError').className = "invalid-feedback";		
				
				}
				
				
				if(error.responseJSON.errors.gateway_mac=="The gateway mac has already been taken."){
							  
				  $('#gateway_macError').html("<b>"+ gateway_mac +"</b> has already been taken.");
				  document.getElementById('gateway_macError').className = "invalid-feedback";
				  document.getElementById('gateway_mac').className = "form-control is-invalid";
				  $('#gateway_mac').val("");
				  
				}else{
					
				  $('#gateway_macError').text(error.responseJSON.errors.gateway_mac);
				  document.getElementById('gateway_macError').className = "invalid-feedback";		
				
				}
			
				if(error.responseJSON.errors.gateway_ip=="The gateway ip has already been taken."){
							  
				  $('#gateway_ipError').html("<b>"+ gateway_ip +"</b> has already been taken.");
				  document.getElementById('gateway_ipError').className = "invalid-feedback";
				  document.getElementById('gateway_ip').className = "form-control is-invalid";
				  $('#gateway_ip').val("");
				  
				}else{
					
				  $('#gateway_ipError').text(error.responseJSON.errors.gateway_ip);
				  document.getElementById('gateway_ipError').className = "invalid-feedback";		
				
				}

				 
				 let location = $("input[name=gateway_location]").val();	

					if(error.responseJSON.errors.location_id=='Area/EE Room is Required'){
							
							if(location==''){
								
								$('#gateway_location_meterError').text('Please Select Area/EE Room');
							}else{
								
								$('#gateway_location_meterError').html("Incorrect Area/EE Room <b>"+location+"</b>");
							}
							
							document.getElementById("gateway_location").value = "";	
							document.getElementById('gateway_location_meterError').className = "invalid-feedback";
					
					}
					
				 
				 
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });

	<!--Select Gateway For Update-->
	$('body').on('click','#EditGateway',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/gateway_info",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-gateway").value = gatewayID;
					$('#CloseManual').attr('data-bs-target','#UpdateGatewayModal');
						
					document.getElementById("update_gateway_sn").value = response.gateway_sn;
					document.getElementById("update_gateway_mac").value = response.gateway_mac;
					document.getElementById("update_gateway_ip").value = response.gateway_ip;
					
					document.getElementById("update_idf_name").value = response.idf_number;
					document.getElementById("update_idf_switch").value = response.switch_name;			
					document.getElementById("update_idf_port").value = response.idf_port;
					
					document.getElementById("update_connection_type").value = response.connection_type;		
					document.getElementById("update_gateway_description").value = response.gateway_description;	
	
					document.getElementById("update_gateway_location").value = response.location_code + " - " + response.location_description;						

				
					$('#UpdateGatewayModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	document.getElementById("update_gateway_sn").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_gateway_mac").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_gateway_ip").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_idf_name").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_idf_switch").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_idf_port").addEventListener('change', doThing_gateway_management_update);
	
	document.getElementById("update_gateway_location").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_gateway_description").addEventListener('change', doThing_gateway_management_update);
	document.getElementById("update_connection_type").addEventListener('change', doThing_gateway_management_update);
	
	function doThing_gateway_management_update(){
			
			let gatewayID = document.getElementById("update-gateway").value;
			
			let gateway_sn 				= $("input[name=update_gateway_sn]").val();
			let gateway_mac 			= $("input[name=update_gateway_mac]").val();
			let gateway_ip 				= $("input[name=update_gateway_ip]").val();
			
			let idf_name 				= $("input[name=update_idf_name]").val();
			let idf_switch 				= $("input[name=update_idf_switch]").val();
			let idf_port 				= $("input[name=update_idf_port]").val();
			
			let gateway_description 	= $("input[name=update_gateway_description]").val();
			let connection_type 		= $("#update_connection_type").val();
			
			let location_id 			= $("#update_gateway_location_list option[value='" + $('#update_gateway_location').val() + "']").attr('data-id');
	
				$.ajax({
				url: "/gateway_info",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
						
						/*Below items to Convert to Empty instead of NULL*/
						 idf_number_value 			= response.idf_number || '';
						 switch_name_value 			= response.switch_name || '';
						 idf_port_value 			= response.idf_port || '';
						 gateway_description_value 	= response.gateway_description || '';
						/*Above items to Convert to Empty instead of NULL*/
						
					  if( 
						  
						  response.gateway_sn	===	gateway_sn &&
						  response.gateway_mac	===	gateway_mac &&
						  response.gateway_ip	===	gateway_ip &&
						  response.connection_type	===	connection_type &&
						  response.location_idx		==	location_id &&
						  idf_number_value			===	idf_name &&
						  switch_name_value			===	idf_switch &&
						  idf_port_value			===	idf_port &&
						  gateway_description_value	===	gateway_description
					  ){
						  
							document.getElementById("update-gateway").disabled = true;
							$('#loading_data_updategateway').hide();
							
						}else{
							document.getElementById("update-gateway").disabled = false;
							$('#loading_data_updategateway').hide();
							
						}
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				},
				beforeSend:function()
				{
					$('#loading_data_updategateway').show();
				}
			   });	
    }

	<!--Update Gateway-->
	$("#update-gateway").click(function(event){
			
			event.preventDefault();
			
					/*Gateway ID*/
					let gatewayID = document.getElementById("update-gateway").value;
					/*Reset Warnings*/
					$('#update_gateway_snError').text('');
					$('#update_gateway_macError').text('');
					$('#update_gateway_ipError').text('');				  
					$('#update_idf_nameError').text('');
					$('#update_idf_switchError').text('');
					$('#update_idf_portError').text('');
					$('#update_gateway_location_meterError').text('');
					$('#update_gateway_descriptionError').text('');
					$('#update_connection_typeError').text('');

			document.getElementById('updategatewayform').className = "g-2 needs-validation was-validated";

			let gateway_sn 				= $("input[name=update_gateway_sn]").val();
			let gateway_mac 			= $("input[name=update_gateway_mac]").val();
			let gateway_ip 				= $("input[name=update_gateway_ip]").val();
			let idf_name 				= $("input[name=update_idf_name]").val();
			let idf_switch 				= $("input[name=update_idf_switch]").val();
			let idf_port 				= $("input[name=update_idf_port]").val();
			let physical_location 		= $("input[name=update_physical_location]").val();
			let gateway_description 	= $("input[name=update_gateway_description]").val();
			let connection_type 		= $("#update_connection_type").val();
			
			let location_id 			= $("#update_gateway_location_list option[value='" + $('#update_gateway_location').val() + "']").attr('data-id');
			
			  $.ajax({
				url: "/update_gateway_post",
				type:"POST",
				data:{
				  site_code:'{{ $SiteData[0]->building_code }}',
				  gatewayID:gatewayID,
				  gateway_sn:gateway_sn,
				  gateway_mac:gateway_mac,
				  gateway_ip:gateway_ip,
				  idf_name:idf_name,
				  idf_switch:idf_switch,
				  idf_port:idf_port,
				  location_id:location_id,
				  gateway_description:gateway_description,
				  connection_type:connection_type,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');
					
					$('#gateway_snError').text('');					
					$('#gateway_macError').text('');
					$('#site_descriptionError').text('');		
					
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);		
					
					ReloadGatewayOption();
					
				  }
				},
				error: function(error) {
				console.log(error);	
				  		
				document.getElementById("InvalidModalBtn").focus();		
				document.getElementById("InvalidModalBtn").click(); 
						
				if(error.responseJSON.errors.gateway_sn=="The gateway sn has already been taken."){
							  
				  $('#update_gateway_snError').html("<b>"+ gateway_sn +"</b> has already been taken.");
				  document.getElementById('update_gateway_snError').className = "invalid-feedback";
				  document.getElementById('update_gateway_sn').className = "form-control is-invalid";
				  $('#update_gateway_sn').val("");
				  
				}else{
					
				  $(gateway_snError).text(error.responseJSON.errors.gateway_sn);
				  document.getElementById('update_gateway_snError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.gateway_mac=="The gateway mac has already been taken."){
							  
				  $('#update_gateway_macError').html("<b>"+ gateway_mac +"</b> has already been taken.");
				  document.getElementById('update_gateway_macError').className = "invalid-feedback";
				  document.getElementById('update_gateway_mac').className = "form-control is-invalid";
				  $('#update_gateway_mac').val("");
				  
				}else{
					
				  $('#update_gateway_macError').text(error.responseJSON.errors.gateway_mac);
				  document.getElementById('update_gateway_macError').className = "invalid-feedback";		
				
				}
				
				if(error.responseJSON.errors.gateway_ip=="The gateway ip has already been taken."){
							  
				  $('#update_gateway_ipError').html("<b>"+ gateway_ip +"</b> has already been taken.");
				  document.getElementById('update_gateway_ipError').className = "invalid-feedback";
				  document.getElementById('update_gateway_ip').className = "form-control is-invalid";
				  $('#update_gateway_ip').val("");
				  
				}else{
					
				  $('#update_gateway_ipError').text(error.responseJSON.errors.gateway_ip);
				  document.getElementById('update_gateway_ipError').className = "invalid-feedback";		
				
				}
					
				 let location = $("input[name=update_gateway_location]").val();	

					if(error.responseJSON.errors.location_id=='Area/EE Room is Required'){
							
							if(location==''){
								$('#update_gateway_location_meterError').text('Please Select Area/EE Room');
							}else{
								
								$('#update_gateway_location_meterError').html("Incorrect Area/EE Room <b>"+location+"</b>");
							}
							
							document.getElementById("update_gateway_location").value = "";	
							document.getElementById('update_gateway_location_meterError').className = "invalid-feedback";
					
					}
					
				$('#InvalidModal').modal('toggle');			
				  
				}
			   });
		
	  });

	<!--Gateway Deletion Confirmation-->
	$('body').on('click','#DeleteGateway',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/gateway_info",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteGatewayConfirmed").value = gatewayID;
					
					$('#delete_gateway_sn_info').html(response.gateway_sn);
					$('#delete_gateway_ip_info').html(response.gateway_ip);
					$('#delete_gateway_mac_info').html(response.gateway_mac);
					$('#delete_gateway_location_info').html(response.location_description);
					
					$('#delete_gateway_sn_info_confirmed').html(response.gateway_sn);
					$('#delete_gateway_ip_info_confirmed').html(response.gateway_ip);
					$('#delete_gateway_mac_info_confirmed').html(response.gateway_mac);
					$('#delete_gateway_location_info_confirmed').html(response.location_description);
					
					$('#GatewayDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		  
	  });

	<!--Gateway Confirmed For Deletion-->
	$('body').on('click','#deleteGatewayConfirmed',function(){
			
			event.preventDefault();

			let gatewayID = document.getElementById("deleteGatewayConfirmed").value;
			
			  $.ajax({
				url: "/delete_gateway_confirmed",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#GatewayDeleteModalConfirmed').modal('toggle');
					
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
					
					ReloadGatewayOption();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });  
	});
				
	<!--CSV Enable Update-->
	$('body').on('click','.enablecsvUpdate',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/enablecsvUpdate",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  });
	  
	<!--CSV Disable Update-->
	$('body').on('click','.disablecsvUpdate',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/disablecsvUpdate",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  });	

	<!--Site Code Enable Update-->
	$('body').on('click','.enablesitecodeUpdate',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/enablesitecodeUpdate",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  });
	  
	<!--Site Code Disable Update-->
	$('body').on('click','.disablesitecodeUpdate',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/disablesitecodeUpdate",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  });

	<!--SSH Enable-->
 	$('body').on('click','.enableSSH',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/enableSSH",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  }); 
	  
	<!--SSH Disable-->
 	$('body').on('click','.disableSSH',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/disableSSH",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  }); 
	
	<!--Force Load Profile Enable-->
 	$('body').on('click','.enableLP',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/enableLP",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  }); 
	  
	<!--Force Load Profile Disable-->
	$('body').on('click','.disableLP',function(){
			
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "/disableLP",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
									
					var table = $("#gatewaylist").DataTable();
				    table.ajax.reload(null, false);
				
				}
				},
				error: function(error) {
				 console.log(error);
				}
			   });
			   
	  }); 
	
	<!--Select Gateway For Update-->
	$('body').on('click','#ViewGateway',function(){
		
			event.preventDefault();
			let gatewayID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('getMetersPerGateway') }}",
				type:"GET",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				}
			 }).done(function (result) {
					meterlistLoadPerGateway.clear().draw();
					meterlistLoadPerGateway.rows.add(result.data).draw();
					
					/*Call Function to Display Gateway Information*/
					get_gateway_details(gatewayID);
					
					$('#ViewGatewayModal').modal('toggle');		
            })	
	});

			/*Load Meter List Per Gateway*/	
			let meterlistLoadPerGateway = $('#meterlistLoadPerGateway').DataTable( {
				processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: true,
				scrollCollapse: true,
				scrollY: '500px',
				//scrollX: '100%',
				paging: true,
				searching: true,
				info: true,
				data: [],
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right"},        
					{data: 'meter_name', className: "text-center"},
					{data: 'customer_name'},
					{data: 'last_log_update'},
					{data: 'meter_status'},		
					{data: 'location_code'},
					{data: 'location_description'},
					{data: 'meter_role'},
					{data: 'config_file'},
					{data: 'meter_default_name'},
					{data: 'meter_remarks'},
					
				]

			} );
					
		//var table = $('#my-dt').DataTable().columns.adjust();
		autoAdjustColumns_meterlistLoadPerGateway(meterlistLoadPerGateway);

		/*Adjust Table Column*/
		function autoAdjustColumns_meterlistLoadPerGateway(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
		
	function get_gateway_details(gatewayID){
		  
			  $.ajax({
				url: "/gateway_info",
				type:"POST",
				data:{
				  gatewayID:gatewayID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {		
					
					/*Set Details*/
					$('#view_serial_number').text(response.gateway_sn);
					$('#view_ip_address').text(response.gateway_mac);
					$('#view_gateway_macess').text(response.gateway_sn);
					
					$('#view_connection_type').text(response.connection_type);
					$('#view_physical_location').text(response.rtu_physical_location);
					$('#view_idf_name').text(response.idf_number);
					
					$('#view_idf_switch').text(response.switch_name);
					$('#view_idf_port').text(response.idf_port);
					$('#view_gateway_description').text(response.gateway_description);
					
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  
	}  
	  
	function downloadofflinegateway(){
		  	  
		var query = {
			 siteID:{{ $SiteData[0]->site_id }},
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('download_offline_gateway')}}?" + $.param(query)
		window.open(url);
	  
	}


	<!--Select Gateway For Update-->
	 $('body').on('click','#UploadGatewayMeter',function(){
		
			 event.preventDefault();
			 let gatewayID = $(this).data('id');
			
			   $.ajax({
				 url: "{{ route('getMetersPerGateway') }}",
				 type:"GET",
				 data:{
				   gatewayID:gatewayID,
				   _token: "{{ csrf_token() }}"
				 }
			  }).done(function (result) {
					
					 document.getElementById("import_gateway_idx").value = gatewayID;
					 $('#UploadGatewayMeterModal').modal('toggle');		
					
             })	
	 });

			
var clear_timer;

  $('#sample_form').on('submit', function(event){
   $('#message').html('');
   event.preventDefault();
   $.ajax({//import_meters
    url:"{{ route('import_meters') }}",
    method:"POST",
    data: new FormData(this),
    dataType:"json",
    contentType:false,
    cache:false,
    processData:false,
    beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("import").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
				},
	complete: function(){
					
					/*Enable Submit Button*/
					document.getElementById("import").disabled = false;
					//$('#import').attr('disabled','disabled');
					//$('#import').val('Importing');
					/*Hide Status*/
					$('#loading_data').hide();
					
	},
	/*beforeSend:function(){
     $('#import').attr('disabled','disabled');
     $('#import').val('Importing');
    },*/
    success:function(data)
    {
     if(data.success)
     {
		//alert(data.total_line);
		 
      //$('#total_data').text(data.total_line);

     // $('#message').html('<div class="alert alert-success">CSV File Uploaded</div>');
	  
	 // $('#total_data').text(data.total_line);

     // start_import();
	 
		/*Display Data*/
	  /*Enable Submit Button*/
					document.getElementById("import").disabled = false;
					//$('#import').attr('disabled','disabled');
					//$('#import').val('Importing');
					/*Hide Status*/
					$('#loading_data').hide();
     }
     if(data.error)
     {
		  //$('#message').html('<div class="alert alert-danger">'+data.error+'</div>');
		  //$('#import').attr('disabled',false);
		  //$('#import').val('Import');
		  
     }
    },
	error: function(error) {
	console.log(error);	
	
			$('#InvalidModal').modal('toggle');	
			$('#csv_fileError').html("<b>"+ error.responseJSON.errors.csv_file +"</b>");
			document.getElementById('csv_fileError').className = "invalid-feedback";
			document.getElementById('csv_file').className = "form-control is-invalid";
	}
	
	
   })
  });
	
	function ResetFormAddGateway(){
		
		event.preventDefault();
		$('#gatewayform')[0].reset();
				
		document.getElementById('gatewayform').className = "g-3 needs-validation";
		
		document.getElementById('gateway_snError').className = "valid-feedback";
		document.getElementById('gateway_sn').className = "form-control";
		
		document.getElementById('gateway_macError').className = "valid-feedback";
		document.getElementById('gateway_mac').className = "form-control";
		
		document.getElementById('gateway_ipError').className = "valid-feedback";
		document.getElementById('gateway_ip').className = "form-control";
		
	}	
	
</script>
