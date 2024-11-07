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
			let cut_off 			= $("input[name=cut_off]").val();
			
			
			  $.ajax({
				url: "/generate_sap_report_sm",
				type:"POST",
				data:{
				  site_id:site_id,
				  building_id:building_id,
				  meter_role:meter_role,
				  cut_off:cut_off,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateSAPReportModal').modal('toggle');
								
				  console.log(response);
				  if(response!='') {
					
					$('#site_idError').text('');
					$('#scut_offError').text('');
					
						var len = response.length;
						for(var i=0; i<len; i++){

							var meter_name = response[i].meter_name;
							var current_reading = response[i].current_reading.toFixed(3);
							var current_reading_datetime = response[i].current_reading_datetime;
							var measuring_point = response[i].measuring_point;
							var customer_name = response[i].customer_name;
							var meter_multiplier = response[i].meter_multiplier;
							
							var dateObject = new Date(current_reading_datetime);
							
							var current_date = dateObject.toLocaleDateString('en-US');
							var current_time = dateObject.toLocaleTimeString('it-IT');
				
							var tr_str = "<tr>" +
								"<td align='center'>" + (i+1) + "</td>" +
								"<td align='center'>" + meter_name + "</td>" +
								"<td align='center'>" + customer_name + "</td>" +
								"<td align='center'>" + current_reading + "</td>" +
								"<td align='center'>" + current_date + "</td>" +
								"<td align='center'>" + current_time + "</td>" +
								"<td align='center'>" + measuring_point + "</td>" +
								"</tr>";
							
							/*Attached the Data on the Table Body*/
							$("#sap_report_html_table tbody").append(tr_str);
							
						}			
						
							let business_entity_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-business-entity');
							$('#business_entity_text').text(business_entity_txt);
							
							let site_code_txt 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#site_code_text').text(site_code_txt);
							
							let site_description_txt 	= $("input[name=site_name]").val();
							$('#site_description_text').text(site_description_txt);
							
							let building_code_txt 		= $("input[id=building_id]").val();
							$('#building_code_text').text(building_code_txt);
							
							let building_name_txt 		=  $("#building_list option[value='" + $('#building_id').val() + "']").attr('data-description');
							$('#building_name_text').text(building_name_txt);
							
							let cut_off_txt 		=  $("input[name=cut_off]").val();
							$('#cut_off_text').text(cut_off_txt);
							
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
			let cut_off 			= $("input[name=cut_off]").val();
		 		  
		var query = {
			site_id:site_id,
			building_id:building_id,
			meter_role:meter_role,
			cut_off:cut_off,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_sap_report_excel_sm')}}?" + $.param(query)
		window.open(url);
	  
	}
	  
</script>
