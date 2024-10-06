   <script type="text/javascript">		 
	<!--Update Sales Order-->
	//ClientInfo();
	LoadProduct();
	LoadPayment();
	LoadReceivables();
	
	function ResetPaymentForm(){
		/*Reset Form*/
		document.getElementById("AddPayment").reset();
		/*Hide Image Reference Div*/
		$("#image_payment_div").hide();
		/*Reset Payment Id*/
		document.getElementById("receivable_payment_id").value = 0;
	}
	
	$("#update-sales-order").click(function(event){

			event.preventDefault();
			
			/*Reset Warnings*/
			$('#client_idxError').text('');

			document.getElementById('UpdateSalesOrderformUpdate').className = "g-3 needs-validation was-validated";

			let sales_order_id 				= {{ $SalesOrderID }};
			let company_header 				= $("#company_header").val();
			let sales_order_payment_type 	= $("#sales_order_payment_type").val();
			let client_idx 					= ($("#client_name option[value='" + $('#client_id').val() + "']").attr('data-id'));	
			let sales_order_date 			= $("input[name=sales_order_date]").val();
			let delivered_to 				= $("input[name=delivered_to]").val();
			let delivered_to_address 		= $("input[name=delivered_to_address]").val();
			let dr_number 					= $("input[name=dr_number]").val();
			
			let sales_order_or_number 					= $("input[name=sales_order_or_number]").val();
			let sales_order_po_number 					= $("input[name=sales_order_po_number]").val();
			let sales_order_charge_invoice 				= $("input[name=sales_order_charge_invoice]").val();
			let sales_order_collection_receipt 			= $("input[name=sales_order_collection_receipt]").val();
			
			let payment_term 				= $("input[name=payment_term]").val();
			let delivery_method 			= $("input[name=delivery_method]").val();
			let hauler 						= $("input[name=hauler]").val();
			let required_date 				= $("input[name=required_date]").val();
			let instructions 				= $("#instructions").val();
			let note 						= $("#note").val();
			let sales_order_net_percentage 	= $("input[name=sales_order_net_percentage]").val();
			let sales_order_withholding_tax = $("input[name=sales_order_withholding_tax]").val();
			
			  $.ajax({
				url: "/update_sales_order_post",
				type:"POST",
				data:{
				  sales_order_id:sales_order_id,
				  company_header:company_header,
				  sales_order_payment_type:sales_order_payment_type,
				  client_idx:client_idx,
				  sales_order_date:sales_order_date,
				  delivered_to:delivered_to,
				  delivered_to_address:delivered_to_address,
				  dr_number:dr_number,
				  
				  sales_order_or_number:sales_order_or_number,
				  sales_order_po_number:sales_order_po_number,
				  sales_order_charge_invoice:sales_order_charge_invoice,
				  sales_order_collection_receipt:sales_order_collection_receipt,
				  
				  payment_term:payment_term,
				  delivery_method:delivery_method,
				  hauler:hauler,
				  required_date:required_date,
				  instructions:instructions,
				  note:note,
				  sales_order_net_percentage:sales_order_net_percentage,
				  sales_order_withholding_tax:sales_order_withholding_tax,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					LoadProduct(sales_order_id);
					LoadProductList(company_header);
					document.getElementById("AddSalesOrderProductBTN").disabled = false;
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#update_order_dateError').text('');					
					$('#update_order_timeError').text('');
					$('#update_order_po_numberError').text('');
					$('#update_client_idxError').text('');
					
					$('#update_plate_noError').text('');
					$('#update_drivers_nameError').text('');
					
				  }
				},
				beforeSend:function()
				{
					$('#upload_loading_data').show();
				},
				complete: function(){
					
					$('#upload_loading_data').hide();
					
					/*Close Form*/
					$('#UpdateSalesOrderModal').modal('toggle');
					
				},
				error: function(error) {
					
				 console.log(error);	
				 
				  $('#client_idxError').text(error.responseJSON.errors.client_idx);
				  document.getElementById('client_idxError').className = "invalid-feedback";
				  
				  $('#product_idxError').html(error.responseJSON.errors.product_idx);
								
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });	  

	function UpdateBranch(){ 
	
		$('#switch_notice_off').show();
		$('#sw_off').html("You selected a new branch, to confirm changes click the update button");
		setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },2000);
		
		/*Disable the Add Product Button Until Changes not Save*/
		document.getElementById("AddSalesOrderProductBTN").disabled = true;
		
	}

	<!--Select Receivable For Update-->	
	function LoadReceivables(){ 
			
			  $.ajax({
				url: "/receivable_info",
				type:"POST",
				data:{
				  receivable_id: {{ @$receivables_details['receivable_id'] }},
				  
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					
					var receivable_amount = response[0].receivable_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
					
					document.getElementById("receivable_control_number_info").innerHTML = response[0].control_number;	
					document.getElementById("receivable_amount_info").innerHTML = "<span >&#8369; "+receivable_amount+"</span>";
					
					document.getElementById("receivable_billing_date_SO").value = response[0].billing_date;
					document.getElementById("receivable_payment_term_SO").value = response[0].payment_term;
					document.getElementById("receivable_description_SO").textContent = response[0].receivable_description;					
							
					
					$('#UpdateReceivablesModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	}

	function LoadProductList(branch_id) {		
	
		$("#product_list span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_list');

			  $.ajax({
				url: "{{ route('ProductListPricingPerBranch') }}",
				type:"POST",
				data:{
				  branch_idx:branch_id,
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
	
							$('#product_list span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
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
	
	$("#save-product").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					
					$('#product_idxError').text('');
					$('#product_manual_priceError').text('');
					$('#order_quantityError').text('');

			document.getElementById('AddProduct').className = "g-3 needs-validation was-validated";
			
			let company_header 			= $("#company_header").val();
		
			let product_idx 			= $("#product_list option[value='" + $('#product_idx').val() + "']").attr('data-id');

			let product_manual_price 	= $("#product_manual_price").val();
			let order_quantity 			= $("input[name=order_quantity]").val();

			/*Product Name*/
			let product_name 			= $("input[name=product_name]").val();
			
			let sales_order_id 			= {{ $SalesOrderID }};
			let receivable_id 			= {{ @$receivables_details['receivable_id'] }};
			
			let sales_order_net_percentage 	= $("input[name=sales_order_net_percentage]").val();
			let sales_order_withholding_tax = $("input[name=sales_order_withholding_tax]").val();
			
			  $.ajax({
				url: "{{ route('SalesOrderComponentCompose') }}",
				type:"POST",
				data:{
				  sales_order_component_id:0,
				  branch_idx:company_header,
				  sales_order_id:sales_order_id,
				  receivable_id:receivable_id,
				  product_idx:product_idx, 
				  item_description:product_name,
				  product_manual_price:product_manual_price,
				  order_quantity:order_quantity,
				  sales_order_net_percentage:sales_order_net_percentage,
				  sales_order_withholding_tax:sales_order_withholding_tax,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);

						$('#product_idxError').text('');
						$('#product_manual_priceError').text('');
						$('#order_quantityError').text('');
						
						/*Clear Form*/
						$('#product_idx').val("");
						$('#product_manual_price').val("");
						$('#order_quantity').val("");
						
						$('#TotalAmount').html('0.00');
						}
						
						document.getElementById("save-product").value = 0;
						
						/*Reload Table*/
						LoadProduct();
						
				  
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("save-product").disabled = true;
					/*Show Status*/
					$('#loading_data_add_product').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("save-product").disabled = false;
					/*Hide Status*/
					$('#loading_data_add_product').hide();
				
				},
				error: function(error) {
					
				 console.log(error);	
				      
					if(error.responseJSON.errors.product_idx=='Item Description or Product is Required'){
							
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

	function LoadProduct() {
		
		$("#table_sales_order_product_body_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_sales_order_product_body_data');
		
		$("#product_list_delivery span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_list_delivery');
		
		let receivable_id = {{ @$receivables_details['receivable_id'] }};

			  $.ajax({
				url: "/get_sales_order_product_list",
				type:"POST",
				data:{
				  sales_order_id:{{ $SalesOrderID }},
				  receivable_idx:receivable_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  
				  console.log(response['productlist']);
				  var len = response['productlist'].length;
				  
					  if(response['productlist']!='') {			  
							
							if(response['paymentcount']!=0){
							
								$(".action_column_class").hide();
								document.getElementById("AddSalesOrderProductBTN").disabled = true;
								
								for(var i=0; i<len; i++){
							
								var id = response['productlist'][i].sales_order_component_id;
								var product_idx = response['productlist'][i].product_idx;
								var product_name = response['productlist'][i].product_name;
								var product_unit_measurement = response['productlist'][i].product_unit_measurement;
								var product_price = response['productlist'][i].product_price;
								var order_quantity = response['productlist'][i].order_quantity;
								
								var order_total_amount = response['productlist'][i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
								
								$('#table_sales_order_product_body_data tr:last').after("<tr>"+
								"<td align='center'>" + (i+1) + "</td>" +
								"<td class='product_td' align='left'>"+product_name+"</td>"+
								"<td class='manual_price_td' align='right'><span >&#8369; "+product_price+"</span></td>"+
								"<td class='calibration_td' align='right'>"+order_quantity+" "+product_unit_measurement+"</td>"+
								"<td class='manual_price_td' align='right'><span >&#8369; "+order_total_amount+"</span></td>"+
								"</tr>");
								
								$('#product_list_delivery span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
								"<option label='Product:"+product_name + " | Quantity:"+order_quantity+"' data-id='"+id+"' product-id='"+product_idx+"' value='"+product_name+"'>" +
								"</span>");
								
								}
								
							}else{
								
								$(".action_column_class").show();
								document.getElementById("AddSalesOrderProductBTN").disabled = false;
								
								for(var i=0; i<len; i++){
							
								var id = response['productlist'][i].sales_order_component_id;
								var product_idx = response['productlist'][i].product_idx;
								var product_name = response['productlist'][i].product_name;
								var product_unit_measurement = response['productlist'][i].product_unit_measurement;
								var product_price = response['productlist'][i].product_price;
								var order_quantity = response['productlist'][i].order_quantity;
								
								var order_total_amount = response['productlist'][i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
								
								var created_at = response['productlist'][i].created_at;
								
								const oneDay = 24 * 60 * 60 * 1000; 	/*hours*minutes*seconds*milliseconds*/
								const firstDate = new Date(created_at);
								const secondDate = new Date();			/*Now*/

								const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));						
								
									<?php
									if(Session::get('UserType')=="Admin"){
									?>
										action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderProduct_Edit' data-id='"+id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteSalesOrderComponentProduct'  data-id='"+id+"'></a>";		
									<?php
									}
									else{
									?>
										if(diffDays>=3){
											action_controls = "ssss";
											}else{
											action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderProduct_Edit' data-id='"+id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteSalesOrderComponentProduct'  data-id='"+id+"'></a>";		
										
										}
									<?php									
									}
									?>
								
								$('#table_sales_order_product_body_data tr:last').after("<tr>"+
								"<td class='action_column_class'><div align='center' class='action_table_menu_Product' >"+action_controls+"</div></td>"+
								"<td align='center'>" + (i+1) + "</td>" +
								"<td class='product_td' align='left'>"+product_name+"</td>"+
								"<td class='manual_price_td' align='right'><span >&#8369; "+product_price+"</span></td>"+
								"<td class='calibration_td' align='right'>"+order_quantity+" "+product_unit_measurement+"</td>"+
								"<td class='manual_price_td' align='right'><span >&#8369; "+order_total_amount+"</span></td>"+
								"</tr>");
								
								$('#product_list_delivery span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
								"<option label='Product:"+product_name + " | Quantity:"+order_quantity+"' data-id='"+id+"' product-id='"+product_idx+"' value='"+product_name+"'>" +
								"</span>");
								
								}
								
							}
					  }
				  else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	  } 
	  
	<!--Select Bill For Update-->
	$('body').on('click','#SalesOrderProduct_Edit',function(){
			
			event.preventDefault();
			let sales_order_component_id = $(this).data('id');
			
			  $.ajax({
				url: "/sales_order_component_info",
				type:"POST",
				data:{
				  sales_order_component_id:sales_order_component_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-product").value = sales_order_component_id;
					
					/*Set Details*/
					document.getElementById("edit_product_idx").value = response[0].product_name;
					document.getElementById("edit_product_manual_price").value = response[0].product_price;
					document.getElementById("edit_order_quantity").value = response[0].order_quantity;
					
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					
					$('#EditProductModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	  

	<!--Update So Product-->
	$("#update-product").click(function(event){
		
			event.preventDefault();
			
			/*Reset Warnings*/
					
			$('#product_idxError').text('');
			$('#product_manual_priceError').text('');
			$('#order_quantityError').text('');

			document.getElementById('EditSalesOrderComponentProduct').className = "g-3 needs-validation was-validated";
			
			let company_header 			= $("#company_header").val();
			
			let sales_order_id 			= {{ $SalesOrderID }};
			let receivable_id 			= {{ @$receivables_details['receivable_id'] }};
			
			let sales_order_component_id 	= document.getElementById("update-product").value;
			let product_idx 				= $("#product_list option[value='" + $('#edit_product_idx').val() + "']").attr('data-id');
			let product_manual_price 		= $("#edit_product_manual_price").val();
			let order_quantity 				= $("input[name=edit_order_quantity]").val();

			/*Product Name*/
			let product_name 				= $("input[name=edit_product_name]").val();
			
			let sales_order_net_percentage 	= $("input[name=sales_order_net_percentage]").val();
			let sales_order_withholding_tax = $("input[name=sales_order_withholding_tax]").val();

			  $.ajax({
				url: "{{ route('SalesOrderComponentCompose') }}",
				type:"POST",
				data:{
				  branch_idx:company_header,	
				  sales_order_id:sales_order_id,
				  receivable_id:receivable_id,
				  sales_order_component_id:sales_order_component_id,
				  product_idx:product_idx,
				  item_description:product_name,
				  product_manual_price:product_manual_price,
				  order_quantity:order_quantity,
				  sales_order_net_percentage:sales_order_net_percentage,
				  sales_order_withholding_tax:sales_order_withholding_tax,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);

						$('#edit_product_idxError').text('');
						$('#edit_product_manual_priceError').text('');
						$('#edit_order_quantityError').text('');
						
						/*Clear Form*/
						$('#edit_product_idx').val("");
						$('#edit_product_manual_price').val("");
						$('#edit_order_quantity').val("");
						$('#EditProductModal').modal('toggle');	
						
						}
						
						document.getElementById("update-product").value = 0;
						
						/*Reload Table*/
						LoadProduct();
						LoadReceivables();
									  
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("update-product").disabled = true;
					/*Show Status*/
					$('#loading_data_update_product').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("update-product").disabled = false;
					/*Hide Status*/
					$('#loading_data_update_product').hide();
				
				},
				error: function(error) {
					
				 console.log(error);	
				      
					if(error.responseJSON.errors.product_idx=='Product is Required'){
							
							if(product_name==''){
								$('#edit_product_idxError').html(error.responseJSON.errors.product_idx);
							}else{
								$('#edit_product_idxError').html("Incorrect Product Name <b>" + product_name + "</b>");
							}
							
							document.getElementById("edit_product_idx").value = "";
							document.getElementById('edit_product_idxError').className = "invalid-feedback";
					
					}			
					
				  $('#edit_order_quantityError').text(error.responseJSON.errors.order_quantity);
				  document.getElementById('edit_order_quantityError').className = "invalid-feedback";					
								
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input" + "");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });		

	/*Re-print Sales Order*/
	$('body').on('click','#PrintSalesOrder',function(){	  
	  
			let salesOrderID = {{ $SalesOrderID }};
			var query = {
						sales_order_id:salesOrderID,
						_token: "{{ csrf_token() }}"
					}

			var url = "{{URL::to('generate_sales_order_pdf')}}?" + $.param(query)
			window.open(url);
	  
	});
	
	/*Saler Order Status*/
	$('body').on('click','#PrintSalesOrderDeliveyStatus',function(){	  
	  
			let salesOrderID = {{ $SalesOrderID }};
			var query = {
						sales_order_id:salesOrderID,
						_token: "{{ csrf_token() }}"
					}

			var url = "{{URL::to('generate_sales_order_delivery_status_pdf')}}?" + $.param(query)
			window.open(url);
	  
	});
	 
	/*Print Statement of Account from Receivable*/
	$('body').on('click','#PrintSOA',function(){	  
	  
			let ReceivableID = {{ @$receivables_details['receivable_id'] }};
			var query_receivable = {
								receivable_id:ReceivableID,
								_token: "{{ csrf_token() }}"
							}

					var url_receivable = "{{URL::to('generate_receivable_soa_pdf')}}?" + $.param(query_receivable)
					window.open(url_receivable);
					
	});	 

	/*Print Receivable*/
	$('body').on('click','#PrintReceivable',function(){	  
	  
			let ReceivableID = {{ @$receivables_details['receivable_id'] }};	
			var query_receivable = {
								receivable_id:ReceivableID,
								_token: "{{ csrf_token() }}"
							}

					var url_receivable = "{{URL::to('generate_receivable_pdf')}}?" + $.param(query_receivable)
					window.open(url_receivable);
	});	 
	
	function ClientInfo() {
			
			let clientID = $("#client_name option[value='" + $('#client_id').val() + "']").attr('data-id');
			
			$.ajax({
				url: "/client_info",
				type:"POST",
				data:{
				  clientID:clientID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					
					document.getElementById("sales_order_withholding_tax").value = response.default_withholding_tax_percentage;
					document.getElementById("sales_order_net_percentage").value = response.default_net_percentage;		
					document.getElementById("payment_term").value = response.default_payment_terms;		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
			 
	}	 

	<!--Bill Deletion Confirmation-->
	$('body').on('click','#deleteSalesOrderComponentProduct',function(){
			
			event.preventDefault();
			let sales_order_component_id = $(this).data('id');
			
			  $.ajax({
				url: "/sales_order_component_info",
				type:"POST",
				data:{
				  sales_order_component_id:sales_order_component_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteSalesOrderComponentConfirmed").value = sales_order_component_id;
					
					/*Set Details*/
					$('#bill_delete_product_name').text(response[0].product_name);
					$('#bill_delete_order_quantity').text(response[0].order_quantity);					
					$('#bill_delete_order_total_amount').text(response[0].order_total_amount);

					$('#SalesOrderComponentDeleteModal').modal('toggle');				
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	<!-- Confirmed For Deletion-->
	$('body').on('click','#deleteSalesOrderComponentConfirmed',function(){
			
			event.preventDefault();
			let sales_order_id 				= {{ $SalesOrderID }};
			let receivable_id 			= {{ @$receivables_details['receivable_id'] }};
			
			let sales_order_component_id 	= document.getElementById("deleteSalesOrderComponentConfirmed").value;
			let sales_order_net_percentage 	= $("input[name=sales_order_net_percentage]").val();
			let sales_order_withholding_tax = $("input[name=sales_order_withholding_tax]").val();
			
			  $.ajax({
				url: "{{ route('SalesOrderDeleteComponent') }}",
				type:"POST",
				data:{
					sales_order_id:sales_order_id,
					receivable_id:receivable_id,
					sales_order_component_id:sales_order_component_id,
					sales_order_net_percentage:sales_order_net_percentage,
					sales_order_withholding_tax:sales_order_withholding_tax,
					_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Sales Order Product Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	

					/*Reload Table*/
					LoadProduct();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	});	  
	
	function TotalAmount(){
		let product_price 			= $('#product_list option[value="' + $('#product_idx').val() + '"]').attr('data-price');
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
		let product_price 			= $('#product_list option[value="' + $('#edit_product_idx').val() + '"]').attr('data-price');
		let product_manual_price 	= $("#edit_product_manual_price").val();
		let order_quantity 			= $("input[name=edit_order_quantity]").val();
		
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

	/*Add Payment and Edit With Upload Function*/
    $('#AddPayment').on('submit', function(e){

                e.preventDefault();
	 		
				$('#receivable_mode_of_paymentError').text('');
				$('#receivable_date_of_payment').text('');
				$('#receivable_referenceError').text('');
				$('#receivable_payment_amountError').text('');
			
				document.getElementById('AddPayment').className = "g-3 needs-validation was-validated";
                
				var form = this;
				
				$.ajax({
                    url:$(form).attr('action'),
                    method:$(form).attr('method'),
                    data:new FormData(form),
                    processData:false,
                    dataType:'json',
                    contentType:false,
                    beforeSend:function(){
                        $(form).find('span.error-text').text('');
                    },
                    success:function(data){
						console.log(data);
					
						$('#switch_notice_on').show();
						$('#sw_on').html(data.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);

						$('#receivable_mode_of_paymentError').text('');
						$('#receivable_date_of_paymentError').text('');
						$('#receivable_referenceError').text('');
						$('#receivable_payment_amountError').text('');
						
						let receivable_payment_id = document.getElementById("receivable_payment_id").value;
						
						/*Close Payment Modal if the item is from Receivable*/
						if(receivable_payment_id!=0){
							
							$('#AddPaymentModal').modal('toggle');	
						
						}
						
						/*Reset Form*/
						ResetPaymentForm();
						/*Reload Table*/
						LoadPayment();
						LoadProduct();
					
                    },error: function(error) {
					
						console.log(error);	
						
						let receivable_reference 	= $("#receivable_reference").val();
						if(error.responseJSON.errors.receivable_reference=="The receivable reference has already been taken."){
							  
						receivable_reference_error = "<b>"+ receivable_reference +"</b> has already been taken.";
						$('#receivable_referenceError').html(receivable_reference_error);
						document.getElementById('receivable_referenceError').className = "invalid-feedback";
					
						$('#receivable_reference').val("");
					
						$('#switch_notice_off').show();
						$('#sw_off').html("Invalid Input" + ' ' + receivable_reference_error);
						setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);		
					  
					}else {		
					
						$('#receivable_mode_of_paymentError').text(error.responseJSON.errors.receivable_mode_of_payment);
						document.getElementById('receivable_mode_of_paymentError').className = "invalid-feedback";
						
						$('#receivable_date_of_payment').text(error.responseJSON.errors.purchase_order_date_of_payment);
						document.getElementById('receivable_date_of_payment').className = "invalid-feedback";					
						
						$('#receivable_referenceError').text(error.responseJSON.errors.receivable_reference);
						document.getElementById('receivable_referenceError').className = "invalid-feedback";					
						
						$('#receivable_payment_amountError').text(error.responseJSON.errors.receivable_payment_amount);
						document.getElementById('receivable_payment_amountError').className = "invalid-feedback";					
						
						$('#switch_notice_off').show();
						$('#sw_off').html("Invalid Input" + "");
						setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			
					
					}
				}
                });
    });

    //Reset input file
    $('input[type="file"][name="payment_image_reference"]').val('');
            //Image preview
            $('input[type="file"][name="payment_image_reference"]').on('change', function(){
                var img_path = $(this)[0].value;
                var img_holder = $('.img-holder');
                var extension = img_path.substring(img_path.lastIndexOf('.')+1).toLowerCase();

                if(extension == 'jpeg' || extension == 'jpg' || extension == 'png'){
                     if(typeof(FileReader) != 'undefined'){
                          img_holder.empty();
                          var reader = new FileReader();
                          reader.onload = function(e){
                              $('<img/>',{'src':e.target.result,'class':'img-fluid','style':'max-width:400px;margin-bottom:5px;'}).appendTo(img_holder);
                          }
                          img_holder.show();
                          reader.readAsDataURL($(this)[0].files[0]);
                     }else{
                         $(img_holder).html('This browser does not support FileReader');
                     }
                }else{
                    $(img_holder).empty();
                }
            });
	
	function LoadPayment() {
				
			  let receivable_id = {{ @$receivables_details['receivable_id'] }};

			  $.ajax({
				url: "/receivable_payment_list",
				type:"POST",
				data:{
				  receivable_idx:receivable_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					 
					$("#update_table_payment_body_data tr").remove();
					$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#update_table_payment_body_data');
					
					$(".carousel-indicators").html('');
					$(".carousel-inner").html('');
					
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].receivable_payment_id;
							
							var receivable_mode_of_payment 		= response[i].receivable_mode_of_payment;
							var receivable_date_of_payment 		= response[i].receivable_date_of_payment;
							var receivable_reference			= response[i].receivable_reference;
							var image_reference 				= response[i].image_reference;
							
							var receivable_payment_amount = response[i].receivable_payment_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							
							var created_at = response[i].created_at;
								
								const oneDay = 24 * 60 * 60 * 1000; 	/*hours*minutes*seconds*milliseconds*/
								const firstDate = new Date(created_at);
								const secondDate = new Date();			/*Now*/

								const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));
									
									<?php
									if(Session::get('UserType')=="Admin"){
									?>
											action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderPayment_Edit' data-id='"+id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteSalesOrderPayment'  data-id='"+id+"'></a>";		
									<?php
									}
									else{
									?>
										if(diffDays>=1){
											action_controls = "";
											}else{
											action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderPayment_Edit' data-id='"+id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteSalesOrderPayment'  data-id='"+id+"'></a>";				
										}
									<?php									
									}
									?>
									
							if(image_reference==null){
								
								$('#update_table_payment_body_data tr:last').after("<tr>"+
								"<td align='center'>" + (i+1) + "</td>" +
								"<td><div align='center' class='action_table_menu_Product' >"+action_controls+"</div></td>"+	
								"<td class='bank_td' align='center'>"+receivable_mode_of_payment+"</td>"+
								"<td class='update_date_of_payment_td' align='center'>"+receivable_date_of_payment+"</td>"+
								"<td class='update_purchase_order_reference_no_td' align='center'>"+receivable_reference+"</td>"+
								"<td class='update_purchase_order_payment_amount_td' align='right'><span >&#8369; "+receivable_payment_amount+"</span></td>");
								
							}else{
								
								$('#update_table_payment_body_data tr:last').after("<tr>"+
								"<td align='center'>" + (i+1) + "</td>" +
								"<td><div align='center' class='action_table_menu_Product' >"+action_controls+" <a href='#' class='btn-danger btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view' id='ViewSalesOrderPayment'  data-id='"+id+"'></a></div></td>"+	
								"<td class='bank_td' align='center'>"+receivable_mode_of_payment+"</td>"+
								"<td class='update_date_of_payment_td' align='center'>"+receivable_date_of_payment+"</td>"+
								"<td class='update_purchase_order_reference_no_td' align='center'>"+receivable_reference+"</td>"+
								"<td class='update_purchase_order_payment_amount_td' align='right'><span >&#8369; "+receivable_payment_amount+"</span></td>");
							
							}	
							
								if(i==0){
									
									slide_btn_status = 'active';
									slide_current_status = 'true';
									
									carousel_item_status = 'active';
									
								}else{
									
									slide_btn_status = '';
									slide_current_status = '';
									
									carousel_item_status = ' ';
								}
							
							$('.carousel-indicators').last().append('<button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="'+i+'" class="'+slide_btn_status +'" aria-current="'+slide_current_status+'" aria-label="Slide '+i+'" style="background-color: magenta !important;"></button>');
					
							if(image_reference==null){
								/*If no Uploaded Image Found*/
								image_src = "data:image/jpg;image/png;base64,"+'iVBORw0KGgoAAAANSUhEUgAACAAAAAgACAMAAACFeSMCAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAB3RJTUUH5QIJCw8RbOjvpwAAAwBQTFRFAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAszD0iAAAAP90Uk5TAAECAwQFBgcICQoLDA0ODxAREhMUFRYXGBkaGxwdHh8gISIjJCUmJygpKissLS4vMDEyMzQ1Njc4OTo7PD0+P0BBQkNERUZHSElKS0xNTk9QUVJTVFVWV1hZWltcXV5fYGFiY2RlZmdoaWprbG1ub3BxcnN0dXZ3eHl6e3x9fn+AgYKDhIWGh4iJiouMjY6PkJGSk5SVlpeYmZqbnJ2en6ChoqOkpaanqKmqq6ytrq+wsbKztLW2t7i5uru8vb6/wMHCw8TFxsfIycrLzM3Oz9DR0tPU1dbX2Nna29zd3t/g4eLj5OXm5+jp6uvs7e7v8PHy8/T19vf4+fr7/P3+6wjZNQAAAAFiS0dE/6UH8sUAAK9SSURBVHja7N1rlKSHXd/56br1jOxwCQQMMRDjmmtP369VJWkuxkiypeCASc4unGVDchbiTXKSXcIuhOOck02y55DkQM7JLmGxjQnEWJYvfb9M99xnulqyJMuSbHwTDsQOTmzjGEeyu6p6akcYbGk0o+qZ6cvz/J/P54XPkd9NPV31+1Y9Vc+zZw8AkBC5I3+l6FEAgGzJ91/4xImSxwEAsqQwfLrZfuaEzwAAIEOKY0vNdvtqARQ8FgCQFaWJ+ef3XwEAQJb2vzrTaH/dJ08qAADIxv7Xpv58/9tXPnKPAgCATOz/5Df2v93eePJ+3wQEgIztf7vd+tCPKAAAiL7/1akX7X+73Xz0RxUAAIRWrExfs//tduORH1MAABBYYWzuJft/tQAefrMCAICw8iOL19n/dnv94R9XAAAQVG5gpdluX78AfAYAAEEdPXeD/fc9AAAI68Bqq31DjQ++SQEAQDzl+svsf7vdfOwBBQAA8fa/2X5ZzSfuc1VgAMjY/rfbradfrwAAIJL9qx33v92+8rHjCgAAwsj1vPz5/2945oTvAQBAEPn+85vb/+cLoOTxAoAICsMrzXZ70wXgMwAACKA4trj5/X++AHwPAABSrzQxdzP7rwAAIML+V2ca7ZvzidcpAABI9/7Xpm52/9tXPnKv7wEAQKr3f/Km97/d3vjw/QoAADK2/+126/EfUQAAkNb9r07d0v63281Hf1QBAEAqFSdmbnH/2+3GI29WAACQQoWxuVve/6sF8LACAID0yQ8vNtu3Yf3hH1cAAJAyuYGV29r/5wvAZwAAkDI9529z/5//HoBvAgJAquyvt9q3rfHBNykAAEiP8lbsf7vdfPwBBQAA6dn/ZntLNJ+4z30BACBj+99ubzz9QwoAANJg/9btf7t95ePHFQAAJF7u6GqrvZWeOeF7AACQcPmB81u7/88XQMnjCgBJVhheabbbW14APgMAgAQrji1u/f4/XwC+BwAAiVWamNuO/VcAAJDk/a/ONNrb4+N+DQgACd3/2tR27X/7ytP3+h4AACRy/ye3bf/b7Y0P368AACBj+99utx53ZyAASNz+V6e2df/b7eaj7g4MAMlSnJjZ5v1vtxuPvFkBAECCFMbmtn3/rxbAwwoAAJIjP7zUbO+AdQUAAImR6z+9I/uvAAAgQXou7ND+P/89AN8EBIBEKNdb7R3TeNSvAQEgCfu/uoP73243H3dFIABIwPv/ZntHtT58r/sCAEDG9r/d3njanYEAYDd1Hai32jvuysfdHRgAdk/u6Oou7P9Vz5zwPQAA2CX5gXO7s//PF0DJ4w8Au6Ewstxst3etAHwGAAC7oDi+uHv7/3wB+B4AAOy4UmUn7v/zcgVwXAEAwE7vf3V6d/e/3f64XwMCwA7vf21qt/e/feXp+3wPAAB2dP8nd33/2+2NJx5QAACQsf1//r4A7gwEADu2/9WpROz/1QL4oLsDA8DOKE7MJGT/2+3GI29WAACwAwqj84nZ/6sF8PCPKwAA2Hb5oaVmO0HWH/YZAABst1z/6UTtvwIAgB1w5ELC9v/57wH4JiAAbKvyWqudOI1Hf0QBAMA27v9qAvf/+esB3K8AAGDb9r/ebCdS68P3ui8AAGRs/9vtjY+8TgEAwDboOlhvtRPryidOKAAA2HK53ssJ3v+rnjnhewAAsMXyg+eSvf/PF0DJcQKArVQcWW6224kvAJ8BAMBW7v/4QvL3//kC8D0AANgypcpso50GzxxXAACwVftfnU7H/rfbH3u9AgCArdn/2lRa9r995en7fA8AALZk/ydTs//tduuJBxQAAGRs/9vt5mNvUgAAcLv7X5lO1f5fLYAP/pgCAIDbUpyYSdn+t9uNR96sAADgNhRG51O3/1cL4OEfVwAAcMvyQ6ea7RRaf9hnAABwq7r6zqRy/58vAN8DAIBbdPhiSvf/+e8B/KgCAIBbUa632qnVePRHFAAA3ML+r6Z4/9vt5ofud1VgALj59//Ndqq1nrxHAQBAxva/3d74yEkFAAA3oetQvdVOvSufPKEAAGDTcr2XAuz/Vc+c8E1AANikwuDZGPv/fAGUHE8A2IziyHKz3Q5TAD4DAIDN7P/4Qpz9b7c/8VccUgDoqFSZbQTa/9bFHscUADruf3U60v43z/TnHFQA6LT/talQ+780nHdQAaDj/k+G2v/5MRcCAICMvf9vzE74CQAAdFKshDr/35iuuggAAHTc//FQ3/9vTNXsPwB0Uhidt/8AkDH5oVNN+w8A2dLVdybS/jdnnP8HgM4OXwy1/3MV+w8AHZXrrUj7vzju938A0Hn/V0Pt/8qI6/8AwCbe/0f6/L91fsD1fwFgE+//Q+1//aj7/wBAJ12HQ53/b9UPOKYA0Em+71Ko8//1smMKAJ0UhmL9/n/N/gNAR8XRU7HO/9t/AOi8/+PzofZ/bb9jCgCdlCqh7v/XutDjmAJAx/2vTUfa/+bpfr//A4DO+z8Vav+Xhl3/BwA67/9kqP2fH3P9XwDouP/VUO//GzMT7v8DAJ0UK6HO/zemq+7/CwAd93881Pf/G1M1+w8AnRRGFuw/AGRMbjDU9f8a0/YfADrq6j0b6vr/M87/A0Bnh2Ld/29+wv4DQEfleqj9Xxrz+z8AyNr+nx52/R8A2MT+h7r/34UB1/8FgM77vxpq/+s97v8DAJ10HQ71+X+rvt8xBYBO8n0XQ53/Xys7pgDQSWHoTKjP/+0/AHRWHD0V6/y//QeAjkoT803n/wEgY/tfmYl0/5/m+aOOKQB03P/adKj9Pz3g93+355WvetWrvvfgwMDA6Ml77rnnTT/5UwAJMXGH1+it3P/JUPu/NOz6P7fsL+4fvudNb3nrr/7qr/7bd8/NzZ358Cc+8Yk/ivTnAaTc//u9Xqnt/w32f27M9X9vwd7v6Xndj/3MP/qVdy0+8akNLzGAAMjA/lenIu1/Y6bi/j83qetbBn7sf/9X75h8+ON/3PLiAgiAjChWQp3/b0xX3f/35t759775F9629NEve1UBBEC29n98NtT+T9Xs/+Z1H/prv/jO5Y9+ySsKIACypjCyYP8zeurntT/yS79z9iNfuOLlBBAA2ZMbXI50/Z/GtP3fpO95869f+tjnm15KAAGQzS9/9Z6NNAHNGef/N+UVd//yxd//715GAAGQWQcvh7r/3/yE/d/EWZ8jv3jmmS/7oR8gADKsXA+1/0tjfv/X0Xf87OIf/De/9QMEgP0Po3V62PV/OvmRt3/uT5z3BwRA5vc/1P3/LvS7/u/Le+X/8tFnrT8gAOz/aqQx2Fjrcf+fl/u6Z/E7f/b3fPIPCAC6jqyFOv9fLzumL3O09+1/6ye8+QcEAHvy/RdC7f+a/X+Zr/1/a88vPuM1AxAAXJ2EodOhzv97//8yx/o77/oXv+cVAxAAXFUcXQq1/97/3/ijnu8+/it/6PUCEAA8rzQxH2r/Vw84pjfwijv/9ae9WgACgK/vf3Um0v1/muePdjmo15XrecujXisAAcCf7X9tKtT+rwz4/d/1verN7/+qlwpAAPDn+z8Zav8XR1z/5/oH+tivftYLBSAACLr/c+Ou/3tdPf9w1f1+AAHAN8//h/r8vzFTcf+f6376/xPv/dIVLxOAAODPFCvTofZ/qur+v9fLvBO/9ozr/gECgG8ojM/F2v+a/b+O7/4Hq896iQAEAN+QH1m0//EN/9pnnP0HBADflBtYjvS5cGPa/l/HHW9e+ROvD4AA4Ju6jp6LtP/NWef/r/ftv5//WMPLAyAAeIEDl0Pd/29+wv6/1KG3f97H/4AA4IXK9VD7f2rM7/9eovveum//AQKAwPvfOjPk+j8v8S1/77MtLw2AAODF+x/q/n8X+13/9yXf8Sw/tO7aP4AA4MX7vxpq/9eOuP/PtUr3fczLAiAAePF7w55Y5//rZcf0Jb/++0k//gMEAC+W778Qav/X7P9L/IWf85oACABerDB8OtTn/97/v9Srft6P/wEBwIsVx5Zinf+3/y/xA//ua14SAAHAi78bNjEXav9XDzqm157iKf/Wc14RAAHAi/e/OhPpw+Hmud4uB/WaUzy1d33FCwIgAHjx/temQu3/yqDf/127/3ev+PwfEABcu/+TofZ/ccT1f67xyr965qteDgABQOj9nxt3/f9r9/9//JCr/wICgGvP/4f6/L8xU7H/1+7/Tzxu/wEBwDW//5sI9f2/xlTV/X9f8v7f/gMCgGu/HDY2F2v/a/b/2kP8V33+DwgArpEfXoz0+3/7f73v/5+x/4AA4MVyAyuh9n/a/r8k8Worvv8PCACucfR8pP1vzjr//xLld/n9PyAAuMb+1VD3/1uYsP/X+oHfcv0/QABw7ZvDeqj9PzXq93/XetWvu/4/IAB4yf6Huv/PmaGCY3qNv/DzPv8HBACx9/9Sn+v/XuuOn2t4DQAEANee/w+1/2uH3f/vWqWf9AoACACu+f1fT6zz//WyY/qSY3zfn3gFAAQAL5IfOB9q/9fs/3XO8XzMCwAgAHiRwnCo6/+0vP+/jm9/yPMfEAC8SHFsMdb5f/v/Ut3/57rnPyAAeNF3wybmQu3/5UOO6Uvd+9krnv+AAOCF+18Ndf/f5rle3/9/qUN1dwACBAAv2v/aVKj9Xx7MOagv8ap3POvZDwgAXrT/k6H2f2HU9f9e6o6f/7wnPyAAiLv/jblx1/+/jjd/bMOTHxAAvPD8f6jP/xvTFft/HcMrLgEMCABr8MLf/02E+v5fY6rm/r/X8d3/ziUAAQEgAF6gMDZn/zPwMc8/+IxnPiAABMA35YdDXf/H/t/AiVVfAAAQAN+U6w91/d/GtP2//i8Af80vAAEEwAv0nI+0/83Ziv2/rp94xvMeQAB80/5Y9/9dmPD9/+s6+r6m5z2AAPiG8mqo/V8etf/X/wbgP/ySpz2AAPjm/tdD3f/n7JDr/13fsVX3AAIQAFH3/1Jf3jG9/jcAf9UvAAAEwDcciLX/a4fd/+8Gv/R482c96QEEwJ+vwtFY5//rZcf0Rr/0+IDnPIAA+DP5gXOh9n/N/t/IK97yVc95AAHwdYXhUNf/aXn/f+PUu/MxT3kAAfB1xbHFWOf/7f8Nffe/9owHEAB/9rPwSqj7/7QuHbbzN/ys5/inPeMBBMDX9786HWn/m2f7fP//hr7zVzzhAQTA1/e/NhVq/5eH/P7/xh8A3PWHnvAAAuDr+z8Zav8XRl3/78a+9f/2fAcQAAH3vzE77vr/N9bV8zHPdwAB8Kfn/0N9/t+Yrtj/l7HvH3u6AwiA53//NzETav+naiUr/zIfAOx/xtMdQADs2VMYm7P/Weq9f+LZDiAA9uzJDy9Fuv6P/e/4G8BPeLYDCIA9uf7TofZ/2v538LNNz3YAAbCn50KkPWjOVuz/y3ulnwAACIA9e8proe7/t+D3f538TMuTHUAAlFdD7f/yqP3v5KOe6wACoFwPdf+/c4Ou/9fJm571XAfIfAAE2//LvTkD38k7fAUQIOsB0HWgHunz/9baIff/6+g7PuepDpDxAMgdvRzq/H+9bN47+zt/4qkOkO0AyA+cC7X/a/Z/EwqLzgAAZDsACiPLoc7/e/+/KUf+wDMdINMBUBxfCLX/3v9vzj/+b57pAFkOgFJlNtL9f1qXDtv2zXjFGVcBAshyAJSq05H2v3m2z/f/N+VuNwIGyHIAlGpTofb/1FDetm/KL3/ZEx0guwFQqk2G2v+FUdf/25zvvbjhiQ6Q2QAItv+N2QnX/9+kH/99z3OAzAZAqRrq8//GdNX9fzd76H/dfQAAMhsAxYmZSPvfnq3Z/80qX/Y0B8hqABRG50Ptf/vv2v9Ne9PHPM0BMhoA+aGlYJeC/Sm7vmm/9HlPc4BsBkBX3+lol4IXAJvW/TvuAwCQ0QA4ciHcBAiATTt81rMcIJsBUF6LdyFYAbBpP/pRz3KATAZAeTXgheAFwKb94hc8ywGyGADlesRTwAJgs/a+84pnOUAGAyDm/guATetd9iQHyF4AdB2sx7wRrADYrDf7CgBA9gIg13s56I3gBcBmE/AXvuRJDpC1ACgMng26/wJgs7717Z7jAFkLgOLIcthLwAiATaqc8xwHyFgAFMcX4l4CTgBs0k884zkOkK0AKFVmG3EPmwDYpF9wK2CAbAVAqTodeP8FwGavAvCvPMUBMhUApdpU5P0XAJv0ve/wFAfIUgCUapOh918AbNLRKU9xgAwFQPT3/wJgs37oEU9xgOwEQKkyHXz/BcAm/dgnPMUBMhMAxYmZ6PsvADbpZ/7YUxwgKwFQGJ0Pv/8CYJP+UctTHCAjAZAfOtWMf9gEwKZ8x694hgNkJAC6+s5kYP8FwObsf5dnOEBGAuDwxSzsvwDYnOFFz3CAbARAuZ6Nk74CYFPu+bBnOEAmAqC8mpEvfQmATXmTWwEBZCIAyvVmRg6bANiUt2x4hgNkIACys/8CYFNe+VZPcID4AdB1qJ6dH30LgM141b/xBAcIHwC53ksZuuiLABAAAALgeYXBs1m66JsAEAAAAuCq4uipZpYOmwDYjL/8/3iCA8QOgOL4Qqb2XwBsysF3e4IDhA6AUmW2ka3DJgA2Y2DeExwgcgCUqtMZ238BIAAABECpNpW1/RcAAgAg8wFQqk1mbv8FwKaMnvEEBwgbAFl8/y8ANufkk57gAFEDoFiZzuD+C4BNufeTnuAAQQOgOD6bxf0XAAIAINMBUBhdyOT+CwABAJDlAMgPZuv6fwLg5rzpj5J23L72yacAEuKX/lJ6X9+7es9mdP8FwKb8ZOI+Hvrk3xgASIhXF9P7+n7oUlb3XwBsyk8l7rg9NeCoANy2cr3VFgAIAAD7LwAQAADR97/ZFgAIAICM7f9qlvdfAAgAgEzqOpzlz/8FgAAAyKZ838Vs778AEAAAGVQYOtNsCwAEAECmFEdPZX3/BYAAAMic0vh85vdfAAgAgMztfyWb9/8TAAIAINP7X5u2/wJAAABkbv+n7L8AEAAAmdv/SfsvAAQAQNb2v+r9vwAQAABZU6w4/y8ABABA5vZ/3Pf/BYAAAMiawsiC/RcAAgAgY3KDy67/IwAEAEDGdPWetf8CQAAAZM2hSy2zLwAEAEDGlOv2XwAIAAD7LwAQAADx99/5fwEgAAAyt/+r9l8ACACAjOk64vN/ASAAALIm33fR/gsAAQCQMYWhMz7/FwACACBjiqNL9l8ACACAjClNzNt/ASAAALK2/9UZ9/8RAAIAIGv7X5uy/wJAAABkbv8n7b8AEAAA9h8BIAAAou9/1ef/AkAAAGRNsTJt/wWAAADImML4rP0XAAIAIGPyIwv2XwAIAICMyQ0uu/6PABAAABnTdfSc/RcAAgAgaw5edv8/ASAAALKmXLf/AkAAANh/BIAAAIi//87/CwABAJC5/V+1/wJAAABkTO7Ims//BYAAAMiYfP8F+y8ABABAxhSGTvv8XwAIAICMKY4t2X8BIAAAMqY0MW//BYAAAMja/ldn3P9HAAgAgKztf23K/gsAAQCQuf2ftP8CQAAA2H8EgAAAiL7/VZ//CwABAJA1xcq0/RcAAgAgYwpjc/ZfAAgAgIzJjyzafwEgAAAyJjew4vo/AkAAAGRM19Fz9l8ACACArDmw6v5/AkAAAGRNuW7/BYAAAMje/vv8XwAIAAD7jwAQAADh93/V/gsAAQCQMbke5/8FgAAAyJp8/3n7LwAEAEDGFIZP+/xfAAgAgIwpji3ZfwEgAAAypjQxZ/8FgAAAyNr+V2fc/0cACACArO1/bcr+CwABAJC5/Z+0/wJAAADYfwSAAACIvv9Vn/8LAAEAkDXFCd//EwACACBrCmNz9l8ACACAjMkPL/r9vwAQAAAZkxtYsf8CQAAAZE3PefsvAAQAQNbsX3X/PwEgAACyply3/wJAAABkb/99/i8ABACA/UcACACA6PbbfwEgAAQAkDW5Ht//EwACQAAAWZMfOG//BYAAEABAxhSGXf9HAAgAAQBkTXHM9X8FgAAQAEDWlCbm7L8AEAACAMja/lfd/1cACAABAGRu/2tT9l8ACAABAGRu/yftvwAQAAIAsP8IAAEAEH3/qz7/FwACQAAAWVOc8P0/ASAABACQNYWxOfsvAASAAAAyJj+85Pf/AkAACAAgY3L9p+2/ABAAAgDImp7z9l8ACAABAGRNue7+fwJAAAgAIHP7v2r/BYAAEABA9t7/+/xfAAgAAQDY/zRrffHzAkAACACATroOhDr/31r7H54WAAJAAAB0kDsa6vx/s14eeEoACAABAPDy8gPnQu3/WnmPABAAAgCgg8LISqjz//XyHgEgAAQAQAfFscVQ+3/1/b8AEAACAKCDUiXU/X9alw7vEQACQAAAdNr/6nSk/W+e7esSAAJAAAB02v/aVKj9PzWU3yMABIAAAOi0/5Oh9n9htLBHAAgAAQCQqf1vzE4U9wgAASAAADrsfzXU5/+N6UppjwAQAAIA4OUVJ2ZC7f9U7Rv7LwAEgAAAuIHC2HzY/RcAAkAAAFxffmipGXb/BYAAEAAA15XrPx1q/6dftP8CQAAIAIDrOnIh0v435yov2n8BIAAEAMD1lNdC3f9vcby4RwAIAAEA0Gn/V0Pt//LINfsvAASAAAC4zv7XQ93/79xgYY8AEAACACBb+3+5N7dHAAgAAQDw8roO1iN9/t9aO9S1RwAIAAEA8PJyvZdDnf+vl6/3rxQAAkAAALxQfvBcqP1fu+7+CwABIAAAXqgwshzq/P/13/8LAAEgAABeqDi+EGr/b/D+XwAIAAEA8AKlymyk+/+0Lh7ZIwAEgAAA6LT/1elI+98809clAASAAADotP+1qVD7f2oov0cACAABANBp/ydD7f/CaGGPABAAAgAgU/vfmJ0o7hEAAkAAAHTY/1jn/xvT1dIeASAABADAyytOzITa/6nay+6/ABAAAgDgqsLofKb2XwAIAAEAsGdPfmipman9FwACQAAA7OnqOxNp/5sz1U77LwAEgAAA2HPkYqj9n6t03H8BIAAEAEC5Hur+f4vjxc7/ZgEgAAQAkPn9Xw21/ysjhU38owWAABAAQObf/4e6/9+5wfxm/tUCQAAIAMD+B9r/1d7cpv7ZAkAACAAgy7oOhTr/31o72LW5f7gAEAACAMiwXO+lUOf/6+XN/ssFgAAQAEB2FQbPhtr/tU3vvwAQAAIAyK7iyHKk8/8bm3//LwAEgAAAMrz/4wuhvv93E+//BYAAEABAZpUqs5Hu/9O6eORm/vUCQAAIACCj+1+djrT/zTP9uZv55wsAASAAgGzuf20q1P6fGs7f1L9fAAgAAQBkc/8nQ+3//Fjh5h4AASAABADg/X/aNWYnijf5CAgAASAAgAzufyXU+f/GdLV0sw+BABAAAgDInOJ4qO//N6ZqN73/AkAACAAgcwqj85nffwEgAAQAkDX5oVPNzO+/ABAAAgDImK6+M5H2vzlTvZX9FwACQAAAGXP4Yqj9n6vc0v4LAAEgAIBsKddD3f9vcbx4a4+DABAAAgDI1P6vhtr/lZHCLT4QAkAACAAgU+//Q93/7/xA/lYfCQEgAAQAYP9Tuv+rR3O3/FAIAAEgAICs6DoU6vx/q36g69YfDAEgAAQAkBH5vkuhzv/Xy7fzaAgAASAAgGwoDJ4N9fu/tdvafwEgAAQAkA3F0VDX/2vd3vt/ASAABACQkf0fXwi1/7f5/l8ACAABAGRCqRLq/n+tCz23+4gIAAEgAIAM7H9tOtL+N0/35273IREAAkAAABnY/6lQ+780nL/tx0QACAABAMTf/8lQ+z8/Vrj9B0UACAABAETf/2qo9/+NmYniFjwqAkAACAAgtmIl1Pn/xnS1tBUPiwAQAAIAiL3/46G+/9+Yqm3J/gsAASAAgNAKowv2XwAIAAEAZExuMNT1/xrTW7X/AkAACAAgsK7eWNf/n6lu1f4LAAEgAIDADsW6/998Zcv2XwAIAAEAxFWuh9r/pbHi1j02AkAACADA/qdi/08PF7bwwREAAkAAAGH3P9T9/y4M5Lfy0REAAkAAAEH3fzXU/td7clv68AgAASAAgIi6Dof6/L9V37/FD5AAEAACAAgo33cx1Pn/tfJWP0ICQAAIACCewtCZUJ//b/3+CwABIACAeIqjp2Kd/9/6/RcAAkAAAOGUJuZj7f/+bXiQBIAAEABAtP2vzES6/0/zwtHteJQEgAAQAECw/a9Nh9r/0wO57XiYBIAAEABAsP2fCrX/S8P5bXmcBIAAEABArP2fDLX/c2OF7XmgBIAAEABApP2vhnr/35ipFLfpkRIAAkAAAHEUK6HO/zemq6XteqgEgAAQAECc/R+fDbX/U7Vt238BIAAEABBGYWTB/gsAASAAgIzJDS5Huv5PY3o7918ACAABAATR1Xs20v43Z6rbuf8CQAAIACCIg5dD3f9vfmJb918ACAABAMRQrofa/6Wx4vY+XgJAAAgAwP4nTev0cGGbHzABIAAEABBi/0Pd/+9Cf367HzEBIAAEABBg/1cj7f/GWk9u2x8yASAABACQdl1H1kKd/6+Xd+BBEwACQAAAKZfvuxBq/9d2Yv8FgAAQAEDKFYZOhzr/vyPv/wWAABAAQMoVR5fsvwAQAAIAyJjSxHyo/V89sEMPnAAQAAIASPP+V2ci3f+nef5olwBAAAB02v/aVKj9XxnI7dRDJwAEgAAAUrz/k6H2f3Ekv2OPnQAQAAIAsP/J2P+58cLOPXgCQAAIACCt+18N9fl/Y6ZS3MFHTwAIAAEApFOxMh1q/6eqpZ18+ASAABAAQCoVxmdj7X9tR/dfAAgAAQCkUn5kwf4LAAEgAICMyQ0sR7r+T2N6p/dfAAgAAQCkUNfRc5H2vzlb3en9FwACQAAAKXTgcqj7/81P7Pj+CwABIACA9CnXQ+3/qbHizj+GAkAACADA/u+m1pnhwi48iAJAAAgAIHX7H+r+fxf787vxKAoAASAAgJTt/2qk/d9YO5LblYdRAAgAAQCkSa5nLdT5/3p5lx5IASAABACQIvn+C6H2f2239l8ACAABAKRIYfh0qPP/u/b+XwAIAAEApEhxbCnU/u/e+38BIAAEAJAepYm5UPu/enAXH0wBIAAEAJCW/a/ORLr/T/Ncb5cAEAACAKDT/temQu3/ymBuNx9OASAABACQkv2fDLX/iyP5XX08BYAAEACA/d/5/Z8bL+zuAyoABIAAANKw/9VQn/83ZirFXX5EBYAAEABA8hUr06H2f6pa2u2HVAAIAAEAJF5hbC7W/td2ff8FgAAQAEDi5YcXm/ZfACAAgGzJDayE2v/pJOy/ABAAAgBIuqPnI+1/c7aahP0XAAJAAAAJd2A11P3/FiYSsf8CQAAIACDZyvVQ+39qtJiMx1UACAABACR7/0Pd/+fMUCEhD6wAEAACALD/O7X/F/vySXlkBYAAEABAcu1fDbX/a0e6EvPQCgABIACApMr1xDr/Xy8n6MEVAAJAAAAJle8/H2r/15K0/wJAAAgAIKEKw6Gu/9NK1Pt/ASAABACQUMWxxVjn/5O1/wJAAAgAIJFKE3Oh9v/yoYQ9wAJAAAgAIIn7X52JdP+/5rneroQ9wgJAAAgAIIH7X5sKtf/Lg7mkPcQCQAAIACCB+z8Zav8XRgqJe4wFgAAQAID931aNufFi8h5kASAABACQtP2vhvr8vzFdSeD+CwABIACAhClOhPr+X2OqVkriwywABIAAABKlMDZn/wUAAgDImPxwqOv/JHb/BYAAEABAkuT6Q13/tzGd1P0XAAJAAABJ0nM+0v43ZytJ3X8BIAAEAJAg+2Pd/3dhIrH7LwAEgAAAkqO8Gmr/T40Wk/tYCwABIACAxOx/PdT9f84OFRL8YAsAASAAAPu/Hft/qS+f5EdbAAgAAQAkw4FY+792uCvRD7cAEAACAEiC3NFY5//r5YQ/4AJAAAgAIAHyA+dD7f9a0vdfAAgAAQAkQGE41PV/Wol//y8ABIAAABKgOLYY6/x/8vdfAAgAAQDsulJlLtb3/w+l4EEXAAJAAAC7vf/V6Uj3/2ue7etKwaMuAASAAAB2ef9rU6H2f3kon4aHXQAIAAEA7PL+T4ba/4XRQioedwEgAAQAYP+3TGN2vJiOB14ACAABAOzm/ldDff7fmK6kZP8FgAAQAMAuKk7MhNr/qVopLQ+9ABAAAgDYNYWxOfsvAASAAAAyJj+81LT/AkAACAAgW3L9p0Pt/3Sa9l8ACAABAOyWnguR9r85W0nT/gsAASAAgF1Sroe6/99CWn7/JwAEgAAAdnX/V0Pt//JouvZfAAgAAQDs0vv/UPf/OTtYSNkBEAACQAAA9v929/9yby5tR0AACAABAOy4rgOhzv+31g51pe4YCAABIACAnZY7ejnU+f96OYUHQQAIAAEA7LD8wLlQ+7+Wxv0XAAJAAAA7rDCyHOr8fyrf/wsAASAAgB1WHF8Mtf/pfP8vAASAAAB2VqkS6v4/rUuHU3ogBIAAEADATu5/dTrS/jfP9nWl9EgIAAEgAIAd3P/aVKj9PzWUT+uhEAACQAAAO7j/k6H2f2G0kNpjIQAEgAAA7P8tacxOFNN7MASAABAAwE7tfzXU5/+N6WopxUdDAAgAAQDsjOLETKj9n6qlef8FgAAQAMDOKIzO238BIAAEAJAx+aGlpv0XAAJAAADZ0tV/OtL+N2fSvv8CQAAIAGAnHLkQav/nKmnffwEgAAQAsAPKa6Hu/7c4Xkz9IREAAkAAANu//6uh9n95JP37LwAEgAAAtn//66Hu/3dusBDgoAgAASAAAPt/M/t/uTcX4agIAAEgAIBt1XWwHunz/9bawa4Qx0UACAABAGynXO/lUOf/6+UgB0YACAABAGyjwuDZUPu/FmX/BYAAEADANiqOLIc6/x/m/b8AEAACANjO/R9fCLX/cd7/CwABIACA7VOqzEa6/0/r4pFAB0cACAABAGzX/lenI+1/80x/V6CjIwAEgAAAtmn/a1Oh9v/UUD7S4REAAkAAANu0/5Oh9n9+tBDq+AgAASAAAPvfUWN2ohjrAAkAASAAgO3Y/0qo8/+N6Wop2BESAAJAAABbrzgxE2r/p2rR9l8ACAABAGy9wui8/RcAAkAAABmTHzrVtP8CQAAIACBbuvrORNr/5kw14P4LAAEgAICtdvhiqP2fq0TcfwEgAAQAsMXK9VD3/1scL4Y8TAJAAAgAYGv3fzXU/q+MFGIeJwEgAAQAsLXv/0Pd/+/8QD7ogRIAAkAAAPb/Rvu/ejQX9UgJAAEgAIAt03Uo1Pn/1tqBrrDHSgAIAAEAbJVc76VQ5//r5cAHSwAIAAEAbJHC4NlQ+78Wef8FgAAQAMAWKY6Euv7fRuj3/wJAAAgAYKv2f3wh1Pf/Yr//FwACQAAAW6NUmY10/5/WhZ7gB0wACAABAGzF/lenI+1/80x/LvgREwACQAAAW7D/talQ+780nI9+yASAABAAwBbs/2So/Z8fK4Q/ZgJAAAgAwPv/F2nMTBTjHzQBIAAEAHCbipVQ5/8b09VSBo6aABAAAgC4zf0fD/X9/8ZULQv7LwAEgAAAbk9hdMH+CwABIACAjMkPhrr+X2b2XwAIAAEA3I6uvjOR9r85U83I/gsAASAAgNtx6FKo/Z+rZGX/BYAAEADAbSjXQ93/b3GsmJlDJwAEgAAAbn3/V0Pt/8pIITvHTgAIAAEA3Pr7/1D3/zs/kM/QwRMAAkAAALf8/j/U/teP5rJ09ASAABAAwC3pOhzq/H+rfiBbx08ACAABANyKfN/FUOf/6+WMHUABIAAEAHALCkOxfv+/lrX9FwACQAAAt6A4eirW+f/M7b8AEAACALh5pfH5UPu/tj97x1AACAABANz0/ldC3f+vdaEngwdRAAgAAQDc7P7XpiPtf/N0fy6DR1EACAABANzs/k+F2v+l4XwWD6MAEAACALjJ/Z8Mtf/zY4VMHkcBIAAEAHBT+18N9f6/MTNRzOaBFAACQAAAN6FYCXX+vzFdLWX0SAoAASAAgJvY//FQ3/9vTNWyuv8CQAAIAGDzCiML9l8ACAABIAAgY3KDy5Gu/9OYzvD+CwABIACAzerqPRvq+v8z1QzvvwAQAAIA2KxDl0Ld/29+Isv7LwAEgAAANqlcD7X/S2PFTB9OASAABACQwf1vnR4uZPt4CgABIACAze1/qPv/XRjIZ/yACgABIACAzez/aqj9r/fksn5EBYAAEABAR12HY33+X9/vmAoAASAAgE7yfRdDff9vreyYCgABIACATgpDZ0J9/m//BYAAEABAZ8XRpVjn/+2/ABAAAgDoqDQxH2v/DzimAkAACACg4/5XZyLd/6d5/qhjKgAEgAAAOu5/bTrU/q8M5BxUASAABADQcf8nQ+3/0kjeQRUAAkAAABnb/7mxgoMqAASAAAA67X91KtL+N2YqRQdVAAgAAQB0UKyEOv/fmKqWHFQBIAAEANBp/8dnY+1/zf4LAAEgAIBO8iML9l8ACAABIAAgY3KDy5Gu/9OYtv8CQAAIAKCjrt5zkfa/OeP8vwAQAAIA6Ozg5VD3/5ufsP8CQAAIAKCjcj3U/i+N+f2fABAAAgDI2P63Tg+7/o8AEAACANjE/oe6/9+Fftf/FQACQAAAnfd/NdL+b6z1uP+PABAAAgDoJHdkLdT5/3rZMRUAAkAAAJ3k+y+E2v81+y8ABIAAADoqDJ0Odf7f+38BIAAEANBZcXQp1P57/y8ABIAAADorTcyH2v/VA46pABAAAgDouP/VmUj3/2meP9rloAoAASAAgE77X5sKtf8rA37/JwAEgAAAOu//ZKj9Xxxx/R8BIAAEAJC1/Z8bd/1fASAABADQcf+roT7/b8xU3P9HAAgAAQB0UqxMh9r/qar7/woAASAAgE4KY3Ox9r9m/wWAABAAQCf5kUX7LwAEgAAQAJAxuYGVSNf/aUzbfwEgAAQA0FHX0XOR9r856/y/ABAAAgDo7MBqqPv/zU/YfwEgAAQA0FG5Hmr/T436/Z8AEAACAMjY/rfODLn+jwAQAAIA2MT+h7r/38V+1/8VAAJAAACd93811P6vHXH/PwEgAAQA0EmuJ9b5/3rZMRUAAkAAAJ3k+y+E2v81+y8ABIAAADoqDJ8O9fm/9/8CQAAIAKCz4thSrPP/9l8ACAABAHRUmpgLtf+XDzqmAkAACACg4/5XZyLd/6d5rtf3/wWAABAAQMf9r02F2v/lwZyDKgAEgAAAOu7/ZKj9Xxxx/T8BIAAEAJC1/Z8bd/1/ASAABADQcf+roT7/b8xU7L8AEAACAOikOBHq+3+Nqar7/woAASAAgE4KY3Ox9r9m/wWAABAAQCf54cWm/UcACAABANmSG1gJtf/T9l8ACAABAHR29Hyk/W/OOv8vAASAAAA6278a6v5/CxP2XwAIAAEAdFSuh9r/U6N+/ycABIAAADax/6Hu/3N2yPX/BIAAEABA1vb/Ul/eMRUAAkAAAJ3sj7X/a4fd/08ACAABAHSS64l1/r9edkwFgAAQAEAn+YHzofZ/zf4LAAEgAICOCsOhrv/T8v5fAAgAAQB0VhxbjHX+3/4LAAEgAICOShNzofb/8iHHVAAIAAEAdNz/aqj7/zbP9vr+vwAQAAIA6Lj/talQ+788mHNQBYAAEABAx/2fDLX/C6Ou/ycABIAAADK2/43Zcdf/FwACQAAAHfe/Gurz/8Z0xf4LAAEgAIBOihOhvv/XmKq5/68AEAACAOikMDZn/xEAAkAAQMbkh0Nd/8f+CwAEALAJuf7TofZ/2v4LAAEgAIDOes5H2v/mbMX+CwABIAAy6Lt7BwcH3/Q3f/qn//bf++mf/un/6d6r/zX4HR4WXkY51v1/F/z+TwAgADKkeOT43/2nv3vxySef/L1PPfPMM5/53Oc+918+f/V//ugPr/7XMx+9+v/Xf/ufv+W4O6Nwnf1fDbX/y6P2XwAgADLhu4Z/8u+/a/HTn/3cl77ytY2Xeew3vvaVL33uM584/bZf+Mneb/Ow8YL3/6Hu/3N2yPX/BAACILxvO/7W3/3UF770lWcbm38Fb60/95UvfeEjH/gnJ7/VA0i8/b/Ul3dMBQACIK6uXP5ba7946r+uNzeu3NKBuLLRXP/iyi/ddUc+53Zp2f5TOhBr/9cO+YMWAAiAqHKlO76z9guLX9iSA/Inp36x9q13lNwzLbN/TUdjnf+v+5aLAEAABJV/xauq/2jqj7bymFz5/Ad+7s7veqXPTTP59zRwLtT+r9l/AYAAiPlu7ZXfd/dbf//KNhyXrz3xL469+pU+B8iawshKqM//vf8XAAiAkF75/RP/YHV92w7Ns2f/fvXV+5w/zZLi2GKs8//2XwAgAOLZ9/0jb3n/F7f56Pznd/2t8b/sJ9SZUaqEuv9P69Jhx1QAIACivVH7ntG/+c5P7sh7tY//2l8f+W6nArKx/9XpSPvfPNvn8ysBgACIpfvIm3/lg/99xw7RVx/5lz/2Wo96Bva/NhVq/5eHfI9VACAAYr377/vZ939mh4/SF3/rb/Q5ExB+/ydD7f/CqOv/CQAEQCR77/xfp7+8C8fpv069pV8C2P/UaMxO+HsVAAiAQLprb73w5d05UFf+eOp/u/MOhyDs/ldDff7fmHb/XwGAAAjkW974y+e/unuH6sqXLvzTmgSIqTgxE2r/p2r2XwAgAML4C2/4tx/a7dfoL1/85yf/okMRT2Fs3v4jAASAAEjmJ7Sv/7UnvpqA4/XfPvhrr3+FwxFMfnipaf8RAAJAACTR4f/rsecScsT++4f/2UE/rwol13861P5P238BgACI4i/9nXNf3EjOMfvChb/9bQ5KIEcuRNr/5pzv/wkABEAQ+944/Z+TdYO2jc/+h4rfWIVRXgt1/7+FcX+bAgABEEK+99c/s564w/bV//jPvsuxCbL/q6H2f3nE/gsABEAI3/5zzzyXyBfar3z0rzs6Ifa/Hur+f+cGXf9PACAAIuj+a488dyWhL7VX/uTdBx0h+5+s/b/c68ZVAgABEEDXq/+/xpUEv9pufPEXv83t1tL9J3awHunz/9baIX+QAgABEOHt/7H/lPQX3I0n7+l2oNIrd/RyqPP/9bJjKgAQAAFem//i/5GGz2b/8899u89c0yo/eC7U/q/ZfwGAAAig1PNQOq7N+pXf7vUhQDoVRpZDnf/3/l8AIAAi+M6/+nha3ps1z7/pO3wIkELF8YVQ++/9vwBAAET4aPa1//S/puil99Nv3e+n1+n7jKkyG+n+P62LRxxTAYAASL1X3vXQV1L14vul37zLDYLStv/V6Uj73zzT5/v/AgABkHqv+p8fTdtXs9bP/MR3OQ2Qqv2vTYXa/1NDblAlABAAqf/4//A/+3QKX4E/+daDrsCWpv2fDLX/C6P++gQAAiDt9h3/3S+l8jX4i//hhF8D2P9d0Zid8CUUAYAASLtv+WvpvTP7+R92F9aU7H811Of/jemqvzwBgABIu2/7qUdT/Musiz/hdTgNihMzofZ/qubvTgA4KgIg9e///+ZHUv1S/Hs/45tYyVcYnbf/CAABIAAS5S+99fdS/mL86Z+9w2FMuPzQUtP+IwAEgABIkm//5/8l9S/Hf/AvFUCydfWdibT/zRnn/wWAABAAqX9h/p5/8/kAL8hf/FcKINGOXAy1/3MV+y8ABIAASLvvev+XI7wiX/ni277fwUyu8lqo+/8tjvv9nwAQAAIg7V49+7UYr8lXvvJeBZDc/V8Ntf8rI/ZfAAgAAZB2Bz8c54vZz84MOqAJ3f96qPv/nRv0qxMBIAAEQOr3/6mNSG/MLlUdUvu/7fu/2uv+EwJAAAiAlOvqCbX/7faVyzW3Zkven9nBeqTP/1trB/2RCQABIABSLj/4RKz9v+rS3W7OkjC53kuhzv/Xy46pABAAAiDlCqOPtNrhnDvu61nJ+jMbPBtq/9fsvwAQAAIg7YqVywH3v90+7eaAifozG1kOdf7f+38BIAAEQOqV7jwfcv/b7WUFkKD9H18Itf/e/wsAASAAUq/7WKgLs77IqZN7PeMSkpmV2Uj3/2ldPOKYCgABIADSvv8nlsPuf7u9pAASsv/V6Uj73zzT7/d/AkAACICU23tyKfD+P18AzgIkYf9rU6H2/9Sw6/8IAAEgAFK//4uh97/dPuV7AEnY/8lQ+z8/5iemAkAACAD7n4JvAvo1oPf/W6kxO+FPSgAIAAGQct0n4u9/u336uI9rd3f/K6HO/zemq+7/KwAEgABI+/4fP5WB/W+3z93tgq27qDge6vv/jama/RcAAkAApP2N2d0rmdj/dvtSzfNu1xRG5+0/AgABkKg3ZrWzGdn/dvuyewPulvxQqI+Z7L8AEAACIMAL88TFzOx/+4rPAHZJV1+oy0w1Z5z/FwACQACkXW6k3mpnR+uyAtgVh0NlZnOuYv8FgAAQAGnX/1iW9v/qS/flOz33dl45VGY2F8f9/k8ACAABkHaHntpotzNWAD4D2Pn9Xw21/ysjrv8jAASAAEi7g5nbfwWwK+//Q93/7/yAC0oIAAEgAOx/Kl/AL1U8/ez/Lf/5rB51/x8BIAAEQMp1Hc3k/rfbVy7f6SV85/7MDoU6/99aO+ByUgJAAAiAlMsPPpHN/b/q4t1O4u6QXO+lUOf/62XHVAAIAAGQcoWxR1rtzDp33Ne4d+bPbDDUZaaaa/ZfAAgAAZB2xcrlDO9/u33a3YF35M9sNNT1/za8/xcAAkAApF7prvOZ3v/n7w6815Nw2/d/fCHU9/+8/xcAAkAApF73sdPNdsYtnVQA252ZlVD3/2td6HFMBYAAEABp3/8Ty5nffwWw/ftfm460/83T/X48IgAEgABIub0nl+z/nxaA7wFs6/5Phdr/pWHX/xEAAkAApH7/F+3/nzp1wi1dtnH/J0Pt//yYn44KAAEgANL++b/9/+Y3Af0a0Pv/TWnMTPhTEQACQACkff9P+Pz/m84c87HutihWQp3/b0xXfVgkAASAAEj7G7Pjp+z/C5y/24Vdt2P/x0N9/78xVbP/AkAACIC07//dK/b/RS65N+DWK4wu2H8EAAIgUW/Mamft/zUuVz0Zt1huMNTHTI1p+y8ABIAASLv8+EX7f60rPgPYYl29sa7/P+P8vwAQAAIg9W/MhtdaBv8lWpcVwJY6FOv+f3MV+y8ABIAASLu+x+z/dV/iFcBWKtdD7f/SmN//CQABIADS7uDTG8ZeAdj/m/rTOD3s+j8CQAAIgNTv/1P2/8YF4JuAW7b/oe7/d37AhSIEgAAQAPY/9PcALlU8Jbdk/1dD7X/9qPv/CAABIABSrqvX5/8v68rlu7zU3/6f2eFQn/+36vsdUwEgAARAyuWHPmT/O7h4zMne2/0z67sY6vz/WtkxFQACQACkXGHsYd//7+jscT/3ur0/s6EzoT7/t/8CQAAIgNQrVi/Z/004faLb8/I2/sxGT8U6/2//BYAAEABpV7rrnOv/bcryib2emLf8ZzYxH2v/nf8XAAJAAKRd97HT9n+Tlk4qgFvd/0qo+/81L/Q4pgJAAAiAtO//iWX7rwC2ff9r06H2//SAH4UIAAEgAFJu78kl+39TBeB7ALe0/1Oh9n9p2PV/BIAAEACp3/9F+39TTp3wW4Bb2P/JUPs/P+YnoQJAAAiAtH/+b/9v2spxN3+52f2vhnr/35ip+BMQAAJAAKR9/0/4/P/mnTnm49+bUqyEOv/fmK76EEgACAABkPY3ZsdO2f9bcP7uLs/Pm9j/8VDf/29M1ey/ABAAAiDt+3+X3//dGvcGvAmFkQX7jwAQAAIgUS/MVdf/uVWXFMBm5QZD/cy0MW3/BYAAEABplx+/ZP9v1cblmmfopnT1no30Z9accf5fAAgAAZD6N2bDa67/f+taCmBzDl4Odf+/+Qn7LwAEgABIu77H7f9tTYEC2IxyPdT+L435/Z8AEAACIPVvzJ7eMOIKwP7f1Mc+p4dd/0cACAABkPr9f8r+334B+CZgx/0Pdf+/C/0uACEABIAAsP+02xuXKp6mL7v/q6H2f63H/X8EgAAQACmX6/X5/9ZYvcsk3FDXkVBfM23Wy46pABAAAiDl8kOP2/8tcuGYk8I3+jPruxhq/9fsvwAQAAIg7QrjD/v+/5Y5e9zPwq7/ZzZ0JtTn/97/CwABIABSr1i9ZP+30MqJbs/V6/yZjS7ZfwSAABAASVK6y/V/t9apE3s9WV/yZzYxH2v/DzimAkAACICU6z7m/j9bbemkArh2/6szke7/0zx/1O0fBYAAEABp3/8T7v+rALZ//2tTofZ/ZcCPPQSAABAAKX9I955csv/bUgC+B/Ci/Z8Mtf+LI67/IwAEgABIeQDsPblo/7fpewB+CxB2/+fG/dRTAAgAAZDyAOi2/9tm5bibxPz5/ldDff7fmKk4tAJAAAiAlAdA9wmf/2+fM8d8TPynipXpUPs/VfXhjgAQAAIg5QFQOub7f9vp/N2esnuev8zUbKz9r9l/ASAABEDKA6B4l9//ba/L7gy0Z09+ZMH+IwAEgABI1Buzquv/bLdL7g6cG1iO9GfWmLb/AkAACIC0B0B+7JL9324bl2sZf8p2HQ2Vmc1Z5/8FgAAQAGkPgK4h9//ZAa2sF8DBy6Hu/zc/Yf8FgAAQAGkPgN4P2f8dmYxsF0C5Hmr/T435/Z8AEAACIO0BcPDpDeOsAOz/TX2cc2bY9X8EgAAQAGkPgINP2f+dK4DMfhOwXA91/7+L/S7sIAAEgABIewDY/520cSmjvwYsr0ba/421I+7/IwAEgABIeQDk+nz+v7NW787gdOR61kKd/6+XvQALAAEgAFIeAPnhx+z/DrtwLHMnj/P9F0Lt/5r9FwACQACkPQCK42u+/7/jzh7P2M/HCsOhLjPZ8v5fAAgAAZD6ACjVLtr/XbByojtLz9TiWKjbTLW8/xcAAkAApD4ASneddf2/XXHq5N7sPFFLE/Oh9n/1oBdfASAABEDKA6D7+Ir93yVL2SmAUnUm0v1/mueOdnnxFQACQACkOwC6T7j/rwLYgdNMU6H2f2XQ7/8EgAAQACkPgL0nl+z/rhZAJr4HUKpNhtr/xRHX/xEAAkAApDwA9p5ctP+7+z2AExn4LUC0/Z8bd/1fASAABEDKA6Db/u+6lePhx6RUDfX5f2Om4v4/AkAACICUB0D3CZ//776zx4KfTi5WpkPt/1TV/X8FgAAQACkPgNKxZfufABfuCv0MLYzNxdr/mv0XAAJAAKQ8AIp3nbb/ibAa+c5A+ZFF+48AEAACIFFvzKrn7X9CXIp7d+DcQKjLTDSm7b8AEAACIO0BkB+77Pq/SbFxuRb1+Xn0XKT9b846/y8ABIAASHsAdA0+Yv8TNCxRC+DAaqj7/81P2H8BIAAEQNoD4OiH7L8C2Hbleqj9PzXq938CQAAIgLQHwMGnNoyuAtj+/Q91/58zQ67/IwAEgABIewDY/wTOy+Vw3wQMtv8X+1z/VwAIAAGQ9gA4ZP8TaONSsF8D7l8Ntf9rR9z/TwAIAAGQ8gDI9dv/JPrqu18d6YmZ64l1/r9e9mIrAASAAEh5AORHHrP/CfTcg98X6XmZ7z8fav/X7L8AEAACIO0BUJwI9cZs44+D1Myz7wm1/4XhUNf/aXn/LwAEgABIfQCUahcj7f+Vj/xcjPMZzz4Uav+LY4uxzv/bfwEgAARA2gOg++6zoa7/++TJ4onHAxTNc7He/5cm5kLt/+VDXmgFgAAQACkPgO7joT6YbT918urYvD791zT8aqzz/6XqTKT7/zTP9fr+vwAQAAIg5QGw98SpUPv/+D1/WjX3pv1bDY13x9r/2lSo/V8ezHmdFQACQACkOwD2ngx1Yrb9yH1fvzDL3jek+75GjQdD/f6vVJsMtf+LI67/JwAEgABIeQBE2//6G/78xiz77k/zNxvX/8Nr7H9y62xu3PX/BYAAEAApD4DuYPt/+Y3d3/i37bv/QmoLYP23ypHOMJeqoT7/b0xX7L8AEAACIOUB0H1iKdT+X7x/3wv+dfseSGsBrP/mwUhnmIsTob7/15iquf+vABAAAiDlAVA6thxq/y/cf8eL/n1pPQuw/vbDke4wUxibs/8IAAEgABL1xuzOM7H2/4FXXPMv3PfGNH4TcP1tRyLtf3441Gkm+y8ABIAASH8AFCrnY33+/5L9v1oAb0jfrwHXf6Mn0jfMcwOhLjPRmLb/AkAACIC0B0B+9HKk6/+2L13z+f/X7b3ngyn7V66/LdT+7+kJlZnN2ar9FwACQACkPAC6Bh8Jtf+r193/PXu6f+hDqbovwPrbj4Ta//2x7v+7MGH/BYAAEABpD4CeJ0Lt/8Nv3HuDf2jpxNMpKoD13zwU6fz/nnKs/T816vd/AkAACIC0B8DBpzYi7f+j93Xf8J9avPPjV1Kz///+QLD9D3X/n7NDrv8nAASAAEh7AATb/yfufbk3Zvnx/5iSAmi8qxzqCvPB9v9SX95rqwAQAAIg5QFwONb+P/X6l5/NrsHPpuP9/4N/JdQd5vbH2v+1w+7/JwAEgABIeQDk+p+MtP8bT57s+E/u+WwKPgP46rtD3f8nd3Q11Pn/etkLqwAQAAIg5QFQGHk01P4/dXIT/+jhTye+AJ59T6j7/+YHzofa/zX7LwAEgABIewAUJ0J9MXvjqddt6u3oxMc27P9OZuZwqOv/tLz/FwACQACkPgBKtQuR9v/K5vb/6iDdlezvPTz7UKj9L44txjr/b/8FgAAQAGkPgO67z4a6/u+mPv//+iSdfDzB5fNcrPf/pYm5UPt/+ZAXVQEgAARAygOg+3ioD2bbT77uJkbp9cm99uHXHoy1/9VQ9/9tnu31/X8BIAAEQMoDYO+JU6H2//F7buaFufvepH77ofHuWPtfmwq1/8uDOa+pAkAACIB0B8Dek6FOzLYfue/mLsy29w3JvP9RI9j7/9pkqP1fGHX9PwEgAARAygMg2v7X33CzN2bZd//FBBbA+u/8oP1PrMbsuOv/CwABIABSHgDdwfb/8g3v//MyBfBA8n4Dsf5b+yOdYS5VQ33+35iu2H8BIAAEQMoDoPv4Uqj9v3j/vlt4FO54IGmfAay/42CkM8zFiVDf/2tM1dz/VwAIAAGQ8gAoHVsOtf8X7r/jlh6HpJ0FWH/b4Uh3mCmMzdl/BIAAEABJCoDinWdi7f8Dr7jFR2LfG5P0TcD13zgSaf/zw6E+ZrL/AkAACID0B0ChciHY5/+vuOXHYt99a63k7H9PpG+Y5/pPh9r/afsvAASAAEh7AORGQ92YrX3pFj///7q993wwIY/G+ttC7f+enlCZ2Zyt2H8BIAAEQMoDoGvgg6H2f/WN+27r8ej+oQ8l4r4A628/Emr/y6FuM9Vc8Ps/ASAABEDqA+DIhyPd/7f98Bv23uYDUjr+dAIekfXfPBTp/P+ecqiPmZrLo/ZfAAgAAZD2ADj4VKj9f/S+7tt+SIp3fuLKru//vz8Qa//roe7/c3bQ9f8EgAAQAGkPgGD7/+F7t+KFOT/+B7tcAI13vTbUFeaD7f/lXtf/FwACQACkPAC6Dofa/ytPvX5rXpi7Bj67u+//H/yBSNf/6zoQ6vx/a+2Q+/8hAARAygMgNxDq/P/Gkye37KE5+tld/Azgq+9+daQnXO5orPP/9bIXUQSAAEh5ABRGPxhq/586uYUPzsjv71oBPPueUPf/yw+cC7X/a/YfASAA0h4AxYlQb8w2nnrdlr5trXxsw/5vRWaOhLrMdMv7fwSAAEh9AJTuvBBp/69s6fv/54fr7id3pQCefSjU/hfHQ91msuX9PwJAAKQ+ALrvjnX9/63e/6vTdfKxXSik52K9/y9VQt3/p3XpsBdQBIAASHkAdB+Pdf+/J1+3DY/R6x/Z8QL42oOx9r86HWn/m2f7fP8fASAAUh4Ae0+GujFb+7F7tuOFee+9O/3ztUas7/+XalOh9v/UUN7rJwJAAKQ7APaeDHVitv3IfdtzYba9b7jU2tn9j/X+vzYZav8XRl3/DwEgAFIeANH2v/6G7box2777L+5gAaz/zg/a/8RqzE64/j8CQACkPAC6g+3/pTfu3bbHat8DO/dbifXf2h/pDHOpGurz/8Z01f1/EQACIOUB0H38VKj9v3j/vm18tO54YKc+A1h/x8FIV5gvTsyE2v+pmv1HAAiAlAdA6e6VUPt/4f47tvXx2nf/znwPYP1thyN9w6wwOm//EQACQAAkKQCKd8b6/f+FB16xzY/Yvjde3oECWP+NI5H2Pz8U6mcm9h8BIADSHwCFiQvBPv9/xbY/ZvvuW2tt//73RPqGeVf/6Uh/Zs0Z+48AEABpD4DcSKjr/zfed+cdO/Co7b3n0W1+1NbfdiTUL8yOhMrM5lzF/iMABEDKA6Br4NFY+z+0Mz/M6v6hJ7b1vgDrb4+1/+W1UPf/Wxz3+z8EgABIewAcfjLS/X+bUwM7NZul4x/Zxkdu/Z2HQl1hrhzqY6bm8oj9RwAIgLQHwMGnQu3/XO/OzWah9skr2/ZBxm8fiLX/9VD3/zs36Pp/CAABkPYACLb/S4d38mfz+bE/3KYCaPzuayP9/j/a/l/uzXnNRAAIgHQHQNeRWPu/Ut7Zy+Z19X92ez7/f/cPRLr+X9fBeqTP/1trB93/DwEgAFIeALnBD0fa//Xl1+z4H07vZ7bhM4DnYt3/L9d7OdT5/3rZCyYCQACkPAAKox8Mtf8rr9mFv5yRZ7b8MXz2PaHu/5cfPBdq/9fsPwJAAKQ9AIqVUF/M3p3935Or/t4WF8CzD4Xa/+LIcqjz/97/IwAEQOoDoHTn+Vg/zHrN7vzpFO7e2vMo0fZ/fCHU/nv/jwAQAKkPgO5joa7/v2v7f3XiTj62hSX1XKzP/0uV2Uj3/2ldPOLFEgEgAFIeAN3HQ30w21zaxTdm3T/88JYVwNcejLX/1elI+9880+f7/wgAAZDyANh7cinWhdkP7+YL8957t+rbFI1Y3/8v1aZC7f+pobzXSgSAAEh3AOw9uRjrxqy7fGGWvW+81Nqa/f/+WPs/GWr/50dd/w8BIABSHgDR9v99/bv9xmzf/Re3oADWf+cH7X9y/8xmJ1z/HwEgAFIeAN0nYu3/Q0O7/8Zs3wMXbrsA1t+5P9IVZkuVUOf/G9NV9/9FAAiAlAdA9/FTofb/weEkvDG744Hb/Qxg/R2HIu1/cWIm1P5P1ew/AkAApDwASnevxNr/hNyYdd/9t/c9gPW3HY70DbPC6Lz9RwAIAAGQpAAo1s7G2v/RpJyY3ffG2/ktwPpvHIn0DbP8UKyPmew/AkAApD4AChMXQr0wv2ckOV/M2nffWuvW978n0v539cW6zNSM8/8IAAGQ9gDIjYS6MWvjoeEkfTF77w8/eotXBV5/W6j3/3sOX4x1mYmK/UcACIC0B0D/o6H2//1DyfphVvfrnrilAlh/x+FQ+18OlZnNxXG//0MACIC0B8ChJyPd/7c51Z+02Swd++gtPMLr7zwU6gpz5VC3mWyujLj+DwJAAKQ9AA4+FWr/544mbzYLtU9duekPMn77QKTf/119/x/q/n/nB13/FwEgANIeAMH2/9ThJM5mfuwPb3b/f/e19j+5+796NOf1EQEgANIdAF09ofa/sfzaZN6Yravvszf1D/nau78/0h3mug6FOv/fWjvo/n8IAAGQ8gDIDz4Raf/Xl1+T2D+mvv90E2cBnnsw1P3/cr2XQp3/r5e9OCIABEDKA6Aw+kikF+b1leTu/56u0U9turWefc/3RXoeFQbPhtr/NfuPABAAaQ+AYuWy/d+5t8HVzf4W4NmHQu1/cSTU9f82vP9HAAiA1AdA6c7zod6YLb8m2X9OhWMf3sji/o8vhPr+n/f/CAABkPoA6D4W68KsK69J+t9T6XWbueLSc7E+/y9VZiPd/6d1sccLIwJAAKQ8ALpPLIfa/6X9yf+D6v7hhzsWwPqDsfa/Oh1p/5tn+v3+DwEgAFIeAHtPLoW6/8/s4TT8MGvvfZ0uh9d4d6jv/5dqU6H2f2nY9X8QAAIg5QGw9+RiqP2f7E3HG7N9b3z5H8Q1fvf7Y+3/ZKj9nx9z/V8EgABIeQB0B9v/9w2k5Y3ZvvsvvkwBrP/2ayNdYSbY+//G7IT7/yAABEDKA6D7RKzP/x8aSs8bs30PXLhhAay/M9T1/4uVUOf/G9NV9/9FAAiAlAdA9/FQP8xuPDiSpjdmdzxwo88A1t8R6v5/xfFQ3/9vTNXsPwJAAKQ8AEp3r9j/XT0LcP3vAay/7XCk/S+Mztt/EAACIEkBUKydjbX/o2k7Mbvvjdf7LcD6bxyJ9A2z/FCsj5nsPwJAAKQ+APLjF0O9ML9nJH1fzNp330uvB7D+Gz2R9r+rL9Zlpmac/0cACIC0B0BueC3S9X8bDw2n8YvZe3/4sY1rP/8P9f5/z+FLofZ/rmL/EQACIO0B0P9YqP1//2A6f5jV/boX3xdg/R2HQ+1/uR7qNhOLY37/hwAQAGkPgINPbUR6YZ7qT+tslo698N6A6+8M9f3/PeXVUPu/MuL6PwgAAZD2AAi2//NH0zubheqnrnzjg4zf2R/qCvPleqj7/50fcP1fBIAASHsABNv/5UNpns3c6H/68/1/9w9Guv7f1ff/ofa/ftT9fxAAAiDlAdB1NNT+N5Zfm+4/r67ez/7pP+Rr7w51/f+uw6HO/7fqB7wSIgAEQMoDID/0oUj7v77ymtT/gfX/4ZV2+7kHQ93/L993KdT5/3rZCyECQACkPAAKY4+07H/C3iyPfWrj2fd8X6TnTGEo1u//1+w/AkAApD0AitXLoX7/F2H/r75drj3+UKj9L46einX+3/4jAARA2gOgdNf5UG/Mll8T40+ssD/W/o/Ph9r/tf1eBREAAiDlAdB97HSo/V95jb/OBCpVQt3/r3WhxzFFAAiAlAdA94nlUPu/6I1ZIve/Nh1p/5un+/3+DwEgAFIeAHtPLoW6/8/skS5/nEnc/6lQ+7807Po/CAABkPIA2HtyMdT+T/Z6Y5bI/Z8Mtf/zY67/iwAQACkPgO5g+/8+F2ZN5P5XQ73/b8xMuP8PAkAApDwAuk/E+vz/oSFvzBKoWAl1/r8xXXX/XwSAAEh5AJSOh/phduPBEW/Mkrj/46G+/9+Yqtl/BIAASHkAlO5asf9st8LIgv0HASAAkhQAhdq5WPs/av8TKDcY62OmafuPABAAaQ+A/PjFUC/M7/H+P4m6es+GuszEjPP/CADSHgC54bVQ1/9/77D9T6JDse7/Nz9h/xEApD0A+h4Ptf/vH/T9/yQq10Pt/9KYzEQAkPYAOPj0RqQX5ul++2//t/3P7PSwPzMEAGkPgINPhdr/+R7X/0nm/oe6/98Fl5lCAJD6AIi1/63lQ67/m8j9Xw21//Uef2YIAFIeAF29oT7/byz/oD/HBOo6HOrz/1bdbSYRAKQ9APJDH4q0/+srr/HXmED5vouhzv+vlR1TBAApD4DC2MMt+882KwydCfX5v/1HAJD6AChWQ/0wu2H/E6k4eirW+X/7jwAg7QFQuivU9X+by/Y/iUoT87H23/l/BABpD4DuY6dD7b/3/8nc/8pMpPv/NM8fdUwRAKQ8ALpPhPpgtrl4wB9iEve/Nh1q/08P+P0fAoCUB8Dek0uh7v8zc6TLH2IS938y1P4vDbv+DwKAlAfA3pOLofb/A33emNn/bd//uTHX/0UAkPIA6A62/+9zYdZE7n91KtL+N2Yq7v+DACDlAdB9Itbn/+8Z8sYsgYqVUOf/G1NV9/9FAJDyACgdC/X9v8aDI96YJXH/x2dj7X/N/iMASHkAFO86HWv/R+1/AuVHFuw/CAABkKQAKFTP2X+2W25wOdSf2bT9RwCQ9gDIj1+Kdf7f5/9J1NUb6zKTM87/IwBIewDkhkLd/6fx3mH7n0QHL4e6/9/8hP1HAJD2AOh9PNT+f2DQ9/+TqFwPtf9LYzITAUDaA+Dg0xuRXpin++y//d9urdPD/swQAKQ9AA4+FWr/F3pc/yeZ+x/q/n8X+v2ZIQBIewDE2v/WykHX/0/k/q9G2v+NtR6XmUYAkPIAyPWF+vy/sez+v0nUdWQt1Pn/etkxRQCQ8gDIDz8eaf/XV+x/EuX7L4Ta/zX7jwAg7QFQGA/1xsz+J1NhKNRlJlve/yMASH0AlKqXQv3+z/4nUnE01G2mWt7/IwBIfQCU7gp1YbaW8/+JVJqYD/VntnrAMUUAkPIA6D4e6oPZpvf/ydz/6kyk+/80zx/1MxMEACkPgO4Toe7/21w46I8uiftfmwq1/ysDfv+HACDlAbD3ZKgTs42ZHm/MErn/k6H2f3HE9X8QAKQ8APaeXAy1/x/o88Js/7d9/+fGXf8XAUDKA6A72P6/1/1/Ern/1VCf/zdmKu7/gwAg5QHQfSLW5//vcWOWJCpWpkPt/1TV/X8RAI5KygOgdGw51P4/OOKNWQIVxudi7X/N/iMABEDKA6B41+lY+z9q/xMoP7Jo/0EACIAkBUChes7+s91yA7E+Zpq2/wgAAZD2AMiPXYp1/t/n/0nUdTRUZjZnnf9HAAiAtAdA19DDoa7//95h+59EB1ZD3f9vfsL+IwAEQNoD4OiHQu3/B/z+L5HK9VD7f2pMZiIABEDaA+DgUxuRXphn+uy//d9urTND/swQAAIg7QEQbP8Xelz/L5n7H+r+fxf7/ZkhAARA2gMg2P6vHHT9/0Tu/2qo/V874s8MASAAUh4AQ32h9r+x7P6/SZTriXX+v152TBEAAiDlAfCRv/VYpP1fX7H/SZTvvxBq/9fsPwJAAKQ+AL7wxy37zzYrDIe6zGTL+38EgAAIEAChNOx/IhXHlmKd/7f/CAABIACS9cLs/H8ilSbmQu3/6kHHFAEgAARAok7Mev+fzP2vzkS6/0/zXK/v/yMABIAASNQL84I3Zonc/9pUqP1fGcw5qAgAASAAknT+f7rHC3Mi938y1P4vjrj+DwJAAAiARO3/B/q8MNv/bd//uXHX/0cACAABkKj9f6/7/yRy/6uhPv9vzFTsPwJAAAiARL0wv2fY/idQcSLU9/8aU1X3/0UACAABkKgX5gdHvDFLoMLYXKz9r9l/BIAAEADJ2v9R+59A+eHFpv0HAYAAsP/ZkhtYCbX/0/YfASAABEDCzv/7/D+Rjp6PtP/NWef/EQACQAAka//fN2T/k2j/aqj7/y1M2H8EgAAQAIna/8kB3/9PonI91P6fcpoJASAABECyXphnXP8nofsf6v4/Z4ZkJgJAAAiARO3/4hHX/7X/277/l2QmAkAACIBk7f/KATdmS6L9sfZ/7bA/MwSAABAAiTr/v+z+v0mU64l1/r9edkwRAAJAACTJ+or9T6L8wPlQ+79m/xEAAkAA2H86KgyHuv5Py/t/BIAAEAAJ+/zf/idScWwx1vl/+48AEAACIFkfzDr/n0iliblQ+3/5kGOKABAAAiBZ+/+D/pqSuP/VUPf/bZ7r9f1/BIAAEACJemGe98Yskftfmwq1/8uDLjOBABAAAiBR5/+nj3phTuT+T4ba/4VR1/9DAAgAAZCo/X9/vwuz2f9t/zObHXf9fwSAABAAiXphfu+gN2ZJ3P9qqM//G9MV+48AEAACIFEvzO8Ztv8JVJwI9f2/xlTN/X8RAAJAACTqhfnBEW/MEqgwNmf/QQAgALZx/92YPYnyw6Gu/2P/EQACQAB4/88m5PpDXf+3MW3/EQACQAAk64X5IfufSD3nI+1/c7Zi/xEAAkAAJGr/3zdk/5Nof6z7/y74/R8CQAAIgGTt/+SA7/8nUXk11P4v+5oJAkAACICEfTDb6/o/idz/eqj7/5wdkpkIAAEgABK1/4tHXP/X/m/7/l/qk5kIAAEgABK1/yv73ZgtiQ7E2v+1w/7MEAACQAAk6vz/8mv8+SRQ7mis8//1smOKABAAAiBJ1lfsfxLlB86F2v81+48AEAACwP7TUWEk1PV/Wt7/IwAEgADw+T+dFccWY53/t/8IAAEgAJL1waz9T6RSJdT9f1qXDjumCAABIAAStf+nXusvJ4n7X52OtP/Ns/9/e3ceH1d153mfWm1JBgJmM14CoSzL2qXaq0iCCSFAwDFe6O5Jg23S00+SJ3km/erpTJaerYNtwIBhMjOdpCeZdLZXYxNQValKUkmWbMlSFcaYQGIbWYYQwo4XFmNbt0ryY0JCwJZ0S1It5/7u5/1Xwn+uU6rv955zzzn1vP8PCgAFgAKg1A9zvIovjor5H46Iyv/OZvb/gwJAAaAAKLX+H6nl/B8l879FUv6f/O0nOP8PFAAKAAVAqfx/uIEHM/K/8J5ZU8aoggJAAaAAKJT/W5p4MFMx/0MRTdhXbd9qrgAGBYACQAFQJ/83u7mYTUGOQEwT92UbXMNcEygAFAAKgCr5/6CH/FeQ3ZfQBH7dhtYwtKAAUAAoAIrkPxezq8jm7siI/MId4FcMFAAKAAWA53+Mx9qwNSP0K7d/LcMLCgAFgAKgwPt/rP8rqaZXav6fHBm8nfEFBYACQAEodf7/spn8V5ErnZX7rRsZ/AIjDAoABYACUFKZlkb2/ymZ/wNZyd+7LHMAoABQACgApc3/1jr2ZCmZ/6mM7G/eyCDvAYACQAGgAJQw/zsWc/4v+V+iBrCGcQYFgAJAAShV/ne5uJhNQZZFqaz8b9/o0BraJygAFAAKQEkMd17OV0VB1tr+rCm+gENref8UFAAKAAWgFPnfRf6ryNa4LWuSr+Dg6pmMNygAFAAKAPmPd9k9nRnTfAn33VbBiBeJs7z87AtPmfPRU1zV1dWVl5/6H3NP/ZcLzisvn8EHRAGgAJiHxvy/khz+toyJvoZ7b5vFmBd6Sql89pwr6jwr//aLf//9H/zg/zzy+Cm/P/XZH/nNqf+R+D8/+MH3Nnzxi3/prV+4YHYFe4IpABQAM7z/R/6r+ZgWbNVM9UXcc+vZjHrBvk3nz13YELjp39/xLz3PDev9IrzQ/9D6Ly+90lO14PwyPjoKAAVAcv4nr+BbouIvdiiqmeyrSAMojLL5zZ9Z/V//d8tjL0xqOI483f5//8vfXu+5jJkZCgAFQGr+xxez/0/F/A9HNNN9GffcStbk2cz5nhu/+s87Dk15TN5O//DvlwUuM/sLGhQACoDI9f9ILTuwlcz/Fs2EX8c9t/ImYP7MmOdf/nf/3PPy6DRHZeRQ/w+/vip0mZn3aVAAKAAS8//hBs7/Jf9VehOQRef8OC9wy9e/v/X5fH2Nsi/3/ehb/+7KOWZ9NZACQAEQmP9bmnnXV8X8D0U0k34l9612Mv7TVu6+9d6tvz2R75+L3/f+y/8bPMeUS4YUAAqAvPzf7Ob8NQU5AjHNtF/KQU4Fnibbx/5iQ/K5Au0gfbXne/++ngJAAaAAGD//H/SQ/wqyexOaib+WQ2v4CkzD7Gvvijz9TiEH6PnkPX9lup3DFAAKgLj895L/Kj7ANXdkTP3FHOKXbapm+r7+8O53Cj5CJ/bH1y+9iAJAAaAA8PyPvLLUd5s7/0+ODq7lazAVVV/evPPV0eKM0Rt7It/0mOh1DQoABUDY+3+s/yupui9j9u/myODtfA8mrXZ9/wvF/OpkXnvsf33KNLs2KQAUAFH5/3Az+a8iVzrLt3Nk8At8EybF6bl715GRYk/VvH3gZ9eUUwAoABQAg8lEGtj/p2T+D5D/p2SZA5gMh+/7g2+NlGKgjv/+58vOpQBQACgAhsr/eC3n/yiZ/6kM38735gB4DyBXMz/x498dGy3VSJ042LnCBAsBFAAKgJz8Ty5mrzX5r3gD4Pctt8n/8CNvaaOlHKrs0e6/mkEBoABQAAyS/50u7v9RkGVRivn/Py8wD3EikP5XpuyalsxI6ccq8+z/45Q9WhQACoAQw52X87VQkLWun/z/oKE1nAo8sTJ/lzqjtWqW5ApAAaAACMn/LvJfRfamHvL/wwZXczPQBGbM2fiWQqOlxdyz5M4sUgAoAOQ/Csbh6WT9/3T7buN24HEL45y/GBxVa7he/89VYhsbBYACIGP9n/xXMv/9beT/mfbeOovvxpguWLLlmHpPFwN/PU/o9mIKAAVAQv7z/K8kZ7BV49s5hj23ns2340wVdXc8N6LieB356dUXiHwVgAJAARCQ/x0uvhEq5n8oSv7TAHKeLnLd3j+s7Isb/8Utcd2GAkABMH7+ty5m/5+K+R+OkP/jNwBWAT7soqU/PajwgL2T/JvL5a0DUAAoAEantdSxs1rJ/G8h/ydqALwJ+MHH/9p/2qv46yKv/sunxU3bUAAoAEbP/182cv4vz//Gs/c2dgO+79wbt7yp/k9N+muXCfutoQBQAAye/1uauf9HxfwPsv6vY99qTgR6j3X+1x4zxLfllR+GZI0ZBYACYOz8f9DN/b8qzugGeP9f/8Wytaxd/eHL0vQ/nzfK7033raLuB6AAUACMnf8e8l9Bdm+C/Nc3tIavyllnzVza+qZxxuzpb59DAaAAUADUyH8v+a8gW3OS839yagD82llm/8OThvqyvP7T+RQACgAFQIH838zzv5K/6fXd5H9ORgfXmv3LUvWLl4z2s5P8NAWAAkABKPkf4hbW/5W0uI/8z9HI4O3m/q58btcJ4w3ak39XTgGgAFAASpv/DzeR/ypypbj/bxIN4Asm/qpU/NdnDDloL9wnZBmAAkABMKhMpIH9f0rm/wD5PwlZE88BVHz/4KghB230jUiQAkABoACULv/jtZz/o+bzP/P/k5wDMOt7AJfF3xk16qgNP76SAkABoACUKv+TVeyhJv+FNABz/uZd1jZs5FHb+3kKAAWAAlCa9f/OK7j/R0GWKtb/pzChPLTGfLNZ1uB2Yx8VMTp4q/HXICkAFAAjzr91Xs5XQMUf9bod5P9UDK0x26nA9mt3G37UDv7juUZ/DKEAUAAMmP9d5L+SP+pNPeT/1AyuNtfNQBWf3Slg1F7/5myDL0RSACgA5D/ywuHl/L8p23ebmW4HPvtLz4gYtdf+6aPGXryhAFAAjCbD/L+a+e9vI/+nbu+ts8zyTbHM/vKQkFE78d2Fhm4AFAAKgNHyn+d/JTmD3P83LXtuPdsk+T/v28+JGbUT/6PKyK8CUgAoAAbL/46FjL6K+R+Okv80gFzyf/4dBwWN2okfeQz8AicFgAJgKFprNfv/lMz/CPk//QZgglUAYfl/SiRo3CPJKQAUAEPlf0sd5/8omf8t5H8eGoD8NwEtc6Xl/8mTsYBh3wOgAFAAjJT/v2zk/F+e/+Xae9tM4fl/6fqD8oYt7jfqYwkFgAJgoPzf0sz9PwpyBFn/z5N9q0XfcGm5+M5DEoet3UsBoABQAAqc/w96uP9Xxfz38/5/3gyulbzGNfvuwzKHLdlMAaAAUADIf/Oxe9vI//w5IPgH8Nx7jwgdtdEODwWAAkABKGT+e8l/BVmbOP8vr4bWSP2qlD/whthRy3YYchWAAkABMEj+b+b5X0WWuh7yP78Pk4Nrheb/pjcl/0AlfRQACgAFoEB/Xg+5yX8VVe0g//NsZPB2kfl//xHRwzZsxAZAAaAAGCL/H24i/1XkSnH/XwEawBckzv8fHpU9bCcM2AAoABQAA8hEG9j/R/6bRlbeHED5A4dGpQ/bcNJw7wFQACgABsj/RC3n/6iZ/8z/F2gOQNh7AGbI/5MnNcPtBaAAUADUz//OKs7/VTL/B8j/QjWA/aJ+B8s3HR41w7BlOwx2HgAFgAKgfK3u/BjjrSDLYub/C+jAWjmzXufc/4ZJRm006TXUwwoFgAKg+sJa1+UMt4Js9X3kfyENrZkhpCnOvu8N8wxbu99IxY0CQAEg/zF59uZu5v8La3B1mYj8v2jjETMNWzxgoA1LFAAKgNrz/+S/khxezv8ruH0Sbge2zLnzsLmGLRZ0UgAoABSAPMh0kv8qcvoT5H/h7b31bMPn/9z1h8w2bBHjNAAKAAVA5fzn+V/N/A9y/19R7DF6A7DMW3fIfMNmnAZAAaAAKJz/7ZWMtIr5H46S/zSAXPJ//h0HzThskaBB3gOgAFAA1F3/j1VbGGkV8z9C/hevAcwy8vO/OfPfOA2AAkABUDb/W+o4/0fJ/G8h/4vYAG4rN2z+z1130KzDFgsYYjcgBYACoGr+/7KR839VzP8Qz/9Ftfe2mQbN/znrD5l32OJ+I0xfUgAoAIrm/5Zm7v9RkCPI+n+RPb3akDdhWi6+85CZh63dCDcDUQAoAGrm/4Me7v9VMf/9vP9fdPvXGvFdmNl3Hzb3sCXdFAAKAAWA/JfD7mkj/4vvgAF/FM+574jJR23UAHcDUgAoAErmv5f8V5C1qZPzf0oyB7DGaF+V8vvfMP2oZTuUXwWgAFAAFMz/zTz/K7mqW9dD/pfmYXLwdoPl/6Y3GbWTWtJHAaAAUAAm+WfzkJv8V9GiHdz/VyIjxmoA5Q8cZsxOGVa9AVAAKADK5f8jTbz/ryJXivwv3XTy4BcMlf+jDJkRGgAFgAKgmEy0nvwn/3FGA7jdOPl/iPx/vwF4KQAUAApAzvmfqOH8HzXzn/X/Eq8CrDVI/t/P8/8HJjSV3gtAAaAAqPWc07WI83+VzP8B8r/ERvcb4rexfNMR8v+DP2ntzRQACgAFIKe63Mn9vyqyVKeZ/y+9A2vVXx07937e/z9N0melAFAAKAD6C2Zd5L+KbPV95L8K9q+ZoXhTnH0v+//P0Oa3UwAoABQA8t+Q7M3dzP+r4enVSt8NaLn47iMM0pniAQcFgAJAAZh4/p/8V5LD20H+q2LfbRUK5/+cO9n/P6ZY0EkBoABQACaQYf1fSc5AgvxXx95bz1Y2/+euP8QAjS2iZgOgAFAAVMl/nv/VzP9QjPt/VLJH1QZgmX8H+W+wBkABoAAokv9tlYyqivkfjpD/NIDc8v8ggzNRA3BQACgAFICx1/9j1RZGVcX8byH/1WsAsxTM/3nk/8SiQTsFgAJAARgr/x+p5/w/8h85NoDblNsLYJm7jvzXEQvYKAAUAArAmfn/UCPn/yu5/s/8v5L2rp6pWP7P4f0/fXG/hQJAAaAAnJ7/m5vJfwU5glHyX01Pr1HqL8Zy0Z3kfw7aPRQACgAF4LT8f9DjYETVY/e3kv+q2r9WpYfJ8zey/z8nHW4KAAWAAvDh/PeS/wqyedok5b+W7HlHUpQcUOiH8pz7OP8vN6OK3Q1IAaAAkP8Yg7WpU9L5P1r0E8GHj4maA1ijylel/H7O/89VJumlAFAAKADv/zBvZv5fRZbabZLyP9MactoaW45LepgcvF2R/N9E/k9mJspHAaAAUAD++OfwkJv8V9Gifkn3/2USAedZZ1nrYicE/aNG1GgA5Q+w/j8Zwyo1AAoABaCk+f9IE+//q8iVEpX/Hb4/1ExrVduwoH9WVoUGUP7AoVFS3agNgAJAASjlD3Osnvwn/wselN3uP37NrFd0SVrYUKABkP9TaQBeCgAFgAKQaavh/D81819UTPY1vP81s8zfLqnajAyuKXH+33+Y/J/8xKcyewEoABSA0v0wdy3i/H8l839AUv6PpGusf/63WS5OSUqs0f23lTT/N7H/b0o/fe3NFAAKgLkLgNbJ/b8qslanRa3/p1wf/vedMzAiKUsOrC3dKtq5D7xJmE9N0melAFAATFwAhrvIfxXZGnpF5X/adfq/8II+SRMcJ/evmVGab4rlgnvZ/zdlbX47BYACYNoCQP6rye7eKmr9P+U68984d6ukvQAnn15dkrsBLRffzfz/NLQGHBQACoBJC4BG/ivJ4esQlf9p11jRdXmHpPMATu67bVYJ8v/SDez/n5Zo0EkBoACYsgBkWf9XkjOQEJX/A4vGfs1hUVzSmYAn99x6dtHzfx73/05XpPQNgAJAASjFwizP/2rmfygm6f6fzPbacbaZWGsiou4FKHoDsMy/4yAJbvwGQAGgAJTgh7ltESOoYv6HI6Lyv6tp3DetrQ0Pi7obsMgNgPzPVwNwUAAoACYrAFq0hv3/SuZ/i6j8b/dMcMyUrfkhYQ2giO8BWOaR//kRDdopABQAUxUA7ZF6zv8j/wue//GJ91nZPFuOimoAtxVtL4Bl7jryP09iARsFgAJgogKgPcT9P2qu/4ua/9dierOrNu9mUQ1g3+oinQdgmcP7f/mT8FsoABQA0xQAbbOb/FeQIxgVlf+RkO77VTafrAbw9Jqi/GVZLrqT/M+jkt4LQAGgABT3h/lBj4PRU4/dF5eV/+Ec3q+2eWWtAuxfW4yvyvkb2f+f3wbgpgBQAMxRALQHveS/gmyedvPl/7v/bFlvAh4ows1AZ9/H+X/5NVLCOQAKAAWA/Dc9a2OXpPN/tGg4x/3VtuaHRZ0HsH9Nob8q5fdz/n++ZZJeCgAFQH4B0DYz/68iS+02SfmfaQ3lfL6KrTEi6UzA0cHbC5z/m8j/AvwwJn0UAAqA9AKgPdRM/quockDU/X+JwCTOV7PWtkq6F2CksA2g/AHW/wthuFQNgAJAASha/j/SyPv/KnKlROV/cnLLTNaqdkl3A2YL2QDKHzg0SlpLagAUAApAsX6YY/Xkv5r5L+r+n+7mSX7NrFeIegGigA2A/C9kA/BSACgAcgtApr2a8//I/4LHX9/kj5m0zO8dEbUKsKZA+X//YfK/cBOkJdkLQAGgABQn/7sqOf9fyfwfEJX/6eopfM0sF6UkJdvo/oL8hlZsYv9fQYtbKc4DoABQAIpSbzu5/1dF1hpZ6/8p19Q+h3MGJM0BnDywNv+rbR+5/01CurCSPisFgAIgsAAMd5H/KrI1bBeV/2nXVD+JC3slTYSc3L9mZn6/KZYL7mX/X8G1+e0UAAqAuAJA/qvJ7t4qav4/5Zp6wM3bKmkvwMmnV+f1bkDLxXcz/18ErQEHBYAfZmEFQCP/leTwtcta/3dNJ+I+1i7pPICTe2+blcf8v3QD+/+LIhp0UgAgqgBkWf9XkjMQF5X//Yum9zrEolZJZwKe3HPr2XnL/3nruP+vSCLFbQAUAApAoRdmOz/GcKmY/6GYpPt/MtvqprnNxFobEXUvQN4agGX+HQdJZpkNgAJAASjwD3NiEaOlYv6HI6Lyv7Np2m9QWxseFnU3YJ4aAPlf7AbgoABASAHQojVWRkvF/G8Rlf/tnjy8P21rfuiorAaQh/cALPPI/+KKBuwUAIgoANoj9Zz/R/4XPP/j/rw8Ndk8W0Q1gL23TXsvgGXuOvK/yFoDNgoABBQA7aEmzv9Xcv1f1Py/FsvXrKnNu1lUA9i3esY08/+S9bz/V3QJv4UCAMMXAG2zm/xXkCMg6v0/LRLO23tTNp+sBvD0mmn9BVouvIv8L4GinQpMAaAAFO6H+UGPg5FSj90XJ//HnwOQtQqwf+10Po3zNrL/X3QDoABQAAqX/17yX0E2t6jzf/Kb/+++B/CQqL0AB6bxg3o29/+UyEiHlwIAIxcAnv/VZG3sEpX/0XCe903bmh8WdR7A1OcAyrn/p2QySS8FAMYtANoW8l9JNdsl5X+mNZT3c1NsDRFJZwKODt4+1fzn/p/SGU76KAAwagHQftlM/qtooaz7f9sCBTg3zVrbKulegJHBL0wp/x9g/V98A6AAUAAKkv8tjbz/ryKXrPxPFuY1E+uidkl3A2anMgdQ/sChUVJYegOgAFAACvHDHKvj/B8181/U/T89zQWqmdaPybooefINgPxXoQF4KQAwXgHItFdz/i/5X/BY21G4YyYt83pHRK0CrJns+v9h8r/0P6QF3w1IAaAA5P9r21VpYYSUXP8Xlf/pxQX8mlkuHJCUgKP7V0/mw6pg/58axa3DbaEAwFAFQOu8nPFRkLV2QNT6f8pV2M/rnJSkOYCTB9bm/LqE5SPs/1NF0melAMBABWC4i/xXka1xu6j8T7sK/YldJGrD5Mn9a2bmOvlxD/v/lJEo6N2AFAAKAPlvBna3qPN/sqmC5/9ZlvldkvYCnHw6t7sBLZfcxf4/hbQGHBQAGKQAaOS/khy+dlnr/64ifGiWK9olnQdwcu9ts3L4R1+6gfxXSjTopADAEAUgw/q/kpyBuKj8768qzmsTVa2SzgQ8uefWs3Xzf9467v9TTKRwDYACQAHIa/5/jKFRMf9Dou7/zfTUFWmbibW2RdS9ALoNwDL/joMkrnkaAAWAApDHH+ZEFSOjYv6HI6Lyv7OpaMdMWBsfFnU3oE4DIP9VbQAOCgAULwBatJbzf5TM/xZR+d/mLeIx07bmh47KagAVE83/k/9qihZoLwAFgAKQt/x/uIHzf8n/gn/NWv1FvWbK5tkiqgHsva1s3Py/dB35r6jWgJUCAIULgPZQE/f/KLn+L2r+X4sGi3zNpM27WVQD2Ld6xjj5f8kG3v9TVsJHAYDCBeD5a7n/V0GOgKj3/7RI2Fnsj9Dmk9UAnl4z5kyd5cK7ROX/i1t2Szr56mTSTQGAugXg142Minrsvjj5P/05gC2i3gQcWjvWv/K8e0Tt/3/pjsUrd4lqAIW4GYgCQAGgAMhlc3dI2v9fmvx/9z2Ah0Q1gANj/MjOknX/z8vr5p9VseIJUVc6dngpAKAAIFfWBlHX2mvR0uT/u3sBHhF1HsD+M+YAyjeJuv/nlfXz3v1H3fyUpAaQSfooAKAAIEc1vZLyP9MadJbqk7Q1RCSdCTgyePtp+X+/qOf/Vzdc+od/VtnSvZIawHDeGwAFgAJAAZDKlRJ1/1+bv4SvmVpr4pLuBRgZ/MKH8v8BUev/B++8+I//sJk3DI3SACgAFAAKgPnyf0BU/nd6S7rNxLqoQ9LrlNkPzgGUP3BIUkwevmv2+/+0Gdc+J6sBeCkAoAAgh+d/Uff/9JT6mAnr5d2SPtCRwbVC8//Njed+YNgcS16WNLmR5wZAAaAAUADIf/Xzv7+u5MdMW+b2iXqpfHDNn9b/D0vK/3fuK//w6xthUcsbmbzuBqQAUAAoAAJZKkWt/2fTVRYFPtULBiQl5ejQ6nc/VGH7/47fd/pMkSUk6h840uGxUAAoABQAjD9dXStr/T/lUuNzPVdUrTp5YK3Dcp6s/X/HN47RhsMvSypuJzv8NgoABYACgHHYGreJyv+0S5VP9uLtkhZWTg6unnePqMfjd+4d8+9hye9ENYBE3u4GpABQACgA0tg9XaLW/1PK5P9ZlgVdw5Ki5OkdohbI37pv7Gdjx7VDkl7fONkacFAAKAAUAIz1a+drF5X/6jz/v9sArmiTdB6ALEfuGe+q4xk3iDoR6GQ0T2diUQAoABQAWZxBUff/ZHcsVuv1iqrYMaJWSQfvPmfcYZt5k6hTgU9G8tMAKAAUAAqArPwPRSXlf6an3qLWB2yta6EBqOi1O2dPMGxly54Q9QJnfhoABYACQAEQlf/hiKj8TzbbVPuIbY0Pv0PcKueVDRdNOGzlK3YJawAOCgAFgAKAD+V/i6j8b/Pa1fuQbe6HjhK4inl5/RydYatY+ZioBhDNw14ACgAFgAJA/itKy9vbznluAN4tNAC1vLRunu6wVazaKaoBtPqtFAAKAAUA76//i5r/16Ihp5oftM23mQagkhfXzc9h2CpukdUA2nwUAAoABQDvcQRiovI/Enaq+lHTANTK/zsW5DRsFatkrQIkmykAFAAKAN5l9ybI/yKuAvAmoDrz/wtyHLaKlY+LagDtHgoABYACgFOR1Nwh6fwftfP/1Mft+SUNQA0v5zT//57yFU+IutKxw0sBoABQAGBt2Coq/6Nq5/+7hesRzgNQwSvr501i2MqXiToRSEv6KAAUAAqA6VX3Ssr/TDzoVP0Tt9VHjxO/JffahjmTGraym0SdCjw8rQZAAaAAUAAkcKVF3f/X7neo/5lba+LcC1BqB++8aJLDNvOGIUl3A06rAVAAKAAUAAn5PyAq/zs9DiN86tbKDo0ILqnDd8+e9LA5P/07WQ3ASwGgAFAAzJz/KVH3/21rshvjc7dc3p0hhEvozXvOmcKwOa56RdKHoCU9FAAKAAWA/JeR//11VqN88pZLd4wQwyXzzn1lU3t9I3xE0seQ6XBTACgAFABzsixKSZr/z6arLAb69GcPjBLEJXL83qneFGUJHZb0QYx0eCwUAAoABcCErHX9otb/Uy5jff4fEfX6haHyf+M0/mqufElUcevw2ygAFAAKgOnYmraJyv+0y2gjcMk23gQszfz/dEbNvuQ5UQ0gMaW7ASkAFAAKgJE5PJ2i1v9Thsv/sywLOoeJ46J7877pvSnquHa/qNc3YkEHBYACQAEwV/7720Tlv/Ge/99tAK42TgQqtsMby6c5bDNuEHUi0MnoFM7OogBQACgAxuUMtkqafs72VRvzNYzFMU4FLq6Dd5877WGbuVTUqcAnI5NvABQACgAFwLj5H4pKyv9Md73FmANhrWvhZqBieu3O2XkYtrKbd4t6gXPyDYACQAGgABg2/8MRUfmfbLYZdShsjdwNWESvbLg4L8NWvmKXsAbgoABQACgAJsn/FlH5n/DajTsYNvdDRwnmInl5/aV5GraKVY+JagDRSe4FoABQACgA5L8CtNaAw8jDYfNuoQEUx0vr5uVt2CpW7RTVAFr9VgoABYACwPq/wfI/GnIae0Bsvs00gGJ48Y75eRy2iltkNYA2HwWAAkABkM4RiInK/0jYafQhoQEUKf8X5HXYpK0CJJspABQACoBsdm+C/FdwFYA3AQs//78gz8NWsfJxUQ2g3UMBoABQACSzNXdIOv9HRv6fGhYPewEK7OV18/M+bOXLn5B0HkC2w0sBoABQAOSy1Iu6hj4TCzllDIyt6RFOBCqkV9bPLcCwlS/7taQGoCV9FAAKAAVArMV9ovI/HnRKGRlbfZRTgQvntQ1zCjJsZTftk9QAhnNuABQACgAFwGhcKVH3/7X7HXLGxlqdOEFQF8jBOy8q0LDNvP6ApLsBc24AFAAKAAXAaPkv6gL6TJfHLml0rAuT3A5cGEfuPr9gw+b89POyGoCXAkABoACIfP4Xdf/ftiabrPGxXNaTJawL4K17zingsDmuekXSh6UlPRQACgAFgPxXO/8Haq3SRsgyZ8cocZ1379xXVtjXN0JvSPq4Mh1uCgAFgAIgLFyqRK3/Z9OLLAJH6fwBGkC+Hb+30DNFltAhSR/YaNJjoQBQACgAkhaY63aIWv9PuWSO03n9rALkOf83FuGv68oXRRW3dr+NAkABoACIYW8StbycSbukjtScHt4EzKej9xXl7+vq34pqAHHduwEpABQACoBRODydktb/R1Ji8/8sy0eTw8R23rx5b3F2ijo+s1/SeQAnY0EHBYACQAGQkf/+NlHv/8l9/n+3ASxMcCJQvhzeWF6kYZvx2T2iGkBU54wtCgAFgAJgDM5gq6Rp5WxftejhslZHORU4P16/69yiDdvMpU+KagCRiRsABYACQAEwRv6HopLyP9PdYJU9YNb6R7gZKB9evfOCIg5b2c27s+ZpABQACgAFwBD5H46Iyv+k2yZ9yGxN3A2YB69suLiow1a+YpewBuCgAFAAKAAGz/8WUfmf8NnlD5rNveUoAT5NL62/tMjDVrHqMVENIDbBXgAKAAWAAsDzf5FprQGHGYbN5t1MA5hm/q+bV/Rhq1i1U1QDiPutFAAKAAXAuPkfFLX+r0VDTnMMnM1HA5iWF++YX4Jhq7hFVgNo91EAKAAUAKNy+EW9/69Fwk6zDB0NYJr5v6AkwyZtFSDZTAGgAFAAjMnuTZD/xl0F2MKbgFOf/19QomGrWPm4rDkADwWAAkABMGSENCcz5L9xh8/NXoApennd/JINW/nyX0k6DyDb4aUAUAAoAMZjqe+WlP+ZWMhprgG0NT3CiUBT8cr6uSUctvJlv5bUALSkjwJAAaAAGM7iPlH5Hw86zTaCtvoopwJP3msb5pR02Mpu3CepAQyP2QAoABQACoDKXClR9/+1+x3mG0NrdYKbgSbr4J0XlXjYZl7/zKj0BkABoABQAFTO/wFR+d/lsZtxFK0Lk9wOPDlH7j6/5MPmvOZ5WQ3ASwGgAFAADPX8L+r+v+2NNnOOo+WjPVlCfRLeuudsBYbN8clXJX2oWoeHAkABoAAY6PlfVP6naq1mHUnLnP5RYj1nx+6bqcSw2YJvSvpYsx1uCgAFgAJgkNCoErX+n01VWkw8mucN0ABydfweVZqiJXRQ0gc7mvRaKAAUAAqAAdjqd4ha/0+5zD2e5+9gFSDH/N+ozqhZP/6iqOLW7rdRACgAFADl2Ztk7f9Pu8w+onN6eBMwF0c3KfV3ePWzohpA/EP3cFEAKAAUABU5vElZ6/+mz/+zLJclTxDvut68V62dos7PDEo6D+BkLOigAFAAKABq57+/TVT+8/z/bgNYmOBEID2HN1YoNmwzPrtHVAOIfOAsLgoABYACoB5nUNT9f9neGsb03QXl6iinAk/s9bs+otywzfzck1mhDYACQAGgAKiX/+GopPzPbG2wMqh/aAD1j3Az0ERevfMCBYet7ObdQhsABYACQAFQL/8jovK/w21jUN9ja+JuwAm8vP5iJYetfMUuYQ3AQQGgAFAAFM3/FlH5n/DZGdT3G4B7y1GCfhwvrbtU0WGrWLVTVAOIBWwUAAoABUDF/A+Jev7XYgEHg/qBBuDdTAMYL//nKTtsFbfIagBxv5UCQAGgACjHERS1/q9FQ04G9UMNwEcDGNOLd8xXeNikNYB2LwWAAkABUC7//aLe/9ciYfL/jAbAKsCY+b9A6WGrWPWYqAaQdFMAKAAUALXYPW3kv/xVgId4E/DM+f8Fig9bxcrHJTWA0XfvBqQAUAAoAOqwNok6/0+Lkv9jNgA3ewFO8/K6+coPW/nyX0k6ESjb4aUAUAAoAOqw1PWIOv8/xvr/OA2gqYUTgT7olfVzDTBs5Z/7jaQGoCV9FAAKAAVAGVWy7v9LBMn/8RpAXYxTgf/stQ2XGGLYym58WlIDGE7+LQWAAkABUIQrJSr/O3zs/xt/sWdx2zDB/0eH7rrQIMM247pnJN0NeOJ3hygAFAAKAPmf//zf6ub8n4kagKuT24Hfc+Tu8wwzbM5rfi+pAYyOUAAoABQANfJf1P1/vY2c/zshy4JtWcL/lLfvOdtAw2b/xGsMGQWAAkAByHP+D4jK/1QN9//oNYBLBkb5vT95bNMMY03dBN9i0CgAFAAKQD7DYLGo+f9saiFjqu8jAyOm/70/fo/FaH+rodeJaQoABYACkDe2+j5R6/9pF2Oai9k7MmbP/43GGzXrJ15g6oYCQAGgAORrXbG5W9T8P/mfq0u7zf0m4NubDPn3+qlnmbqhAFAAKAB54fAmZa3/k/85Tydf1nHCxD/2b9xrzJMinNcN0gAoABQACkA+fk0CCVn5z/r/JBpAZdy8JwId2jjLoMM248bf0AAoABQACsD08z8YkzQNnNley5hOgrUmatZTgV+/6yOGHbaZn/sVmzgpABQACsB08z8cFZX/WxvZ/ze5BlD/iDlvBnp1wwUGHray5btpABQACgAFYJr53yIq/zvcnP8zSbYmU94N+PL6Sww9bOUrd9EAKAAUAAoA+f9+/sd9nP87+Qbg2XLUdL/zL6271ODDVrFqJw2AAkABoABMPf9DEUn5r8WC3P8zlQbg3XzUdPk/z/DDVnELDYACQAGgAEyVIyhq/V+Lhrj/d2oNwGeyBvDiHfMFDBsNgAJAAaAATDn//a2i8j8SJv+n3ABMtQrw4h0LRAxbxSreA6AAUAAoAFNh97SR//jTKsBD5nkT8KV1C4QMW8VK9gJQACgAFIDJszZ1Sjr/R4uS/9NqAO6HzXIewMsC1v//pHz5rzgRiAJAAaAATJKlrkdS/mdirP9PswE0tpijAby6fq6gYSv7HGcCUgAoABSASVrUL+r+v0SA/J9uA6iLmeFegNc3XCJq2GbeyL0AFAAKAAVgUlwpUfnf4WP/3/QXhRa3DYv/fT9014XChm3Gdc9yOzAFgALAqJg1/7Nb3Zz/k48G4OqUfjvwGxvPEzdszk+9QG5TACgAyD3/Rd3/19vA+b95YVmwXfZL5W/fO0vgsNk/8TrBTQGgACDH/B+QlP8j6Rru/8lXA7h4QPJ08vFNM2RO3YTeIrkpABQA5PIjX50Wtf6fcjGm+XPugNxXyo5vtEj9mw6+xnsAFAAKAHTZGnpF5X+a/M+rC/oyYvNf8F/1J35PA6AAUACgt17YvFXU+j/P//k2t1vmm4Bvb7JI/ru+5hl2A1IAKACYkMPbISr/ef7P/3Ty5R0SzwN4494ZoofNed3TNAAKAAUAE/1KBBKi8n+gkjHNO2tl/Li43/VDG2cJH7YZN3ImIAWAAoAJ8j8UkzS9m9lea2FQC9AAaiLSTgV+/a7zxA9b2ed+xc1AFAAKAMbL/3BEVP53NbL/rzANoOERWXcDvrj+QhMMW/nyx2kAFAAKAMbJ/xZR+d/u4fyfArE1/9shQV+Vfd+4yBTDVr7yMRoABYACABPkf9zP+b+FawA1//NVMV+VPX/zEZMMW8WqnTQACgAFAGfmf0jU/L8WC3L/TyFdcZ+QBpB59LbzTDNqFbfQACgAFACczhGMisr/SIj7fwvcAP7zfhH537N0lolGjTkACgAFAKez++Oy8j9M/hfahf/fkPG/KUcTN5SZatQqVu2iAVAAKAD4AJunnfzHJF2w1vD7yo7+W6DMZKNWsWI3DYACQAHA+6yNnZLO/9Gi5H9RnH2zwfeVvbO5yXw7RcpvfpITgSgAFAD8kaV2m6T8z7Sy/l8kZTemjTxzdPCBRWbcKVr2uT00AAoABQDvqewXdf9fIkD+F8tMT9ewYb8ph++71KSj9tlB7gakAFAA8C5XSlT+J33s/yvi6lF1q1HnAA7+40VmHbUZn/ktDYACQAGAtPzPdrs5/6eoDeDidYacAxh9duk55h0159UvkuUUAAoAXClR9//1NXD+b5FVLH3DeF+UkfbLTT1RZP/4QcKcAkABMH3+D4jK/3Q19/8Une2ytOG+Kf800+QXRVpDb5LmFAAKgLl/BWpkrf+nXIxpKZTffdxIa8rZl6/lnmhL8FXeA6AAUADM/OjW0Csq/9Pkf4nMXLnXOG8CHG3/KPl/6q//k8/TACgAFADTsru3ipr/5/m/hHNJdb80yJRy9uVvnct4vctxzQHOA6AAUADM+vfv65C1/k/+l9Kc77xghOmkd9Kfnclgvcd5/T4aAAWAAmDOv/5AXFT+DyxiTEtq1l+k31H9WzLy6k8b2Sby55WbG39NA6AAUADMmP+hmKT7fzLb6ljXLfWSUvjHz6ndKY89/p/mMk4fULbsV9wMRAGgAJgv/8MRUfnf1cT+v9K7YG376+o+Umae/ddrmP7/sPLlj9MAKAAUANPlf4uo/G/3MLGrhNr/9tgxRb8kR9r/5hIG6IwGsPIxGgAFgAJA/hs4/+N+zv9XRMV1//w7FRPlxK82uPmSjDVgq3bSACgAFAAz5X9I1Py/Fgvy066Oi9fE3lBtg/noaz+98SMMzdgN4BYaAAWAAmAejmBUVP5HQtz/q5TGbzyu1psAw91frmRYxp8DYBWAAkABMAu7Ly4r/8Pkv2JmXXPvIXW+ISO9/4kpookbwC4aAAWAAmAKNnd7hvxHgdcB/jJ+WJFvyP71gXIGZOIGsGI35wFQACgAJmBt7BKV/1HyX83vWd3Knx1R4AsydOenLmQ09JTf/CQNgAJAAZCvdruk/M+0sv6vKvvHbv7FGyX+fjxz51UXcUBUDsqW7qEBUAAoANJVDoi6/68tQP6ry/HRzzzw+xJ+PV657+MXcj5UbmZ+dj93A1IAKACyuVKi8j/p5eUutWcBLvzE/3qpRN+O39975QUcD5WzGZ/5LQ2AAkABkJ3/ou7/6W62M6aqV4DzPN95rgRfjsF/bPwI8T+p+ZqrXyTfKQAUAPLfIPnfV88vvAFYyxf+9yLPAhzrWzu3jMn/yXa1Kw8R8BQACoBUCwdE5X+6mte7jMHiuGBFyztFmmAezTx73ydmUg2nUtVCb5DwFAAKgMy/7hpZ6/8pF39nRnLpF7YdHS50CRg5fvCHS2fxYU+1qYVe4T0ACgAFQCBbw3ZR+Z8m/w2n4c7dh44XLmGyR1/t/puz+Zin8ytx1fM0AAoABUAcu1vU+T9Znv8Naea13/v1qycKMvP/9u/7v7WQdf9pcnz6AOcBUAAoANL+rn3tstb/yX+jOv/f/fyp3x7M620Uo8deGUpvDM3kw50+5/X7aAAUAAqArL/qQFxU/vdX8TdmYBd+5lsP7dz/eiY/4f/Snu0//HKAqf98TdLc9GsaAAWAAiAp/0MxSff/ZbbV8f6/0c377Lcf7Nv3+vRWnE+8+GTXv3zl47P5OPOobNkT3A1IAaAAyMn/cERU/nc2sdQrwseWfusXPXtfPzalr8HBF3Ynvv/VJRfzMeZb+fLHaQAUAAqAmPxvEZX/bR7O/5Nj8fJv3PPjlq2/OnAk19B559W9j3Zs+dG3vnLNXD6+wjSAlY/RACgAFADyXz1a3M/5/8LMWND42Vu/ufH/tmx78rlXx4+e4d8/s6vr337w37+26uqaizjpp4AqVu2kAVAAKAAS8j8kav5fiwbJf6FmV4aWrv7Sf/vOd75z709+8pOfRbf/Qde/nfo/P7nj1H/9xy98/jPe+RzzU4wGcAsNgAJAATA+R0DU+39aJMz9vyaYg16wYMFldVf+gd916v8s4K3PYs8BsApAAaAAGJ3dFyf/AUy2AazcRQOgAFAADM3mFnX+D/kPFKsBrHiC8wAoABQAA7M2iDr/V4uS/0CRlN/8FA2AAkABMK6a7ZLyP9MaJP+BYilbupcGQAGgABjVQln3/7YFyH+geGbesJ+7ASkAFABjcg2Iyv9OL/v/gGKace1zNAAKAAXAkPmfEnX/T08z5/8BxeVY8hKZTwGgAJD/Jc7/HfUc/QYUm/3Kw4Q+BYACYDSVsvI/vZiTYIDis4aOkPoUAAqAsf5qa2Wt/6dc/E0BpWAJv8x7ABQACoCB2Bq3i8r/NPkPlOrX5Krf0QAoABQAw7C7RZ3/k+X5Hygdx6eHOA+AAkABMMrfq69d1vo/+Q+UkPMGTgSiAFAADPLXGozLev+/ir8noJRm3sSpwBQACoAh8j8UlXT/X6annvf/gdIqW/YEdwNSACgA6ud/OCIq/zub2f8PlFr5isdpABQACoDy+d8iKv/bvJz/ByjQAFY+RgOgAFAAyP/i0Vr9nP8PqKBi1U4aAAWAAqBy/odEzf9r0SD5DyjSAG6hAVAAKADqcgRiovI/Eub+X0CdOQBWASgAFABV2X1x8h9AoRrAyl1ZCgAoACqyuTsy5D+AgjWAFU+MUABAAVCPtWGrqPyPkv+AYsqXmf1EIAoABUBJNb2S8j/TGiT/AdWU3WTyU4EpABQAFbnSou7/a2P/H6CgmTcMjVIAQAFQK/8HROV/p5f8B1Q041pT3w5MAaAAKJj/KVH3//Q0cf4foCbHkpcpAKAAkP8Fyv/+Oit/QoCibOEjFABQABRhqUxJmv/Ppqu4/w9Q+BcndJgCAAqAEqy1/aLW/1Mu/n4ApRtA+OVRCgAoAArMxzVuE5X/afIfUP1XZ8lzoxQAUABKze7pFLX+z/M/oD7HtUMjFABQAEr8d+hvE5X/PP8DRjDjBnOeCEQBoACowxlslXT/T3bHYv52ACOYudSUpwJTACgA6uR/KCop/zM99bz/DxhD2c1PZCkAoACULP/DEVH5n2y28acDGET5ChPeDkwBoAAok/8tovK/zcv5f4BxVKx8LEsBAAWA/J82rTXA+f+AoRrAqp1ZCgAoACXI/5Co+X8tGuL+X8BgDeAWszUACgAFQAWOQExU/kfC5D9gvDkAk60CUAAoAAqwexPkP4BSN4CVj2cpAKAAFJOtuSND/gMotfLlT4xQAEABKB5L/VZJ+Z+Jsf4PGLUBLDPTiUAUAApAyVX3isr/eJD8B4yq7KZ9IxQAUACKxJUWdf9fu5/9f4Bxzbz+wCgFABSA4uT/gKj87/SQ/4CROT/9u1EKACgAxcj/lKj7/7Y1cf4fYGyOq16hAIACQP5PMv/766z8uQAGZwu9QQEABaDALItSkub/s+lF3P8HCPhlCh2iAIACUFDWun5R6/8pF38rgIjfpitfGqUAgAJQOPamHlH5nyb/ASHsVz83SgEABaBQHJ5OUev/PP8Dcjiu3T9CAQAFoEB/X/42UfnP8z8gyYzP7hmhAIACUAjOYKuk+3+yfdX8nQCSzFz65AgFABSAAuR/KCop/zPdDbz/D8hSdvPuLAUAFIC85384Iir/k802/kwAYcpX7MpSACgAFIB853+LqPxPeDn/D5CnYtVjWQoABYACwPP/uLTWAOf/AyIbwPKeYxQACgAFII/5HxS1/q9FQ9z/C8g0Y0nsGAXA3AL/WzX/OM+4n6YjEBOV/5Ew+Q9IZf946zEKgKmVX6qaC40752z3Jsh/AEb5xQrHj1MAgHywNScz5D8Aw/xmBdtPUACA6bPUd0vK/0yM9X9AegPwJYcpAMC0Le4Tlf/xIPkPiG8Azd0aBQCYJldK1P1/7X72/wEmaAD1vRkKADC9/B8Qlf9dHs7/AczAWpUaoQAA03r+F3X/3/ZGzv8FTNIAPraLAgCQ/3/M/4FaK2MKmIRl7m8oAMBU/36qRK3/Z9OV3P8HmMjcX41SAIApzaDV7RC1/p9yMaaAqZ5hPvpolgIATJ69qUdU/qfJf8BsDaByh0YBACbL4RV1/t8Iz/+A+Vhrtg1TAIBJ5r+/TdT7fzz/A6ZsAI1dJygAwGQ4g62SZs6yvTWMKWBGNm/HcQoAMIn8D0Ul5X9mawP7/wCTNoBA4hgFAMg5/8MRUfnf4eb8H8C0DSAcP0YBAHLN/xZR+Z/wcf4vYF72K2PHKACACZ//tViA+38AUzeAj4tpABQAFJQjKGr9X4uGuP8XoAFQAADd/PeLev9fi4TJf4AG0HqMAgDo/J1428h/ANJ+2cKJ4xQAYCK2JlHn/5H/AN77bQu2n6AAAOOz1PdIyv9MjPV/AO81AF/nMAUAGFfVDlH5Hw+S/wD+2ACauzUKADAOV0rU/X8dPvb/AfgTa11vhgIAmCH/uzyc/wPgAw2gKjVCAQDGzH9R9/9tb+T8XwAfZLn8cQoAMEb+D4jK/1Qt9/8AOK0BzN1DAQBO/7tYLGr+P5uqZEwBnGHuE6MUAOCDbPV9otb/0y7GFMAYzzqXPZqlAAB/Zm/uFjX/T/4DGKcBVO7QKADAnzi8SVnr/+Q/gHFYa3uGKQDAe5z+hKz8X8iYAhi3ATR1nqAAAH/I/6Co+/+yvTWMKYDx2bztxykAwKn8D0cl5X9mayP7/wBM2AACiWMUAMAZjojK/w435/8A0GkA4dZjFACQ/y2i8j/h4/xfAHrsV8aOUQBg8vwPiXr+12IB7v8BkEMD+LghGwAFAHnjCIpa/9eiIe7/BSC3AVAAkLf894t6/1+LhMl/ALk2AAO+B0ABQL6+/5428h+AWX8Bw4njFACYk7WpU9L5P1qU/AcwCbZg+wkKAMzIUtcjKf8zMdb/AUyuAXg7hykAMKGqHaLu/0sEyH8Ak2wATT0aBQCm40qJyv8OH/v/AEyWtc5YN6FTAED+nya71c35PwCm0AAWpUcoADBZ/ou6/6+3gfN/AUyF5fLdFACYKv8HROV/uob7fwBMsQHM3UMBgHm+79Wy1v9TCxlTAFM2d/cIBQDmYKvvE5X/aRdjCmAaz0SXp7MUAJiBvblb1Px/ivwHML0GsKhPowBAPoe3g/wHgA+w1vUMUwAgnTOQkJX/lYwpgGk3gKbOExQACM//UEzS/T+Z7bWMKYDps3nbj1MAIDr/w1FR+d/VyP4/AHlpAMHEMQoAJOd/i6j8b/dw/g+APDWAcOsxCgDIf2Pkf9zH+b8A8sX+8dgxCgCE5n8oIin/tViQ+38AmKoBUAAwJY6gqPV/LRLi/l8A5moAFABM6Zvtb5WV/2HyH0CefyevVPw9AAoApsDmaSP/AWDiBhBOHKcAQBZrU6ek83+0KPkPoBDPSoH2ExQASGKp2yYp/zMx1v8BFKYBeDuHKQAQZFG/qPv/EgHyH0CBGkBTj0YBgBiulKj87/Cx/w9AoVhr1b0xnQIAU+d/ttvN+T8ACtgAKh8dpQBASP6Luv+vt4HzfwEUkuWyJygAEJH/A5LyfyRdw/0/AArcAObuoQDA+HNZ1WlR6/8pF2MKoODmPj5CAYCx2Rp6ReV/mvwHUIw5gMuVXDulACBn9uatotb/ef4HUKQGUNWrUQBgXA5fh6j85/kfQLFY63oyFAAYlTOQEJX/A5WMKYCiNYCrXqAAwKj5H4pJuv8ns73WwqACKJrGX1MAYND8D0dE5X9XI/v/AFAAAP38bxGV/+0ezv8BQAEAzJb/cT/n/wKgAAC6+R8SNf+vxYLc/wOAAsCoQI8jGBWV/5EQ9/8CoABQAKDH7ovLyv8w+Q+AAkABgB6bp538BwAKAEzG2tgl6fwfLUr+A6AAUACgy1K7TVL+Z1pZ/wdAAaAAQF/lgKj7/xIB8h8ABYACAF2ulKj8T3rZ/weAAkABgMnyP9vdzPk/ACgAFADkkP+i7v/ra+D8XwAUAAoA9PN/QFT+p6u5/w8ABYACAD3WGlnr/ykXYwqAAkABgB5bw3ZR+Z8m/wFQACgA0GV3bxU1/8/zPwAKAAUA+hy+Dlnr/+Q/AAoABQC6nIG4qPzvX8SYAqAAUACgm/+hmKT7fzLb6nj/HwAFgAIA3fwPR0Tlf2eTlUEFQAGgAEA3/1tE5X+7h/P/AFAAKAAwW/7H/Zz/D4ACQAGAbv6HRM3/a7Eg+Q+AAkABgB5HQNT7f1okzP2/ACgAFADosfvi5D8AUABgMjZ3e4b8BwAKAMzF2tglKv+j5D8ACgAFAPpqtkvK/0xriPwHQAGgAEDXwgFR9/+1Bch/ABQACgB0uVKi8j/pZf8fAAoABQA55L+o+396mjn/DwAFgAIAs+X/jnobYwqAAkABgJ6FsvI/vZj7/wBQACgA0GOtkfX+X8rFmAKgAFAAoMfWuF1U/qfJfwAUAAoAdNndos7/yfL8D4ACQAGAPoevXdb6P/kPgAJAAYAuZyAuKv/7qxhTABQACgB08z8k6v7fTE8d7/8DoABQAKCb/+GIqPzvbLIyqAAoABQA6OZ/i6j8b/Ny/h8ACgAFACbLf63Vz/n/ACgAFADo5n9I1Py/Fg2S/wAoABQA6HEERL3/p0XC3P8LgAJAAYAeuy9O/gMABQAmY3N3ZMh/AKAAwFysDVtF5X+U/AdAAaAAQF/Ndkn5n2kNkv8AKAAUAOhypUTd/9fG/j8AFAAKAHLI/wFR+d/pJf8BUAAoAMjh+V/U/T89zZz/B4ACQAGA2fJ/R72NMQVAAaAAQIelUlb+p6u4/w8ABYACAD3WWlnr/ykXYwqAAkABgB5b4zZR+Z8m/wFQACgA0GX3dIma/+f5HwAFgAIAfQ5fu6z1f/IfAAWAAgBdzqCo+3+yOxYzpgAoABQA6OZ/KCop/zM99bz/D4ACQAGAbv6HI6LyP9nM/n8AFAAKAPTzv0VU/rd5Of8PAAWAAgCT5b/WGuD8fwAUAAoAdPM/JGr+X4ty/y8ACgAFALocgZio/I+EyX8AFAAKAPTYfQnyHwAoABQAk7E1d2TIfwCgAFAAzMXasFVU/kfJfwAUAAoA9FX3Ssr/TJz3/wBQACgA0OdKi7r/r83P/j8AFAAKAPTzf0BU/nd6yH8AFAAKAPTzPyXq/r9tTZz/B4ACQAGA2fK/v87KmAKgAFAAoMOyKCVp/j+bruL+PwAUAAoA9Fhr+0Wt/6dcjCkACgAFAHpsTdtE5X+a/AdAAaAAQJfd0ylq/Z/nfwAUAAoA9Dn8baLyn+d/ABQACgD0OYOtku7/yfZVM6YAKAAUAOjmfygqKf8z3fW8/w+AAkABgG7+hyOi8j/ZbGNQAVAAKADQzf8WUfnf5uX8PwAUAAoATJb/WmuA8/8BUAAoANDN/5Co+X8tGuL+XwAUAAoA9DgCMVH5HwmT/wAoABQA6LF7E+Q/AFAAKAAmY2vuyJD/AEABoACYi6W+W1L+Z2Ks/wOgAFAAoK+6T1T+x4PkPwAKAAUAulwpUff/tfvZ/weAAkABgH7+D4jK/y4P5/8AoABQAJDD87+o+/+2NXH+LwAKAAUAZsv/gTorYwqAAkABgA5Llaj1/2x6Eff/AaAAUACgx1q3Q9T6f8rFmAKgAFAAoMfe1CMq/9PkPwAKAAUAuhyeTknr/yM8/wOgAFAAkEP++9tEvf/H8z8ACgAFAPqcwVZJ9/9k+6oZUwAUAAoAdPM/FJWU/5nuBvb/AaAAUACgm//hiKj8T7o5/wcABYACAP38bxGV/wkf5/8CoABQAGCy53+tNcD9PwAoABQA6OZ/UNT6vxYNcf8vAAoABQB6HH5R7/9rkTD5D4ACQAGAHrs3Qf4DAAWAAmAytuZkhvwHAAoABcBcLPXdkvI/E2P9HwAFgAIAfYv7ROV/PEj+A6AAUACgy5USdf9fu5/9fwAoABQA6Of/gKj87/Jw/g8ACgAFADk8/4u6/297I+f/AqAAUABgtvwfqOX+HwAUAAoA9FiqRK3/Z1OVFgYVAAWAAgAdtrodotb/Uy7GFAAFgAIAPfamHlH7/9LkPwAKAAUAuhxeUef/jfD8D4ACQAFADvnvbxP1/h/P/wAoABQA6HMGRd3/l+2tYUwBUAAoANDN/3BUUv5ntjaw/w8ABYACAP38j4jK/w435/8AoABQAKCf/y2i8j/h4/xfABQACgB08z8k6vlfiwW4/wcABYACAD2OoKj1fy0a4v5fABQACgB0898v6v1/LRIm/wFQACgA0GP3tpH/AEABoACYjLVJ1Pl/WpT8B0ABoABAl6VO1vn/Mdb/AVAAKADQVyXr/r94kPwHQAGgAECXKyUq/zt87P8DQAGgAMBs+b/Vzfk/ACgAFADkkP+i7v/rbeT8XwAUAAoA9PN/QFT+p2q5/wcABYACAD2WxaLm/7OphYwpAAoABQB6bPV9otb/0y7GFAAFgAIAPfbmblHz/+Q/AAoABQD6HN6krPV/8h8ABYACAF3OQEJW/rP+D4ACQAGAfv4HY5Lu/8n01jCmACgAFADo5n84Kir/tzay/w8ABYACAP38j4jK/w435/8AoABQAKCf/y2i8j/h4/xfABQACgB08z8k6vlfiwW5/wcABYACAD2OoKj1fy0a4v5fABQACgB089/fKir/I2HyHwAFgAIAPXZPG/kPABQACoDJWJs6JZ3/o0XJfwAUAAoAdFnqeiTlfybG+j8ACgAFAPoW9Yu6/y8RIP8BUAAoANDlSonK/w4f+/8AUAAoADBZ/me3ujn/BwAFgAKAHPJf1P1/vQ2c/wsAFADo5/+AqPxP13D/DwBQAKDHUp0Wtf6fcjGmAEABgB5bfa+o/E+T/wBAAYAue/NWUfP/PP8DAAUA+hzeDvIfACgAFACTcQYSovJ/oJIxBQAKAHTzPxSTdP9PZnuthUEFAAoA9PI/HBGV/12N7P8DAAoA9PO/RVT+t3s4/wcAKAAwW/7H/Zz/CwAUAOjmf0jU/L8WC3L/DwBQAKDHEYyKyv9IiPt/AYACAD12f6us/A+T/wBAAYAem6eN/AcACgAFwGSsjZ2Szv/RouQ/AFAAoMtSu01S/mdaWf8HAAoA9FX2i7r/LxEg/wGAAgBdrpSo/E/62P8HABQAmCz/s91uzv8BAAoAcsh/Uff/9TVw/i8AUACgn/8DkvJ/JF3N/T8AQAGAHmtNWtT6f8rFmAIABQB6bA29ovI/Tf4DAAUAuuzuraLW/3n+BwAKAPQ5fB2i8p/nfwCgAECfMxAXlf8DixhTAKAAQDf/QzFJ9/9kttVZGFQAoABAL//DEVH539XE/j8AoABAP/9bROV/u4fzfwCAAgCz5X/cz/m/AEABgG7+h0TN/2uxIPf/AAAFAHocwaio/I+EuP8XACgA0GP3xWXlf5j8BwAKAPTY3O3kPwBQACgAJmNt7JJ0/o8WJf8BgAIAfbXbJOV/ppX1fwCgAEBf5YCo+//aAuQ/AFAAoMuVEpX/SS/7/wCAAoAc8l/U/T/dzZz/AwAUAJgt//vqOf8XACgA0LVwQFT+p6u5/w8AKADQY62Rtf6fcjGmAEABgB5bw3ZR+Z8m/wGAAgBddreo83+yPP8DAAUA+hy+dlnr/+Q/AFAAoMsZiIvK//4qxhQAKADQzf9QTNL9P5ltdbz/DwAUAOjmfzgiKv87m6wMKgBQAKCb/y2i8r/dw/l/AEABgMnyX4v7Of8fACgA0M3/kKj5fy0aJP8BgAIAPY6AqPf/tEiY+38BgAIAPXZfnPwHAFAATMbmFnX+D/kPABQA5MDaKOr8Xy1K/gMABQD6arZLyv9Ma5D8BwAKAHQtlHX/b1uA/AcACgB0uQZE5X/Sy/4/AKAAQD//U6Lu/+lp5vw/AKAAwGz5v6PexpgCAAUAehbKyv/0Yu7/AwAKAPRYa2Wt/6dcjCkAUACgx9a4XVT+p8l/AKAAQJfdLer8nyzP/wBAAYA+h69d1vo/+Q8AFADocgbikvL/5KG/bAQATNtfDKn2+37i82R2XvM/JOr+35MnX/v1UwCAaRs6rtrv++9uJLTzmf/hiKz8BwAINXQdqZ3P/G8h/wEAFADyHwAACoD49X/m/wEABvH4JwnufO3/C8TIfwCAQSTdJHd+2H1x8h8AYBSJRqI7L2zujgxfJwAABcBcrA1byX8AAAXAbGp6yX8AgIH86xWEdx64Ulm+SwAAA/nuHNI7D/k/QP4DAAzlgUuI7zw8/zP/DwCgAJD/AABQAISzVLL+DwAwnK9aiPDp7f+r7Sf/AQBGM/JlInx65/80biP/AQCGc2AZGT6t8389naz/AwCMZ+cSQnwaHP528h8AYECJZlJ86pxB7v8BABjSjy8jxqee/6Eo+Q8AMKRNs8nxKed/OEL+AwAMKfsP5PjU87+F/AcAGNPhLxLk5D8AwHT2sgtwyuv/zP8DAAxr4CqifGr7/wIx8h8AYFiRWrJ8Suf/eBPkPwDAuH50KWE+lfN/mzs4/wcAYGD3zCTNJ8/SsJX8BwAY2Jt/T5pPQXUv+Q8AMLKhvyLNJ8+V5v4/AICh9X+SOJ98/g+Q/wAAY9u8gDyfdP6nmP8HABjcd20EOvkPADCbV/4DgT7J9/8XpZj/BwAY3e7riPRJsdb1k/8AAMP75RwyfVLn/zX1kP8AAMM7fi+ZPqnz/z2drP8DAIxv6K8J9cnkv7+N/AcACNDXSKrnzhls5f4fAIAAmZ/PINZzz/9QlPwHAEjw2jeJ9dzzPxwh/wEAIjx1Nbmee/63kP8AABFGO2YT7OQ/AMBs3r6fYM/5/T/W/wEAUjz3eZI9x/1/gRj5DwCQYvdCoj238/+8CfIfACDF0e85yPZc2JqTnP8DABDj+eVkey4s9d3kPwBAjOx29gDkZHEf+Q8AkOOtdWR7Llwp7v8DAAjygp9wzyX/B8h/AIAgmQ4n6Z7L8z/z/wAASd76EulO/gMATOcVXgHUf/+/ivV/AIAsmR+S73qsdTvIfwCALG9/ioDXO/+vqYf8BwAIkyLg9c7/93D+HwBAmiz3AOnlv7+N/AcASPP0LCJ+Qs5gK/f/AACkyXyRiJ84/0NR8h8AIM6TF5DxE+Z/OEL+AwDk+YqdkJ8w/1vIfwCAPM8stJDyPP8DAMzm22Wk/ATv/wdZ/wcASPR0DRMAE+3/4/1/AIBI32AP4ATn/3kT5D8AQKLn3VZyfjy2Zs7/AwDItIk9gOPf/1ffTf4DAET67RL2AI5r8Q7yHwAg0zomAMblSnH/HwBAph0+3gAYN/8HyH8AgEzHv1RO0I/7/M/8PwBAqF9UEvTjPv+T/wAAoZ5ZygLAOO//L2b9HwAg1ciG84n6sff/1/eR/wAAoUZ7gkT92Of/NbP/HwAg1pG/c5L1Y57/7+X8PwCAWNrPeQNwnPt/EuQ/AECsvSvI+rE4g9z/BwCQ6+1NvAE4Zv6Ho+Q/AECskd4wYT9m/kfIfwCAXC98jTcAx8z/FvIfACDXG9+9mLQfI/9DPP8DAAQ7Ea8n7cd4/z/I+j8AQLDRPdeT9mPt/+P9fwCAZK9+1UHcn3n+n6eN/AcACPbOD2cT92ewNnVy/g8AQLBsqoq4P/P+v7oe8h8AINmL1xH3Z6rawf1/AADJhr86g7g/gytF/gMARPvxOcQ9+Q8AMJvBC4n7MfKf9X8AgGhvX0ncn5n/A+Q/AEB2/t/ECQBnvP+/mPl/AIBs2n8sI/BPY6vvI/8BAKK98/WzCfzTz/9r7mb+HwAg2rE7OAHwjPP/vUnyHwAg2ts/uYzAP/3+30CC/AcAiHbiZy4C//T8D8a4/wcAIDv/uzwE/un5H46S/wAA0Y4nP2kn8U/P/xbyHwAgWrb/WvKf/AcAmC3/d39+Fol/Wv6HIuQ/AID8N9v+vyDr/wAA2Y73k/9n5L+/lfwHAIh2Inkt+X/6+b+eNvIfACA7/7t4//901qZOzv8BAIj29s885P/p9//VbSP/AQCiHfsJ5/+dYVE/9/8BAER75w7O/z+DK0X+AwBE077O/X/kPwDAbN7+j2eT92fmP+v/AADZ+X9TGXl/Rv4PkP8AANEGr3SQ96fv/6tOM/8PAJBs+McXkvdnnP/T0Ev+AwAEy7741XPI+9PZm7cy/w8AEOyd1HUzyPszzv/3dpD/AAC5Rl/9YRVxf+b9v4EE+Q8AkOvEnq+y+3+M/A/FuP8HACDXG/Hreft/jPwPR8h/AIBYIy98t560Hyv/W8h/AIBYb/d+7WLSnvwHAJiKtndT2Enaj7X+z/w/AECq0SM/X3E+YT/W/r9glPwHAAg10vN3lWT9mOf/+OPkPwBAqGc2BJn9H/v8X087+Q8AkOn4L5Yy+z/O/T+NnZz/AwCQaceXKq1E/ZgstdvIfwCASL9d5ysn6cdROcD9fwAAiZ7ftOQCHv/H40qR/wAAgZ7+hvsCOzlP/gMAzOSZb9fM4ul/ovxn/R8AIE3mya8sLLOQ8hPk/wD5DwAQJvv0Fy+wE/8T7f+rYf4fACDs4f9o6vOziPiJz/9p6CX/AQCS0v+tV360jIDXO//XvZX5fwCAGNm3Xuj40mzyXff+H18H+Q8AkOLo89vX+TnyX58zECf/AQAijL793O7vLefhP6f8D8W4/wcAIEDmtac67v/8QgfZnlP+hyPkPwDA8I4P9f38m1fz7J97/reQ/wAAg3tl9y/v/evGGcQ6+Q8AMIk3h/o3f/c/XDeHTJ/c+j/z/wAAY8oe3jsQ+dE9f/9Xn1xgI9Enuf8vwPt/AADDGTmwM/HjTf/wxWVX1V46kzSfwvk/vjj5DwBQ3onfDQ0NPZ5MJBL/+t0HHnjgq19etqT5Ml73m/r5v+52Sfv/tdavrAYASPT5G6+77rpPuhsbG6+Yc8kll3C5zzTv/2nsEpX/0TCnPgEAoKt2u6T8z7SGyH8AAHQtHJB0/1+mLUD+AwCgy5USlf9JLwc/AgCQQ/5Lmv/P9jTbGVMAAEyW/zvqOQICAABdC2Xlf3oxO0IAANBjrZG1/p9yMaYAAOixNW4Xlf9p8h8AAF12t6jzf7I8/wMAoM/ha5e1/k/+AwCgyxmIi8r//irGFAAA3fwPibr/N7Otjvf/AQDQzf9wRFT+dzZZGVQAAHTzv0VU/rd5Of8PAACT5b/W6uf8fwAAdPM/JGr+X4sGyX8AAPQ4AqLe/9MiYe7/BQBAj90XJ/8BADAZm1vU+T/kPwAAObA2iDr/V4uS/wAA6KvZLin/M61B8h8AAF0LZd3/28b+PwAA9LkGROV/p5f8BwBAP/9Tou7/6Wnm/D8AAMyW/zvqbYwpAAB6KmXlf3ox9/8BAKDHWitr/T/lYkwBANBja9wmKv/T5D8AALrsHlHn/2R5/gcAQJ/D1y5r/Z/8BwBAlzMo6v6f7I7FjCkAALr5H4pKyv9MTz3v/wMAoJv/4Yio/O9sZv8/AAD6+d8iKv/bvJz/BwCAyfJfaw1w/j8AALr5HxI1/69Fg+Q/AAB6HIGYqPyPhJ0MKgAAOuy+BPkPAIDJ2NwdGfIfAABzsTZsFZX/UfIfAAB91b2S8j8TD5L/AADou2xI1P5/P+//AwCQA8eSZwWd/+cl/wEAyIlTTAPIbmvi/D8AAEw2B5Dtr7MymgAA5Mq+ZGhUQP6nq7j/DwCAyTSAT+0dMfz6f8rFQAIAMLkGcN1TWYPnf5r8BwBg0g3gxicMfRxAlud/AACmwPG5xzUjr/+T/wAATKkBLH/MsA0gu2MxAwgAwNQawMqdBm0Ame563v8HAGCqDWDVo8OGzP9ks43RAwBgGg3AgHMAmTYv5/8BADCdBmDAVQCtNcD5/wAATK8BrNhlsN2AWjTE/b8AAEy3Ady821ANQIuEyX8AAKbfAJY+mSX/AQAwXQO44Tej5D8AAGZj//SgUd7/j7H+DwBA3hrAVc8aI//jQfIfAID8NYCrjdAAMu1+9v8BAJBHjiXqN4BMl4f8BwAgr5zKN4DstibO/wUAwGRzANmBOiujBABAvtmXDCm8GzCbXsT9fwAAFKIBfGrviLLr/ykXAwQAQGEawHVPKXomYCZN/gMAUCiOG59Q8l6ALM//AAAUsgEs26WpuP5P/gMAUNAGsPwx5RpAtq+agQEAoLANYOVOxRpApruB/X8AABS6Aax6VKkGkEk2c/4PAAAmawCZhM/OmAAAUIQGoNAqgNYa4Px/AACK0wBW7FJkN6AWDXH/LwAAxWoAy3YrcSKQFgmT/wAAFK8B3PTkCPkPAIDpGsD1e0bJfwAAzMZ+zWCp3/+Psf4PAEDRG8CSZ0ub//Eg+Q8AQPEbwNWlbACZdj/7/wAAKAFHCecAMl0ezv8BAKAknCVrANntjZz/CwCAyeYAsgO13P8DAEDJ2JfsL8FuwGy60sJnDwBACRvANXuKfiJQJuXigwcAoLQN4Poni3wqcCZN/gMAUGqOm3YX9WagEZ7/AQBQoQEs26UVc/2f/AcAQIkGsHxn0RpAtreGDxwAADUawMpHi9QAMlsb2P8HAIAqDWBVcRpApsPN+T8AAJisAWQSPs7/BQBApQawsvDvAWixAPf/AACgVgNYvqvAuwG1aIj7fwEAUK0BLNtd0BOBtEiY/AcAQL0GcNOTI+Q/AACmawDX7xkt3Pw/+Q8AgJrs1+wv1Pv/Mdb/AQBQtgEsebYw+R8Pkv8AAJisAWQ6fOz/AwBAYY4CNIDMVjfn/wAAoDRn3htAdnsj5/8CAKD6HMDV+W0A2VQt9/8AAKA8+5LB0Xzm/0I+UgAAjNAArtmTtxOBMmkXHygAAMZoANc/madTgbPkPwAAhuG4aXcmP/P/5D8AAAZqAMt2aaz/AwBgugawYue0G0Cmt4YPEgAAYzWAlY9q0z3/p5H9fwAAGK0BrJpeA8h0uDn/BwAAAzaA6awCZBI+zv8FAMCIDWDlY1NuAFoswP0/AAAYswEs3zXF3YBaNMT9vwAAGLUBLHtiSicCaZEw+Q8AgHEbwI1PjZD/AACYrgFct3d08vP/5D8AAMZm/9TQZN//j7H+DwCA4RvAkmcnuf8vQP4DAGCyBpDp8LH/DwAAARyTaADZrW7O/wEAQARnzg0g29vA+b8AAEiZA7g6twaQTddw/w8AAGLYrxrMYTdgJrWQjwoAAEkN4NO/0T0TMJN28UEBACCrAdzwpM69ANkU+Q8AgDSOpbsz5D8AAKZrADfv0ibK/0o+IgAAJDaAFTvHbQCZ7bV8QAAAyGwAKx8dHif/uxrZ/wcAgNQGsOrRMecAMu0ezv8BAEBwAxhrFSAT93P+LwAAkhvAysfOaABaLMj9PwAAyG4Ayx8/bTegFglx/y8AANIbwOeeyH44/8PkPwAA8hvAjU+NkP8AAJiN/bq9798MpEXJfwAATNIAPjX0p/f/W1n/BwDANA1gybPv5X8iQP4DAGCyBpDp8LH/DwAAE3GcagDZbjfn/wAAYCrOJUN9DZz/CwCA2eYALqvh/h8AAJTx/wM1pWSe6vr3uQAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAyMS0wMi0wOVQxMToxNToxNyswMDowMNxIGHoAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMjEtMDItMDlUMTE6MTU6MTcrMDA6MDCtFaDGAAAAAElFTkSuQmCC';
							
							}else{
								
								image_src = "data:image/jpg;image/png;base64,"+response[i].image_reference;
							
							}
							
							$('.carousel-inner').last().append('<div class="carousel-item '+carousel_item_status+'">'+
							'<img src="'+image_src+'" class="d-block w-100" alt="..." style="min-width:400px; max-width:500px;width:100%">'+
							'<div class="carousel-caption d-none d-md-block" style="background-color:#000000de; border-top:solid orange 2px; border-bottom:solid orange 2px; position: fixed !important; bottom: 30px !important; transition: all 0.3s;">'+
						    '<table width="100%"><thead>'+
							'<tr>'+
							'<th style="text-align:center !important;">Bank</th>'+
							'<th style="text-align:center !important;">Date of Payment</th>'+
							'<th style="text-align:center !important;">Reference No.</th>'+
							'<th style="text-align:center !important;">Amount</th>'+
							'</tr>'+
							'</thead>'+
							'<tbody>'+
							'<tr>'+
							'<td>'+receivable_mode_of_payment+'</td>'+
							'<td>'+receivable_date_of_payment+'</td>'+
							'<td>'+receivable_reference+'</td>'+
							'<td>'+receivable_payment_amount+'</td>'+
							'</tr>'+
							'</tbody>'+
							'</table>'+
							'</div>'+
							'</div>');
							

						}			
				  }else{
							/*No Result Found or Error*/
							$("#update_table_payment_body_data tr").remove();
							$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#update_table_payment_body_data');
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	  }  	  	
	
	<!--Select For Update-->
	$('body').on('click','#SalesOrderPayment_Edit',function(){
			
			event.preventDefault();
			let sales_order_payment_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('ReceivablePaymentInfo') }}",
				type:"POST",
				data:{
				  receivable_payment_id:sales_order_payment_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("receivable_idx_payment").value 	= response[0].receivable_idx;
					document.getElementById("receivable_payment_id").value 		= response[0].receivable_payment_id;
					
					/*Set Details*/
					document.getElementById("receivable_mode_of_payment").value = response[0].receivable_mode_of_payment;
					document.getElementById("receivable_date_of_payment").value = response[0].receivable_date_of_payment;
					document.getElementById("receivable_reference").value 		= response[0].receivable_reference;
					document.getElementById("receivable_payment_amount").value 	= response[0].receivable_payment_amount;
					
					/*Display Image*/
					if(response[0].image_reference != null){
						
						var img_holder = $('.img-holder');
						img_holder.empty();
						image_src = "data:image/jpg;image/png;base64,"+response[0].image_reference;
						
						$('<img/>',{'src':image_src,'class':'img-fluid','style':'max-width:400px;margin-bottom:5px;'}).appendTo(img_holder);
						$("#image_payment_div").show();
					
					}else{
					}
					
					$('#AddPaymentModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	  	 

	<!--Select Bill For Update-->
	$('body').on('click','#ViewSalesOrderPayment',function(){
			
			event.preventDefault();
			let sales_order_payment_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('ReceivablePaymentInfo') }}",
				type:"POST",
				data:{
				  receivable_payment_id:sales_order_payment_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					$('#view_receivable_mode_of_payment').text(response[0].receivable_mode_of_payment);
					$('#view_receivable_date_of_payment').text(response[0].receivable_date_of_payment);
					$('#view_receivable_reference').text(response[0].receivable_reference);
					$('#view_receivable_payment_amount').text(response[0].receivable_payment_amount);
					
					if(response[0].image_reference != null){
					
						/*Display Image*/
						
						var img_holder = $('.view_img-holder');
						img_holder.empty();
						image_src = "data:image/jpg;image/png;base64,"+response[0].image_reference;
						
						$('<img/>',{'src':image_src,'class':'img-fluid','style':'max-width:600px;margin-top:5px;margin-bottom:5px;'}).appendTo(img_holder);
						
					}else{
					
					}
					
					$('#ViewOrderViewPaymentReferenceModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	

	<!--Select Bill For Update-->
	$('body').on('click','#deleteSalesOrderPayment',function(){
			
			event.preventDefault();
			let sales_order_payment_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('ReceivablePaymentInfo') }}",
				type:"POST",
				data:{
				  receivable_payment_id:sales_order_payment_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteSalesOrderPaymentConfirmed").value = response[0].receivable_payment_id;
					
					/*Set Details*/
				
					$('#delete_receivable_mode_of_payment').text(response[0].receivable_mode_of_payment);
					$('#delete_receivable_date_of_payment').text(response[0].receivable_date_of_payment);
					$('#delete_receivable_reference').text(response[0].receivable_reference);
					$('#delete_receivable_payment_amount').text(response[0].receivable_payment_amount);
				
					if(response[0].image_reference != null){
					
						/*Display Image*/
						var img_holder = $('.delete_img-holder');
						img_holder.empty();
						image_src = "data:image/jpg;image/png;base64,"+response[0].image_reference;
						
						$('<img/>',{'src':image_src,'class':'img-fluid','style':'max-width:400px;margin-top:5px;margin-bottom:5px;'}).appendTo(img_holder);
					
					}else{
					
					}
					
					$('#SalesOrderPaymentDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	

	  <!-- Confirmed For Deletion-->
	  $('body').on('click','#deleteSalesOrderPaymentConfirmed',function(){
			
			event.preventDefault();
			
			let receivable_id 		= {{ @$receivables_details['receivable_id'] }};
			let paymentitemID 		= document.getElementById("deleteSalesOrderPaymentConfirmed").value;
			
			  $.ajax({
				url: "{{ route('SalesOrderDeletePayment') }}",
				type:"POST",
				data:{
					receivable_idx:receivable_id,
					paymentitemID:paymentitemID,
					_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Purchase Order Payment Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	

					/*Reload Table*/
					LoadPayment();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });	  

	<!--Save New receivables->
	$("#SO-update-receivables").click(function(event){
			
			event.preventDefault();
			$('#receivable_description_SO_Error').text('');

			document.getElementById('ReceivableformEditFromSalesOrder').className = "g-3 needs-validation was-validated";
			
			let ReceivableID 			= {{ @$receivables_details['receivable_id'] }};

			let billing_date			= $("input[name=receivable_billing_date_SO]").val();	
			let payment_term 			= $("input[name=receivable_payment_term_SO]").val();
			let receivable_description 	= $("#receivable_description_SO").val();
			
			$.ajax({
				url: "/update_receivables_from_sale_order_post",
				type:"POST",
				data:{
				  ReceivableID:ReceivableID,
				  billing_date:billing_date,
				  payment_term:payment_term,
				  receivable_description:receivable_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					/*Reset Warnings*/
					$('#receivable_payment_termError').text('');
					$('#receivable_descriptionError').text('');
					
					/*Clear Form*/
					$('#receivable_payment_term').val("");
					$('#receivable_description').val("");
					
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					
					
				  }
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("SO-update-receivables").disabled = true;
					/*Show Status*/
					$('#update_loading_data_receivable').show();
				},
				complete: function(){
					
					/*Enable Submit Button*/
					document.getElementById("SO-update-receivables").disabled = false;
					/*Hide Status*/
					$('#update_loading_data_receivable').hide();
					
				},
				error: function(error) {
				 console.log(error);	
				
				$('#receivable_descriptionError').text(error.responseJSON.errors.receivable_description);
				document.getElementById('receivable_descriptionError').className = "invalid-feedback";					
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });		
	  });	


	
 </script>