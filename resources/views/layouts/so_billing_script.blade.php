    <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
	<script type="text/javascript">
	<!--Load Table-->
	$(function () {

		var getSOBillingTransactionList = $('#getSOBillingTransactionList').DataTable({
			"language": {
						 "decimal": ".",
            "thousands": ",",
						"lengthMenu":'<select class="dt-input">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> '
		    },
			processing: true,
			serverSide: true,
			pageLength: 10,
			stateSave: true,/*Remember Searches*/
			responsive: true,
			ajax: "{{ route('getSOBillingTransactionList') }}",
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'order_date', className: "text-center"},
					{data: 'order_time', orderable: false, className: "text-center"},
					{data: 'so_number', orderable: false, className: "text-left"},					
					{data: 'client_name', orderable: false},  
					{data: 'drivers_name', orderable: false}, 
					{data: 'plate_no', orderable: false},  		
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			order: [[ 1, "desc" ]],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1, 2, 3] },
					//{ type: 'numeric-comma', targets: [8,9] }
			]
		});
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#AddSOModal"></button>'+
				'</div>').appendTo('#billing_option');
				
		$('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
		
		});				
				
	});
	
	/*Create SO*/
	$("#save-so-billing-transaction").click(function(event){
			
					event.preventDefault();
			
					/*Reset Warnings*/
					$('#so_order_dateError').text('');
					$('#so_order_timeError').text('');
					$('#so_numberError').text('');				  
					$('#sclient_idxError').text('');
					$('#splate_noError').text('');
					$('#sdrivers_nameError').text('');

			document.getElementById('SOBillingformNew').className = "g-3 needs-validation was-validated";
			
			let branch_id 				= $("#branch_id").val();
			let order_date 				= $("input[name=so_order_date]").val();
			let order_time 				= $("input[name=so_order_time]").val();
			let so_number 				= $("input[name=so_number]").val();/*SO NUMBER*/
			let client_idx 				= $('#so_client_name option[value="' + $('#so_client_idx').val() + '"]').attr('data-id');
			let plate_no 				= $("input[name=so_plate_no]").val();
			let drivers_name 			= $("input[name=so_drivers_name]").val();
			
			/*Client and Product Name*/
			let client_name 			= $("input[name=so_client_name]").val();
			
			  $.ajax({
				url: "{{ route('CreateSOPost') }}",
				type:"POST",
				data:{
				  branch_id:branch_id,
				  order_date:order_date,
				  order_time:order_time,
				  so_number:so_number,
				  client_idx:client_idx,
				  plate_no:plate_no,
				  drivers_name:drivers_name,
				   _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#so_order_dateError').text('');
					$('#so_order_timeError').text('');
					$('#so_numberError').text('');				  
					$('#so_client_idxError').text('');
					$('#so_plate_noError').text('');
					$('#so_drivers_nameError').text('');
					
					document.getElementById("SOBillingformNew").reset();				
					document.getElementById('SOBillingformNew').className = "g-3 needs-validation";
					
					so_id = response.so_id;
					
					/*Open Cashier's Report*/
					var url = "{{URL::to('so_add_product')}}";
					window.location.href = url+'/'+so_id;
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.so_number=="The so number has already been taken."){
							  
				  $('#so_numberError').html("<b>"+ so_number +"</b> has already been taken.");
				  document.getElementById('so_numberError').className = "invalid-feedback";
				  document.getElementById('so_number').className = "form-control is-invalid";
				  $('#so_number').val("");
				  
				}else{
				  $('#so_numberError').text(error.responseJSON.errors.so_number);
				  document.getElementById('so_numberError').className = "invalid-feedback";
				}
				
				  $('#so_client_idxError').text(error.responseJSON.errors.client_idx);
				  document.getElementById('so_client_idxError').className = "invalid-feedback";
				  
				  $('#plate_noError').text(error.responseJSON.errors.plate_no);
				  document.getElementById('plate_noError').className = "invalid-feedback";	
				  
				  $('#drivers_nameError').text(error.responseJSON.errors.drivers_name);
				  document.getElementById('drivers_nameError').className = "invalid-feedback";			

				  $('#switch_notice_off').show();
				  $('#sw_off').html("Invalid Input");
				  setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Pay Receivables-->
	$('body').on('click','#deleteSO',function(){
			
			event.preventDefault();
			let SOId = $(this).data('id');
			
			  $.ajax({
				url: "/so_info",
				type:"POST",
				data:{
				  so_id:SOId,
				  
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteSOConfirmed").value = SOId;

					/*Set Details*/		
					document.getElementById("delete_so_so_number").innerHTML = response[0].so_number;
					document.getElementById("delete_so_client_name").innerHTML = response[0].client_name;
					document.getElementById("delete_so_order_date").innerHTML = response[0].order_date;	
					document.getElementById("delete_so_order_time").innerHTML = response[0].order_time;					
					document.getElementById("delete_so_plate_no").innerHTML = response[0].plate_no;
					document.getElementById("delete_so_drivers_name").innerHTML = response[0].drivers_name;
					
					/*Load Receivable Payment Table*/
					LoadSOProductList(SOId);
					
					$('#DeleteSOModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });


	function LoadSOProductList(SOId) {		
		
		$("#so_product_list_data_delete tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#so_product_list_data_delete');


			  $.ajax({
				url: "{{ route('GetSoProduct') }}",
				type:"POST",
				data:{
				  so_id:SOId,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var billing_id = response[i].billing_id;						
							var product_price = response[i].product_price.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var product_name = response[i].product_name;
							var order_quantity = response[i].order_quantity.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#so_product_list_data_delete tr:last').after("<tr>"+
							"<td align='center'>" + (i+1) + "</td>" +
							"<td class='product_td' align='left'>"+product_name+"</td>"+
							"<td class='calibration_td' align='right'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='right'>"+product_price+"</td>"+
							"<td class='manual_price_td' align='right'>"+order_total_amount+"</td>"+
							"</tr>");				
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


	  <!--Site Confirmed For Deletion-->
	  $('body').on('click','#deleteSOConfirmed',function(){
			
			event.preventDefault();

			let SOId = document.getElementById("deleteSOConfirmed").value;
			
			  $.ajax({
				url: "/delete_so_confirmed",
				type:"POST",
				data:{
				  so_id:SOId,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("SO Information Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getSOBillingTransactionList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });		
	  });	 
	</script>