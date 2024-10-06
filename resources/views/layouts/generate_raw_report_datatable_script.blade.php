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
	
	//$(window).resize(function(){get_raw_data();});
	
	function get_raw_data(){
	// $("#generate_raw_report").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#site_idError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
					/*Reset Table Upon Resubmit form*/					
					
			document.getElementById('generate_report_form').className = "g-3 needs-validation was-validated";

			let site_id 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_id 	= $("input[name=meter_list]").val();
			let start_date 	= $("input[name=start_date]").val();
			let end_date 	= $("input[name=end_date]").val();
			let start_time 	= $("input[name=start_time]").val();
			let end_time 	= $("input[name=end_time]").val();
			
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
					
					LoadRawData.clear().draw();
					LoadRawData.rows.add(response.data).draw();	

						/*Default Visible*/
						LoadRawData.column(0).visible(true);
						LoadRawData.column(1).visible(true);
						LoadRawData.column(2).visible(true);
						LoadRawData.column(3).visible(true);
						LoadRawData.column(4).visible(true);
						LoadRawData.column(5).visible(true);
						
						 var total_result = response.data.length; 
						 
						 if(total_result!=0){
						 
							 if(cols_set1_chk==true){ 
									LoadRawData.column(6).visible(true);
									LoadRawData.column(7).visible(true);
									LoadRawData.column(8).visible(true);
							 } else{ 
									LoadRawData.column(6).visible(false);
									LoadRawData.column(7).visible(false);
									LoadRawData.column(8).visible(false);
							 }
						
							if(cols_set2_chk==true){ 
									 LoadRawData.column(9).visible(true);
									 LoadRawData.column(10).visible(true);
									 LoadRawData.column(11).visible(true);
							} else{ 
									 LoadRawData.column(9).visible(false);
									 LoadRawData.column(10).visible(false);
									 LoadRawData.column(11).visible(false);
							}
										
							
							if(cols_set3_chk==true){ 
									LoadRawData.column(12).visible(true);
									LoadRawData.column(13).visible(true);
									LoadRawData.column(14).visible(true);
									LoadRawData.column(15).visible(true);
									LoadRawData.column(16).visible(true);
							} else{ 
									LoadRawData.column(12).visible(false);
									LoadRawData.column(13).visible(false);
									LoadRawData.column(14).visible(false);
									LoadRawData.column(15).visible(false);
									LoadRawData.column(16).visible(false);
							}
							
							if(cols_set5_chk==true){ 
									LoadRawData.column(17).visible(true);
									LoadRawData.column(18).visible(true);
									LoadRawData.column(19).visible(true);
									LoadRawData.column(20).visible(true);
									LoadRawData.column(21).visible(true);
							} else{ 
									LoadRawData.column(17).visible(false);
									LoadRawData.column(18).visible(false);
									LoadRawData.column(19).visible(false);
									LoadRawData.column(20).visible(false);	
									LoadRawData.column(21).visible(false);							
							}
							
											  
							if(cols_set6_chk==true){ 
									LoadRawData.column(22).visible(true);
									LoadRawData.column(23).visible(true);
							} else{ 
									LoadRawData.column(22).visible(false);
									LoadRawData.column(23).visible(false);
							}
							
								
							if(cols_set7_chk==true){ 
									LoadRawData.column(24).visible(true);
									LoadRawData.column(25).visible(true);
							} else{ 
									LoadRawData.column(24).visible(false);
									LoadRawData.column(25).visible(false);
							}		
						
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

				 
				},
				error: function(error) {
				 console.log(error);	
				 
				 LoadRawData.clear().draw();
				 
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
		
	  // });
	}
		/*Load Meter List Per Gateway*/	
		let LoadRawData = $('#raw_report_html_table').DataTable( {
				//processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				// responsive: true,
				// paging: true,
				// searching: true,
				// info: false,
				// data: [],
				// scrollCollapse: true,
				// scrollY: '500px',
				// scrollX: '1060px',
				// LengthChange : false, 
				// "pageLength": 96,
				//processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				scrollCollapse: true,
				scrollY: '500px',
				//scrollX: '100%',
				responsive: false,
				paging: false,
				searching: false,
				info: true,
				"columns": [
				/*0*/	{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-center",},  
				/*1*/	{data: 'datetime', className: "text-center"},
				/*2*/	{data: 'wh_del', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*3*/	{data: 'wh_rec', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*4*/	{data: 'wh_net', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },		
				/*5*/	{data: 'wh_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },	
				/*6*/	{data: 'vrms_a', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*7*/	{data: 'vrms_b', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*8*/	{data: 'vrms_c', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*9*/	{data: 'irms_a', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*10*/	{data: 'irms_b', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*11*/	{data: 'irms_c', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*12*/	{data: 'freq', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*13*/	{data: 'pf', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*14*/	{data: 'watt', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*15*/	{data: 'va', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*16*/	{data: 'var', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*17*/	{data: 'varh_neg', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*18*/	{data: 'varh_pos', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*19*/	{data: 'varh_net', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*20*/	{data: 'varh_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*21*/	{data: 'vah_total', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*22*/	{data: 'max_rec_kw_dmd', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ), visible: false },
				/*23*/	{data: 'max_rec_kw_dmd_time', className: "text-right", orderable: false, visible: false},
				/*24*/	{data: 'mac_addr', className: "text-center", orderable: false, visible: false},
				/*25*/	{data: 'soft_rev', className: "text-center", orderable: false, visible: false}
				],
		
		} );

		 autoAdjustColumns(LoadRawData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }		
		
		
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
