   <!-- Page level plugins -->
   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   
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
			let start_date 			= $("input[name=start_date]").val();
	
			var end_date = new Date(start_date);
			end_date.setMonth(end_date.getMonth() - 1);
			var new_end_date = end_date.toISOString().slice(0, 10);

			  $.ajax({
				url: "/generate_sap_report",
				type:"POST",
				timeout: 3600000,/*1 Hour in Milliseconds for Waiting time*/
				data:{
				  site_id:site_id,
				  meter_role:meter_role,
				  start_date:start_date,
				  end_date:new_end_date,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#GenerateSAPReportModal').modal('toggle');
								
				  console.log(response);
				  if(response!='') {
					
					$('#site_idError').text('');
					$('#start_dateError').text('');
					
						LoadSAPData.clear().draw();
						LoadSAPData.rows.add(response.data).draw();			
						
						document.getElementById('site_idError').className = "";
						document.getElementById('start_dateError').className = "";
						
							let building_code_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-code');
							$('#building_code').text(building_code_txt);
							
							let building_name_txt 	= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-description');
							$('#building_name').text(building_name_txt);
							
							$('#cut_off').text(start_date);
							
							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi bi-file-earmark-excel" onclick="download_sap_report_excel()"> Excel</button>'+
							'</div>');
							
				  }else{
							
							LoadSAPData.clear().draw();
							
							$('#building_code').text('');
							$('#building_name').text('');
							$('#cut_off').text('');
					  
							/*Close Form*/
							$('#GenerateSAPReportModal').modal('toggle');
							/*No Result Found*/
							$("#download_options").html(''); 
							
					}
				},
				error: function(error) {
				 console.log(error);	
				 
				  $('#site_idError').text(error.responseJSON.errors.site_id);
				  document.getElementById('site_idError').className = "invalid-feedback";
				  			  
				  $('#start_dateError').text(error.responseJSON.errors.start_date);
				  document.getElementById('start_dateError').className = "invalid-feedback";		
				
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
		let LoadSAPData = $('#sap_report_html_table').DataTable( {
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
				/*1*/	{data: 'meter_name', className: "text-center", orderable: false},
				/*2*/	{data: 'current_reading', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*3*/	{data: 'date_generated', className: "text-center", orderable: false },
				/*4*/	{data: 'time_generated', className: "text-center", orderable: false},
				/*5*/	{data: 'initial', className: "text-center", orderable: false  },		
				/*6*/	{data: 'building_code', className: "text-center", orderable: false },		
				/*7*/	{data: 'customer_name', className: "text-left", orderable: false },		
				/*8*/	{data: 'meter_type', className: "text-center", orderable: false },	
				/*9*/	{data: 'current_reading', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*10*/	{data: 'prev_reading', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 3, '' ) },
				/*11*/	{data: 'meter_multiplier', className: "text-right", orderable: false },
				/*12*/	{data: 'current_consumption', className: "text-right", orderable: false},
				/*13*/	{data: 'previous_consumption', className: "text-right", orderable: false},
				/*14*/	{data: 'difference_consumption', className: "text-right", orderable: false }
				]
		} );		 

		autoAdjustColumns(LoadSAPData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }			
	  
	function download_sap_report_excel(){
		  
			let site_id 			= $("#site_name option[value='" + $('#site_id').val() + "']").attr('data-id');
			let meter_role 			= $("#meter_role").val();		
			let start_date 			= $("input[name=start_date]").val();
			
		 	var end_date = new Date(start_date);
			end_date.setMonth(end_date.getMonth() - 1);
			var new_end_date = end_date.toISOString().slice(0, 10);
			
		var query = {
			site_id:site_id,
			meter_role:meter_role,
			start_date:start_date,
			end_date:new_end_date,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_sap_report_excel')}}?" + $.param(query)
		window.open(url);
	  
	}
	  
</script>
