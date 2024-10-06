   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   
<script type="text/javascript">

	// setMaxonEndDate();
	
	function setMaxonEndDate(){
	
		let start_date 			= $("input[name=start_date]").val();
		
		var myDate = new Date(start_date);
		var result1 = myDate.setMonth(myDate.getMonth()+1);
		
		const date_new = new Date(result1);
		
		const max_date = document.getElementById('end_date');
		
		document.getElementById("end_date").min = start_date;
		document.getElementById("end_date").max = date_new.toISOString("en-US").substring(0, 10);
		
		document.getElementById("end_date").value = start_date;
		
	}
	
	function CheckEndDateValidity(){
		
		let start_date 			= $("input[name=start_date]").val();
		let end_date 			= $("input[name=end_date]").val();
		
		let end_date_max 		= document.getElementById("end_date").max;
		
		const x = new Date(start_date);
		const y = new Date(end_date);
		
		const edt = new Date(end_date_max);
		
			if(x > y){
					
					/*Set The End Date same with Start Date*/
					document.getElementById("end_date").value = start_date;
				
			}
			else if(edt < y){
					
					/*Set The End Date same with Start Date*/
					document.getElementById("end_date").value = start_date;
					
			}else{
				
					$('#end_dateError').html('');
					document.getElementById('end_dateError').className = "valid-feedback";
			
			}
	
	}
	<!--Load Table-->
	$("#generate_sap_report").click(function(event){
		
			event.preventDefault();
	
					/*Reset Warnings*/
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/	
					
					$("#sap_report_html_table tbody").html("");					
					
			document.getElementById('generate_report_form').className = "g-3 needs-validation was-validated";

			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_role 			= $("#meter_role").val();		
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();
			//let valid_sap_meter_chk 	= document.getElementById("valid_sap_meter").checked;
			
			  $.ajax({
				url: "/generate_site_report",
				type:"POST",
				timeout: 3600000,/*1 Hour in Milliseconds for Waiting time*/
				data:{
				  site_id:site_id,
				  meter_role:meter_role,
				  start_date:start_date,
				  start_time:start_time,
				  end_date:end_date,
				  end_time:end_time,
				  //valid_sap_meter:valid_sap_meter_chk,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateSAPReportModal').modal('toggle');
								
				  console.log(response.data);
				  if(response.data!='') {
					
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					$('#end_dateError').text('');	
					
						var len = response.length;
						var total_current_consumption = 0;
						
						LoadSiteData.clear().draw();
						LoadSiteData.rows.add(response.data).draw();	
						
						var len = response.data.length;
						var total_current_consumption = 0;
						
						 for(var i=0; i<len; i++){
							
							 var meter_multiplier = response.data[i].meter_multiplier;
							 var start_reading = Number(response.data[i].start_reading * meter_multiplier).toFixed(3);
							 var ending_reading = Number(response.data[i].ending_reading * meter_multiplier).toFixed(3);
							
							 var _current_consumption = (ending_reading - start_reading);
							 current_consumption = _current_consumption.toFixed(3);
							
							 total_current_consumption += _current_consumption;
							
						 }			
									
							$('#total_current_consumption_top').text(total_current_consumption.toLocaleString("en-PH", {maximumFractionDigits: 3}));
							$('#total_current_consumption').text(total_current_consumption.toLocaleString("en-PH", {maximumFractionDigits: 3}));
							
							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							$('#date_start_txt').text(start_date + " " +start_time);
							$('#date_end_txt').text(end_date + " " +end_time);
							
							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_sap_report_excel()"> Excel</button>'+
							'</div>');
							
				  }else{
							LoadSiteData.clear().draw();
							/*Close Form*/
							$('#GenerateSAPReportModal').modal('toggle');
							
							$('#building_code').text('');
							$('#building_name').text('');
							$('#date_start_txt').text('');
							$('#date_end_txt').text('');
							
							/*No Result Found*/
							// $("#sap_report_html_table tbody").append("<tr><td colspan='15' align='center'>No Result Found</td></tr>");
							$("#download_options").html(''); 
							$('#total_current_consumption_top').text('');
							$('#total_current_consumption').text('');
					}
				},
				error: function(error) {
				 console.log(error);	
				 
				  $('#site_idError').text(error.responseJSON.errors.site_id);
				  document.getElementById('site_idError').className = "invalid-feedback";
				  			  
				  $('#start_dateError').text(error.responseJSON.errors.start_date);
				  document.getElementById('start_dateError').className = "invalid-feedback";		

				  $('#end_dateError').text(error.responseJSON.errors.end_date);
				  document.getElementById('end_dateError').className = "invalid-feedback";		
				
				  $('#InvalidModal').modal('toggle');				  	  
				  
				},
				beforeSend:function()
				{
					$('#loading_data').show();
				},
				complete: function(){
					$('#loading_data').hide();
				}
			   });
		
	  });

		/*Load to Datatables*/	
		let LoadSiteData = $('#site_report_html_table').DataTable( {
				"language": {
						"emptyTable": "No Result Found",
						"infoEmpty": "No entries to show"
			    }, 
				// processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: false,
				paging: true,
				searching: true,
				info: true,
				data: [],
				scrollCollapse: true,
				scrollY: '500px',
				"columns": [
				/*0*/	{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right",},  
				/*1*/	{data: 'customer_name', className: "text-left", orderable: false},
				/*2*/	{data: 'meter_name', className: "text-center", orderable: false},
				/*3*/	{data: 'gateway_sn', className: "text-center", orderable: false },
				/*4*/	{data: 'location_code', className: "text-left", orderable: false},
				/*5*/	{data: 'start_reading_datetime', className: "text-center", orderable: false},		
				/*6*/	{data: 'start_reading', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 3, '' ), orderable: false },		
				/*7*/	{data: 'ending_reading_datetime', className: "text-center", orderable: false },		
				/*8*/	{data: 'ending_reading', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 3, '' ), orderable: false },	
				/*9*/	{data: 'meter_multiplier', className: "text-right", orderable: false },
				/*10*/	{data: 'current_consumption', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 3, '' ), orderable: false }
				]
		} );	
		
		autoAdjustColumns(LoadSiteData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	

	function download_sap_report_excel(){
		  
			let site_id 				= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_role 				= $("#meter_role").val();		
			
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();
			//let valid_sap_meter_chk 	= document.getElementById("valid_sap_meter").checked;
		 		  
		var query = {
			site_id:site_id,
			meter_role:meter_role,
			start_date:start_date,
			start_time:start_time,
			end_date:end_date,
			end_time:end_time,
			//valid_sap_meter:valid_sap_meter_chk,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_site_report_excel')}}?" + $.param(query)
		window.open(url);
	  
	}
	  
</script>
