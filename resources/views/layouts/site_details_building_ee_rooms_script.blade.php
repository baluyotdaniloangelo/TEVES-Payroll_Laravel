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
	  
	  
	GatewayPerBuilding(0);
	function GatewayPerBuilding(location_id){
		
		/*Load Meter*/
		MeterPerBuilding(location_id);
						
			  $.ajax({
				url: "{{ route('getGatewayPerBLGandEEROOM') }}",
				type:"GET",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
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
				processing: true,
				//serverSide: true,
				stateSave: true,/*Remember Searches*/
				scrollCollapse: true,
				scrollY: '500px',
				responsive: true,
				paging: true,
				searching: true,
				info: false,
				data: [],
				"columns": [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , searchable: false},
					{data: 'gateway_sn', className: "text-left" },        
					{data: 'gateway_ip', className: "text-left" },
					{data: 'gateway_mac', className: "text-left" },
					{data: 'status', name: 'status', orderable: true, searchable: true},
					{data: 'update', name: 'status', orderable: false, searchable: true},
					{data: 'action', name: 'action', orderable: false, searchable: false},
					{data: 'location_code'},
					{data: 'idf_number'},
					{data: 'switch_name'},
					{data: 'idf_port'},
				],
				columnDefs: [
						{ className: 'text-center', targets: [0, 4, 5, 6] },
				]
	} );
	
		/*Adjust Table Column for Gateway*/
		autoAdjustColumns(LoadGatewayPerBuildingList);

		
		function autoAdjustColumns(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
		}	
		
	MeterPerBuilding(0);
	
	function MeterPerBuilding(location_id){
			
			  $.ajax({
				url: "{{ route('getMeter') }}",
				type:"GET",
				data:{
				  siteID:{{ $SiteData[0]->site_id }},
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
				processing: true,
				//serverSide: true,
				stateSave: true,/*Remember Searches*/
				scrollCollapse: true,
				scrollY: '500px',
				scrollX: '100%',
				responsive: true,
				paging: true,
				searching: true,
				info: true,
				data: [],
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
					
				 ]
	} );	

		/*Adjust Table Column for Gateway*/
		autoAdjustColumns(LoadMeterPerBuildingList);

		
		function autoAdjustColumns(table) {
			var container = table.table().container();
			var resizeObserver = new ResizeObserver(function () {
				table.columns.adjust();
			});
			resizeObserver.observe(container);
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
						$('#eeroomlist').html('');
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var id = response[i].location_id;
							var location_code = response[i].location_code;
							var location_description = response[i].location_description;
							
							$('#eeroomlist').prepend(
							'<li class="list-group-item d-flex justify-content-between align-items-start" title="">'+
							    '<div class="ms-2 me-auto">'+
								'<div class="fw-bold">'+location_code+'</div>'+
								''+location_description+
								'</div>'+
							'<span class="badge">'+
							'<div align="right" class="action_table_menu_site">'+
							'<a href="#" title="Click to View the List of Meters and Gateways per Location" data-id="'+id+'" style="cursor: pointer;" class="btn-info btn-circle bi bi-eye-fill btn_icon_accordion btn_icon_table_view" id="viewMeterLocation" onclick="GatewayPerBuilding('+id+')"></a>'+
							'<a href="#" title="Click to Edit" data-id="'+id+'" style="cursor: pointer;" class="btn-warning btn-circle bi bi-pencil-fill btn_icon_accordion btn_icon_table_edit" id="editMeterLocation"></a>'+
							'<a href="#" title="Click to Delete" data-id="'+id+'" style="cursor: pointer;" class="btn-danger btn-circle bi-trash3-fill btn_icon_accordion btn_icon_table_delete" id="deleteMeterLocation"></a>'+
							'</div>'+'</span>'+
							'</li>');
							
							$('#gateway_location_list option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#update_gateway_location_list option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#location option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							$('#update_location option:last').after("<option label='Code:"+location_code+" - Description:"+location_description+"' data-id='"+id+"' value='"+location_code+" - "+location_description+"'>");
							
					}			
				  }else{
					  
							/*No Result Found or Error*/	
							$('#eeroomlist').html('');
							
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
			
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	} 	
 </script>
