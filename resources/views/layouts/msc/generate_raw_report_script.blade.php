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
	$("#generate_raw_report").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/					
					$("#raw_report_html_table tbody").html("");					
					
			document.getElementById('generate_report_form').className = "g-3 needs-validation was-validated";

			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_id 			= $("input[name=meter_list]").val();
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();
			
			let cols_set1_chk = document.getElementById("cols_set1").checked;
			let cols_set2_chk = document.getElementById("cols_set2").checked;
			let cols_set3_chk = document.getElementById("cols_set3").checked;			
			let cols_set4_chk = document.getElementById("cols_set4").checked;	
			let cols_set5_chk = document.getElementById("cols_set5").checked;
			let cols_set6_chk = document.getElementById("cols_set6").checked;	
			let cols_set7_chk = document.getElementById("cols_set7").checked;
			
			  $.ajax({
				url: "/generate_raw_report",
				type:"POST",
				timeout: 3600000,/*1 Hour in Milliseconds for Waiting time*/
				data:{
				  site_id:site_id,
				  meter_id:meter_id,
				  start_date:start_date,
				  start_time:start_time,
				  end_date:end_date,
				  end_time:end_time,
				  cols_set1:cols_set1_chk,
				  cols_set2:cols_set2_chk,
				  cols_set3:cols_set3_chk,
				  cols_set4:cols_set4_chk,
				  cols_set5:cols_set5_chk,
				  cols_set6:cols_set6_chk,
				  cols_set7:cols_set7_chk,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateRAWReportModal').modal('toggle');
								
				  console.log(response);
				  if(response!='') {
					
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
					$('#end_dateError').text('');	
					
						var len = response.length;
						for(var i=0; i<len; i++){

							var datetime = response[i].datetime;
							
							var vrms_a = Number(response[i].vrms_a).toFixed(3);
							var vrms_b = Number(response[i].vrms_b).toFixed(3);
							var vrms_c = Number(response[i].vrms_b).toFixed(3);
							
							var irms_a = Number(response[i].irms_a).toFixed(3);
							var irms_b = Number(response[i].irms_b).toFixed(3);
							var irms_c = Number(response[i].irms_c).toFixed(3);
							
							var freq = Number(response[i].freq).toFixed(3);
							var pf = Number(response[i].pf).toFixed(3);
							var kw = Number(response[i].watt).toFixed(3);
							var kva = Number(response[i].va).toFixed(3);
							var kvar = Number(response[i].var).toFixed(3);
							
							var kwh_del = Number(response[i].wh_del).toFixed(3);
							var kwh_rec = Number(response[i].wh_rec).toFixed(3);
							var kwh_net = Number(response[i].wh_net).toFixed(3);
							var kwh_total = Number(response[i].wh_total).toFixed(3);
		
							var kvarh_neg = Number(response[i].varh_neg).toFixed(3);
							var kvarh_pos = Number(response[i].varh_pos).toFixed(3);
							var kvarh_net = Number(response[i].varh_net).toFixed(3);							
							var kvarh_total = Number(response[i].varh_total).toFixed(3);
							var kvah_total = Number(response[i].vah_total).toFixed(3);
							
							var max_rec_kw_dmd = Number(response[i].max_rec_kw_dmd).toFixed(3);
							var max_rec_kw_dmd_time = response[i].max_rec_kw_dmd_time;
							
							var mac_addr = response[i].mac_addr;
							var soft_rev = response[i].soft_rev;
							
							//current_consumption = _current_consumption.toFixed(3);
							//var dateObject = new Date(current_reading_datetime);					
							//var current_date = dateObject.toLocaleDateString('en-US');
							//var current_time = dateObject.toLocaleTimeString('it-IT');
							
							var tr_str = "<tr>" +
								"<td align='center'>" + (i+1) + "</td>" +
								"<td align='center' nowrap>" + datetime + "</td>" +
								"<td align='right' class='column_set1 hidden'>" + vrms_a + "</td>" +
								"<td align='right' class='column_set1 hidden'>" + vrms_b + "</td>" +
								"<td align='right' class='column_set1 hidden'>" + vrms_c + "</td>" +
								
								"<td align='right' class='column_set2 hidden'>" + irms_a + "</td>" +
								"<td align='right' class='column_set2 hidden'>" + irms_b + "</td>" +
								"<td align='right' class='column_set2 hidden'>" + irms_c + "</td>" +
								
								"<td align='right' class='column_set3 hidden'>" + freq + "</td>" +
								"<td align='right' class='column_set3 hidden'>" + pf + "</td>" +
								"<td align='right' class='column_set3 hidden'>" + kw + "</td>" +
								"<td align='right' class='column_set3 hidden'>" + kva + "</td>" +
								"<td align='right' class='column_set3 hidden'>" + kvar + "</td>" +
								
								"<td align='right'>" + kwh_del + "</td>" +
								"<td align='right'>" + kwh_rec + "</td>" +
								"<td align='right'>" + kwh_net + "</td>" +
								"<td align='right'>" + kwh_total + "</td>" +
								
								"<td align='right' class='column_set5 hidden'>" + kvarh_neg + "</td>" +
								"<td align='right' class='column_set5 hidden'>" + kvarh_pos + "</td>" +
								"<td align='right' class='column_set5 hidden'>" + kvarh_net + "</td>" +
								"<td align='right' class='column_set5 hidden'>" + kvarh_total + "</td>" +
								"<td align='right' class='column_set5 hidden'>" + kvah_total + "</td>" +
								
								"<td align='right' class='column_set6 hidden'>" + max_rec_kw_dmd + "</td>" +
								"<td align='center' class='column_set6 hidden'>" + max_rec_kw_dmd_time + "</td>" +
								
								"<td align='center' class='column_set7 hidden'>" + mac_addr + "</td>" +
								"<td align='center' class='column_set7 hidden'>" + soft_rev + "</td>" +
								
								"</tr>";
							
							/*Attached the Data on the Table Body*/
							$("#raw_report_html_table tbody").append(tr_str);
							
						}			
						
						/*Inialize Column*/
						
						if(cols_set1_chk==true){ 
								$('.column_set1').removeClass('hidden_cols');
								var colset1 = 1;
						} else{ 
								$('.column_set1').addClass('hidden_cols');
								var colset1 = 0;
						}
						
						
						if(cols_set2_chk==true){ 
								$('.column_set2').removeClass('hidden_cols');
								var colset2 = 1;
						} else{ 
								$('.column_set2').addClass('hidden_cols');
								var colset2 = 0;					
						}
						
						
						if(cols_set3_chk==true){ 
								$('.column_set3').removeClass('hidden_cols');
								var colset3 = 1;
						} else{ 
								$('.column_set3').addClass('hidden_cols');
								var colset3 = 0;
						}
						
							
						if(cols_set5_chk==true){ 
								$('.column_set5').removeClass('hidden_cols');
								var colset5 = 1;
						} else{ 
								$('.column_set5').addClass('hidden_cols');
								var colset5 = 0;					
						}
						
						
						if(cols_set6_chk==true){ 
								$('.column_set6').removeClass('hidden_cols');
								var colset6 = 1;
						} else{ 
								$('.column_set6').addClass('hidden_cols');
								var colset6 = 0;
						}
						
							
						if(cols_set7_chk==true){ 
								$('.column_set7').removeClass('hidden_cols');
								var colset7 = 1;
						} else{ 
								$('.column_set7').addClass('hidden_cols');
								var colset7 = 0;					
						}				

							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							let meter_description_txt 	= $("input[name=meter_list]").val();
							$('#meter_description').text(meter_description_txt);
							
							let tenant_name_txt 	= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-description');
							$('#tenant_name').text(tenant_name_txt);
							
							$('#date_start_txt').text(start_date + " " +start_time);
							$('#date_end_txt').text(end_date + " " +end_time);
								
							let gateway_description_txt 			= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-gateway');
							$('#gateway_description').text(gateway_description_txt);

							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_raw_report_excel()"> Excel</button>'+
							'</div>');
							
				  }else{
					  
							/*Close Form*/
							$('#GenerateRAWReportModal').modal('toggle');
							/*No Result Found*/
							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							let meter_description_txt 	= $("input[name=meter_list]").val();
							$('#meter_description').text(meter_description_txt);
							
							let tenant_name_txt 	= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-description');
							$('#tenant_name').text(tenant_name_txt);
							
							$('#date_start_txt').text(start_date + " " +start_time);
							$('#date_end_txt').text(end_date + " " +end_time);

							let gateway_description_txt 			= $("#meter_list option[value='" + $('#meter_id').val() + "']").attr('data-gateway');
							$('#gateway_description').text(gateway_description_txt);

							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_raw_report_excel()"> Excel</button>'+
							'</div>');
							
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
		
	  });

	function download_raw_report_excel(){
		  
			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_id 			= $("input[name=meter_list]").val();
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			let start_time 			= $("input[name=start_time]").val();
			let end_time 			= $("input[name=end_time]").val();
			
			let cols_set1_chk = document.getElementById("cols_set1").checked;
			let cols_set2_chk = document.getElementById("cols_set2").checked;
			let cols_set3_chk = document.getElementById("cols_set3").checked;			
			let cols_set4_chk = document.getElementById("cols_set4").checked;	
			let cols_set5_chk = document.getElementById("cols_set5").checked;
			let cols_set6_chk = document.getElementById("cols_set6").checked;	
			let cols_set7_chk = document.getElementById("cols_set7").checked;

		var query = {
				site_id:site_id,
				meter_id:meter_id,
				start_date:start_date,
				start_time:start_time,
				end_date:end_date,
				end_time:end_time,
				cols_set1:cols_set1_chk,
				cols_set2:cols_set2_chk,
				cols_set3:cols_set3_chk,
				cols_set4:cols_set4_chk,
				cols_set5:cols_set5_chk,
				cols_set6:cols_set6_chk,
				cols_set7:cols_set7_chk,
				_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_raw_report_excel')}}?" + $.param(query)
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
							
							var id = response[i].meter_id;
							var meter_name 		= response[i].meter_name;
							var customer_name 	= response[i].customer_name;
							var gateway_sn 	= response[i].gateway_sn;
							
							meter_label =  (meter_name + ' | ' + customer_name);
							
							//$('#meter_list option:last').after("<option label='"+meter_label+"' data-id="+id+" data-description='"+customer_name+"' value="+meter_name+">");
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
