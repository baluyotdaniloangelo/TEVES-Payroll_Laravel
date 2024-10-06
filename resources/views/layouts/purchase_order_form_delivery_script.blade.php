<script type="text/javascript">		 

	$("#save-product-delivery").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					
					$('#purchase_order_component_product_idxError').text('');
					$('#purchase_order_delivery_quantityError').text('');
					$('#purchase_order_delivery_withdrawal_referenceError').text('');

			document.getElementById('AddProductDelivery').className = "g-3 needs-validation was-validated";
			
			let purchase_order_delivery_details_id 	= document.getElementById("save-product-delivery").value;
			
			let purchase_order_id 								= {{ $PurchaseOrderID }};
			
			let purchase_order_delivery_date 					= $("input[name=purchase_order_delivery_date]").val();
			
			let purchase_order_component_product_idx 			= $("#product_list_delivery option[value='" + $('#purchase_order_component_product_idx').val() + "']").attr('product-id');
			let purchase_order_component_idx 					= $("#product_list_delivery option[value='" + $('#purchase_order_component_product_idx').val() + "']").attr('data-id');
		
			let purchase_order_delivery_quantity 				= $("#purchase_order_delivery_quantity").val();
			let purchase_order_delivery_withdrawal_reference 	= $("input[name=purchase_order_delivery_withdrawal_reference]").val();
			let purchase_order_delivery_hauler_details 			= $("input[name=purchase_order_delivery_hauler_details]").val();
			let purchase_order_delivery_remarks 				= $("input[name=purchase_order_delivery_remarks]").val();
			
			  $.ajax({
				url: "{{ route('PurchaseOrderDeliveryCompose') }}",
				type:"POST",
				data:{
				  purchase_order_delivery_details_id:purchase_order_delivery_details_id,
				  purchase_order_id:purchase_order_id,
				  purchase_order_delivery_date:purchase_order_delivery_date,
				  purchase_order_component_idx:purchase_order_component_idx, 
				  purchase_order_component_product_idx:purchase_order_component_product_idx,   
				  purchase_order_delivery_hauler_details:purchase_order_delivery_hauler_details,
				  purchase_order_delivery_quantity:purchase_order_delivery_quantity,
				  purchase_order_delivery_withdrawal_reference:purchase_order_delivery_withdrawal_reference,
				  purchase_order_delivery_remarks:purchase_order_delivery_remarks,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);

						$('#purchase_order_component_product_idxError').text('');
						$('#purchase_order_delivery_quantityError').text('');
						
						/*Clear Form*/
						$('#purchase_order_component_product_idx').val("");
						$('#purchase_order_delivery_quantity').val("");
						$('#purchase_order_delivery_withdrawal_reference').val("");
						$('#purchase_order_delivery_remarks').val("");
						
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
				      
					if(error.responseJSON.errors.purchase_order_component_product_idx=='Product is Required'){
							
							if(purchase_order_delivery_hauler_details==''){
								$('#purchase_order_component_product_idxError').html(error.responseJSON.errors.purchase_order_component_product_idx);
							}else{
								$('#purchase_order_component_product_idxError').html("Incorrect Product Name <b>" + purchase_order_delivery_hauler_details + "</b>");
							}
							
							document.getElementById("purchase_order_component_product_idx").value = "";
							document.getElementById('purchase_order_component_product_idxError').className = "invalid-feedback";
					
					}			
					
					$('#purchase_order_delivery_quantityError').text(error.responseJSON.errors.purchase_order_delivery_quantity);
					document.getElementById('purchase_order_delivery_quantityError').className = "invalid-feedback";					
								
					$('#switch_notice_off').show();
					$('#sw_off').html("Invalid Input" + "");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	});	
	  
	  
	  
	<!--Select Bill For Update-->
	$('body').on('click','#PurchaseOrderProductDelivery_Edit',function(){
			
			event.preventDefault();
			let purchase_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('PurchaseOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  purchase_order_delivery_details_id:purchase_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					/*Set Details*/
					document.getElementById("save-product-delivery").value = purchase_order_delivery_details_id;
					document.getElementById("purchase_order_delivery_date").value = response[0].purchase_order_delivery_date;
					document.getElementById("purchase_order_component_product_idx").value = response[0].product_name;
					document.getElementById("purchase_order_delivery_quantity").value = response[0].purchase_order_delivery_quantity;
					document.getElementById("purchase_order_delivery_withdrawal_reference").value = response[0].purchase_order_delivery_withdrawal_reference;
					document.getElementById("purchase_order_delivery_hauler_details").value = response[0].purchase_order_delivery_hauler_details;
					document.getElementById("purchase_order_delivery_remarks").value = response[0].purchase_order_delivery_remarks;
					
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
	$('body').on('click','#PurchaseOrderProductDelivery_Delete',function(){
			
			event.preventDefault();
			let purchase_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('PurchaseOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  purchase_order_delivery_details_id:purchase_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deletePurchaseOrderProdcutDeliveryConfirmed").value = purchase_order_delivery_details_id;
					
					$('#delete_delivery_purchase_order_delivery_date').text(response[0].purchase_order_delivery_date);
					$('#delete_delivery_delete_product_name').text(response[0].product_name);
					$('#delete_delivery_delete_purchase_order_delivery_quantity').text(response[0].purchase_order_delivery_quantity);
					
					$('#delete_delivery_purchase_order_delivery_withdrawal_reference').text(response[0].purchase_order_delivery_withdrawal_reference);
					$('#delete_delivery_purchase_order_delivery_hauler_details').text(response[0].purchase_order_delivery_hauler_details);
					$('#delete_delivery_purchase_order_delivery_remarks').text(response[0].purchase_order_delivery_remarks);

					$('#PurchaseOrderProductDeliveryDeleteModal').modal('toggle');				
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	});


	<!--Bill Deletion Confirmation-->
	$('body').on('click','#PurchaseOrderProductDelivery_View',function(){
			
			event.preventDefault();
			let purchase_order_delivery_details_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('PurchaseOrderDeliveryInfo') }}",
				type:"POST",
				data:{
				  purchase_order_delivery_details_id:purchase_order_delivery_details_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#view_delivery_purchase_order_delivery_date').text(response[0].purchase_order_delivery_date);
					$('#view_delivery_delete_product_name').text(response[0].product_name);
					$('#view_delivery_delete_purchase_order_delivery_quantity').text(response[0].purchase_order_delivery_quantity);
					
					$('#view_delivery_purchase_order_delivery_withdrawal_reference').text(response[0].purchase_order_delivery_withdrawal_reference);
					$('#view_delivery_purchase_order_delivery_hauler_details').text(response[0].purchase_order_delivery_hauler_details);
					$('#view_delivery_purchase_order_delivery_remarks').text(response[0].purchase_order_delivery_remarks);

					$('#PurchaseOrderProductDeliveryViewModal').modal('toggle');				
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	});


	<!-- Confirmed For Deletion-->
	$('body').on('click','#deletePurchaseOrderProdcutDeliveryConfirmed',function(){
			
			event.preventDefault();
			
			let purchase_order_id 				= {{ $PurchaseOrderID }};
			
			let purchase_order_delivery_details_id 	= document.getElementById("deletePurchaseOrderProdcutDeliveryConfirmed").value;
			
			  $.ajax({
				url: "{{ route('PurchaseOrderDeleteDelivery') }}",
				type:"POST",
				data:{
					purchase_order_id:purchase_order_id,
					purchase_order_delivery_details_id:purchase_order_delivery_details_id,
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
			document.getElementById("purchase_order_delivery_date").value = '';
			document.getElementById("purchase_order_component_product_idx").value = '';
			document.getElementById("purchase_order_delivery_quantity").value = '';
			document.getElementById("purchase_order_delivery_withdrawal_reference").value = '';
			document.getElementById("purchase_order_delivery_hauler_details").value = '';
			document.getElementById("purchase_order_delivery_remarks").value = '';
		
	}
	  
	LoadProductListDelivery();
	
	function LoadProductListDelivery() {
		
		$("#product_list_delivery_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#product_list_delivery_data');
		

			  $.ajax({
				url: "{{ route('PurchaseOrderProductListDelivery') }}",
				type:"POST",
				data:{
				  purchase_order_id:{{ $PurchaseOrderID }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  
				  console.log(response['productlist_delivery']);
				  var len = response['productlist_delivery'].length;
				  
					  if(response['productlist_delivery']!='') {			  
							
								for(var i=0; i<len; i++){
							
									var id = response['productlist_delivery'][i].purchase_order_delivery_details_id;
									
									var product_name = response['productlist_delivery'][i].product_name;
									var product_unit_measurement = response['productlist_delivery'][i].product_unit_measurement;
									//var purchase_order_departure_date = response['productlist_delivery'][i].purchase_order_departure_date;
									var purchase_order_delivery_date = response['productlist_delivery'][i].purchase_order_delivery_date;
									var purchase_order_delivery_withdrawal_reference = response['productlist_delivery'][i].purchase_order_delivery_withdrawal_reference;
									var purchase_order_delivery_quantity = response['productlist_delivery'][i].purchase_order_delivery_quantity;
									
									$('#product_list_delivery_data tr:last').after("<tr>"+
									"<td class=''><div align='center' class='action_table_menu_Product' >"+
									"<a href='#' class='btn-danger btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view' id='PurchaseOrderProductDelivery_View' data-id='"+id+"'></a>"+
									"<a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='PurchaseOrderProductDelivery_Edit' data-id='"+id+"'></a>"+
									"<a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='PurchaseOrderProductDelivery_Delete'  data-id='"+id+"'></a></div></td>"+
									"<td align='center'>" + (i+1) + "</td>" +
									"<td class='manual_price_td' align='center'>"+purchase_order_delivery_date+"</td>"+
									"<td class='product_td' align='left'>"+product_name+"</td>"+
									"<td class='calibration_td' align='right'>"+purchase_order_delivery_quantity+" "+product_unit_measurement+"</td>"+
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