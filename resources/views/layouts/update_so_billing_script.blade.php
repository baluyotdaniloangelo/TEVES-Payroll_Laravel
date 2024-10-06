	<script type="text/javascript">
	LoadSOProductList();
	
	/*Create SO*/
	$("#update-so-billing-transaction").click(function(event){
			
					event.preventDefault();
			
					/*Reset Warnings*/
					$('#so_order_dateError').text('');
					$('#so_order_timeError').text('');
					$('#so_numberError').text('');				  
					$('#sclient_idxError').text('');
					$('#splate_noError').text('');
					$('#sdrivers_nameError').text('');

			document.getElementById('SOBillingformUpdate').className = "g-3 needs-validation was-validated";
			
			let SOId 			= {{ $SOId }};
			
			let branch_id 				= $("#branch_id").val();
			let order_date 				= $("input[name=so_order_date]").val();
			let order_time 				= $("input[name=so_order_time]").val();
			let so_number 				= $("input[name=so_number]").val();/*SO NUMBER*/
			let client_idx 				= $('#so_client_name option[value="' + $('#so_client_idx').val() + '"]').attr('data-id');
			let plate_no 				= $("input[name=so_plate_no]").val();
			let drivers_name 			= $("input[name=so_drivers_name]").val();
			
			/*Client and Product Name*/
			let client_name 					= $("input[name=so_client_name]").val();
			
			  $.ajax({
				url: "{{ route('UpdateSOPost') }}",
				type:"POST",
				data:{
				  so_id:SOId,
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
								
					document.getElementById('SOBillingformUpdate').className = "g-3 needs-validation";
					
					so_id = response.so_id;
					LoadProductList(branch_id);
					
					/*Enable the Add Product Button Until Changes not Save*/
					document.getElementById("AddProductBTN").disabled = false;
					document.getElementById("SOBilling_Edit").disabled = false;
					document.getElementById("deleteSOProduct").disabled = false;
					
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
	  
	function UpdateBranch(){ 
	
		$('#switch_notice_off').show();
		$('#sw_off').html("You selected a new branch, to confirm changes click the update button");
		setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },2000);
		
		/*Disable the Add Product Button Until Changes not Save*/
		document.getElementById("AddProductBTN").disabled = true;
		document.getElementById("SOBilling_Edit").disabled = true;
		document.getElementById("deleteSOProduct").disabled = true;
	
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
	
	<!--Save New Billing-->
	$("#so-save-product").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					
					$('#product_idxError').text('');
					$('#product_manual_priceError').text('');
					$('#order_quantityError').text('');

			document.getElementById('AddSOProduct').className = "g-3 needs-validation was-validated";
			
			let branch_id 				= $("#branch_id").val();
			let product_idx 			= $('#product_list option[value="' + $('#product_idx').val() + '"]').attr('data-id');
			let product_manual_price 	= $("#product_manual_price").val();
			let order_quantity 			= $("input[name=order_quantity]").val();

			/*Client and Product Name*/
			let product_name 			= $("input[name=product_name]").val();
			
			let SOId 			= {{ $SOId }};
			
			  $.ajax({
				url: "{{ route('SOAddProductPost') }}",
				type:"POST",
				data:{
				  branch_idx:branch_id,
				  so_id:SOId,
				  product_idx:product_idx,
				  product_manual_price:product_manual_price,
				  order_quantity:order_quantity,
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
						
						/*Reload Table*/
						LoadSOProductList();
						
				  
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("so-save-product").disabled = true;
					/*Show Status*/
					$('#loading_data_add_product').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("so-save-product").disabled = false;
					/*Hide Status*/
					$('#loading_data_add_product').hide();
				
				},
				error: function(error) {
					
				 console.log(error);	
				      
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

	function LoadSOProductList() {	
	
		let SOId 			= {{ $SOId }};
		$("#table_so_product_body_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_so_product_body_data');


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
							
							$('#table_so_product_body_data tr:last').after("<tr>"+
							"<td align='center'>" + (i+1) + "</td>" +
							"<td class='product_td' align='left'>"+product_name+"</td>"+
							"<td class='calibration_td' align='center'>"+product_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='SOBilling_Edit' data-id='"+billing_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteSOProduct'  data-id='"+billing_id+"'></a></div></td>"+
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

	<!--Select Bill For Update-->
	$('body').on('click','#SOBilling_Edit',function(){
			
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
					
					document.getElementById("so-update-product").value = billID;
					
					/*Set Details*/
					document.getElementById("edit_product_idx").value = response[0].product_name;
					document.getElementById("edit_product_manual_price").value = response[0].product_price;
					document.getElementById("edit_order_quantity").value = response[0].order_quantity;
					
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					
					LoadProductList(response[0].branch_id);
					
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
	$("#so-update-product").click(function(event){
		
			event.preventDefault();
			
					/*Reset Warnings*/
					
					$('#product_idxError').text('');
					$('#product_manual_priceError').text('');
					$('#order_quantityError').text('');

			document.getElementById('EditSOProduct').className = "g-3 needs-validation was-validated";
			
			let SOId 						= {{ $SOId }};
			let branch_id 					= $("#branch_id").val();
			let billID 						= document.getElementById("so-update-product").value;
			let product_idx 				= $('#product_list option[value="' + $('#edit_product_idx').val() + '"]').attr('data-id');
			let product_manual_price 		= $("#edit_product_manual_price").val();
			let order_quantity 				= $("input[name=edit_order_quantity]").val();

			/*Client and Product Name*/
			let product_name 				= $("input[name=product_name]").val();
			
			  $.ajax({
				url: "{{ route('SOUpdateProductPost') }}",
				type:"POST",
				data:{
				  branch_idx:branch_id,
				  so_id:SOId,
				  billing_id:billID,
				  product_idx:product_idx,
				  product_manual_price:product_manual_price,
				  order_quantity:order_quantity,
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
						//$('#TotalAmount').html('0.00');
						}
						
						/*Reload Table*/
						LoadSOProductList();
									  
				},
				beforeSend:function()
				{
					/*Disable Submit Button*/
					document.getElementById("so-update-product").disabled = true;
					/*Show Status*/
					$('#loading_data_update_product').show();
			
				},
				complete: function(){
					
					/*Disable Submit Button*/
					document.getElementById("so-update-product").disabled = false;
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

	<!--Bill Deletion Confirmation-->
	$('body').on('click','#deleteSOProduct',function(){
			
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

	  <!-- Confirmed For Deletion-->
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
					
					/*Reload Table*/
					LoadSOProductList();
					
				  }
				},
				error: function(error) {
				 console.log(error);
					//alert(error);
				}
			   });		
	  });	  
	</script>