<script type="text/javascript">		 

	$("#save-product-delivery").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					
					$('#sales_order_component_product_idxError').text('');
					$('#sales_order_delivery_quantityError').text('');
					$('#sales_order_delivery_withdrawal_referenceError').text('');

			document.getElementById('AddProductDelivery').className = "g-3 needs-validation was-validated";
			
			let sales_order_delivery_details_id 	= document.getElementById("save-product-delivery").value;
			
			let sales_order_id 								= {{ $SalesOrderID }};
			let receivable_id 								= {{ @$receivables_details['receivable_id'] }};
			
			let sales_order_delivery_date 					= $("input[name=sales_order_delivery_date]").val();
			//let sales_order_departure_date 					= $("input[name=sales_order_departure_date]").val();
			
			let sales_order_component_product_idx 			= $("#product_list_delivery option[value='" + $('#sales_order_component_product_idx').val() + "']").attr('product-id');
			let sales_order_component_idx 					= $("#product_list_delivery option[value='" + $('#sales_order_component_product_idx').val() + "']").attr('data-id');
			
			let sales_order_delivery_quantity 				= $("#sales_order_delivery_quantity").val();
			let sales_order_delivery_withdrawal_reference 	= $("input[name=sales_order_delivery_withdrawal_reference]").val();
			let sales_order_delivery_hauler_details 		= $("input[name=sales_order_delivery_hauler_details]").val();
			let sales_order_delivery_remarks 		= $("input[name=sales_order_delivery_remarks]").val();
			//let sales_order_branch_delivery 				= $("#sales_order_branch_delivery").val();
			
			  $.ajax({
				url: "{{ route('SalesOrderDeliveryCompose') }}",
				type:"POST",
				data:{
				  sales_order_delivery_details_id:sales_order_delivery_details_id,
				  sales_order_id:sales_order_id,
				  receivable_id:receivable_id,
				  sales_order_delivery_date:sales_order_delivery_date,
				  //sales_order_departure_date:sales_order_departure_date,
				  sales_order_component_idx:sales_order_component_idx, 
				  sales_order_component_product_idx:sales_order_component_product_idx,   
				  sales_order_delivery_hauler_details:sales_order_delivery_hauler_details,
				  sales_order_delivery_quantity:sales_order_delivery_quantity,
				  sales_order_delivery_withdrawal_reference:sales_order_delivery_withdrawal_reference,
				  sales_order_delivery_remarks:sales_order_delivery_remarks,
				  //sales_order_branch_delivery:sales_order_branch_delivery,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);

						$('#sales_order_component_product_idxError').text('');
						$('#sales_order_delivery_quantityError').text('');
						
						/*Clear Form*/
						$('#sales_order_component_product_idx').val("");
						$('#sales_order_delivery_quantity').val("");
						$('#sales_order_delivery_withdrawal_reference').val("");
						$('#sales_order_delivery_remarks').val("");
						//$('#sales_order_branch_delivery').val("");
						
						document.getElementById("save-product-delivery").value = 0;
						
						/*Reload Table*/
						LoadProductListDelivery();
						
						$('#AddProductDeliveryModal').modal('toggle');
						
						}
						
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("save-product-delivery").disabled = true;
					/*Show Status*/
					$('#loading_data_add_product_delivery').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("save-product-delivery").disabled = false;
					/*Hide Status*/
					$('#loading_data_add_product_delivery').hide();
				
				},
				error: function(error) {
					
				 console.log(error);	
				 
					/*Disable Submit Button*/
					document.getElementById("save-product-delivery").disabled = false;
					/*Hide Status*/
					$('#loading_data_add_product_delivery').hide();
				      
					if(error.responseJSON.errors.sales_order_component_product_idx=='Product is Required'){
							
							if(sales_order_delivery_hauler_details==''){
								$('#sales_order_component_product_idxError').html(error.responseJSON.errors.sales_order_component_product_idx);
							}else{
								$('#sales_order_component_product_idxError').html("Incorrect Product Name <b>" + sales_order_delivery_hauler_details + "</b>");
							}
							
							document.getElementById("sales_order_component_product_idx").value = "";
							document.getElementById('sales_order_component_product_idxError').className = "invalid-feedback";
					
					}			
					
					$('#sales_order_delivery_quantityError').text(error.responseJSON.errors.sales_order_delivery_quantity);
					document.getElementById('sales_order_delivery_quantityError').className = "invalid-feedback";					
								
					$('#switch_notice_off').show();
					$('#sw_off').html("Invalid Input" + "");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	});	
	  
	  
	  
	<!--Select Bill For Update-->
	$('body').on('click','#SalesOrderProductDelivery_Edit',function(){
			
			event.preventDefault();
			let sales_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('SalesOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  sales_order_delivery_details_id:sales_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					document.getElementById("save-product-delivery").value = sales_order_delivery_details_id;
					document.getElementById("sales_order_delivery_date").value = response[0].sales_order_delivery_date;
					//document.getElementById("sales_order_departure_date").value = response[0].sales_order_departure_date;
					document.getElementById("sales_order_component_product_idx").value = response[0].product_name;
					document.getElementById("sales_order_delivery_quantity").value = response[0].sales_order_delivery_quantity;
					document.getElementById("sales_order_delivery_withdrawal_reference").value = response[0].sales_order_delivery_withdrawal_reference;
					document.getElementById("sales_order_delivery_hauler_details").value = response[0].sales_order_delivery_hauler_details;
					document.getElementById("sales_order_delivery_remarks").value = response[0].sales_order_delivery_remarks;
					//document.getElementById("sales_order_branch_delivery").value = response[0].sales_order_branch_delivery;
					
					$('#AddProductDeliveryModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	});	

	<!--Bill Deletion Confirmation-->
	$('body').on('click','#SalesOrderProductDelivery_Delete',function(){
			
			event.preventDefault();
			let sales_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('SalesOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  sales_order_delivery_details_id:sales_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteSalesOrderProdcutDeliveryConfirmed").value = sales_order_delivery_details_id;
					
					$('#delete_delivery_sales_order_delivery_date').text(response[0].sales_order_delivery_date);
					//$('#delete_delivery_sales_order_departure_date').text(response[0].sales_order_departure_date);
					$('#delete_delivery_delete_product_name').text(response[0].product_name);
					$('#delete_delivery_delete_sales_order_delivery_quantity').text(response[0].sales_order_delivery_quantity);
					
					$('#delete_delivery_sales_order_delivery_withdrawal_reference').text(response[0].sales_order_delivery_withdrawal_reference);
					$('#delete_delivery_sales_order_delivery_hauler_details').text(response[0].sales_order_delivery_hauler_details);
					$('#delete_delivery_sales_order_delivery_remarks').text(response[0].sales_order_delivery_remarks);
					//$('#delete_delivery_sales_order_branch_delivery').text(response[0].branch_name);

					$('#SalesOrderProductDeliveryDeleteModal').modal('toggle');				
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	});


	<!--Bill Deletion Confirmation-->
	$('body').on('click','#SalesOrderProductDelivery_View',function(){
			
			event.preventDefault();
			let sales_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('SalesOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  sales_order_delivery_details_id:sales_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#view_delivery_sales_order_delivery_date').text(response[0].sales_order_delivery_date);
					//$('#view_delivery_sales_order_departure_date').text(response[0].sales_order_departure_date);
					$('#view_delivery_delete_product_name').text(response[0].product_name);
					$('#view_delivery_delete_sales_order_delivery_quantity').text(response[0].sales_order_delivery_quantity);
					
					$('#view_delivery_sales_order_delivery_withdrawal_reference').text(response[0].sales_order_delivery_withdrawal_reference);
					$('#view_delivery_sales_order_delivery_hauler_details').text(response[0].sales_order_delivery_hauler_details);
					$('#view_delivery_sales_order_delivery_remarks').text(response[0].sales_order_delivery_remarks);
					// $('#view_delivery_sales_order_branch_delivery').text(response[0].branch_name);

					$('#SalesOrderProductDeliveryViewModal').modal('toggle');				
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	});


	<!-- Confirmed For Deletion-->
	$('body').on('click','#deleteSalesOrderProdcutDeliveryConfirmed',function(){
			
			event.preventDefault();
			
			let sales_order_id 				= {{ $SalesOrderID }};
			let receivable_id 				= {{ @$receivables_details['receivable_id'] }};
			
			let sales_order_delivery_details_id 	= document.getElementById("deleteSalesOrderProdcutDeliveryConfirmed").value;
			
			  $.ajax({
				url: "{{ route('SalesOrderDeleteDelivery') }}",
				type:"POST",
				data:{
					sales_order_id:sales_order_id,
					receivable_id:receivable_id,
					sales_order_delivery_details_id:sales_order_delivery_details_id,
					_token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Sales Order Product Delivery Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	

					/*Reload Table*/
					LoadProductListDelivery();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	});	  
	  
	/*reset Delivery Form*/  
	function ResetDeliveryForm() {
		
			/*Set Details*/
			document.getElementById("save-product-delivery").value = '';
			document.getElementById("sales_order_delivery_date").value = '';
			//document.getElementById("sales_order_departure_date").value = '';
			document.getElementById("sales_order_component_product_idx").value = '';
			document.getElementById("sales_order_delivery_quantity").value = '';
			document.getElementById("sales_order_delivery_withdrawal_reference").value = '';
			document.getElementById("sales_order_delivery_hauler_details").value = '';
			document.getElementById("sales_order_delivery_remarks").value = '';
			//document.getElementById("sales_order_branch_delivery").value = '';
		
	}
	  
	LoadProductListDelivery();
	
	function LoadProductListDelivery() {
		
		$("#product_list_delivery_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#product_list_delivery_data');
		
		let receivable_id = {{ @$receivables_details['receivable_id'] }};

			  $.ajax({
				url: "{{ route('ProductListDelivery') }}",
				type:"POST",
				data:{
				  sales_order_id:{{ $SalesOrderID }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  
				  console.log(response['productlist_delivery']);
				  var len = response['productlist_delivery'].length;
				  
					  if(response['productlist_delivery']!='') {			  
							
								for(var i=0; i<len; i++){
							
									var id = response['productlist_delivery'][i].sales_order_delivery_details_id;
									
									var product_name = response['productlist_delivery'][i].product_name;
									var product_unit_measurement = response['productlist_delivery'][i].product_unit_measurement;
									//var sales_order_departure_date = response['productlist_delivery'][i].sales_order_departure_date;
									var sales_order_delivery_date = response['productlist_delivery'][i].sales_order_delivery_date;
									var sales_order_delivery_withdrawal_reference = response['productlist_delivery'][i].sales_order_delivery_withdrawal_reference;
									var sales_order_delivery_quantity = response['productlist_delivery'][i].sales_order_delivery_quantity;
									
									var created_at = response['productlist_delivery'][i].created_at;
								
									const oneDay = 24 * 60 * 60 * 1000; 	/*hours*minutes*seconds*milliseconds*/
									const firstDate = new Date(created_at);
									const secondDate = new Date();			/*Now*/

									const diffDays = Math.round(Math.abs((firstDate - secondDate) / oneDay));

									<?php
									if(Session::get('UserType')=="Admin"){
									?>
									
											action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view' id='SalesOrderProductDelivery_View' data-id='"+id+"'></a>"+
																"<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderProductDelivery_Edit' data-id='"+id+"'></a>"+
																"<a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='SalesOrderProductDelivery_Delete'  data-id='"+id+"'></a>";		
									
									<?php
									}
									else{
									?>
									
										if(diffDays>=3){
										
											action_controls = "";
									
										}else{
												
											action_controls = "<a href='#' class='btn-danger btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view' id='SalesOrderProductDelivery_View' data-id='"+id+"'></a>"+
																"<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SalesOrderProductDelivery_Edit' data-id='"+id+"'></a>"+
																"<a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='SalesOrderProductDelivery_Delete'  data-id='"+id+"'></a>";		
																
											}
									
									<?php									
									}
									?>

									$('#product_list_delivery_data tr:last').after("<tr>"+
									"<td class=''><div align='center' class='action_table_menu_Product' >"+action_controls+"</div></td>"+
									"<td align='center'>" + (i+1) + "</td>" +
									"<td class='manual_price_td' align='center'>"+sales_order_delivery_date+"</td>"+
									"<td class='product_td' align='left'>"+product_name+"</td>"+
									"<td class='calibration_td' align='right'>"+sales_order_delivery_quantity+" "+product_unit_measurement+"</td>"+
									"</tr>");
									
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
 </script>