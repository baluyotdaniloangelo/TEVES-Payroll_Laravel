   <script type="text/javascript">
	/*Load Cashiers Report Phase 1*/
	LoadCashiersReportPH1();
	LoadCashiersReportPH2();

    LoadCashiersReportPH3_SALES_CREDIT();
	LoadCashiersReportPH3_DISCOUNT();
    LoadCashiersReportPH3_OTHERS();

	//LoadCashiersReportPH4();
	LoadCashiersReportPH5();
	LoadCashiersReportSummary();
	<!--Save New Client->
	$("#update-cashiers-report").click(function(event){
			
			event.preventDefault();
			
			/*Reset Warnings*/
				$('#teves_branchError').text('');
				$('#cashiers_nameError').text('');
				$('#forecourt_attendantError').text('');
				$('#report_dateError').text('');

			document.getElementById('CashierReportformNew').className = "g-3 needs-validation was-validated";
			
			let CashiersReportId 			= {{ $CashiersReportId }};
			
			let teves_branch 				= $("#teves_branch").val();
			let forecourt_attendant 		= $("input[name=forecourt_attendant]").val();
			let cashiers_name 				= $("input[name=cashiers_name]").val();
			let report_date 				= $("input[name=report_date]").val();
			let shift 						= $("input[name=shift]").val();
			
			  $.ajax({
				url: "/update_cashier_report_post",
				type:"POST",
				data:{
				  CashiersReportId:CashiersReportId,
				  teves_branch:teves_branch, 
				  cashiers_name:cashiers_name,
				  forecourt_attendant:forecourt_attendant,
				  report_date:report_date,
				  shift:shift,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#teves_branchError').text('');
					$('#cashiers_nameError').text('');
					$('#forecourt_attendantError').text('');
					$('#report_dateError').text('');

					LoadCashiersReportPH1();
					LoadProductList(teves_branch);
					document.getElementById("CRPH1_Modal_add").disabled = false;
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.teves_branch=="The client name has already been taken."){
							  
				  $('#teves_branchError').html("<b>"+ teves_branch +"</b> has already been taken.");
				  document.getElementById('teves_branchError').className = "invalid-feedback";
				  document.getElementById('teves_branch').className = "form-control is-invalid";
				  $('#teves_branch').val("");
				  
				  
				  
				}else{
				  $('#teves_branchError').text(error.responseJSON.errors.teves_branch);
				  document.getElementById('teves_branchError').className = "invalid-feedback";
				}
				
				  $('#cashiers_nameError').text(error.responseJSON.errors.cashiers_name);
				  document.getElementById('cashiers_nameError').className = "invalid-feedback";
				  
				  $('#forecourt_attendantError').text(error.responseJSON.errors.forecourt_attendant);
				  document.getElementById('forecourt_attendantError').className = "invalid-feedback";	
				  
				  $('#report_dateError').text(error.responseJSON.errors.report_date);
				  document.getElementById('report_dateError').className = "invalid-feedback";			

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				
				document.getElementById("CRPH1_Modal_add").disabled = false;
				  
				}
			   });		
	  });


	function LoadProductList(branch_id) {		
	
		$("#product_name span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_name');
		
		$("#product_name_PH2 span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_name_PH2');

		$("#product_list_PH3 span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_list_PH3');

		$("#product_list_inventory span").remove();
		$('<span style="display: none;"></span>').appendTo('#product_list_inventory');
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
	
							$('#product_name span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
							"<option label='&#8369; "+product_price+" | "+product_name+"' data-id='"+product_id+"' value='"+product_name+"' data-price='"+product_price+"' >" +
							"</span>");	
							
							$('#product_name_PH2 span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
							"<option label='&#8369; "+product_price+" | "+product_name+"' data-id='"+product_id+"' value='"+product_name+"' data-price='"+product_price+"' >" +
							"</span>");	
							
							$('#product_list_PH3 span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
							"<option label='&#8369; "+product_price+" | "+product_name+"' data-id='"+product_id+"' value='"+product_name+"' data-price='"+product_price+"' >" +
							"</span>");	
							
							$('#product_list_inventory span:last').after("<span style='font-family: DejaVu Sans; sans-serif;'>"+
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
	
	function UpdateBranch(){ 
	
		$('#switch_notice_off').show();
		$('#sw_off').html("You selected a new branch, to confirm changes click the update button");
		setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },2000);
		
		/*Disable the Add Product Button Until Changes not Save*/
		document.getElementById("CRPH1_Modal_add").disabled = false;
		
	}

	function TotalAmount(){
		
		let product_price 			= $('#product_name option[value="' + $('#product_idx').val() + '"]').attr('data-price');
		let product_manual_price 	= $("#product_manual_price").val();	
		var beginning_reading 		= $("input[name=beginning_reading]").val();
		var closing_reading 		= $("input[name=closing_reading]").val();
		var calibration 			= $("input[name=calibration]").val();	
		let order_quantity 			= (closing_reading - beginning_reading) - calibration;
		
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
		
		let product_price 			= $('#update_product_name option[value="' + $('#update_product_idx').val() + '"]').attr('data-price');
		let product_manual_price 	= $("#update_product_manual_price").val();		
		var beginning_reading 		= $("input[name=update_beginning_reading]").val();
		var closing_reading 		= $("input[name=update_closing_reading]").val();
		var calibration 			= $("input[name=update_calibration]").val();		
		let order_quantity 			= (closing_reading - beginning_reading) - calibration;
		
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
	
	/*Call Function for Product Cahier's Report*/
	$("#save-CRPH1").click(function(event){
		
			event.preventDefault();
			
			$('#product_idxError').text('');
			$('#beginning_readingError').text('');
			$('#closing_readingError').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
		
			var product_idx 			= $('#product_name option[value="' + $('#product_idx').val() + '"]').attr('data-id');
			var beginning_reading 		= $("input[name=beginning_reading]").val();
			var closing_reading 		= $("input[name=closing_reading]").val();
			var calibration 			= $("input[name=calibration]").val();
			var product_manual_price 	= $("input[name=product_manual_price]").val();
			let teves_branch 			= $("#teves_branch").val();

			document.getElementById('CRPH1_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH1') }}",
					type:"POST",
					data:{
					  CHPH1_ID:0,
					  CashiersReportId:CashiersReportId,
					  branch_idx:teves_branch,
					  product_idx:product_idx,
					  beginning_reading:beginning_reading,
					  closing_reading:closing_reading,
					  calibration:calibration,
					  product_manual_price:product_manual_price, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						LoadCashiersReportPH1();
						$('#product_idxError').text('');					
						$('#beginning_readingError').text('');		
						$('#closing_readingError').text('');
						
						/*Clear Form*/
						document.getElementById("product_idx").value = '';
						document.getElementById("beginning_reading").value = '';
						document.getElementById("closing_reading").value = '';
						document.getElementById("calibration").value = '';
						document.getElementById("product_manual_price").value = '';
						
						LoadCashiersReportSummary();
						
					  }
					},
					error: function(error) {
					 console.log(error);
						
							$('#product_idxError').text(error.responseJSON.errors.product_idx);
							document.getElementById('product_idxError').className = "invalid-feedback";
							
							
							if(error.responseJSON.errors.beginning_reading=="The beginning reading has already been taken."){
							  
								$('#beginning_readingError').text("The Beginning Reading already exist for the Selected Product.");
								document.getElementById('beginning_readingError').className = "invalid-feedback";	
								
								$('#beginning_reading').val("");
								  
							}else {
								
								$('#beginning_readingError').text(error.responseJSON.errors.beginning_reading);
								document.getElementById('beginning_readingError').className = "invalid-feedback";		
								
							}
				
							if(error.responseJSON.errors.closing_reading=="The closing reading has already been taken."){
							  
								$('#closing_readingError').text("The Closing Reading already exist for the Selected Product.");
								document.getElementById('closing_readingError').className = "invalid-feedback";	
								
								$('#closing_reading').val("");
								  
							}else {
								
								$('#closing_readingError').text(error.responseJSON.errors.closing_reading);
								document.getElementById('closing_readingError').className = "invalid-feedback";		
								
							}
							
							
							
					}
				   });		
		 });

	function LoadCashiersReportPH1() {		
		//event.preventDefault();
		$("#table_product_body_data tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_body_data');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersProductP1') }}",
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
						
							var cashiers_report_p1_id = response[i].cashiers_report_p1_id;						
							var product_idx = response[i].product_idx;						
							var product_price = response[i].product_price.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var product_name = response[i].product_name;
							var beginning_reading = response[i].beginning_reading;
							var order_quantity = response[i].order_quantity.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var closing_reading = response[i].closing_reading;
							var calibration = response[i].calibration;
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_body_data tr:last').after("<tr>"+
							"<td class='product_td' align='center'>"+product_name+"</td>"+
							"<td class='beginning_reading_td' align='center'>"+beginning_reading+"</td>"+
							"<td class='closing_reading_td' align='center'>"+closing_reading+"</td>"+
							"<td class='calibration_td' align='center'>"+calibration+"</td>"+
							"<td class='calibration_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+product_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH1_Edit' data-id='"+cashiers_report_p1_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersProductP1'  data-id='"+cashiers_report_p1_id+"'></a></div></td>"+
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
	$('body').on('click','#CHPH1_Edit',function(){			
			event.preventDefault();
			let CHPH1_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP1_info') }}",
				type:"POST",
				data:{
				  CHPH1_ID:CHPH1_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH1").value = CHPH1_ID;				
					/*Set Details*/					
					document.getElementById("update_product_idx").value 			= response[0].product_name;
					document.getElementById("update_beginning_reading").value 		= response[0].beginning_reading;
					document.getElementById('update_closing_reading').value 		= response[0].closing_reading;
					document.getElementById("update_calibration").value 			= response[0].calibration;
					document.getElementById("update_product_manual_price").value 	= response[0].product_price;
					
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));				
					$('#Update_CRPH1_Modal').modal('toggle');		
						
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	/*Call Function for Product Cahier's Report*/
	$("#update-CRPH1").click(function(event){
		
			event.preventDefault();
			
			$('#update_product_idxError').text('');
			$('#update_beginning_readingError').text('');
			$('#update_closing_readingError').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
			let CHPH1_ID 				= document.getElementById("update-CRPH1").value;
			var product_idx 			= $('#update_product_name option[value="' + $('#update_product_idx').val() + '"]').attr('data-id');
			var beginning_reading 		= $("input[name=update_beginning_reading]").val();
			var closing_reading 		= $("input[name=update_closing_reading]").val();
			var calibration 			= $("input[name=update_calibration]").val();
			var product_manual_price 	= $("input[name=update_product_manual_price]").val();
			let teves_branch 			= $("#teves_branch").val();

			document.getElementById('CRPH1_form_edit').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH1') }}",
					type:"POST",
					data:{ 
					  CHPH1_ID:CHPH1_ID,
					  CashiersReportId:CashiersReportId,
					  product_idx:product_idx,
					  branch_idx:teves_branch,
					  beginning_reading:beginning_reading,
					  closing_reading:closing_reading,
					  calibration:calibration,
					  product_manual_price:product_manual_price, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						LoadCashiersReportPH1();
						$('#update_product_idxError').text('');					
						$('#update_beginning_readingError').text('');		
						$('#update_closing_readingError').text('');
						
						/*Clear Form*/
						$('#Update_CRPH1_Modal').modal('toggle');
						LoadCashiersReportSummary();
					  }
					},
					error: function(error) {
					 console.log(error);
					 
							$('#update_product_idxError').text(error.responseJSON.errors.product_idx);
							document.getElementById('update_product_idxError').className = "invalid-feedback";
							
							if(error.responseJSON.errors.beginning_reading=="The beginning reading has already been taken."){
							  
								$('#update_beginning_readingError').text("The Beginning Reading already exist for the Selected Product.");
								document.getElementById('update_beginning_readingError').className = "invalid-feedback";	
								
								$('#update_beginning_reading').val("");
								  
							}else {
								
								$('#update_beginning_readingError').text(error.responseJSON.errors.beginning_reading);
								document.getElementById('update_beginning_readingError').className = "invalid-feedback";		
								
							}
				
							if(error.responseJSON.errors.closing_reading=="The closing reading has already been taken."){
							  
								$('#update_closing_readingError').text("The Closing Reading already exist for the Selected Product.");
								document.getElementById('update_closing_readingError').className = "invalid-feedback";	
								
								$('#update_closing_reading').val("");
								  
							}else {
								
								$('#update_closing_readingError').text(error.responseJSON.errors.closing_reading);
								document.getElementById('update_closing_readingError').className = "invalid-feedback";		
								
							}
							
					}
				   });		
	});

	<!--CRPH1 Deletion Confirmation-->	
	$('body').on('click','#deleteCashiersProductP1',function(){
			
			event.preventDefault();
			let CHPH1_ID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('CRP1_info') }}",
				type:"POST",
				data:{
				  CHPH1_ID:CHPH1_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteCRPH1Confirmed").value = CHPH1_ID;
					
					/*Set Details*/
					$('#bill_delete_order_date').text(response[0].order_date);

					$('#CRPH1_delete_product_idx').text(response[0].product_name);
					$('#CRPH1_delete_beginning_reading').text(response[0].beginning_reading);
					$('#CRPH1_delete_closing_reading').text(response[0].closing_reading);
					$('#CRPH1_delete_calibration').text(response[0].calibration);
					$('#CRPH1_delete_product_order_quantity').text(response[0].order_quantity);
					$('#CRPH1_delete_product_manual_price').text(response[0].product_price);
					$('#CRPH1_delete_order_total_amount').text(response[0].order_total_amount);
					
					$('#CRPH1DeleteModal').modal('toggle');					
					LoadCashiersReportSummary();
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	$('body').on('click','#deleteCRPH1Confirmed',function(){
			
		let CHPH1_ID = document.getElementById("deleteCRPH1Confirmed").value;
		
		if(CHPH1_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP1') }}",
				type:"POST",
				data:{
				  CHPH1_ID:CHPH1_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					LoadCashiersReportPH1();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});


	/*Part 2*/
	$("#save-CRPH2").click(function(event){
		
			event.preventDefault();
			
			$('#product_idx_PH2Error').text('');
			$('#order_quantity_PH2Error').text('');
			$('#product_manual_price_PH2Error').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
		
			var product_idx 			= $('#product_name_PH2 option[value="' + $('#product_idx_PH2').val() + '"]').attr('data-id');
			var order_quantity 			= $("input[name=order_quantity_PH2]").val();
			var product_manual_price 	= $("input[name=product_manual_price_PH2]").val();
			let teves_branch 			= $("#teves_branch").val();

			document.getElementById('CRPH2_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH2') }}",
					type:"POST",
					data:{
					  CHPH2_ID:0,
					  CashiersReportId:CashiersReportId,
					  product_idx:product_idx,
					  branch_idx:teves_branch,
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
						LoadCashiersReportPH2();
						$('#product_idx_PH2Error').text('');					
						$('#order_quantity_PH2Error').text('');		
						$('#product_manual_price_PH2Error').text('');
						
						/*Clear Form*/
						document.getElementById("product_idx_PH2").value = '';
						document.getElementById("order_quantity_PH2").value = '';
						document.getElementById("product_manual_price_PH2").value = '';
						LoadCashiersReportSummary();
					  }
					},
					error: function(error) {
					 console.log(error);
						/*alert(error);*/
							$('#product_idx_PH2Error').text(error.responseJSON.errors.product_idx);
							document.getElementById('product_idx_PH2Error').className = "invalid-feedback";
							
							$('#order_quantity_PH2Error').text(error.responseJSON.errors.order_quantity);
							document.getElementById('order_quantity_PH2Error').className = "invalid-feedback";
							
							$('#product_manual_price_PH2Error').text(error.responseJSON.errors.closing_reading);
							document.getElementById('product_manual_price_PH2Error').className = "invalid-feedback";
					}
				   });		
		 });

	function LoadCashiersReportPH2() {		
		$("#table_product_data_other_sales tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_data_other_sales');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersProductP2') }}",
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
						
							var cashiers_report_p2_id = response[i].cashiers_report_p2_id;						
							var product_idx = response[i].product_idx;						
							var product_price = response[i].product_price.toLocaleString("en-PH", {minimumFractionDigits: 2});
							var product_name = response[i].product_name;
							var order_quantity = response[i].order_quantity;
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2});
							
							$('#table_product_data_other_sales tr:last').after("<tr>"+
							"<td class='product_td' align='center'>"+product_name+"</td>"+
							"<td class='calibration_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+product_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH2_Edit' data-id='"+cashiers_report_p2_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersProductP2'  data-id='"+cashiers_report_p2_id+"'></a></div></td>"+
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

	$('body').on('click','#CHPH2_Edit',function(){			
			event.preventDefault();
			let CHPH2_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP2_info') }}",
				type:"POST",
				data:{
				  CHPH2_ID:CHPH2_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH2").value = CHPH2_ID;				
					/*Set Details*/					
					document.getElementById("update_product_idx_PH2").value 			= response[0].product_name;
					document.getElementById("update_order_quantity_PH2").value 			= response[0].order_quantity;
					document.getElementById("update_product_manual_price_PH2").value 	= response[0].product_price;
					
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));				
					$('#Update_CRPH2_Modal').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$("#update-CRPH2").click(function(event){
		
			event.preventDefault();
			
			$('#update_product_idx_PH2Error').text('');
			$('#update_order_quantity_PH2Error').text('');
			$('#update_product_manual_price_PH2Error').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
			let CHPH2_ID 				= document.getElementById("update-CRPH2").value;
			var product_idx 			= $('#update_product_name_PH2 option[value="' + $('#update_product_idx_PH2').val() + '"]').attr('data-id');
			var order_quantity 			= $("input[name=update_order_quantity_PH2]").val();
			var product_manual_price 	= $("input[name=update_product_manual_price_PH2]").val();
			let teves_branch 			= $("#teves_branch").val();

			document.getElementById('CRPH2_form_edit').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH2') }}",
					type:"POST",
					data:{
					  CHPH2_ID:CHPH2_ID,
					  CashiersReportId:CashiersReportId,
					  product_idx:product_idx,
					  branch_idx:teves_branch,
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
						LoadCashiersReportPH2();
						LoadCashiersReportSummary();
						$('#update_product_idx_PH2Error').text('');					
						$('#update_order_quantity_PH2Error').text('');		
						$('#update_product_manual_price_PH2Error').text('');
						/*Close Form*/
						$('#Update_CRPH2_Modal').modal('toggle');		
					  }
					},
					error: function(error) {
					 console.log(error);
						/*alert(error);*/
							$('#update_product_idx_PH2Error').text(error.responseJSON.errors.product_idx);
							document.getElementById('update_product_idx_PH2Error').className = "invalid-feedback";
							
							$('#update_order_quantity_PH2Error').text(error.responseJSON.errors.order_quantity);
							document.getElementById('update_order_quantity_PH2Error').className = "invalid-feedback";
							
							$('#update_product_manual_price_PH2Error').text(error.responseJSON.errors.closing_reading);
							document.getElementById('update_product_manual_price_PH2Error').className = "invalid-feedback";
					}
				   });		
		 });


	<!--CRPH1 Deletion Confirmation-->	
	$('body').on('click','#deleteCashiersProductP2',function(){
			event.preventDefault();
			let CHPH2_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP2_info') }}",
				type:"POST",
				data:{
				  CHPH2_ID:CHPH2_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("deleteCRPH2Confirmed").value = CHPH2_ID;				
					/*Set Details*/					
					//$('#CRPH1_delete_product_idx').text(response[0].product_name);
					$('#delete_product_idx_PH2').text(response[0].product_name);
					$('#delete_order_quantity_PH2').text(response[0].order_quantity);
					$('#delete_product_manual_price_PH2').text(response[0].product_price);
					
					var total_amount = response[0].order_total_amount;
					$('#delete_TotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));				
					$('#CRPH2DeleteModal').modal('toggle');			
										
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$('body').on('click','#deleteCRPH2Confirmed',function(){
			
		let CHPH2_ID = document.getElementById("deleteCRPH2Confirmed").value;
		
		if(CHPH2_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP2') }}",
				type:"POST",
				data:{
				  CHPH2_ID:CHPH2_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					LoadCashiersReportPH2();
					LoadCashiersReportSummary();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});

	function TotalAmount_PH2(){
		
		let product_price 			= $('#product_name_PH2 option[value="' + $('#product_idx_PH2').val() + '"]').attr('data-price');
		let product_manual_price 	= $("#product_manual_price_PH2").val();		
		let order_quantity 			= $("input[name=order_quantity_PH2]").val();
		
		if(order_quantity!=0 || order_quantity!=''){
			if(product_manual_price!='' && product_manual_price!=0){
				var total_amount = product_manual_price * order_quantity;
				$('#TotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}else{
				var total_amount = product_price * order_quantity;
				$('#TotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}
		}		
	}

	function UpdateTotalAmount_PH2(){
		
		let product_price 			= $('#update_product_name_PH2 option[value="' + $('#update_product_idx_PH2').val() + '"]').attr('data-price');
		let product_manual_price 	= $("#update_product_manual_price_PH2").val();		
		let order_quantity 			= $("input[name=update_order_quantity_PH2]").val();
		
		if(order_quantity!=0 || order_quantity!=''){
			if(product_manual_price!='' && product_manual_price!=0){
				var total_amount = product_manual_price * order_quantity;
				$('#UpdateTotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}else{
				var total_amount = product_price * order_quantity;
				$('#UpdateTotalAmount_PH2').html(total_amount.toLocaleString("en-PH", {minimumFractionDigits: 2}));
			}
		}		
	}	

	/*Part 3*/
    function input_settings_create_PH3(){

    var miscellaneous_items_type 	= $("#miscellaneous_items_type_PH3").val();

        if(miscellaneous_items_type == 'DISCOUNTS'){

            document.getElementById("reference_no_PH3").disabled = false;
            document.getElementById("product_manual_price_PH3").disabled = false;
			document.getElementById("product_idx_PH3").disabled = false;
			document.getElementById("order_quantity_PH3").disabled = false;
			
            document.getElementById("quantity_label").innerHTML = "LITERS";
            document.getElementById("manual_price_label").innerHTML = "UNIT PRICE";
           

        }else if(miscellaneous_items_type == 'SALES_CREDIT'){

            document.getElementById("reference_no_PH3").disabled = true;
            document.getElementById("product_manual_price_PH3").disabled = false;
			document.getElementById("product_idx_PH3").disabled = false;
			document.getElementById("order_quantity_PH3").disabled = false;
			
            document.getElementById("quantity_label").innerHTML = "LITERS";
            document.getElementById("manual_price_label").innerHTML = "AMOUNT";

        }/*else if(miscellaneous_items_type == 'SALES_CREDIT'){
			
            document.getElementById("reference_no_PH3").disabled = false;
            document.getElementById("product_manual_price_PH3").disabled = false;
			document.getElementById("product_idx_PH3").disabled = true;
			document.getElementById("order_quantity_PH3").disabled = true;
			
            document.getElementById("quantity_label").innerHTML = "LITERS/PCS";
            document.getElementById("manual_price_label").innerHTML = "AMOUNT";

		}*/else{

            document.getElementById("reference_no_PH3").disabled = false;
            document.getElementById("product_manual_price_PH3").disabled = false;
			document.getElementById("product_idx_PH3").disabled = false;
			document.getElementById("order_quantity_PH3").disabled = false;
			
            document.getElementById("quantity_label").innerHTML = "LITERS/PCS";
            document.getElementById("manual_price_label").innerHTML = "AMOUNT";

        }
        

    }

    function update_input_settings_create_PH3(){

    var miscellaneous_items_type 	= $("#update_miscellaneous_items_type_PH3").val();

        if(miscellaneous_items_type == 'DISCOUNTS'){

            document.getElementById("update_reference_no_PH3").disabled = false;
            document.getElementById("update_product_manual_price_PH3").disabled = false;
			document.getElementById("update_product_idx_PH3").disabled = false;
			document.getElementById("update_order_quantity_PH3").disabled = false;
			
            document.getElementById("update_quantity_label").innerHTML = "LITERS";
            document.getElementById("update_manual_price_label").innerHTML = "UNIT PRICE";
           

        }else if(miscellaneous_items_type == 'SALES_CREDIT'){
			
            document.getElementById("update_reference_no_PH3").disabled = false;
            document.getElementById("update_product_manual_price_PH3").disabled = false;
			document.getElementById("update_product_idx_PH3").disabled = false;
			document.getElementById("update_order_quantity_PH3").disabled = false;
			
            document.getElementById("update_quantity_label").innerHTML = "LITERS/PCS";
            document.getElementById("update_manual_price_label").innerHTML = "AMOUNT";

		}else{

            document.getElementById("update_reference_no_PH3").disabled = false;
            document.getElementById("update_product_manual_price_PH3").disabled = false;
			document.getElementById("update_product_idx_PH3").disabled = false;
			document.getElementById("update_order_quantity_PH3").disabled = false;
			
            document.getElementById("update_quantity_label").innerHTML = "LITERS/PCS";
            document.getElementById("update_manual_price_label").innerHTML = "AMOUNT";

        }
    }

	$("#save-CRPH3").click(function(event){
		
			event.preventDefault();
			
			$('#product_idx_PH3Error').text('');
			$('#order_quantity_PH3Error').text('');
			$('#product_manual_price_PH3Error').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			

            var miscellaneous_items_type 	= $("#miscellaneous_items_type_PH3").val();
            var reference_no 			    = $("input[name=reference_no_PH3]").val();
			
			/*Product ID*/
			var product_idx 			    = $('#product_list_PH3 option[value="' + $('#product_idx_PH3').val() + '"]').attr('data-id');
			/*Product Name*/
			let product_name 				= $("input[name=product_name_PH3]").val();
			
			var order_quantity 			    = $("input[name=order_quantity_PH3]").val();
			var product_manual_price 	    = $("input[name=product_manual_price_PH3]").val();
			
			let teves_branch 			= $("#teves_branch").val();



			document.getElementById('CRPH3_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH3') }}",
					type:"POST",
					data:{
					  CHPH3_ID:0,
					  CashiersReportId:CashiersReportId,
					  miscellaneous_items_type:miscellaneous_items_type,
                      reference_no:reference_no,
                      product_idx:product_idx,
					  branch_idx:teves_branch,
					  item_description:product_name,
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
						    LoadCashiersReportPH3_SALES_CREDIT();
							LoadCashiersReportPH3_DISCOUNT();
							LoadCashiersReportPH3_OTHERS();
						$('#product_idx_PH3Error').text('');					
						$('#order_quantity_PH3Error').text('');		
						$('#product_manual_price_PH3Error').text('');
						
						/*Clear Form*/
						document.getElementById("reference_no_PH3").value = '';
						document.getElementById("product_idx_PH3").value = '';
						document.getElementById("order_quantity_PH3").value = '';
						document.getElementById("product_manual_price_PH3").value = '';
						LoadCashiersReportSummary();
						$('#pump_price_txt').html('0');
						$('#discounted_price_txt').html('0');
						$('#TotalAmount_PH3').html('0');
						
					  }
					},
					error: function(error) {
					 console.log(error);
						
							if(error.responseJSON.errors.product_idx=='Item Description or Product is Required'){
								if(product_name==''){
									$('#product_idx_PH3Error').html(error.responseJSON.errors.product_idx);
								}else{
									$('#product_idx_PH3Error').html("Incorrect Product Name <b>" + product_name + "</b>");
								}
								
								document.getElementById("product_idx_PH3").value = "";
								document.getElementById('product_idx_PH3Error').className = "invalid-feedback";
							}
						
							$('#order_quantity_PH3Error').text(error.responseJSON.errors.order_quantity);
							document.getElementById('order_quantity_PH3Error').className = "invalid-feedback";
							
							$('#product_manual_price_PH3Error').text(error.responseJSON.errors.closing_reading);
							document.getElementById('product_manual_price_PH3Error').className = "invalid-feedback";
					}
				   });		
		 });

	function LoadCashiersReportPH3_DISCOUNT() {		
		$("#table_product_data_msc_DISCOUNT tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_data_msc_DISCOUNT');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersProductP3_DISCOUNTS') }}",
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
							var reference_no = response[i].reference_no;			
							var cashiers_report_p3_id = response[i].cashiers_report_p3_id;						
							var product_idx = response[i].product_idx;						
							var pump_price = response[i].pump_price.toLocaleString("en-PH", {minimumFractionDigits: 2});
							var unit_price = response[i].unit_price.toLocaleString("en-PH", {minimumFractionDigits: 2});
							var discounted_price = response[i].discounted_price.toLocaleString("en-PH", {minimumFractionDigits: 2});
							var product_name = response[i].product_name;
							var order_quantity = response[i].order_quantity;
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_data_msc_DISCOUNT tr:last').after("<tr>"+
							"<td align='center'>" + (i+1) + "</td>" +
							"<td class='calibration_td' align='center'>"+reference_no+"</td>"+
							"<td class='product_td' align='center'>"+product_name+"</td>"+
							"<td class='calibration_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+pump_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+unit_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+discounted_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH3_Edit_DISCOUNT' data-id='"+cashiers_report_p3_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersProductP3_DISCOUNT'  data-id='"+cashiers_report_p3_id+"'></a></div></td>"+
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
	
	function LoadCashiersReportPH3_SALES_CREDIT() {		
		$("#table_product_data_msc_SALES_CREDIT tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_data_msc_SALES_CREDIT');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersProductP3_SALES_CREDIT') }}",
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
						
							var cashiers_report_p3_id = response[i].cashiers_report_p3_id;						
							var product_idx = response[i].product_idx;						
							var unit_price = response[i].unit_price.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var product_name = response[i].product_name;
							var order_quantity = response[i].order_quantity.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_data_msc_SALES_CREDIT tr:last').after("<tr>"+
							"<td align='center'>" + (i+1) + "</td>" +
							"<td class='product_td' align='center'>"+product_name+"</td>"+
							"<td class='calibration_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+unit_price+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH3_Edit_SALES_CREDIT' data-id='"+cashiers_report_p3_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersProductP3_SALES_CREDIT'  data-id='"+cashiers_report_p3_id+"'></a></div></td>"+
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
	
	function LoadCashiersReportPH3_OTHERS() {		
		$("#table_product_data_msc_OTHERS tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_data_msc_OTHERS');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersProductP3_OTHERS') }}",
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
							
							var reference_no = response[i].reference_no;		
							var cashiers_report_p3_id = response[i].cashiers_report_p3_id;						
							var product_idx = response[i].product_idx;						
							var unit_price = response[i].unit_price.toLocaleString("en-PH", {maximumFractionDigits: 2});
							var item_description = response[i].item_description;
							var order_quantity = response[i].order_quantity;
							var order_total_amount = response[i].order_total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_data_msc_OTHERS tr:last').after("<tr>"+
							"<td align='center'>" + (i+1) + "</td>" +
							"<td class='calibration_td' align='center'>"+reference_no+"</td>"+
							"<td class='calibration_td' align='center'>"+item_description+"</td>"+
							"<td class='calibration_td' align='center'>"+order_quantity+"</td>"+
							"<td class='manual_price_td' align='center'>"+order_total_amount+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH3_Edit_OTHERS' data-id='"+cashiers_report_p3_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersProductP3_OTHERS'  data-id='"+cashiers_report_p3_id+"'></a></div></td>"+
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
    
	$('body').on('click','#CHPH3_Edit_SALES_CREDIT',function(){			
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');			
			
			/*Reset Form*/
			document.getElementById("CRPH3_form_edit").reset();	
			
			$.ajax({
				url: "{{ route('CRP3_info_SALES_CREDIT') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH3").value = CHPH3_ID;				
					/*Set Details*/					
					
					document.getElementById("update_miscellaneous_items_type_PH3").value 	= response[0].miscellaneous_items_type;
					document.getElementById("update_product_idx_PH3").value 			= response[0].product_name;
					document.getElementById("update_order_quantity_PH3").value 			= response[0].order_quantity;
					document.getElementById("update_product_manual_price_PH3").value 	= response[0].unit_price;
					
					
					update_input_settings_create_PH3();
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));		

					UpdateTotalAmount_PH3();
					LoadCashiersReportSummary();
					
					$('#Update_CRPH3_Modal').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });
	  
	$('body').on('click','#CHPH3_Edit_DISCOUNT',function(){			
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');
			
			/*Reset Form*/
			document.getElementById("CRPH3_form_edit").reset();		
			
			$.ajax({
				url: "{{ route('CRP3_info_DISCOUNT') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH3").value = CHPH3_ID;				
					/*Set Details*/					
					
					document.getElementById("update_miscellaneous_items_type_PH3").value 	= response[0].miscellaneous_items_type;
					document.getElementById("update_reference_no_PH3").value 				= response[0].reference_no;
					document.getElementById("update_product_idx_PH3").value 				= response[0].product_name;
					document.getElementById("update_order_quantity_PH3").value 				= response[0].order_quantity;
					document.getElementById("update_product_manual_price_PH3").value 		= response[0].unit_price;
					
					
					update_input_settings_create_PH3();
					var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));		

					UpdateTotalAmount_PH3();

					
					$('#Update_CRPH3_Modal').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });
	  	  
	$('body').on('click','#CHPH3_Edit_OTHERS',function(){			
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');

			/*Reset Form*/
			document.getElementById("CRPH3_form_edit").reset();
			
			$.ajax({
				url: "{{ route('CRP3_info_OTHERS') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH3").value = CHPH3_ID;				
					/*Set Details*/					
					
					document.getElementById("update_miscellaneous_items_type_PH3").value 	= response[0].miscellaneous_items_type;
					document.getElementById("update_reference_no_PH3").value 				= response[0].reference_no;
					//alert(response[0].item_description);
					document.getElementById("update_product_idx_PH3").value = response[0].item_description;
					
					document.getElementById("update_order_quantity_PH3").value 				= response[0].order_quantity;
					document.getElementById("update_product_manual_price_PH3").value 		= response[0].unit_price;
					
					
					update_input_settings_create_PH3();
					//var total_amount = response[0].order_total_amount;
					$('#UpdateTotalAmount_PH3').html('0');		

					UpdateTotalAmount_PH3();
					LoadCashiersReportSummary();
					$('#Update_CRPH3_Modal').modal('toggle');		
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$("#update-CRPH3").click(function(event){
		
			event.preventDefault();
			
			$('#update_product_idx_PH3Error').text('');
			$('#update_order_quantity_PH3Error').text('');
			$('#update_product_manual_price_PH3Error').text('');
			
			let CashiersReportId 			= {{ $CashiersReportId }};			
			let CHPH3_ID 					= document.getElementById("update-CRPH3").value;
			var miscellaneous_items_type 	= $("#update_miscellaneous_items_type_PH3").val();
			var reference_no 				= $("input[name=update_reference_no_PH3]").val();
	
			var product_idx 				= $('#product_list_PH3 option[value="' + $('#update_product_idx_PH3').val() + '"]').attr('data-id');
			/*Product Name*/
			let product_name 				= $("input[name=update_product_name_PH3]").val();
			
			var order_quantity 				= $("input[name=update_order_quantity_PH3]").val();
			var product_manual_price 		= $("input[name=update_product_manual_price_PH3]").val();
			
			document.getElementById('CRPH3_form_edit').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH3') }}",
					type:"POST",
					data:{
					  CHPH3_ID:CHPH3_ID,
					  CashiersReportId:CashiersReportId,
					  miscellaneous_items_type:miscellaneous_items_type, 
					  reference_no:reference_no,
					  product_idx:product_idx,
					  item_description:product_name,
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
						    LoadCashiersReportPH3_SALES_CREDIT();
							LoadCashiersReportPH3_DISCOUNT();
							LoadCashiersReportPH3_OTHERS();
						$('#update_product_idx_PH3Error').text('');					
						$('#update_order_quantity_PH3Error').text('');		
						$('#update_product_manual_price_PH3Error').text('');
						
						$('#Update_CRPH3_Modal').modal('toggle');
						
					  }
					},
					error: function(error) {
					 console.log(error);
						/*alert(error);*/
							$('#update_product_idx_PH3Error').text(error.responseJSON.errors.product_idx);
							document.getElementById('update_product_idx_PH3Error').className = "invalid-feedback";
							
							$('#update_order_quantity_PH3Error').text(error.responseJSON.errors.order_quantity);
							document.getElementById('update_order_quantity_PH3Error').className = "invalid-feedback";
							
							$('#update_product_manual_price_PH3Error').text(error.responseJSON.errors.closing_reading);
							document.getElementById('update_product_manual_price_PH3Error').className = "invalid-feedback";
					}
				   });		
		 });

	<!--CRPH1 Deletion Confirmation-->	
	$('body').on('click','#deleteCashiersProductP3_SALES_CREDIT',function(){
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP3_info_SALES_CREDIT') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("deleteCRPH3Confirmed").value = CHPH3_ID;				
					/*Set Details*/					
					$('#delete_product_idx_PH3').text(response[0].product_name);
					$('#delete_order_quantity_PH3').text(response[0].order_quantity);
					$('#delete_product_manual_price_PH3').text(response[0].product_price);
					
					var total_amount = response[0].order_total_amount;
					$('#delete_TotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));				
					$('#CRPH3DeleteModal').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });
	$('body').on('click','#deleteCashiersProductP3_DISCOUNT',function(){
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP3_info_DISCOUNT') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("deleteCRPH3Confirmed_DISCOUNT").value = CHPH3_ID;				
					/*Set Details*/					
					//$('#CRPH1_delete_product_idx').text(response[0].product_name);
					$('#delete_reference_PH3_DISCOUNT').text(response[0].reference_no);
					$('#delete_product_idx_PH3_DISCOUNT').text(response[0].product_name);
					$('#delete_order_quantity_PH3_DISCOUNT').text(response[0].order_quantity);
					$('#delete_product_manual_price_PH3_DISCOUNT').text(response[0].product_price);
					
					var total_amount = response[0].order_total_amount;
					$('#delete_TotalAmount_PH3_DISCOUNT').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));				
					$('#CRPH3DeleteModal_DISCOUNT').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });
	<!--CRPH1 Deletion Confirmation-->	
	$('body').on('click','#deleteCashiersProductP3_OTHERS',function(){
			event.preventDefault();
			let CHPH3_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP3_info_OTHERS') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("deleteCRPH3Confirmed_OTHERS").value = CHPH3_ID;				
					/*Set Details*/
					
					$('#delete_reference_PH3_others').text(response[0].reference_no);
					$('#delete_liters_pcs_PH3_others').text(response[0].order_quantity);
					$('#delete_amount_PH3_others').text(response[0].unit_price);
					
					//var total_amount = response[0].order_total_amount;
					//$('#delete_TotalAmount_PH3_OTHERS').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));				
					
					$('#CRPH3DeleteModal_OTHERS').modal('toggle');							  
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$('body').on('click','#deleteCRPH3Confirmed',function(){
			
		let CHPH3_ID = document.getElementById("deleteCRPH3Confirmed").value;
		
		if(CHPH3_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP3') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					    LoadCashiersReportPH3_SALES_CREDIT();
						LoadCashiersReportPH3_DISCOUNT();
						LoadCashiersReportPH3_OTHERS();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});
	$('body').on('click','#deleteCRPH3Confirmed_OTHERS',function(){
			
		let CHPH3_ID = document.getElementById("deleteCRPH3Confirmed_OTHERS").value;
		
		if(CHPH3_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP3') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
						LoadCashiersReportPH3_OTHERS();
						LoadCashiersReportSummary();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});
	
	$('body').on('click','#deleteCRPH3Confirmed_DISCOUNT',function(){
			
		let CHPH3_ID = document.getElementById("deleteCRPH3Confirmed_DISCOUNT").value;
		
		if(CHPH3_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersProductP3') }}",
				type:"POST",
				data:{
				  CHPH3_ID:CHPH3_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
						LoadCashiersReportPH3_DISCOUNT();
						LoadCashiersReportSummary();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});
	
	function TotalAmount_PH3(){
		
		let CashiersReportId 		= {{ $CashiersReportId }};

        let miscellaneous_items_type 	= $("#miscellaneous_items_type_PH3").val();

        
		    let product_id 				= $('#product_list_PH3 option[value="' + $('#product_idx_PH3').val() + '"]').attr('data-id');		
		    let product_price 			= $('#product_list_PH3 option[value="' + $('#product_idx_PH3').val() + '"]').attr('data-price');
	
		    let product_manual_price 	= $("#product_manual_price_PH3").val();		
		    let order_quantity 			= $("input[name=order_quantity_PH3]").val();
		
		    /*GET PUMP PRICE*/
		    event.preventDefault();
			
			    let CHPH1_ID = $(this).data('id');			
			    $.ajax({
				    url: "{{ route('CRP1_info') }}",
				    type:"POST",
				    data:{
				      CashiersReportId:CashiersReportId,
				      CHPH1_ID:CHPH1_ID,
				      product_id:product_id,
				      _token: "{{ csrf_token() }}"
				    },
				    success:function(response){
				      console.log(response);
				      if(response) {				
					
					    if(response==''){
						   
						   $('#pump_price_txt').html('0');
						   $('#discounted_price_txt').html('0');
						
						    if(order_quantity!=0 || order_quantity!=''){
							
							    if(product_manual_price!='' && product_manual_price!=0){
								
								    var total_amount = (product_manual_price) * order_quantity;
								    $('#TotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							    }else{
								
								    var total_amount = product_price * order_quantity;
								    $('#TotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							    }
							
						    }		
						
					    }else{
						
						    var pump_price	= response[0].product_price;
						    
							if(miscellaneous_items_type=='DISCOUNTS'){
								var discounted_price	= response[0].product_price - product_manual_price;
							}else{
								var discounted_price	= 0;
							}
							
						    $('#pump_price_txt').html(pump_price.toLocaleString("en-PH", {maximumFractionDigits: 2}));
						
						    $('#discounted_price_txt').html(discounted_price.toLocaleString("en-PH", {maximumFractionDigits: 2}));
						
						    if(order_quantity!=0 || order_quantity!=''){
							
							    if(product_manual_price!='' && product_manual_price!=0){
								
								    var total_amount = (pump_price - product_manual_price) * order_quantity;
								    $('#TotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							    }else{
								
								    var total_amount = pump_price * order_quantity;
								    $('#TotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							    }
							
						    }		
				      }
						
				      }
				    },
				    error: function(error) {
				     console.log(error);
					    alert(error);
				    }
			       });	
	}

	function UpdateTotalAmount_PH3(){
		
		var miscellaneous_items_type 	= $("#update_miscellaneous_items_type_PH3").val();
		
		let CashiersReportId 		= {{ $CashiersReportId }};
		let product_price 			= $('#product_list_PH3 option[value="' + $('#update_product_idx_PH3').val() + '"]').attr('data-price');
		let product_id 				= $('#product_list_PH3 option[value="' + $('#update_product_idx_PH3').val() + '"]').attr('data-id');	
		let product_manual_price 	= $("#update_product_manual_price_PH3").val();		
		let order_quantity 			= $("input[name=update_order_quantity_PH3]").val();
		
		
		/*GET PUMP PRICE*/
		event.preventDefault();
			
			let CHPH1_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP1_info') }}",
				type:"POST",
				data:{
				  CashiersReportId:CashiersReportId,
				  CHPH1_ID:CHPH1_ID,
				  miscellaneous_items_type:miscellaneous_items_type,
				  product_id:product_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {				
					
					if(response==''){
						
					$('#pump_price_txt_update').html('0');
					
					$('#discounted_price_txt_update').html('0');
					
						$('#pump_price_txt').html('0');
						$('#discounted_price_txt').html('0');
						
						if(order_quantity!=0 || order_quantity!=''){
							
							if(product_manual_price!='' && product_manual_price!=0){
								
								var total_amount = (product_manual_price) * order_quantity;
								$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							}else{
								alert('s');
								var total_amount = product_price * order_quantity;
								$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							}
							
						}		
						
					}else{
					
					var pump_price	= response[0].product_price;
					
							if(miscellaneous_items_type=='DISCOUNTS'){
								var discounted_price	= response[0].product_price - product_manual_price;
							}else{
								var discounted_price	= 0;
							}
							
					$('#pump_price_txt_update').html(pump_price.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#discounted_price_txt_update').html(discounted_price.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					
						if(order_quantity!=0 || order_quantity!=''){
							
							if(product_manual_price!='' && product_manual_price!=0){
								
								var total_amount = (pump_price - product_manual_price) * order_quantity;
								$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							}else{
								
								var total_amount = pump_price * order_quantity;
								$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
							
							}
							
						}	
					
					}					
					
					
					
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });			
		
		/*
		if(order_quantity!=0 || order_quantity!=''){
			if(product_manual_price!='' && product_manual_price!=0){
				var total_amount = product_manual_price * order_quantity;
				$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
			}else{
				var total_amount = product_price * order_quantity;
				$('#UpdateTotalAmount_PH3').html(total_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));
			}
		}		
		*/
	}	

	
	/*Part 4*/
	$("#save-CRPH4").click(function(event){
		
			event.preventDefault();
			
			$('#description_p4Error').text('');
			$('#amount_p4Error').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
			
			var description_p4 		= $("input[name=description_p4]").val();
			var amount_p4 			= $("input[name=amount_p4]").val();
			
			document.getElementById('CRPH4_form').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH4') }}",
					type:"POST",
					data:{
					  CHPH4_ID:0,
					  CashiersReportId:CashiersReportId,
					  description_p4:description_p4,
					  amount_p4:amount_p4, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						LoadCashiersReportPH4();
						LoadCashiersReportSummary();
						$('#description_p4Error').text('');					
						$('#amount_p4Error').text('');		
					  }
					},
					error: function(error) {
					 console.log(error);
						/*alert(error);*/
							$('#description_p4Error').text(error.responseJSON.errors.description_p4);
							document.getElementById('description_p4Error').className = "invalid-feedback";
							
							$('#amount_p4Error').text(error.responseJSON.errors.amount_p4);
							document.getElementById('amount_p4Error').className = "invalid-feedback";
							
					}
				   });		
		 });

	function LoadCashiersReportPH4() {		
		$("#table_product_data_PH4 tr").remove();
		$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#table_product_data_PH4');
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  $.ajax({
				url: "{{ route('GetCashiersP4') }}",
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
						
							var cashiers_report_p4_id = response[i].cashiers_report_p4_id;					
							var description_p4 = response[i].description_p4;
							var amount_p4 = response[i].amount_p4.toLocaleString("en-PH", {maximumFractionDigits: 2});
							
							$('#table_product_data_PH4 tr:last').after("<tr>"+
							"<td class='product_td' align='center'>"+description_p4+"</td>"+
							"<td class='calibration_td' align='center'>"+amount_p4+"</td>"+
							"<td><div align='center' class='action_table_menu_Product' style='margin-top: 6px;'><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='CHPH4_Edit' data-id='"+cashiers_report_p4_id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='deleteCashiersP4'  data-id='"+cashiers_report_p4_id+"'></a></div></td>"+
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

	$('body').on('click','#CHPH4_Edit',function(){			
			event.preventDefault();
			let CHPH4_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP4_info') }}",
				type:"POST",
				data:{
				  CHPH4_ID:CHPH4_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("update-CRPH4").value = CHPH4_ID;				
					/*Set Details*/					
					document.getElementById("update_description_p4").value 	= response[0].description_p4;
					document.getElementById("update_amount_p4").value 		= response[0].amount_p4;
					$('#Update_CRPH4_Modal').modal('toggle');	
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$("#update-CRPH4").click(function(event){
		
			event.preventDefault();
			
			$('#update_description_p4Error').text('');
			$('#update_amount_p4Error').text('');
			
			let CashiersReportId 		= {{ $CashiersReportId }};			
			let CHPH4_ID 				= document.getElementById("update-CRPH4").value;
			
			var description_p4 		= $("input[name=update_description_p4]").val();
			var amount_p4 			= $("input[name=update_amount_p4]").val();
			
			document.getElementById('CRPH3_form_edit').className = "g-3 needs-validation was-validated";
			
				/*Delete the Selected Item*/			
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH4') }}",
					type:"POST",
					data:{
						CHPH4_ID:CHPH4_ID,
						CashiersReportId:CashiersReportId,
						description_p4:description_p4,
						amount_p4:amount_p4, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						LoadCashiersReportPH4();
						LoadCashiersReportSummary();
						$('#update_description_p4Error').text('');					
						$('#update_amount_p4Error').text('');		
					  }
					},
					error: function(error) {
					 console.log(error);
						/*alert(error);*/
							$('#update_description_p4Error').text(error.responseJSON.errors.description_p4);
							document.getElementById('update_description_p4Error').className = "invalid-feedback";
							
							$('#update_amount_p4Error').text(error.responseJSON.errors.amount_p4);
							document.getElementById('update_amount_p4Error').className = "invalid-feedback";
					}
				   });		
		 });

	<!--CRPH1 Deletion Confirmation-->	
	$('body').on('click','#deleteCashiersP4',function(){
			event.preventDefault();
			let CHPH4_ID = $(this).data('id');			
			$.ajax({
				url: "{{ route('CRP4_info') }}",
				type:"POST",
				data:{
				  CHPH4_ID:CHPH4_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				
					document.getElementById("deleteCRPH4Confirmed").value = CHPH4_ID;				
					/*Set Details*/					
					$('#delete_description_p4').text(response[0].description_p4);
					$('#delete_amount_p4').text(response[0].amount_p4);
					
					$('#CRPH4DeleteModal').modal('toggle');							  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$('body').on('click','#deleteCRPH4Confirmed',function(){
			
		let CHPH4_ID = document.getElementById("deleteCRPH4Confirmed").value;
		
		if(CHPH4_ID!=0){
			/*Delete the Selected Item*/	
			  $.ajax({
				url: "{{ route('DeleteCashiersP4') }}",
				type:"POST",
				data:{
				  CHPH4_ID:CHPH4_ID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					$('#switch_notice_off').show();
					$('#sw_off').html("Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					LoadCashiersReportPH4();
					LoadCashiersReportSummary();
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
		}	
	});
	
	$('body').on('click','#cashonhand',function(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
			
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));

	});
	
	function one_thousand_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(one_thousand_deno!=0 || one_thousand_deno!=''){
			
				deno_amount = one_thousand_deno * 1000;
				$('#one_thousand_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}			
			
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
	}

	function five_hundred_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(five_hundred_deno!=0 || five_hundred_deno!=''){
			
				deno_amount = five_hundred_deno * 500;
				$('#five_hundred_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));		
		
	}	
	
	function two_hundred_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(two_hundred_deno!=0 || two_hundred_deno!=''){
			
				deno_amount = two_hundred_deno * 200;
				$('#two_hundred_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));		
		
	}	

	function one_hundred_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(one_hundred_deno!=0 || one_hundred_deno!=''){
			
				deno_amount = one_hundred_deno * 100;
				$('#one_hundred_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
				
	}

	function fifty_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
			
		if(fifty_deno!=0 || fifty_deno!=''){
			
				deno_amount = fifty_deno * 50;
				$('#fifty_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));		
		
	}

	function twenty_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
	
		if(twenty_deno!=0 || twenty_deno!=''){
			
				deno_amount = twenty_deno * 20;
				$('#twenty_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
				
	}
	
	function ten_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(ten_deno!=0 || ten_deno!=''){
			
				deno_amount = ten_deno * 10;
				$('#ten_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));		
		
	}

	function five_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
		
		if(five_deno!=0 || five_deno!=''){
			
				deno_amount = five_deno * 5;
				$('#five_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
	
	}
	
	function one_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
			
		if(one_deno!=0 || one_deno!=''){
			
				deno_amount = one_deno * 1;
				$('#one_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		
	}
	
	function twenty_five_cent_deno_total(){
		
		var one_thousand_deno = $("input[name=one_thousand_deno]").val();
		var five_hundred_deno = $("input[name=five_hundred_deno]").val();
		var two_hundred_deno = $("input[name=two_hundred_deno]").val();
		var one_hundred_deno = $("input[name=one_hundred_deno]").val();
		var fifty_deno = $("input[name=fifty_deno]").val();
		var twenty_deno = $("input[name=twenty_deno]").val();
		var ten_deno = $("input[name=ten_deno]").val();
		var five_deno = $("input[name=five_deno]").val();
		var one_deno = $("input[name=one_deno]").val();
		var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
			
		if(twenty_five_cent_deno!=0 || twenty_five_cent_deno!=''){
			
				deno_amount = twenty_five_cent_deno * 0.25;
				$('#twenty_five_cent_deno_total_amt').html(deno_amount.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		}		
		one_thousand_deno_amt = one_thousand_deno * 1000;	
		five_hundred_deno_amt = five_hundred_deno * 500;	
		two_hundred_deno_amt = two_hundred_deno * 200;
		one_hundred_deno_amt = one_hundred_deno * 100;
		fifty_deno_amt = fifty_deno * 50;
		twenty_deno_amt = twenty_deno * 20;
		ten_deno_amt = ten_deno * 10;
		five_deno_amt = five_deno * 5;
		one_deno_amt = one_deno * 1;
		twenty_five_deno_amt = twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		
	}	

	function LoadCashiersReportPH5() {		
		
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  
			  $.ajax({
				url: "{{ route('CRP5_info') }}",
				type:"POST",
				data:{
				  CashiersReportId:CashiersReportId,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
				  
					document.getElementById("one_thousand_deno").value 	= response[0].one_thousand_deno;
					
					document.getElementById("five_hundred_deno").value 	= response[0].five_hundred_deno;
					document.getElementById("two_hundred_deno").value 	= response[0].two_hundred_deno;
					document.getElementById("one_hundred_deno").value 	= response[0].one_hundred_deno;

					document.getElementById("fifty_deno").value 	= response[0].fifty_deno;
					document.getElementById("twenty_deno").value 	= response[0].twenty_deno;
					document.getElementById("ten_deno").value 		= response[0].ten_deno;
					
					document.getElementById("five_deno").value 	= response[0].five_deno;
					document.getElementById("one_deno").value 	= response[0].one_deno;
					
					document.getElementById("twenty_five_cent_deno").value 	= response[0].twenty_five_cent_deno;
					document.getElementById("cash_drop").value 	= response[0].cash_drop;
					
					document.getElementById("CASHONHAND").value = response[0].cashiers_report_p5_id;
					
					/*Load Amount*/
		one_thousand_deno_amt 		= response[0].one_thousand_deno * 1000;	
		five_hundred_deno_amt 		= response[0].five_hundred_deno * 500;	
		two_hundred_deno_amt 		= response[0].two_hundred_deno * 200;
		one_hundred_deno_amt 		= response[0].one_hundred_deno * 100;
		fifty_deno_amt 				= response[0].fifty_deno * 50;
		twenty_deno_amt 			= response[0].twenty_deno * 20;
		ten_deno_amt 				= response[0].ten_deno * 10;
		five_deno_amt 				= response[0].five_deno * 5;
		one_deno_amt 				= response[0].one_deno * 1;
		twenty_five_deno_amt 		= response[0].twenty_five_cent_deno * 0.25;
	
		total_cash_on_hand = one_thousand_deno_amt
		+ five_hundred_deno_amt
		+ two_hundred_deno_amt
		+ one_hundred_deno_amt
		+ fifty_deno_amt
		+ twenty_deno_amt
		+ ten_deno_amt
		+ five_deno_amt
		+ one_deno_amt
		+ twenty_five_deno_amt;
		
		$('#one_thousand_deno_total_amt').html(one_thousand_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		$('#five_hundred_deno_total_amt').html(five_hundred_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		$('#two_hundred_deno_total_amt').html(two_hundred_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		$('#one_hundred_deno_total_amt').html(one_hundred_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		$('#fifty_deno_total_amt').html(fifty_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		$('#twenty_deno_total_amt').html(twenty_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		$('#ten_deno_total_amt').html(ten_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));

		$('#five_deno_total_amt').html(five_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		$('#one_deno_total_amt').html(one_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		$('#twenty_five_cent_deno_total_amt').html(twenty_five_deno_amt.toLocaleString("en-PH", {maximumFractionDigits: 2}));
		
		$('#total_cash_on_hand_amt').html(total_cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));

					
				  }else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	  }  	  
	
	$("#CASHONHAND").click(function(event){
		
			event.preventDefault();
		
			let CashiersReportId 		= {{ $CashiersReportId }};			
			let CHPH5_ID 				= document.getElementById("CASHONHAND").value;
			
			var one_thousand_deno = $("input[name=one_thousand_deno]").val();
			var five_hundred_deno = $("input[name=five_hundred_deno]").val();
			var two_hundred_deno = $("input[name=two_hundred_deno]").val();
			var one_hundred_deno = $("input[name=one_hundred_deno]").val();
			var fifty_deno = $("input[name=fifty_deno]").val();
			var twenty_deno = $("input[name=twenty_deno]").val();
			var ten_deno = $("input[name=ten_deno]").val();
			var five_deno = $("input[name=five_deno]").val();
			var one_deno = $("input[name=one_deno]").val();
			var twenty_five_cent_deno = $("input[name=twenty_five_cent_deno]").val();
			
			var cash_drop = $("input[name=cash_drop]").val();
			
			document.getElementById('CASHONHAND_FORM').className = "g-3 needs-validation was-validated";
					
				  $.ajax({
					url: "{{ route('SAVE_CHR_PH5') }}",
					type:"POST",
					data:{
						CashiersReportId:CashiersReportId,
						CHPH5_ID:CHPH5_ID,
						one_thousand_deno:one_thousand_deno,
						five_hundred_deno:five_hundred_deno,
						two_hundred_deno:two_hundred_deno,
						one_hundred_deno:one_hundred_deno,
						fifty_deno:fifty_deno,
						twenty_deno:twenty_deno,
						ten_deno:ten_deno,
						five_deno:five_deno,
						one_deno:one_deno,
						twenty_five_cent_deno:twenty_five_cent_deno,
						cash_drop:cash_drop, 
					  _token: "{{ csrf_token() }}"
					},
					success:function(response){
					  console.log(response);
					  if(response) {
						  
						$('#switch_notice_on').show();
						$('#sw_on').html(response.success);
						setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
						LoadCashiersReportPH5();
						LoadCashiersReportSummary();
						$('#cash_dropError').text('');
						
					  }
					},
					error: function(error) {
					 console.log(error);
						
						$('#cash_dropError').text(error.responseJSON.errors.cash_drop);
						document.getElementById('cash_dropError').className = "invalid-feedback";			

						$('#switch_notice_off').show();
						$('#sw_off').html("Invalid Input");
						setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
							
					}
				   });		
	});


	function LoadCashiersReportSummary() {		
		
		let CashiersReportId 			= {{ $CashiersReportId }};	
			  
			  $.ajax({
				url: "{{ route('CashiersReportSummary') }}",
				type:"POST",
				data:{
				  CashiersReportId:CashiersReportId,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){						
				  console.log(response);
				  if(response!='') {			  
				  
					fuel_sales_total = response.fuel_sales_total;
					other_sales_total = response.other_sales_total;
					total_sales = fuel_sales_total + other_sales_total;
					miscellaneous_total = response.miscellaneous_total;
					theoretical_sales = total_sales - miscellaneous_total;
					cash_on_hand = response.cash_on_hand;
					
					cash_short_or_over = cash_on_hand - (theoretical_sales);				
					
					$('#fuel_sales_total').html(fuel_sales_total.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#other_sales_total').html(other_sales_total.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#total_sales').html(total_sales.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#miscellaneous_total').html(miscellaneous_total.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#theoretical_sales').html(theoretical_sales.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#cash_on_hand').html(cash_on_hand.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
					$('#cash_short_or_over').html(cash_short_or_over.toLocaleString("en-PH", {maximumFractionDigits: 2}));
					
				  }else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	  }  	

	function printCashierReportPDF(){
	  
		let CashiersReportId 			= {{ $CashiersReportId }};
		
		var query = {
			CashiersReportId:CashiersReportId,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_cashier_report_pdf')}}?" + $.param(query)
		window.open(url);
	  
	}
	
	function postTest() {	
		$.ajax({
		  type: "POST",
		  url: "http://localhost:8000/check_time.php",
		  data: {
			"Id": 78912,
			"Customer": "Jason Sweet",
		  },
		  success: function (result) {
			 console.log(result);
			 alert(result);
		  },
		  dataType: "html"
		});
	}
	
</script>
