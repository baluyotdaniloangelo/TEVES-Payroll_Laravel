<script type="text/javascript">

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
			let building_id 		= $("#building_list option[value='" + $('#building_id').val() + "']").attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			
			  $.ajax({
				url: "/generate_sap_report",
				type:"POST",
				data:{
				  site_id:site_id,
				  building_id:building_id,
				  meter_role:meter_role,
				  start_date:start_date,
				  end_date:end_date,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateSAPReportModal').modal('toggle');
								
				  console.log(response);
				  if(response!='') {
					
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					$('#end_dateError').text('');	
					
						var len = response.length;
						for(var i=0; i<len; i++){

							var meter_name = response[i].meter_name;
							var current_reading = response[i].current_reading.toFixed(3);
							var current_reading_datetime = response[i].current_reading_datetime;
							var building_code = response[i].building_code;
							var customer_name = response[i].customer_name;
							var meter_type = response[i].meter_type;
							var prev_reading = response[i].prev_reading.toFixed(3);
							var past_two_months_reading = response[i].past_two_months_reading.toFixed(3);
							var meter_multiplier = response[i].meter_multiplier;
							
							var _current_consumption = (current_reading - prev_reading) * 1;
							current_consumption = _current_consumption.toFixed(3);

							var dateObject = new Date(current_reading_datetime);
							
							var current_date = dateObject.toLocaleDateString('en-US');
							var current_time = dateObject.toLocaleTimeString('it-IT');
							
							/*Previous Reading*/
							var _previous_consumption = prev_reading - past_two_months_reading * 1;
							previous_consumption = _previous_consumption.toFixed(3);
							
							var _difference = ((_current_consumption - _previous_consumption) / _previous_consumption) * 100;
							meter_difference = _difference.toFixed(2);
							
							var tr_str = "<tr>" +
								"<td align='center'>" + (i+1) + "</td>" +
								"<td align='center'>" + meter_name + "</td>" +
								"<td align='center'>" + current_reading + "</td>" +
								"<td align='center'>" + current_date + "</td>" +
								"<td align='center'>" + current_time + "</td>" +
								"<td align='center'></td>" +
								"<td align='center'>" + building_code + "</td>" +
								"<td align='center'>" + customer_name + "</td>" +
								"<td align='center'>" + meter_type + "</td>" +
								"<td align='center'>" + current_reading + "</td>" +
								"<td align='center'>" + prev_reading + "</td>" +
								"<td align='center'>" + meter_multiplier + "</td>" +
								"<td align='center'>" + current_consumption + "</td>" +
								"<td align='center'>" + previous_consumption + "</td>" +
								"<td align='center'>" + meter_difference + " %</td>" +
								"</tr>";
							
							
							/*Attached the Data on the Table Body*/
							$("#sap_report_html_table tbody").append(tr_str);
							
						}			
						
							let business_entity_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-business-entity');
							$('#business_entity').text(business_entity_txt);
							
							let site_code_txt 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#site_code').text(site_code_txt);
							
							let site_description_txt 	= $("input[name=site_name]").val();
							$('#site_description').text(site_description_txt);
							
							let building_code_txt 		= $("input[id=building_id]").val();
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 		=  $("#building_list option[value='" + $('#building_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							
							$('#cut_off').text(start_date);
							
							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_sap_report_excel()"> Excel</button>'+
							'</div>');
							
				  }else{
							/*Close Form*/
							$('#GenerateSAPReportModal').modal('toggle');
							/*No Result Found*/
							$("#sap_report_html_table tbody").append("<tr><td colspan='15' align='center'>No Result Found</td></tr>");
							$("#download_options").html(''); 
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

	function LoadBuildingList(SiteID) {
		
		$("#building_list option").remove();
		$("<option>").appendTo('#building_list');
		document.getElementById('building_id').value = ''
		
		let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
		
		event.preventDefault();
			  $.ajax({
				url: "{{ route('GetBuildingList') }}",
				type:"POST",
				data:{
				  site_id:site_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					  
						$('#building_list option:last').after("<option label='All' value='All' data-id=''>");
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].id;
							var building_code 			= response[i].building_code;
							var building_description 	= response[i].building_description;
							
							building_label =  (building_code + ' | ' + building_description);
							
							$('#building_list option:last').after("<option label='"+building_label+"' data-id="+id+" data-description='"+building_description+"' value="+building_code+">");


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
	  
	function download_sap_report_excel(){
		  
			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_role 			= $("#meter_role").val();		
			let building_id 		= $("#building_list option[value='" + $('#building_id').val() + "']").attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
		 		  
		var query = {
			site_id:site_id,
			building_id:building_id,
			meter_role:meter_role,
			start_date:start_date,
			end_date:end_date,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_sap_report_excel')}}?" + $.param(query)
		window.open(url);
	  
	}
	  
</script>
