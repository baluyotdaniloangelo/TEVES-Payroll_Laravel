   <script src="{{asset('template/assets/vendor/chart.js/chart.min.js')}}"></script>
   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>

<script type="text/javascript">

	setMaxonEndDate();
	
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
	var meter_description_chart = [];
	$("#generate_raw_report").click(function(event){
		
			event.preventDefault();
			meter_description_chart.pop();	
			document.querySelector("#chartarea").innerHTML = '<canvas id="KWhChart" style="max-height: 400px; display: block; box-sizing: border-box; height: 393px; width: 787px;" width="787" height="393"></canvas>';

					/*Reset Warnings*/
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/					
					$("#raw_report_html_table tbody").html("");					
					
			document.getElementById('generate_report_form').className = "g-3 needs-validation was-validated";

			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_id 			= $("input[name=meter_list]").val();
			let interval_type 		= $("#interval_type").val();
			let chart_type			= $("#chart_type").val();
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();

			  $.ajax({
				url: "/generate_demand_report/"+interval_type,
				type:"POST",
				timeout: 3600000,/*1 Hour in Milliseconds for Waiting time*/
				data:{
				  site_id:site_id,
				  meter_id:meter_id,
				  start_date:start_date,
				  start_time:start_time,
				  end_date:end_date,
				  end_time:end_time,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateRAWReportModal').modal('toggle');
								
				  console.log(response['data']);
				  if(response['data']!='') {
					//alert(response['data']);
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					$('#end_dateError').text('');	
					
						var len = response['data'].length;
						
						for(var i=0; i<len; i++){

							var multiplier = response['data'][i].multiplier;
							
							var datetime = response['data'][i].hour;
							
							var min_datetime = response['data'][i].min_datetime || "0000-00-00 00:00:00";
							var min_wh_total = Number(response['data'][i].min_wh_total * multiplier).toFixed(3);
							
							var max_datetime  = response['data'][i].max_datetime || "0000-00-00 00:00:00";
							var max_wh_total  = Number(response['data'][i].max_wh_total * multiplier).toFixed(3);
							
							var _kwh_total = (max_wh_total - min_wh_total);
							
							var kwh_total = Number(_kwh_total).toFixed(3);
							
							var kw_demand  = Number(response['data'][i].kw_demand).toFixed(3);
							
							var data_count = i+1;
							addData(datetime,kw_demand,data_count);
							
						}		
						
						LoadDemandData.clear().draw();
						LoadDemandData.rows.add(response.data).draw();	
						
							/*Inialize Column*/
							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							let meter_description_txt 	= $("input[name=meter_list]").val();
							$('#meter_description').text(meter_description_txt);
							
							let tenant_name_txt 	= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-description');
							$('#tenant_name').text(tenant_name_txt);

							let gateway_description_txt 			= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-gateway');
							$('#gateway_description').text(gateway_description_txt);
							
							meter_description_chart.push(meter_description_txt+" | "+tenant_name_txt);
							
							$('#date_start_txt').text(start_date + " " +start_time);
							$('#date_end_txt').text(end_date + " " +end_time);
							
							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_report()"> Excel</button>'+
							'</div>');
							
				  }else{
							LoadDemandData.clear().draw();
							/*Close Form*/
							$('#GenerateRAWReportModal').modal('toggle');
							/*No Result Found*/
							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							let meter_description_txt 	= $("input[name=meter_list]").val();
							$('#meter_description').text(meter_description_txt);
							
							let gateway_description_txt 			= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-gateway');
							$('#gateway_description').text(gateway_description_txt);
							
							document.querySelector("#chartarea").innerHTML = '<canvas id="KWhChart" style="max-height: 400px; display: block; box-sizing: border-box; height: 393px; width: 787px;" width="787" height="393"></canvas>';
							
							let tenant_name_txt 	= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-description');
							$('#tenant_name').text(tenant_name_txt);
							
							$('#date_start_txt').text(start_date + " " +start_time);
							$('#date_end_txt').text(end_date + " " +end_time);
							
							//$('#total_current_consumption').text('0.00');
							
							//$('#total_KWh_txt').text('0.00');
							
							$("#raw_report_html_table tbody").append("<tr><td colspan='15' align='center'>No Result Found</td></tr>");
							$("#download_options").html(''); 
							
					}
				},
				error: function(error) {
				 console.log(error);	
				 
				  $('#site_idError').text(error.responseJSON.errors.site_id);
				  document.getElementById('site_idError').className = "invalid-feedback";
				  
				  $('#meter_idError').text(error.responseJSON.errors.meter_id);
				  document.getElementById('meter_idError').className = "invalid-feedback";
				  			  
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
			   

		/*chart Setup*/		
		var canvas = document.getElementById("KWhChart");
		var ctx = canvas.getContext('2d');
		var chartType = chart_type;
		var myBarChart;
			  
		var data = {
		  labels: [],
		  datasets: [{
			label: meter_description_chart,
			fill: false,
			lineTension: 0.1,
			backgroundColor: "#ffab11",
			borderColor: "#ffab11", // The main line color
			borderCapStyle: 'square',
			pointBorderColor: "white",
			pointBackgroundColor: "#ffab11",
			pointBorderWidth: 1,
			pointHoverRadius: 4,
			pointHoverBackgroundColor: "green",
			pointHoverBorderColor: "rgba(33, 124, 83, 0.8)",
			pointHoverBorderWidth: 9,
			pointRadius: 2,
			pointHitRadius: 5,
			data: [],
			spanGaps: true,
		  }]
		};
				
		var options = {
		  /*scales: {
			yAxes: [{
			  ticks: {
				beginAtZero: true
			  }
			}]
		  },*/
		  title: {
			fontSize: 18,
			display: true,
			text: '',
			position: 'bottom'
		  }
		};

		init();

		function init() {
		  // Chart declaration:
		  myBarChart = new Chart(ctx, {
			type: chartType,
			data: data,
			options: options
		  });
		}

		function removeData(myBarChart) {
			myBarChart.data.labels.pop();
			myBarChart.data.datasets.forEach((dataset) => {
				dataset.data.pop();
			});
			myBarChart.update();
		}

		function addData(datetime,kwh_total,data_count) {
			  myBarChart.data.labels[data_count] =datetime;
			  myBarChart.data.datasets[0].data[data_count] = kwh_total;
			  myBarChart.update();
		}
	
	});
	
		/*Load to Dtatables*/	
		let LoadDemandData = $('#meter_demand_html_table').DataTable( {
				"language": {
						"emptyTable": "No Result Found",
						"infoEmpty": "No entries to show"
			    }, 
				// processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: false,
				paging: true,
				searching: false,
				info: true,
				data: [],
				scrollCollapse: true,
				scrollY: '500px',
				"columns": [
				/*0*/	{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-center",},  
				/*1*/	{data: 'hour', className: "text-center"},
				/*2*/	{data: 'min_datetime', className: "text-center", orderable: false },
				/*3*/	{data: 'min_wh_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*4*/	{data: 'max_datetime', className: "text-center", orderable: false },		
				/*5*/	{data: 'max_wh_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },	
				/*6*/	{data: 'multiplier', className: "text-right", orderable: false },
				/*7*/	{data: 'kw_demand', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) }
				],

		} );
		
	autoAdjustColumns(LoadDemandData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }
	
	<!--Meter Deletion Confirmation-->
	function MeterInfo() {
			
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
					
					$('#meter_name_info').html(response.meter_name);
					$('#meter_name_info_confirmed').html(response.meter_name);
					$('#MeterDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		  
	}
	
	function download_report(){
		  
			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_id 			= $("input[name=meter_list]").val();
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();
		 	
			let interval_type 		= $("#interval_type").val();
			
		var query = {
			site_id:site_id,
			meter_id:meter_id,
			start_date:start_date,
			start_time:start_time,
			end_date:end_date,
			end_time:end_time,
			interval_type:interval_type,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('/download_demand_report/')}}?" + $.param(query)
		window.open(url);
	  
	}
	
	function LoadMeterList() {
		
		$("#meter_list option").remove();
		$("<option>").appendTo('#meter_list');
		document.getElementById('meter_id').value = ''
		
		let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
		
		event.preventDefault();
			  $.ajax({
				url: "{{ route('GetMeterList') }}",
				type:"POST",
				data:{
				  site_id:site_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					  
						//$('#meter_list option:last').after("<option label='All' value='All' data-id=''>");
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id				= response[i].meter_id;
							var meter_name 		= response[i].meter_name;
							var customer_name 	= response[i].customer_name;
							var gateway_sn	 	= response[i].gateway_sn;
							
							meter_label =  (meter_name + ' | ' + customer_name);
							
							$('#meter_list option:last').after("<option label='"+meter_label+"' data-id="+id+" data-description='"+customer_name+"' data-gateway='"+gateway_sn+"' value="+meter_name+">");

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
