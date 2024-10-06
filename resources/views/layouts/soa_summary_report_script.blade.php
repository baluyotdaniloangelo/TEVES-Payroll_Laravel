   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   
<script type="text/javascript">

	<!--Load Table-->
	$("#generate_report").click(function(event){
		
			event.preventDefault();
	
					/*Reset Warnings*/
					$('#client_idxError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');		
					
			document.getElementById('generate_report_form').className = "g-3 needs-validation was-validated";

			let client_idx 			= $('#client_name option[value="' + $('#client_id').val() + '"]').attr('data-id');
			let start_date 			= $("input[name=start_date]").val();
			let end_date 			= $("input[name=end_date]").val();
			
			  $.ajax({
				url: "/generate_soa_summary",
				type:"POST",
				//dataType: 'JSON',
				data:{
				  client_idx:client_idx,
				  start_date:start_date,
				  end_date:end_date,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				
				/*Close Form*/
				$('#CreateReportModal').modal('toggle');
				
				/*Call Function to Get the Client Name and Address*/
				get_client_details();
							
				  console.log(response);
				  if(response!='') {
					
					$('#client_idxError').text('');
					$('#start_dateError').text('');
					$('#end_dateError').text('');	
						
							LoadSOASummaryData.clear().draw();
							LoadSOASummaryData.rows.add(response.data).draw();	
							
							var start_date_new  = new Date(start_date);
							start_date_new_format = (start_date_new.toLocaleDateString("en-PH")); // 9/17/2016
							
							var end_date_new  = new Date(end_date);
							end_date_new_format = (end_date_new.toLocaleDateString("en-PH")); // 9/17/2016

							$('#po_info').text(start_date_new_format + ' - ' +end_date_new_format);	
							$('#billing_date_info').text('<?php echo strtoupper(date('M/d/Y')); ?>');	
							
							$("#download_options").html('<div class="btn-group" role="group" aria-label="Basic outlined example" style="">'+
							'<button type="button" class="btn btn-outline-primary btn-sm bi-file-earmark-pdf" onclick="download_soa_summary_report_pdf()"> PDF</button>'+
							'</div>');
							
				  }else{
							/*Close Form*/
							$('#CreateReportModal').modal('toggle');
							/*No Result Found*/
							$('#total_due').text('');
							$("#download_options").html(''); 
				
					}
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("generate_report").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
				},
				complete: function(){
					
					/*Enable Submit Button*/
					document.getElementById("generate_report").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();
					
				},
				error: function(error) {
				 console.log(error);	
				 
					$('#client_idxError').text(error.responseJSON.errors.client_idx);
					document.getElementById('client_idxError').className = "invalid-feedback";
				  			  
					$('#start_dateError').text(error.responseJSON.errors.start_date);
					document.getElementById('start_dateError').className = "invalid-feedback";		

					$('#end_dateError').text(error.responseJSON.errors.end_date);
					document.getElementById('end_dateError').className = "invalid-feedback";		
				
					$('#InvalidModal').modal('toggle');				  	  
				  
				}
			   });
		
	  });


		/*Load to Datatables*/	
		let LoadSOASummaryData = $('#soasummary').DataTable( {
				"language": {
						"emptyTable": "No Result Found",
						"infoEmpty": "No entries to show"
			    }, 
				responsive: false,
				paging: true,
				searching: false,
				info: false,
				data: [],
				scrollCollapse: true,
				scrollY: '500px',
				scrollx: false,
				"columns": [
				/*0*/	{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-center",},  
				/*1*/	{data: 'billing_date', className: "text-left", orderable: false },
				/*2*/	{data: 'control_number', className: "text-left", orderable: false },
				/*3*/	{data: 'receivable_description', className: "text-left", orderable: false },	
				/*4*/	{data: 'receivable_amount', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
				/*5*/	{data: 'receivable_remaining_balance', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
				/*6*/	{data: 'current_balance', className: "text-right", orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
				],
				
		} );
		
	autoAdjustColumns(LoadSOASummaryData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }

	function get_client_details(){
		  
			let client_idx 			= $('#client_name option[value="' + $('#client_id').val() + '"]').attr('data-id');
			
			  $.ajax({
				url: "/client_info",
				type:"POST",
				data:{
				  clientID:client_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {		
					
					/*Set Details*/
					$('#client_name_report').text(response.client_name);
					$('#client_address_report').text(response.client_address);
					
					/*Set Details for Receivables*/
					$('#client_name_receivables').text(response.client_name);
					$('#client_address_receivables').text(response.client_address);
					$('#client_tin_receivables').text(response.client_tin);
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  
	}

	function download_soa_summary_report_pdf(receivable_id){
			
			let client_idx 		= $('#client_name option[value="' + $('#client_id').val() + '"]').attr('data-id');
			let start_date 		= $("input[name=start_date]").val();
			let end_date 		= $("input[name=end_date]").val();

			let company_header 					= $("#company_header").val();	  
			
		var query = {
			company_header:company_header,
			client_idx:client_idx,
			start_date:start_date,
			end_date:end_date,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_soa_summary_pdf')}}?" + $.param(query)
		window.open(url);
	  
	}
</script>
