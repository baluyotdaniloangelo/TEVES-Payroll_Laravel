   <script type="text/javascript">
	<!--Load Table-->
	$(function () {

		var ReceivableListTable = $('#getPurchaseOrderList').DataTable({
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
			/*processing: true,*/
			serverSide: true,
			responsive: true,
			stateSave: true,/*Remember Searches*/
			ajax: "{{ route('getPurchaseOrderList') }}",
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'purchase_order_date'},
					{data: 'purchase_order_control_number'},
					{data: 'supplier_name'},
					{data: 'purchase_order_total_payable', render: $.fn.dataTable.render.number( ',', '.', 4, '' ) },
					{data: 'status', name: 'status', orderable: true, searchable: true},
					{data: 'action', name: 'action', orderable: false, searchable: false},
			],
			order: [[ 1, "desc" ]],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1] },
			]
		});
				/**/
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreatePurchaseOrderModal"></button>'+
				'</div>').appendTo('#purchase_order_option');
				
		$('a.toggle-vis').on('click', function (e) {
        e.preventDefault();
 
        // Get the column API object
        var column = table.column($(this).attr('data-column'));
 
        // Toggle the visibility
        column.visible(!column.visible());
		
		});				
								
	});
	
	function AddPaymentRow() {
		
		var x = document.getElementById("table_payment_body_data").rows.length;
		/*Limit to 5 rows*/
		
		if(x > 3){
		   return;
		}else{
		
			$('#table_payment_body_data tr:last').after("<tr>"+
			"<td class='bank_td' align='center'>"+
			"<input type='text' class='form-control purchase_order_bank' id='purchase_order_bank' name='purchase_order_bank' list='purchase_order_bank_list' autocomplete='off'>"+
							"<datalist id='purchase_order_bank_list'>"+
								<?php foreach ($purchase_payment_suggestion as $purchase_order_bank_cols) {?>
									"<option value='<?=$purchase_order_bank_cols->purchase_order_bank;?>'>"+
								<?php } ?>
							"</datalist>"+
			"</td>"+
			"<td class='date_of_payment_td' align='center'><input type='date' class='form-control purchase_order_date_of_payment' id='purchase_order_date_of_payment' name='purchase_order_date_of_payment' value='<?=date('Y-m-d');?>'></td>"+
			"<td class='purchase_order_reference_no_td' align='center'><input type='text' class='form-control purchase_order_reference_no' id='purchase_order_reference_no' name='purchase_order_reference_no'></td>"+
			"<td class='purchase_order_payment_amount_td' align='center'><input type='number' class='form-control purchase_order_payment_amount' id='purchase_order_payment_amount' name='purchase_order_payment_amount'></td>"+
			"<td><div onclick='deletePaymentRow(this)' data-id='0' id='payment_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deletePayment'></a></div></div></td></tr>");
		
		}	
	}		
	
	function UpdatePaymentRow() {
		
		var x = document.getElementById("update_table_payment_body_data").rows.length;
		/*Limit to 5 rows*/
		if(x > 3){
		   return;
		}else{
						
			$('#update_table_payment_body_data tr:last').after("<tr>"+
							"<td class='bank_td' align='center'>"+
							"<input type='text' class='form-control update_purchase_order_bank' id='update_purchase_order_bank' name='update_purchase_order_bank' list='update_purchase_order_bank_list' value='' autocomplete='off'>"+
											"<datalist id='update_purchase_order_bank_list'>"+
												<?php foreach ($purchase_payment_suggestion as $purchase_order_bank_cols) {?>
													"<option value='<?=$purchase_order_bank_cols->purchase_order_bank;?>'>"+
												<?php } ?>
											"</datalist>"+
							"</td>"+
							"<td class='update_date_of_payment_td' align='center'><input type='date' class='form-control update_purchase_order_date_of_payment' id='update_purchase_order_date_of_payment' name='update_purchase_order_date_of_payment' value=''></td>"+
							"<td class='update_purchase_order_reference_no_td' align='center'><input type='text' class='form-control update_purchase_order_reference_no' id='update_purchase_order_reference_no' name='update_purchase_order_reference_no' value=''></td>"+
							"<td class='update_purchase_order_payment_amount_td' align='center'><input type='number' class='form-control update_purchase_order_payment_amount' id='update_purchase_order_payment_amount' name='update_purchase_order_payment_amount' value=''></td>"+
							"<td><div onclick='deletePaymentRow(this)' data-id='' id='payment_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deletePayment'></a></div></div></td></tr>");
					
		}	
	}
	
	function deletePaymentRow(btn) {
			
		var paymentitemID= $(btn).data("id");			
		
		var row = btn.parentNode.parentNode;
		row.parentNode.removeChild(row);
		
		if(paymentitemID!=0){
			/*Delete the Selected Item*/
			
			  $.ajax({
				url: "/delete_purchase_order_payment_item",
				type:"POST",
				data:{
				  paymentitemID:paymentitemID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });		
		}
		
	}

	function AddProductRow() {
		
		var x = document.getElementById("table_product_body_data").rows.length;
		/*Limit to 5 rows*/
		
		if(x > 5){
		   return;
		}else{
		
			$('#table_product_body_data tr:last').after("<tr>"+
			"<td class='product_td' align='center'>"+
			"<select class='form-control form-select product_idx' name='product_idx' id='product_idx' required>"+
				"<option selected='' disabled='' value=''>Choose...</option>"+
					<?php foreach ($product_data as $product_data_cols){ ?>
						"<option value='<?=$product_data_cols->product_id;?>'>"+
						"<?=$product_data_cols->product_name;?> | <span style='font-family: DejaVu Sans; sans-serif;'>&#8369;</span>&nbsp;<?=$product_data_cols->product_price;?></option>"+
					<?php } ?>
			"</select></td>"+
			"<td class='quantity_td' align='center'><input type='number' class='form-control order_quantity' id='order_quantity' name='order_quantity' ></td>"+
			"<td class='manual_price_td' align='center'><input type='text' class='form-control product_manual_price' placeholder='0.00' aria-label='' name='product_manual_price' id='product_manual_price' value=''></td>"+
			"<td><div onclick='deleteRow(this)' data-id='0' id='product_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteReceivables'></a></div></div></td></tr>");
		
		}	
	  }
	  
	function UpdateProductRow() {
		
		var x = document.getElementById("update_table_product_body_data").rows.length;
		/*Limit to 5 rows*/
		if(x > 5){
		   return;
		}else{

		
			$('#update_table_product_body_data tr:last').after("<tr>"+
			"<td class='product_td' align='center'>"+
			"<select class='form-control form-select update_product_idx' name='update_product_idx' id='update_product_idx' required>"+
				"<option selected='' disabled='' value=''>Choose...</option>"+
					<?php foreach ($product_data as $product_data_cols){ ?>
						"<option value='<?=$product_data_cols->product_id;?>'>"+
						"<?=$product_data_cols->product_name;?> | <span style='font-family: DejaVu Sans; sans-serif;'>&#8369;</span>&nbsp;<?=$product_data_cols->product_price;?></option>"+
					<?php } ?>
			"</select></td>"+
			"<td class='quantity_td' align='center'><input type='number' class='form-control update_order_quantity' id='update_order_quantity' name='update_order_quantity' ></td>"+
			"<td class='manual_price_td' align='center'><input type='text' class='form-control update_product_manual_price' placeholder='0.00' aria-label='' name='update_product_manual_price' id='update_product_manual_price' value=''></td>"+
			"<td><div onclick='deleteRow(this)' data-id='0' id='product_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteReceivables'></a></div></div></td></tr>");
		
		}	
	}
	  
	function deleteRow(btn) {
			
		var productitemID= $(btn).data("id");			
		
		var row = btn.parentNode.parentNode;
		row.parentNode.removeChild(row);
		
		if(productitemID!=0){
			/*Delete the Selected Item*/
			
			  $.ajax({
				url: "/delete_purchase_order_item",
				type:"POST",
				data:{
				  productitemID:productitemID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });		
		}
		
	}
	
	<!--Save New Sales Order-->
	$("#save-purchase-order").click(function(event){

			event.preventDefault();
			
			document.getElementById('PurchaseOrderformNew').className = "g-3 needs-validation was-validated";

			let purchase_order_date 					= $("input[name=purchase_order_date]").val();
			
			let supplier_idx 							= ($("#supplier_name option[value='" + $('#supplier_idx').val() + "']").attr('data-id'));
			/*Supplier's Name and Product Name*/
			let supplier_name 					= $("input[name=supplier_name]").val();
			
			/*Added May 6, 2023*/
			let company_header 							= $("#company_header").val();
			
			let purchase_order_sales_order_number 		= $("input[name=purchase_order_sales_order_number]").val();
			let purchase_order_collection_receipt_no 	= $("input[name=purchase_order_collection_receipt_no]").val();
			let purchase_order_official_receipt_no 		= $("input[name=purchase_order_official_receipt_no]").val();
			let purchase_order_delivery_receipt_no 		= $("input[name=purchase_order_delivery_receipt_no]").val();
		
			let purchase_order_delivery_method 			= $("#purchase_order_delivery_method").val();
			let purchase_loading_terminal 				= $("#purchase_loading_terminal").val();
				
			let purchase_order_net_percentage 			= $("input[name=purchase_order_net_percentage]").val();
			let purchase_order_less_percentage 			= $("input[name=purchase_order_less_percentage]").val();
			
			let hauler_operator 						= $("input[name=hauler_operator]").val();
			let lorry_driver 							= $("input[name=lorry_driver]").val();
			let plate_number 							= $("input[name=plate_number]").val();
			let contact_number 							= $("input[name=contact_number]").val();
			
			let purchase_destination 					= $("input[name=purchase_destination]").val();
			let purchase_destination_address 			= $("input[name=purchase_destination_address]").val();
			let purchase_date_of_departure 				= $("input[name=purchase_date_of_departure]").val();
			let purchase_date_of_arrival 				= $("input[name=purchase_date_of_arrival]").val();
			
			
			let purchase_order_instructions 			= $("#purchase_order_instructions").val();
			let purchase_order_note 					= $("#purchase_order_note").val();

				var product_idx = [];
				var order_quantity = [];
				var product_manual_price = [];
				  
				  $('.product_idx').each(function(){
					if($(this).val() == ''){
						alert('Please Select a Product');
						exit();
					}else{  				  
				   		product_idx.push($(this).val());
					}				  
				  });
				  
				  $('.order_quantity').each(function(){
					if($(this).val() == ''){
						alert('Quantity is Empty');
						exit(); 
					}else{  				  
				   		order_quantity.push($(this).val());
					}				  
				  });
				  
				  $('.product_manual_price').each(function(){ 				  
				   		product_manual_price.push($(this).val());			  
				  });		
				 
				/*Payment Options*/
				var purchase_order_bank = [];
				var purchase_order_date_of_payment = [];
				var purchase_order_reference_no = [];
				var purchase_order_payment_amount = [];
				  
				  $('.purchase_order_bank').each(function(){
					if($(this).val() == ''){
						alert('Please Select a Bank');
						exit();
					}else{  				  
				   		purchase_order_bank.push($(this).val());
					}				  
				  });
				  
				  $('.purchase_order_date_of_payment').each(function(){
					if($(this).val() == ''){
						alert('Date of Payment is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_date_of_payment.push($(this).val());
					}				  
				  });
				  
				  $('.purchase_order_reference_no').each(function(){
					if($(this).val() == ''){
						alert('Reference is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_reference_no.push($(this).val());
					}				  
				  });	
				  
				  $('.purchase_order_payment_amount').each(function(){
					if($(this).val() == ''){
						alert('Payment Amount is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_payment_amount.push($(this).val());
					}				  
				  });	
				 	 
			  $.ajax({
				url: "/create_purchase_order_post",
				type:"POST",
				data:{
			
					purchase_order_date:purchase_order_date,
					supplier_idx:supplier_idx,
					company_header:company_header,
					purchase_order_sales_order_number:purchase_order_sales_order_number,
					purchase_order_collection_receipt_no:purchase_order_collection_receipt_no,
					purchase_order_official_receipt_no:purchase_order_official_receipt_no,
					purchase_order_delivery_receipt_no:purchase_order_delivery_receipt_no,
				
					purchase_order_delivery_method:purchase_order_delivery_method,
					purchase_loading_terminal:purchase_loading_terminal,
					
					purchase_order_net_percentage:purchase_order_net_percentage,
					purchase_order_less_percentage:purchase_order_less_percentage,
					
					purchase_order_bank:purchase_order_bank,
					purchase_order_date_of_payment:purchase_order_date_of_payment,	
					purchase_order_reference_no:purchase_order_reference_no,
					purchase_order_payment_amount:purchase_order_payment_amount,
			
					hauler_operator:hauler_operator,
					lorry_driver:lorry_driver,
					
					
					plate_number:plate_number,
					contact_number:contact_number,
					
					purchase_destination:purchase_destination,
					purchase_destination_address:purchase_destination_address,
					purchase_date_of_departure:purchase_date_of_departure,
					purchase_date_of_arrival:purchase_date_of_arrival,
									
					purchase_order_instructions:purchase_order_instructions,
					purchase_order_note:purchase_order_note,
				  
					product_idx:product_idx,
					order_quantity:order_quantity,
					product_manual_price:product_manual_price,
				  
					_token: "{{ csrf_token() }}"
				},		
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#supplier_idxError').text('');					

					/*Clear Form*/
					$('#company_header').val("");
					$('#supplier_name').val("");
					$('#purchase_order_sales_order_number').val("");
					$('#purchase_order_collection_receipt_no').val("");
					$('#purchase_order_official_receipt_no').val("");
					$('#purchase_order_delivery_receipt_no').val("");
					$('#purchase_order_delivery_method').val("");
					$('#purchase_loading_terminal').val("");
					$('#hauler_operator').val("");
					$('#lorry_driver').val("");
					$('#plate_number').val("");
					$('#contact_number').val("");
					
					$('#purchase_destination').val("");
					$('#purchase_destination_address').val("");
					$('#purchase_date_of_departure').val("");
					$('#purchase_date_of_arrival').val("");
					
					$('#purchase_order_instructions').val("");
					$('#purchase_order_note').val("");
					
				    /*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getPurchaseOrderList").DataTable();
					table.ajax.reload(null, false);
					
					/*Close Modal*/
					$('#CreatePurchaseOrderModal').modal('toggle');
					/*Open PDF for Printing*/
					var query = {
						purchase_order_id:response.purchase_order_id,
						_token: "{{ csrf_token() }}"
					}

					var url = "{{URL::to('generate_purchase_order_pdf')}}?" + $.param(query)
					window.open(url);
					
					/*Reload Page
					location.reload();
					*/
				  }
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("save-purchase-order").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
					
				},
				complete: function(){
						
					/*Enable Submit Button*/
					document.getElementById("save-purchase-order").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();	
					
				},
				error: function(error) {
					
				console.log(error);	
				
					if(error.responseJSON.errors.supplier_idx=="Supplier's Name is Required"){
						
							if(supplier_idx==''){
								$('#supplier_idxError').html(error.responseJSON.errors.supplier_idx);
							}else{
								$('#supplier_idxError').html("Incorrect Supplier's Name <b>" + supplier_name + "</b>");
							}
						
							document.getElementById("supplier_idx").value = "";
							document.getElementById('supplier_idxError').className = "invalid-feedback";
							
					}
				
				$('#product_idxError').html(error.responseJSON.errors.product_idx);
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });
	
	<!--Product Deletion Confirmation-->
	$('body').on('click','#deletePurchaseOrder',function(){
			
			event.preventDefault();
			let purchase_order_id = $(this).data('id');
			
			  $.ajax({
				url: "/purchase_order_info",
				type:"POST",
				data:{
				  purchase_order_id:purchase_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deletePurchaseOrderConfirmed").value = purchase_order_id;
							
					/*Set Details*/
					$('#confirm_delete_purchase_order_date').text(response.purchase_order_date);
					$('#confirm_delete_purchase_control_number').text(response.purchase_order_control_number);
					$('#confirm_delete_suppliers_name').text(response.purchase_supplier_name);
					$('#confirm_delete_amount').text(response.purchase_order_total_payable);
					
					$('#SalesOrderDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });	
	  
	  
	  <!--Product Confirmed For Deletion-->
	$('body').on('click','#deletePurchaseOrderConfirmed',function(){
			
			event.preventDefault();

			let purchase_order_id = document.getElementById("deletePurchaseOrderConfirmed").value;
			
			  $.ajax({
				url: "/delete_purchase_order_confirmed",
				type:"POST",
				data:{
				  purchase_order_id:purchase_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Purchase Order Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					//var table = $("#getPurchaseOrderList").DataTable();
				    //table.ajax.reload(null, false);
					
					/*Reload Page*/
					location.reload();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	  
	  
	<!--Select Product For Update-->
	$('body').on('click','#EditPurchaseOrder',function(){
			
			event.preventDefault();
			let purchase_order_id = $(this).data('id');
			
			/*Call Product List for Sales Order*/
			LoadProductRowForUpdate(purchase_order_id);
			
			/*Call Product List for Sales Order*/
			LoadPaymentRowForUpdate(purchase_order_id);
			
			  $.ajax({
				url: "/purchase_order_info",
				type:"POST",
				data:{
				  purchase_order_id:purchase_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {					

			document.getElementById("update_purchase_order_date").value = response[0].purchase_order_date;
			document.getElementById("update_supplier_idx").value = response[0].supplier_name;
				
			document.getElementById("update_purchase_order_sales_order_number").value = response[0].purchase_order_sales_order_number;
			document.getElementById("update_purchase_order_collection_receipt_no").value = response[0].purchase_order_collection_receipt_no;
			document.getElementById("update_purchase_order_official_receipt_no").value = response[0].purchase_order_official_receipt_no;
			document.getElementById("update_purchase_order_delivery_receipt_no").value = response[0].purchase_order_delivery_receipt_no;
		
			document.getElementById("update_purchase_order_delivery_method").value = response[0].purchase_order_delivery_method;
			document.getElementById("update_purchase_loading_terminal").value = response[0].purchase_loading_terminal;
			
			document.getElementById("update_purchase_order_net_percentage").value = response[0].purchase_order_net_percentage;
			document.getElementById("update_purchase_order_less_percentage").value = response[0].purchase_order_less_percentage;
				
			document.getElementById("update_hauler_operator").value = response[0].hauler_operator;
			document.getElementById("update_lorry_driver").value = response[0].lorry_driver;
			document.getElementById("update_plate_number").value = response[0].plate_number;
			document.getElementById("update_contact_number").value = response[0].contact_number;		
			
			document.getElementById("update_purchase_destination").value = response[0].purchase_destination;
			document.getElementById("update_purchase_destination_address").value = response[0].purchase_destination_address;
			document.getElementById("update_purchase_date_of_departure").value = response[0].purchase_date_of_departure;
			document.getElementById("update_purchase_date_of_arrival").value = response[0].purchase_date_of_arrival;
			
			document.getElementById("update_purchase_order_instructions").value = response[0].purchase_order_instructions;
			document.getElementById("update_purchase_order_note").value = response[0].purchase_order_note;		
					
			document.getElementById("update-purchase-order").value = purchase_order_id;		
			document.getElementById("update_company_header").value = response[0].company_header;	
			
				var update_product_idx = [];
				var update_order_quantity = [];
				var update_product_manual_price = [];
				  
					 $('.update_product_idx').each(function(){
						if($(this).val() == ''){
							alert('Please Select a Product');
							exit();
						}else{  				  
							update_product_idx.push($(this).val());
						}				  
					});
					  
					$('.update_order_quantity').each(function(){
						if($(this).val() == ''){
							alert('Quantity is Empty');
							exit(); 
						}else{  				  
							update_order_quantity.push($(this).val());
						}				  
					});
					  
					$('.update_product_manual_price').each(function(){ 				  
							update_product_manual_price.push($(this).val());			  
					});		

					$('#UpdatePurchaseOrderModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	

	function LoadProductRowForUpdate(purchase_order_id) {
		
		event.preventDefault();

			  $.ajax({
				url: "/get_purchase_order_product_list",
				type:"POST",
				data:{
				  purchase_order_id:purchase_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					  
						$("#update_table_product_body_data tr").remove();
						$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#update_table_product_body_data');
		
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].purchase_order_component_id;
							
							var product_idx = response[i].product_idx;
							var product_price = response[i].product_price;
							var order_quantity = response[i].order_quantity;
							
							$('#update_table_product_body_data tr:last').after("<tr>"+
								"<td class='product_td' align='center'>"+
								"<select class='form-control form-select update_product_idx' name='update_product_idx' id='update_product_idx' required>"+
									"<option selected='' disabled='' value=''>Choose...</option>"+
										<?php foreach ($product_data as $product_data_cols){ ?>
											"<option value='<?=$product_data_cols->product_id;?>'"+
											((product_idx == <?=$product_data_cols->product_id;?>) ? 'selected' : '') +
											">"+
											"<?=$product_data_cols->product_name;?> | <span style='font-family: DejaVu Sans; sans-serif;'>&#8369;</span>&nbsp;<?=$product_data_cols->product_price;?></option>"+
										<?php } ?>
								"</select></td>"+
								"<td class='quantity_td' align='center'>"+
								"<input type='number' class='form-control update_order_quantity' id='update_order_quantity' name='update_order_quantity' value='"+order_quantity+"'>"+
								"</td>"+
								"<td class='manual_price_td' align='center'>"+
								"<input type='text' class='form-control update_product_manual_price' placeholder='0.00' aria-label='' name='update_product_manual_price' id='update_product_manual_price' value='"+product_price+"'>"+
								"</td>"+
								"<td><div onclick='deleteRow(this)' data-id="+id+" id='product_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'>"+
								"<a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteReceivables'></a>"+"</div></div>"+"</td></tr>");
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
	  
	function LoadPaymentRowForUpdate(purchase_order_id) {
		
		event.preventDefault();
		
			  $.ajax({
				url: "/get_purchase_order_payment_list",
				type:"POST",
				data:{
				  purchase_order_id:purchase_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					 
					$("#update_table_payment_body_data tr").remove();
					$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#update_table_payment_body_data');

						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].purchase_order_payment_details_id;
							
							var purchase_order_bank 			= response[i].purchase_order_bank;
							var purchase_order_date_of_payment 	= response[i].purchase_order_date_of_payment;
							var purchase_order_reference_no		= response[i].purchase_order_reference_no;
							var purchase_order_payment_amount 	= response[i].purchase_order_payment_amount;
													
							$('#update_table_payment_body_data tr:last').after("<tr>"+
							"<td class='bank_td' align='center'>"+
							"<input type='text' class='form-control update_purchase_order_bank' id='update_purchase_order_bank' name='update_purchase_order_bank' list='update_purchase_order_bank_list'  value='"+purchase_order_bank+"'>"+
											"<datalist id='update_purchase_order_bank_list'>"+
											<?php foreach ($purchase_payment_suggestion as $purchase_order_bank_cols) {?>
												"<option value='<?=$purchase_order_bank_cols->purchase_order_bank;?>'>"+
											<?php } ?>	
											"</datalist>"+
							"</td>"+
							"<td class='update_date_of_payment_td' align='center'><input type='date' class='form-control update_purchase_order_date_of_payment' id='update_purchase_order_date_of_payment' name='update_purchase_order_date_of_payment' value='"+purchase_order_date_of_payment+"'></td>"+
							"<td class='update_purchase_order_reference_no_td' align='center'><input type='text' class='form-control update_purchase_order_reference_no' id='update_purchase_order_reference_no' name='update_purchase_order_reference_no' value='"+purchase_order_reference_no+"'></td>"+
							"<td class='update_purchase_order_payment_amount_td' align='center'><input type='number' class='form-control update_purchase_order_payment_amount' id='update_purchase_order_payment_amount' name='update_purchase_order_payment_amount' value='"+purchase_order_payment_amount+"'></td>"+
							"<td><div onclick='deletePaymentRow(this)' data-id='"+id+"' id='payment_item'><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deletePayment'></a></div></div></td></tr>");

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

	<!--Save New Sales Order-->
	$("#update-purchase-order").click(function(event){

			event.preventDefault();

			document.getElementById('PurchaseOrderformUpdate').className = "g-3 needs-validation was-validated";
			
			let purchase_order_id						= document.getElementById("update-purchase-order").value;
			
			let purchase_order_date 					= $("input[name=update_purchase_order_date]").val();
			
			let supplier_idx 							= $('#update_supplier_name option[value="' + $('#update_supplier_idx').val() + '"]').attr('data-id');
			/*Supplier's Name and Product Name*/
			let supplier_name 							= $("input[name=update_supplier_name]").val();
			/*Added May 6, 2023*/
			let company_header 							= $("#update_company_header").val();
			let purchase_order_sales_order_number 		= $("input[name=update_purchase_order_sales_order_number]").val();
			let purchase_order_collection_receipt_no 	= $("input[name=update_purchase_order_collection_receipt_no]").val();
			let purchase_order_official_receipt_no 		= $("input[name=update_purchase_order_official_receipt_no]").val();
			let purchase_order_delivery_receipt_no 		= $("input[name=update_purchase_order_delivery_receipt_no]").val();
		
			let purchase_order_delivery_method 			= $("#update_purchase_order_delivery_method").val();
			let purchase_loading_terminal 				= $("#update_purchase_loading_terminal").val();
			
			let purchase_order_net_percentage 			= $("input[name=update_purchase_order_net_percentage]").val();
			let purchase_order_less_percentage 			= $("input[name=update_purchase_order_less_percentage]").val();
			
			let hauler_operator 					= $("input[name=update_hauler_operator]").val();
			let lorry_driver 						= $("input[name=update_lorry_driver]").val();
			let plate_number 						= $("input[name=update_plate_number]").val();
			let contact_number 						= $("input[name=update_contact_number]").val();
						
			let purchase_destination 				= $("input[name=update_purchase_destination]").val();
			let purchase_destination_address 		= $("input[name=update_purchase_destination_address]").val();
			let purchase_date_of_departure 			= $("input[name=update_purchase_date_of_departure]").val();
			let purchase_date_of_arrival 			= $("input[name=update_purchase_date_of_arrival]").val();
			
			
			let purchase_order_instructions 			= $("#update_purchase_order_instructions").val();
			let purchase_order_note 					= $("#update_purchase_order_note").val();

				var product_idx = [];
				var order_quantity = [];
				var product_manual_price = [];
				var purchase_order_item_id = [];
				
				$('.update_product_idx').each(function(){
					if($(this).val() == ''){
						alert('Please Select a Product');
						exit();
					}else{  				  
				   		product_idx.push($(this).val());
					}				  
				  });
				  
				  $('.update_order_quantity').each(function(){
					if($(this).val() == ''){
						alert('Quantity is Empty');
						exit(); 
					}else{  				  
				   		order_quantity.push($(this).val());
					}				  
				  });
				  
				  $('.update_product_manual_price').each(function(){ 				  
				   		product_manual_price.push($(this).val());			  
				  });		
				 
				  $.each($("[id='product_item']"), function(){
					purchase_order_item_id.push($(this).attr("data-id"));
				  });
				
				/*Payment Options*/
				var purchase_order_bank = [];
				var purchase_order_date_of_payment = [];
				var purchase_order_reference_no = [];
				var purchase_order_payment_amount = [];
				var purchase_order_payment_item_id = [];
				  
				  $('.update_purchase_order_bank').each(function(){
					if($(this).val() == ''){
						alert('Please Select a Bank');
						exit();
					}else{  				  
				   		purchase_order_bank.push($(this).val());
					}				  
				  });
				  
				  $('.update_purchase_order_date_of_payment').each(function(){
					if($(this).val() == ''){
						alert('Date of Payment is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_date_of_payment.push($(this).val());
					}				  
				  });
				  
				  $('.update_purchase_order_reference_no').each(function(){
					if($(this).val() == ''){
						alert('Reference is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_reference_no.push($(this).val());
					}				  
				  });	
				  
				  $('.update_purchase_order_payment_amount').each(function(){
					if($(this).val() == ''){
						alert('Payment Amount is Empty');
						exit(); 
					}else{  				  
				   		purchase_order_payment_amount.push($(this).val());
					}				  
				  });	
				 	 
				  $.each($("[id='payment_item']"), function(){
					purchase_order_payment_item_id.push($(this).attr("data-id"));
				  });	
				 
			  $.ajax({
				url: "/update_purchase_order_post",
				type:"POST",
				data:{
			
					purchase_order_id:purchase_order_id,
					
					purchase_order_date:purchase_order_date,
					supplier_idx:supplier_idx,
					company_header:company_header,
					purchase_order_sales_order_number:purchase_order_sales_order_number,
					purchase_order_collection_receipt_no:purchase_order_collection_receipt_no,
					purchase_order_official_receipt_no:purchase_order_official_receipt_no,
					purchase_order_delivery_receipt_no:purchase_order_delivery_receipt_no,
				
					purchase_order_delivery_method:purchase_order_delivery_method,
					purchase_loading_terminal:purchase_loading_terminal,
					
					purchase_order_net_percentage:purchase_order_net_percentage,
					purchase_order_less_percentage:purchase_order_less_percentage,
					
					purchase_order_bank:purchase_order_bank,
					purchase_order_date_of_payment:purchase_order_date_of_payment,
					
					
					purchase_order_reference_no:purchase_order_reference_no,
					purchase_order_payment_amount:purchase_order_payment_amount,
			
					hauler_operator:hauler_operator,
					lorry_driver:lorry_driver,
					
					
					plate_number:plate_number,
					contact_number:contact_number,
					
					purchase_destination:purchase_destination,
					purchase_destination_address:purchase_destination_address,
					purchase_date_of_departure:purchase_date_of_departure,
					purchase_date_of_arrival:purchase_date_of_arrival,
									
					purchase_order_instructions:purchase_order_instructions,
					purchase_order_note:purchase_order_note,
				  
					purchase_order_item_ids:purchase_order_item_id,
					purchase_order_payment_item_id:purchase_order_payment_item_id,
				  
					product_idx:product_idx,
					order_quantity:order_quantity,
					product_manual_price:product_manual_price,
					_token: "{{ csrf_token() }}"
				},		
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#update_purchase_supplier_nameError').text('');					

					
				    /*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					//var table = $("#getPurchaseOrderList").DataTable();
					//table.ajax.reload(null, false);
					
					/*Close Modal*/
					$('#UpdatePurchaseOrderModal').modal('toggle');
					/*Open PDF for Printing*/
					var query = {
						purchase_order_id:response.purchase_order_id,
						_token: "{{ csrf_token() }}"
					}

					var url = "{{URL::to('generate_purchase_order_pdf')}}?" + $.param(query)
					window.open(url);
					
					/*Reload Page*/
					location.reload();
					
				  }
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("update-purchase-order").disabled = true;
					/*Show Status*/
					$('#update_loading_data').show();
					
					
				},
				complete: function(){
						
					/*Enable Submit Button*/
					document.getElementById("update-purchase-order").disabled = false;
					/*Hide Status*/
					$('#update_loading_data').hide();	
					
				},
				error: function(error) {
					
				 console.log(error);	
				
					if(error.responseJSON.errors.supplier_idx=="Supplier's Name is Required"){
						
							if(supplier_idx==''){
								$('#update_supplier_idxError').html(error.responseJSON.errors.supplier_idx);
							}else{
								$('#update_supplier_idxError').html("Incorrect Supplier's Name <b>" + supplier_name + "</b>");
							}
						
							document.getElementById("update_supplier_idx").value = "";
							document.getElementById('update_supplier_idxError').className = "invalid-feedback";
							
					}
				
				$('#product_idxError').html(error.responseJSON.errors.product_idx);
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });
	
	  /*Re-print*/
	  $('body').on('click','#PrintPurchaseOrder',function(){	  
	  
			let purchaseOrderID = $(this).data('id');
			var query = {
						purchase_order_id:purchaseOrderID,
						_token: "{{ csrf_token() }}"
					}

			var url = "{{URL::to('generate_purchase_order_pdf')}}?" + $.param(query)
			window.open(url);
	  
	  });
	  
	  function purchase_update_status(id){
		  
			event.preventDefault();
			var purchase_status = document.getElementById("purchase_order_status_"+id).value;
		
			  $.ajax({
				url: "/update_purchase_status",
				type:"POST",
				data:{
				  purchase_order_id:id,
				  purchase_status:purchase_status,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					  
				  		
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