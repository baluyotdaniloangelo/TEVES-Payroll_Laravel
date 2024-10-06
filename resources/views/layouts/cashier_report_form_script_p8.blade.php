   <script type="text/javascript">
	
	function dipstick_inventory_tank_compute(){
		
			var beginning_dipstick_inventory 			= $("input[name=beginning_dipstick_inventory]").val() || 0;
			var sales_in_liters_dipstick_inventory 		= $("input[name=sales_in_liters_dipstick_inventory]").val() || 0;
			var ugt_pumping_dipstick_inventory 			= $("input[name=ugt_pumping_dipstick_inventory]").val() || 0;
			var delivery_dipstick_inventory 			= $("input[name=delivery_dipstick_inventory]").val() || 0;
			var ending_dipstick_inventory 				= $("input[name=ending_dipstick_inventory]").val() || 0;
		
				var TotalBookStock = (parseFloat(beginning_dipstick_inventory) - parseFloat(sales_in_liters_dipstick_inventory) - parseFloat(ugt_pumping_dipstick_inventory)) + parseFloat(delivery_dipstick_inventory);
				var TotalVariance = parseFloat(TotalBookStock) - parseFloat(ending_dipstick_inventory);
				
				if(TotalVariance!=''){
			
					$('#TotalBookStock_dipstick_inventory').html(TotalBookStock.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					$('#TotalVariance_dipstick_inventory').html(TotalVariance.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			
				}
		
	}		

	function update_dipstick_inventory_compute(){
		
			var beginning_dipstick_inventory 			= $("input[name=update_beginning_dipstick_inventory]").val() || 0;
			var sales_in_liters_dipstick_inventory 		= $("input[name=update_sales_in_liters_dipstick_inventory]").val() || 0;
			var ugt_pumping_dipstick_inventory 			= $("input[name=update_ugt_pumping_dipstick_inventory]").val() || 0;
			var delivery_dipstick_inventory 			= $("input[name=update_delivery_dipstick_inventory]").val() || 0;
			var ending_dipstick_inventory 				= $("input[name=update_ending_dipstick_inventory]").val() || 0;
			
				var TotalBookStock = (parseFloat(beginning_dipstick_inventory) - parseFloat(sales_in_liters_dipstick_inventory) - parseFloat(ugt_pumping_dipstick_inventory)) + parseFloat(delivery_dipstick_inventory);
				var TotalVariance = parseFloat(TotalBookStock) - parseFloat(ending_dipstick_inventory);
				
				$('#update_TotalBookStock_dipstick_inventory').html(TotalBookStock.toLocaleString("en-PH", {minimumFractionDigits: 2}));
				$('#update_TotalVariance_dipstick_inventory').html(TotalVariance.toLocaleString("en-PH", {minimumFractionDigits: 2}));

		
	}
	
	/*Call Function for Product Cahier's Report*/
	$("#save-CRPH7").click(function(event){
		
			event.preventDefault();
			
				$('#product_idx_dipstick_inventoryError').text('');	
				$('#product_tank_idx_dipstick_inventoryError').text('');					
				$('#beginning_dipstick_inventoryError').text('');		
				$('#delivery_dipstick_inventoryError').text('');	
				$('#ugt_pumping_dipstick_inventoryError').text('');
				$('#ending_dipstick_inventoryError').text('');
				$('#sales_in_liters_dipstick_inventoryError').text('');
			
			let CashiersReportId 				= {{ $CashiersReportId }};			
		
			var product_idx 					= $('#product_list_inventory option[value="' + $('#product_idx_dipstick_inventory').val() + '"]').attr('data-id');
			var tank_idx 						= $('#product_tank_list option[value="' + $('#product_tank_idx_dipstick_inventory').val() + '"]').attr('data-id');
			var beginning_inventory 			= $("input[name=beginning_dipstick_inventory]").val();
			var sales_in_liters_inventory 		= $("input[name=sales_in_liters_dipstick_inventory]").val();
			var ugt_pumping_inventory			= $("input[name=ugt_pumping_dipstick_inventory]").val();
			var delivery_inventory 				= $("input[name=delivery_dipstick_inventory]").val();
			var ending_inventory 				= $("input[name=ending_dipstick_inventory]").val();
			
			document.getElementById('CRPH7_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH7') }}",
					type:"POST",
					data:{
					  CRPH7_ID:0,
					  CashiersReportId:CashiersReportId,
					  product_idx:product_idx,
					  tank_idx:tank_idx,
					  beginning_inventory:beginning_inventory,
					  sales_in_liters_inventory:sales_in_liters_inventory,
					  ugt_pumping_inventory:ugt_pumping_inventory,
					  delivery_inventory:delivery_inventory,
					  ending_inventory:ending_inventory, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
							
						$('#product_idx_dipstick_inventoryError').text('');	
						$('#product_tank_idx_dipstick_inventoryError').text('');					
						$('#beginning_dipstick_inventoryError').text('');		
						$('#delivery_dipstick_inventoryError').text('');
						$('#ending_dipstick_inventoryError').text('');
						$('#sales_in_liters_dipstick_inventoryError').text('');
						$('#ugt_pumping_dipstick_inventoryError').text('');
						
						/*Clear Form*/
						document.getElementById("product_idx_dipstick_inventory").value = '';
						document.getElementById("product_tank_idx_dipstick_inventory").value = '';
						document.getElementById("beginning_dipstick_inventory").value = '';
						document.getElementById("sales_in_liters_dipstick_inventory").value = '';
						document.getElementById("ugt_pumping_dipstick_inventory").value = '';
						document.getElementById("delivery_dipstick_inventory").value = '';
						document.getElementById("ending_dipstick_inventory").value = '';
						
						LoadCashiersReportPH7();
						document.getElementById('CRPH7_form').className = "g-3 needs-validation";
					  }
					},
					error: function(error) {
					 console.log(error);
					 	
							let product_name_dipstick_inventory = $("input[name=product_name_dipstick_inventory]").val();				
							if(error.responseJSON.errors.product_idx=='Product is Required'){
									
									if(product_name_dipstick_inventory==''){
										$('#product_idx_dipstick_inventoryError').html(error.responseJSON.errors.product_idx);
									}else{
										$('#product_idx_dipstick_inventoryError').html("Incorrect Product <b>"+product_name_dipstick_inventory+"</b>");
									}
									
									document.getElementById("product_idx_dipstick_inventory").value = "";
									document.getElementById('product_idx_dipstick_inventoryError').className = "invalid-feedback";
							
							}
							
							
							let product_tank_name_dipstick_inventory = $("input[name=product_tank_name_dipstick_inventory]").val();				
							if(error.responseJSON.errors.tank_idx=='Tank is Required'){
									
									if(product_tank_name_dipstick_inventory==''){
										$('#product_tank_idx_dipstick_inventoryError').html(error.responseJSON.errors.tank_idx);
									}else{
										$('#product_tank_idx_dipstick_inventoryError').html("Incorrect Tank <b>"+product_tank_name_dipstick_inventory+"</b>");
									}
									
									document.getElementById("product_tank_idx_dipstick_inventory").value = "";
									document.getElementById('product_tank_idx_dipstick_inventoryError').className = "invalid-feedback";
							
							}	
							
							$('#beginning_dipstick_inventoryError').text(error.responseJSON.errors.beginning_inventory);
							document.getElementById('beginning_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#delivery_dipstick_inventoryError').text(error.responseJSON.errors.delivery_inventory);
							document.getElementById('delivery_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#ending_dipstick_inventoryError').text(error.responseJSON.errors.ending_inventory);
							document.getElementById('ending_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#sales_in_liters_dipstick_inventoryError').text(error.responseJSON.errors.sales_in_liters_inventory);
							document.getElementById('sales_in_liters_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#ugt_pumping_dipstick_inventoryError').text(error.responseJSON.errors.ugt_pumping_inventory);
							document.getElementById('ugt_pumping_dipstick_inventoryError').className = "invalid-feedback";
							
					}
				   });		
		 });
		
	$('body').on('click','#CHPH7_Edit',function(){		
	
			event.preventDefault();
			let CHPH7_ID = $(this).data('id');	
			
			$.ajax({
				url: "{{ route('CRP7_info') }}",
				type:"POST",
				data:{
				  CHPH7_ID:CHPH7_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {				
				
					document.getElementById("update-CRPH7").value = CHPH7_ID;				
					/*Set Details		*/			
					
					LoadProductTank_Update('dipstick');
					
					document.getElementById("update_product_idx_dipstick_inventory").value 			= response[0].product_name;
					
					document.getElementById("update_product_tank_idx_dipstick_inventory").value 	= response[0].tank_name;
					
					document.getElementById("update_beginning_dipstick_inventory").value 			= response[0].beginning_inventory;
					document.getElementById("update_sales_in_liters_dipstick_inventory").value 		= response[0].sales_in_liters;
					document.getElementById("update_ugt_pumping_dipstick_inventory").value 			= response[0].ugt_pumping;
					
					document.getElementById("update_delivery_dipstick_inventory").value 			= response[0].delivery;
					document.getElementById("update_ending_dipstick_inventory").value 				= response[0].ending_inventory;
				
					var TotalBookStock 	= response[0].beginning_inventory - (response[0].sales_in_liters - response[0].ugt_pumping) + response[0].delivery;
					var TotalVariance 	= TotalBookStock - response[0].ending_inventory;
				
					$('#update_TotalBookStock_dipstick_inventory').html(TotalBookStock.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					$('#update_TotalVariance_dipstick_inventory').html(TotalVariance.toLocaleString("en-PH", {minimumFractionDigits: 2}));
					
					
					
					$('#Update_CRPH7_Modal').modal('toggle');
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	});


	/*Call Function for Product Cahier's Report*/
	$("#update-CRPH7").click(function(event){
		alert();
			event.preventDefault();
				
				$('#update_product_idx_dipstick_inventoryError').text('');	
				$('#update_product_tank_idx_dipstick_inventoryError').text('');					
				$('#update_beginning_dipstick_inventoryError').text('');		
				$('#update_delivery_dipstick_inventoryError').text('');	
				$('#update_ugt_pumping_dipstick_inventoryError').text('');
				$('#update_ending_dipstick_inventoryError').text('');
				$('#update_sales_in_liters_dipstick_inventoryError').text('');
			
			let CashiersReportId 				= {{ $CashiersReportId }};			
			
			let CRPH7_ID = document.getElementById("update-CRPH7").value;
		
			var product_idx 					= $('#product_list_inventory option[value="' + $('#update_product_idx_dipstick_inventory').val() + '"]').attr('data-id');
			var tank_idx 						= $('#update_product_tank_list option[value="' + $('#update_product_tank_idx_dipstick_inventory').val() + '"]').attr('data-id');
			var beginning_inventory 			= $("input[name=update_beginning_dipstick_inventory]").val();
			var sales_in_liters_inventory 		= $("input[name=update_sales_in_liters_dipstick_inventory]").val();
			var ugt_pumping_inventory			= $("input[name=update_ugt_pumping_dipstick_inventory]").val();
			var delivery_inventory 				= $("input[name=update_delivery_dipstick_inventory]").val();
			var ending_inventory 				= $("input[name=update_ending_dipstick_inventory]").val();
			
			document.getElementById('update_CRPH7_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH7') }}",
					type:"POST",
					data:{
					  CRPH7_ID:CRPH7_ID,
					  CashiersReportId:CashiersReportId,
					  CashiersReportId:CashiersReportId,
					  product_idx:product_idx,
					  tank_idx:tank_idx,
					  beginning_inventory:beginning_inventory,
					  sales_in_liters_inventory:sales_in_liters_inventory,
					  ugt_pumping_inventory:ugt_pumping_inventory,
					  delivery_inventory:delivery_inventory,
					  ending_inventory:ending_inventory, 
					  
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
							
						$('#update_product_idx_dipstick_inventoryError').text('');	
						$('#update_product_tank_idx_dipstick_inventoryError').text('');					
						$('#update_beginning_dipstick_inventoryError').text('');		
						$('#update_delivery_dipstick_inventoryError').text('');	
						$('#update_ugt_pumping_dipstick_inventoryError').text('');
						$('#update_ending_dipstick_inventoryError').text('');
						$('#update_sales_in_liters_dipstick_inventoryError').text('');
						
						/*Clear Form*/
						document.getElementById("update_product_idx_dipstick_inventory").value = '';
						document.getElementById("update_product_tank_idx_dipstick_inventory").value = '';
						document.getElementById("update_beginning_dipstick_inventory").value = '';
						document.getElementById("update_sales_in_liters_dipstick_inventory").value = '';
						document.getElementById("update_ugt_pumping_dipstick_inventory").value = '';
						document.getElementById("update_delivery_dipstick_inventory").value = '';
						document.getElementById("update_ending_dipstick_inventory").value = '';
						
						LoadCashiersReportPH7();
						$('#Update_CRPH7_Modal').modal('toggle');
						document.getElementById('update_CRPH7_form').className = "g-3 needs-validation";
						
					  }
					},
					error: function(error) {
					 console.log(error);
					 
							let product_name_dipstick_inventory = $("input[name=update_product_name_dipstick_inventory]").val();				
							if(error.responseJSON.errors.product_idx=='Product is Required'){
									
									if(product_name_dipstick_inventory==''){
										$('#update_product_idx_dipstick_inventoryError').html(error.responseJSON.errors.product_idx);
									}else{
										$('#update_product_idx_dipstick_inventoryError').html("Incorrect Product <b>"+product_name_dipstick_inventory+"</b>");
									}
									
									document.getElementById("update_product_idx_dipstick_inventory").value = "";
									document.getElementById('update_product_idx_dipstick_inventoryError').className = "invalid-feedback";
							
							}
							
							
							let product_tank_name_dipstick_inventory = $("input[name=update_product_tank_name_dipstick_inventory]").val();				
							if(error.responseJSON.errors.tank_idx=='Tank is Required'){
									
									if(product_tank_name_dipstick_inventory==''){
										$('#update_product_tank_idx_dipstick_inventoryError').html(error.responseJSON.errors.tank_idx);
									}else{
										$('#update_product_tank_idx_dipstick_inventoryError').html("Incorrect Tank <b>"+product_tank_name_dipstick_inventory+"</b>");
									}
									
									document.getElementById("update_product_tank_idx_dipstick_inventory").value = "";
									document.getElementById('update_product_tank_idx_dipstick_inventoryError').className = "invalid-feedback";
							
							}	
							
							$('#update_beginning_dipstick_inventoryError').text(error.responseJSON.errors.beginning_inventory);
							document.getElementById('update_beginning_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#update_delivery_dipstick_inventoryError').text(error.responseJSON.errors.delivery_inventory);
							document.getElementById('update_delivery_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#update_ending_dipstick_inventoryError').text(error.responseJSON.errors.ending_inventory);
							document.getElementById('update_ending_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#update_sales_in_liters_dipstick_inventoryError').text(error.responseJSON.errors.sales_in_liters_inventory);
							document.getElementById('update_sales_in_liters_dipstick_inventoryError').className = "invalid-feedback";
							
							$('#update_ugt_pumping_dipstick_inventoryError').text(error.responseJSON.errors.ugt_pumping_inventory);
							document.getElementById('update_ugt_pumping_dipstick_inventoryError').className = "invalid-feedback";							
							
					}
				   });		
		 });

	LoadCashiersReportPH7(); 

	function LoadCashiersReportPH7() {		
	
		$("#table_product_dipstick_inventory_body_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_dipstick_inventory_body_data');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersP7') }}",
				type:"POST",
				data:{
				  CashiersReportId:CashiersReportId,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
						var len = response.length;
						for(var i=0; i<len; i++){

							var cashiers_report_p7_id = response[i].cashiers_report_p7_id;
						
							var product_name = response[i].product_name;
							var tank_name = response[i].tank_name;	
							var tank_capacity = response[i].tank_capacity;		
							
							var beginning_dipstick_inventory = response[i].beginning_inventory.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var sales_in_liters = response[i].sales_in_liters.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var delivery = response[i].delivery.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var ending_dipstick_inventory = response[i].ending_inventory.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var book_stock = response[i].book_stock.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var variance = response[i].variance.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_dipstick_inventory_body_data tr:last').after("<tr>"+
							"<td class='product_td' align='left'>"+product_name+"</td>"+
							"<td class='beginning_reading_dipstick_inventory_td' align='left'>"+tank_name+"</td>"+
							"<td class='beginning_reading_dipstick_inventory_td' align='right'>"+tank_capacity+"</td>"+
							"<td class='sales_in_liters_dipstick_inventory_td' align='right'>"+beginning_dipstick_inventory+"</td>"+
							"<td class='delivery_dipstick_inventory_td' align='right'>"+sales_in_liters+"</td>"+
							"<td class='delivery_dipstick_inventory_td' align='right'>"+delivery+"</td>"+
							"<td class='manual_price_td' align='right'>"+ending_dipstick_inventory+"</td>"+
							"<td class='manual_price_td' align='right'>"+book_stock+"</td>"+
							"<td class='manual_price_td' align='right'>"+variance+"</td>"+
							
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH7_Edit' data-id='"+cashiers_report_p7_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='CHPH7_Delete'  data-id='"+cashiers_report_p7_id+"'></a></div></td>"+
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

	$('body').on('click','#CHPH7_Delete',function(){		
	
			event.preventDefault();
			let CHPH7_ID = $(this).data('id');	
			
			$.ajax({
				url: "{{ route('CRP7_info') }}",
				type:"POST",
				data:{
				  CHPH7_ID:CHPH7_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {				
				
					document.getElementById("deleteCRPH7Confirmed").value = CHPH7_ID;				
					/*Set Details*/					
					
					$('#delete_product_idx_dipstick_inventory').text(response[0].product_name);
					$('#delete_product_tank_idx_dipstick_inventory').text(response[0].tank_name);
					$('#delete_beginning_dipstick_inventory').text(response[0].beginning_inventory);
					$('#delete_sales_in_liters_dipstick_inventory').text(response[0].sales_in_liters);
					$('#delete_ugt_pumping').text(response[0].ugt_pumping);
					$('#delete_delivery_dipstick_inventory').text(response[0].delivery);
					$('#delete_ending_dipstick_inventory').text(response[0].ending_inventory);
					
					$('#CRPH7_delete_TotalBookStock').text(response[0].book_stock);
					$('#CRPH7_delete_TotalVariance').text(response[0].variance);
					
					$('#CRPH7DeleteModal').modal('toggle');	
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	});


	$('body').on('click','#deleteCRPH7Confirmed',function(){
			
		let CHPH7_ID = document.getElementById("deleteCRPH7Confirmed").value;
		
		if(CHPH7_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP7') }}",
				type:"POST",
				data:{
				  CHPH7_ID:CHPH7_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					LoadCashiersReportPH7();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});
</script>
