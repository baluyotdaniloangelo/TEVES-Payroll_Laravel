    <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
 <script type="text/javascript">
	<!--Load Table-->
	$(function () {

		var BillingListTable = $('#getBillingTransactionList').DataTable({
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
			scrollCollapse: true,
			scrollY: '500px',
			ajax: "{{ route('getBillingTransactionList') }}",
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'order_date', className: "text-center"},
					{data: 'order_time',orderable: false, className: "text-center"},
					{data: 'control_number', className: "text-left",orderable: false,},  				
					{data: 'drivers_name',orderable: false,},     
					{data: 'order_po_number',orderable: false,},     
					{data: 'plate_no',orderable: false,},     
					{data: 'product_name',orderable: false,}, 
					{data: 'product_price', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) }, 					
					{data: 'quantity_measurement', name: 'quantity_measurement', orderable: true, searchable: true},
					{data: "order_total_amount", render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			order: [[ 1, "desc" ]],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1] },
					{ type: 'numeric-comma', targets: [8,9] }
			]
		});
				
		autoAdjustColumns(BillingListTable);

				 /*Adjust Table Column*/
				 function autoAdjustColumns(table) {
					 var container = table.table().container();
					 var resizeObserver = new ResizeObserver(function () {
						 table.columns.adjust();
					 });
					 resizeObserver.observe(container);
				 }	
				 
		$('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
		
		});				
				
	});

	$('body').on('click','#generate_billed',function(){
		
			event.preventDefault();
			//let gatewayID = $(this).data('id');
			let client_idx_billed 			= $('#client_name_billed option[value="' + $('#client_id_billed').val() + '"]').attr('data-id');
			let start_date_billed 			= $("input[name=start_date_billed]").val();
			let end_date_billed 			= $("input[name=end_date_billed]").val();
			
			  $.ajax({
				url: "{{ route('getBillingTransactionList_Billed') }}",
				type:"GET",
				data:{
				  client_idx_billed:client_idx_billed,
				  start_date_billed:start_date_billed,
				  end_date_billed:end_date_billed,
				  _token: "{{ csrf_token() }}"
				}
			 }).done(function (result) {
				 
					BillingListTable_billed.clear().draw();
					BillingListTable_billed.rows.add(result.data).draw();
					
					$('#BilledModal').modal('toggle');		
            })	
	  });

		/*Load Billed List*/	
			let BillingListTable_billed = $('#BillingListTable_billed').DataTable( {
				"language": {
						"lengthMenu":'<select class="form-select form-control form-control-sm">'+
			             '<option value="10">10</option>'+
			             '<option value="20">20</option>'+
			             '<option value="30">30</option>'+
			             '<option value="40">40</option>'+
			             '<option value="50">50</option>'+
			             '<option value="-1">All</option>'+
			             '</select> '
			    }, 
				//processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: true,
				scrollCollapse: true,
				scrollY: '500px',
				paging: true,
				searching: true,
				info: true,
				data: [],
				"columns": [
					// {data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					// {data: 'order_date', className: "text-center"},
					// {data: 'order_time', orderable: false},
					// {data: 'control_number'},  					
					// {data: 'drivers_name'},     
					// {data: 'order_po_number'},     
					// {data: 'plate_no'},     
					// {data: 'product_name'}, 
					// {data: 'product_price', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) }, 					
					// {data: 'quantity_measurement', name: 'quantity_measurement', orderable: true, searchable: true},
					// {data: "order_total_amount", render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					// {data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
					
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'order_date', className: "text-center"},
					{data: 'order_time',orderable: false, className: "text-center"},
					{data: 'control_number', className: "text-left",orderable: false,},  				
					{data: 'drivers_name',orderable: false,},     
					{data: 'order_po_number',orderable: false,},     
					{data: 'plate_no',orderable: false,},     
					{data: 'product_name',orderable: false,}, 
					{data: 'product_price', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) }, 					
					{data: 'quantity_measurement', name: 'quantity_measurement', orderable: true, searchable: true},
					{data: "order_total_amount", render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
				]
			} 
			);
			
			autoAdjustColumns(BillingListTable_billed);

				 /*Adjust Table Column*/
				 function autoAdjustColumns(table) {
					 var container = table.table().container();
					 var resizeObserver = new ResizeObserver(function () {
						 table.columns.adjust();
					 });
					 resizeObserver.observe(container);
				 }	

	
	function TotalAmount(){
		
		let product_price 			= $("#product_name option[value='" + $('#product_idx').val() + "']").attr('data-price');
		let product_manual_price 	= $("#product_manual_price").val();
		let order_quantity 			= $("input[name=order_quantity]").val();
		
		if(order_quantity!=0 || order_quantity!=''){
			if(product_manual_price!='' && product_manual_price!=0){
				var total_amount = product_manual_price * order_quantity;
				$('#TotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}else{
				var total_amount = product_price * order_quantity;
				$('#TotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}
		}
		
	}
	
	function UpdateTotalAmount(){
		
		let product_price 			= $("#update_product_name option[value='" + $('#update_product_idx').val() + "']").attr('data-price');
		let product_manual_price 	= $("#update_product_manual_price").val();
		let order_quantity 			= $("input[name=update_order_quantity]").val();
		
		if(order_quantity!=0 || order_quantity!=''){
			if(product_manual_price!='' && product_manual_price!=0){
				var total_amount = product_manual_price * order_quantity;
				$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}else{
				var total_amount = product_price * order_quantity;
				$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}
		}
		
	}	
	
	<!--Save New Billing-->
	$("#save-billing-transaction").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#order_dateError').text('');
					$('#order_timeError').text('');
					$('#order_po_numberError').text('');				  
					$('#client_idxError').text('');
					$('#plate_noError').text('');
					$('#drivers_nameError').text('');
					$('#product_idxError').text('');
					$('#product_manual_priceError').text('');
					$('#order_quantityError').text('');

			document.getElementById('BillingformNew').className = "g-3 needs-validation was-validated";

			let order_date 				= $("input[name=order_date]").val();
			let order_time 				= $("input[name=order_time]").val();
			let order_po_number 		= $("input[name=order_po_number]").val();
			
			let client_idx 				= $('#client_name option[value="' + $('#client_idx').val() + '"]').attr('data-id')
			let plate_no 				= $("input[name=plate_no]").val();
			let drivers_name 			= $("input[name=drivers_name]").val();
			let product_idx 			= $("#product_name option[value='" + $('#product_idx').val() + "']").attr('data-id');
			let product_manual_price 	= $("#product_manual_price").val();
			let order_quantity 			= $("input[name=order_quantity]").val();

			/*Client and Product Name*/
			let client_name 					= $("input[name=client_name]").val();
			let product_name 					= $("input[name=product_name]").val();
			
			  $.ajax({
				url: "/create_bill_post",
				type:"POST",
				data:{
				  order_date:order_date,
				  order_time:order_time,
				  order_po_number:order_po_number,
				  client_idx:client_idx,
				  plate_no:plate_no,
				  drivers_name:drivers_name,
				  product_idx:product_idx,
				  product_manual_price:product_manual_price,
				  order_quantity:order_quantity,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					let so_error = response.so_error;
					
					if(so_error == true){
							
						$('#switch_notice_off').show();
						$('#sw_off').html("The SO "+ order_po_number +" Number Exceeds Allowable Entry (6)");
						setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
						
						$('#order_po_numberError').text("The SO "+ order_po_number +" Number Exceeds Allowable Entry (6)");
						document.getElementById('order_po_numberError').className = "invalid-feedback";
						
						$('#order_po_number').val("");
						
					}else{
					
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						
						$('#order_dateError').text('');					
						$('#order_timeError').text('');
						$('#order_po_numberError').text('');
						document.getElementById('order_po_numberError').className = "valid-feedback";
						
						$('#client_idxError').text('');
						
						$('#plate_noError').text('');
						$('#drivers_nameError').text('');
						$('#product_idxError').text('');
						$('#product_manual_priceError').text('');
						$('#order_quantityError').text('');
						
						/*Clear Form*/
						//$('#order_po_number').val("");
						$('#client_idx').val("");
						$('#plate_no').val("");
						$('#drivers_name').val("");
						$('#product_idx').val("");
						$('#product_manual_price').val("");
						$('#order_quantity').val("");
						
						$('#TotalAmount').html('0.00');
						}
						/*
						If you are using server side datatable, then you can use ajax.reload() 
						function to reload the datatable and pass the true or false as a parameter for refresh paging.
						*/
						
						var table = $("#getBillingTransactionList").DataTable();
						table.ajax.reload(null, false);
						
				  }
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("save-billing-transaction").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("save-billing-transaction").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();
				
				},
				error: function(error) {
					
				 console.log(error);	
				    
				  $('#order_dateError').text(error.responseJSON.errors.order_date);
				  document.getElementById('order_dateError').className = "invalid-feedback";
				  			  
				  $('#order_timeError').text(error.responseJSON.errors.order_time);
				  document.getElementById('order_timeError').className = "invalid-feedback";		

				  $('#order_po_numberError').text(error.responseJSON.errors.order_po_number);
				  document.getElementById('order_po_numberError').className = "invalid-feedback";		
				  
					if(error.responseJSON.errors.client_idx=='Client is Required'){
							
							if(client_name==''){
								$('#client_idxError').html(error.responseJSON.errors.client_idx);
							}else{
								$('#client_idxError').html("Incorrect Client Name <b>" + client_name + "</b>");
							}
						
							document.getElementById("client_idx").value = "";
							document.getElementById('client_idxError').className = "invalid-feedback";
					}			  
				  				  
				  $('#plate_noError').text(error.responseJSON.errors.plate_no);
				  document.getElementById('plate_noError').className = "invalid-feedback";				
				 
				  $('#drivers_nameError').text(error.responseJSON.errors.drivers_name);
				  document.getElementById('drivers_nameError').className = "invalid-feedback";				
				   
					if(error.responseJSON.errors.product_idx=='Product is Required'){
							
							if(product_name==''){
								$('#product_idxError').html(error.responseJSON.errors.product_idx);
							}else{
								$('#product_idxError').html("Incorrect Product Name <b>" + product_name + "</b>");
							}
							
							document.getElementById("product_idx").value = "";
							document.getElementById('product_idxError').className = "invalid-feedback";
					
					}			
					
				  $('#order_quantityError').text(error.responseJSON.errors.order_quantity);
				  document.getElementById('order_quantityError').className = "invalid-feedback";					
								
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input" + "");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });

	<!--Select Bill For Update-->
	$('body').on('click','#editBill',function(){
			
			event.preventDefault();
			let billID = $(this).data('id');
			
			  $.ajax({
				url: "/bill_info",
				type:"POST",
				data:{
				  billID:billID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-billing-transaction").value = billID;
					
					/*Load Selection Product*/
					let branch_idx = response[0].branch_id;
					//alert(branch_idx);
					LoadProductList(branch_idx);
					
					/*Set Details*/
					document.getElementById("update_order_date").value = response[0].order_date;
					document.getElementById("update_order_time").value = response[0].order_time;
					document.getElementById("update_order_po_number").value = response[0].order_po_number;
					document.getElementById("update_client_idx").value = response[0].client_name;
					
					document.getElementById("update_plate_no").value = response[0].plate_no;
					document.getElementById("update_product_idx").value = response[0].product_name;
					document.getElementById("update_product_manual_price").value = response[0].product_price;
					document.getElementById("update_drivers_name").value = response[0].drivers_name;
					document.getElementById("update_order_quantity").value = response[0].order_quantity;
					
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					
					$('#UpdateBillingModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	function LoadProductList(branch_idx) {		
	
		$("#update_product_name span").remove();
		$('<span style="display: none;"></span>').appendTo('#update_product_name');


			  $.ajax({
				url: "{{ route('ProductListPricingPerBranch') }}",
				type:"POST",
				data:{
				  branch_idx:branch_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){
						
							var product_id = response[i].product_id;						
							var product_price = response[i].product_price.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var product_name = response[i].product_name;
	
							$('#update_product_name span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
							"<option label='&#8369; "+product_price+" | "+product_name+"' data-id='"+product_id+"' value='"+product_name+"' data-price='"+product_price+"' >" +
							"</span>");	
							
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

	$("#update-billing-transaction").click(function(event){			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#order_dateError').text('');
					$('#order_timeError').text('');
					$('#order_po_numberError').text('');				  
					$('#client_idxError').text('');
					$('#plate_noError').text('');
					$('#drivers_nameError').text('');
					$('#product_idxError').text('');
					$('#update_product_manual_priceError').text('');
					$('#order_quantityError').text('');

			document.getElementById('BillingformEdit').className = "g-3 needs-validation was-validated";
			
			let billID 							= document.getElementById("update-billing-transaction").value;
			let order_date 						= $("input[name=update_order_date]").val();
			let order_time 						= $("input[name=update_order_time]").val();
			let order_po_number 				= $("input[name=update_order_po_number]").val();	

			let client_idx 						= $('#update_client_name option[value="' + $('#update_client_idx').val() + '"]').attr('data-id');
			let plate_no 						= $("input[name=update_plate_no]").val();
			let drivers_name 					= $("input[name=update_drivers_name]").val();
			let product_idx 					= $("#update_product_name option[value='" + $('#update_product_idx').val() + "']").attr('data-id');
			let update_product_manual_price 	= $("#update_product_manual_price").val();
			let order_quantity 					= $("input[name=update_order_quantity]").val();
			
			/*Client and Product Name*/
			let client_name 					= $("input[name=update_client_name]").val();
			let product_name 					= $("input[name=update_product_name]").val();
			
			$.ajax({
				url: "/update_bill_post",
				type:"POST",
				data:{
				  billID:billID,
				  order_date:order_date,
				  order_time:order_time,
				  order_po_number:order_po_number,
				  client_idx:client_idx,
				  plate_no:plate_no,
				  drivers_name:drivers_name,
				  product_idx:product_idx,
				  product_manual_price:update_product_manual_price,
				  order_quantity:order_quantity,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					let so_error = response.so_error;
					
					if(so_error == true){
							
						$('#switch_notice_off').show();
						$('#sw_off').html("The SO "+ order_po_number +" Number Exceeds Allowable Entry (6)");
						setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
						
						$('#update_order_po_numberError').text("The SO "+ order_po_number +" Number Exceeds Allowable Entry (6)");
						document.getElementById('update_order_po_numberError').className = "invalid-feedback";
						
						$('#order_po_number').val("");
						
					}else{
						
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						
						$('#update_order_dateError').text('');					
						$('#update_order_timeError').text('');
						//$('#update_order_po_numberError').text('');
						$('#update_client_idxError').text('');
						
						$('#update_plate_noError').text('');
						$('#update_drivers_nameError').text('');
						$('#update_product_idxError').text('');
						$('#update_product_manual_priceError').text('');
						$('#update_order_quantityError').text('');
					  
						/*
						If you are using server side datatable, then you can use ajax.reload() 
						function to reload the datatable and pass the true or false as a parameter for refresh paging.
						*/
						
						var table = $("#getBillingTransactionList").DataTable();
						table.ajax.reload(null, false);
					}
				  }
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("update-billing-transaction").disabled = true;
					/*Show Status*/
					$('#update_loading_data').show();
					
				},
				complete: function(){
					
					/*Enable Submit Button*/
					document.getElementById("update-billing-transaction").disabled = false;
					/*Hide Status*/
					$('#update_loading_data').hide();
					
				},
				error: function(error) {
				 console.log(error);	
				 
				  $('#update_order_dateError').text(error.responseJSON.errors.order_date);
				  document.getElementById('update_order_dateError').className = "invalid-feedback";
				  			  
				  $('#update_order_timeError').text(error.responseJSON.errors.order_time);
				  document.getElementById('update_order_timeError').className = "invalid-feedback";		

				  $('#update_order_po_numberError').text(error.responseJSON.errors.order_po_number);
				  document.getElementById('update_order_po_numberError').className = "invalid-feedback";		
				
					if(error.responseJSON.errors.client_idx=='Client is Required'){
						
							if(client_name==''){
								$('#update_client_idxError').html(error.responseJSON.errors.client_idx);
							}else{
								$('#update_client_idxError').html("Incorrect Client Name <b>" + client_name + "</b>");
							}
						
							document.getElementById("update_client_idx").value = "";
							document.getElementById('update_client_idxError').className = "invalid-feedback";
							
					}
					
				  $('#update_plate_noError').text(error.responseJSON.errors.plate_no);
				  document.getElementById('update_plate_noError').className = "invalid-feedback";				
				 
				  $('#update_drivers_nameError').text(error.responseJSON.errors.drivers_name);
				  document.getElementById('update_drivers_nameError').className = "invalid-feedback";				
				  
				  $('#update_product_idxError').text(error.responseJSON.errors.product_idx);
				  document.getElementById('update_product_idxError').className = "invalid-feedback";

			      	if(error.responseJSON.errors.product_idx=='Product is Required'){
						
							if(product_name==''){
								$('#update_product_idxError').html(error.responseJSON.errors.product_idx);
							}else{
								$('#update_product_idxError').html("Incorrect Product Name <b>" + product_name + "</b>");
							}
							
							document.getElementById("update_product_idx").value = "";
							document.getElementById('update_product_idxError').className = "invalid-feedback";
			
					}

 				  $('#update_order_quantityError').text(error.responseJSON.errors.order_quantity);
				  document.getElementById('update_order_quantityError').className = "invalid-feedback";
		
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });
	  
	<!--Bill Deletion Confirmation-->
	$('body').on('click','#deleteBill',function(){
			
			event.preventDefault();
			let billID = $(this).data('id');
			
			  $.ajax({
				url: "/bill_info",
				type:"POST",
				data:{
				  billID:billID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteBillConfirmed").value = billID;
					
					/*Set Details*/
					$('#bill_delete_order_date').text(response[0].order_date);
					$('#bill_delete_order_time').text(response[0].order_time);
					$('#bill_delete_order_po_number').text(response[0].order_po_number);
					$('#bill_delete_client_name').text(response[0].client_name);
					$('#bill_delete_plate_no').text(response[0].plate_no);
					$('#bill_delete_product_name').text(response[0].product_name);
					$('#bill_delete_drivers_name').text(response[0].drivers_name);
					$('#bill_delete_order_quantity').text(response[0].order_quantity);					
					$('#bill_delete_order_total_amount').text(response[0].order_total_amount);

					$('#BillDeleteModal').modal('toggle');									  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--Site Confirmed For Deletion-->
	  $('body').on('click','#deleteBillConfirmed',function(){
			
			event.preventDefault();

			let billID = document.getElementById("deleteBillConfirmed").value;
			
			  $.ajax({
				url: "/delete_bill_confirmed",
				type:"POST",
				data:{
				  billID:billID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Bill Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getBillingTransactionList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });		
	  });
  
  	<!--Select Bill For Update-->
	$('body').on('click','#viewBill',function(){
			
			event.preventDefault();
			let billID = $(this).data('id');
			
			  $.ajax({
				url: "/bill_info",
				type:"POST",
				data:{
				  billID:billID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					/*Set Details*/
					$("#view_order_date").html(response[0].order_date);
					$("#view_order_time").html(response[0].order_time);
					$("#view_order_po_number").html(response[0].order_po_number);
					$("#view_client_name").html(response[0].client_name);
					
					$("#view_plate_no").html(response[0].plate_no);
					$("#view_product_name").html(response[0].product_name);
					$("#view_product_price").html(response[0].product_price);
					$("#view_drivers_name").html(response[0].drivers_name);
					$("#view_order_quantity").html(response[0].order_quantity);
					
					var total_amount = response[0].order_total_amount;
					$('#ViewTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					
					$('#ViewBillingModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });
	</script>