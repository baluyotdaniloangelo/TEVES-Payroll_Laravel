   <script type="text/javascript">

	ProductInfo();
	<!--Select Product For Update-->
	function ProductInfo() {
			
			//event.preventDefault();
			let productID = {{ $productID }};
			
			  $.ajax({
				url: "/product_info",
				type:"POST",
				data:{
				  productID:productID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-product").value = productID;
					
					/*Set Details*/
					document.getElementById("update_product_name").value = response.product_name;
					document.getElementById("update_product_price").value = response.product_price;
					document.getElementById("update_product_unit_measurement").value = response.product_unit_measurement;
										
					$('#UpdateProductModal').modal('toggle');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	}

	$("#update-product").click(function(event){			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#update_product_nameError').text('');
					$('#update_product_priceError').text('');

			document.getElementById('ProductformEdit').className = "g-3 needs-validation was-validated";
			
			let productID 					= document.getElementById("update-product").value;
			let product_name 				= $("input[name=update_product_name]").val();
			let product_price 				= $("input[name=update_product_price]").val();
			let product_unit_measurement 	= $("#update_product_unit_measurement").val();
			
			  $.ajax({
				url: "/update_product_post",
				type:"POST",
				data:{
				  productID:productID,
				  product_name:product_name,
				  product_price:product_price,
				  product_unit_measurement:product_unit_measurement ,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#update_product_nameError').text('');	
					$('#update_product_priceError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					/*Close form*/
					$('#UpdateProductModal').modal('toggle');
					
					/*Refresh Table*/
					var table = $("#getProductList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.product_name=="The product name has already been taken."){
							  
					$('#product_nameError').html("<b>"+ product_name +"</b> has already been taken.");
					document.getElementById('product_nameError').className = "invalid-feedback";
					document.getElementById('product_name').className = "form-control is-invalid";
					$('#update_product_name').val("");
				  
				}else{
					$('#product_nameError').text(error.responseJSON.errors.product_name);
					document.getElementById('product_nameError').className = "invalid-feedback";
				}
				
					$('#product_priceError').text(error.responseJSON.errors.product_price);
					document.getElementById('product_priceError').className = "invalid-feedback";			
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);		  	  
				  
				}
			   });
		
	  });
	  
	<!--Pay Receivables-->
	$('body').on('click','#LoadProductPricePerBranch',function(){
			
			event.preventDefault();
			let productID = $(this).data('id');
			
			  $.ajax({
				url: "/product_info",
				type:"POST",
				data:{
				  productID:productID,
				  
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("save-product-price-per-branch").value = productID;
					
					/*Set Details*/		
					document.getElementById("branch_product_name").innerHTML = response.product_name;
					document.getElementById("branch_product_price").innerHTML = response.product_price;
					document.getElementById("branch_product_unit").innerHTML = response.product_unit_measurement;	
					
					/*Load Receivable Payment Table*/
					loadPricingPerBranch(productID);
					
					$('#ProductBranchPriceModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	  


	loadPricingListPerBranch();
	
	function loadPricingListPerBranch() {
		
			  $.ajax({
				url: "{{ route('ProductPricingPerBranch') }}",
				type:"POST",
				data:{
				  productID:{{ $productID }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
						
								
				  console.log(response);
				  if(response!='') {
					  
					  //document.getElementById("save-product-price-per-branch").value = productID;
					  
					    $("#branch_pricing_table_body_data tr").remove();
						$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#branch_pricing_table_body_data');
					  
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].branch_price_id;
							var branch_code 			= response[i].branch_code;
							var branch_price 			= response[i].branch_price;
							
							$('#branch_pricing_table_body_data tr:last').after("<tr>"+
							"<td class='item_no' align='center'>"+(i+1)+"</td>"+
							"<td class='branch_code_no_td' align='left' data-id='' id='branch_id'>"+branch_code+"</td>"+
							"<td class='payment_amount_td' data-id='"+id+"' id='branch_price_id' align='center'><input type='number' class='form-control branch_price' id='branch_price' name='branch_price' value='"+branch_price+"'></td>"+
							"</tr>");
							
						}			
				  }else{
							/*No Result Found or Error*/
							$("#branch_pricing_table_body_data tr").remove();
							$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#branch_pricing_table_body_data');
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	}  	  

	$("#save-product-price-per-branch").click(function(event){

			event.preventDefault();
				
				let productID 			= {{ $productID }};
				
				var branch_price_id = [];
				var branch_price = [];
				  
				  $.each($("[id='branch_price_id']"), function(){
					branch_price_id.push($(this).attr("data-id"));
				  });
				  
				  $('.branch_price').each(function(){
					if($(this).val() == ''){
						alert('Price is Empty');
						exit(); 
					}else{  				  
				   		branch_price.push($(this).val());
					}				  
				  });		
			
				$.ajax({
				url: "/save_branches_product_pricing_post",
				type:"POST",
				data:{
				  branch_price_id:branch_price_id,
				  branch_price:branch_price,
				  _token: "{{ csrf_token() }}"
				},
			
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
				  }
				},
				error: function(error) {
					
				 console.log(error);
								
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });


	/*Tank*/
	LoadProductTank();
	
	function LoadProductTank() {
		
			  $.ajax({
				url: "{{ route('ProductTank') }}",
				type:"POST",
				data:{
				  productID:{{ $productID }},
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
						
								
				  console.log(response);
				  if(response!='') {
					  
					    $("#branch_tank_table_body_data tr").remove();
						$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#branch_tank_table_body_data');
					  
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id			 	= response[i].tank_id;
							var branch_code  	= response[i].branch_code;
							var tank_name 		= response[i].tank_name;
							var tank_capacity 	= response[i].tank_capacity;
							
							var product_unit_measurement 			= response[i].product_unit_measurement;
							
							$('#branch_tank_table_body_data tr:last').after("<tr>"+
							"<td class='action_column_class'><div align='center' class='action_table_menu_Product' ><a href='#' class='btn-danger btn-circle btn-sm bi-pencil-fill btn_icon_table btn_icon_table_edit' id='ProductTank_Edit' data-id='"+id+"'></a> <a href='#' class='btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete' id='ProductTank_delete'  data-id='"+id+"'></a></div></td>"+
							"<td class='item_no' align='center'>"+(i+1)+"</td>"+
							"<td class='branch_code_no_td' align='center' data-id='' id='branch_id'>"+branch_code+"</td>"+
							"<td class='branch_code_no_td' align='left' data-id='' id='branch_id'>"+tank_name+"</td>"+
							"<td class='branch_code_no_td' align='center' data-id='' id='branch_id'>"+tank_capacity+" "+product_unit_measurement+"</td>"+
							"</tr>");
							
						}			
				  }else{
							/*No Result Found or Error*/
							$("#branch_tank_table_body_data tr").remove();
							$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#branch_tank_table_body_data');
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	}  
	
	<!--Save New Tank->
	$("#save-tank").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#tank_nameError').text('');
					$('#tank_capacityError').text('');

			document.getElementById('AddProductTank').className = "g-3 needs-validation was-validated";

			let tank_name 		= $("input[name=tank_name]").val();
			let tank_capacity 	= $("input[name=tank_capacity]").val();
			let branch_idx 		= $("#branch_idx").val();
			
			  $.ajax({
				url: "/create_tank_post",
				type:"POST",
				data:{
				  product_idx:{{ $productID }},
				  tank_name:tank_name,
				  tank_capacity:tank_capacity,
				  branch_idx:branch_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#tank_nameError').text('');
					$('#tank_capacityError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					document.getElementById("AddProductTank").reset();
										
					$('#AddProductTankModal').modal('toggle');	
					
					LoadProductTank();
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.tank_name=="The tank name has already been taken."){
							  
				  $('#tank_nameError').html("<b>"+ tank_name +"</b> has already been taken.");
				  document.getElementById('tank_nameError').className = "invalid-feedback";
				  document.getElementById('tank_name').className = "form-control is-invalid";
				  $('#tank_name').val("");
				  
				}else{
					
				  $('#tank_nameError').text(error.responseJSON.errors.tank_name);
				  document.getElementById('tank_nameError').className = "invalid-feedback";
				  
				}
				
				  $('#tank_capacityError').text(error.responseJSON.errors.tank_capacity);
				  document.getElementById('tank_capacityError').className = "invalid-feedback";			
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });		
			   
	});
	
	<!--Select Tank For Update-->
	$('body').on('click','#ProductTank_Edit',function(){
			
			event.preventDefault();
			let tankID = $(this).data('id');
			
			  $.ajax({
				url: "/product_tank_info",
				type:"POST",
				data:{
				  tankID:tankID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-tank").value = tankID;
					
					/*Set Details*/
					document.getElementById("update_tank_name").value = response.tank_name;
					document.getElementById("update_tank_capacity").value = response.tank_capacity;
					document.getElementById("update_branch_idx").value = response.branch_idx;
										
					$('#UpdateProductTankModal').modal('toggle');		
					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });	

	<!--Save New Tank->
	$("#update-tank").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#tank_nameError').text('');
					$('#tank_capacityError').text('');

			document.getElementById('AddProductTank').className = "g-3 needs-validation was-validated";

			let tank_id 	= document.getElementById("update-tank").value;
			
			let tank_name 		= $("input[name=update_tank_name]").val();
			let tank_capacity 	= $("input[name=update_tank_capacity]").val();
			let branch_idx 		= $("#update_branch_idx").val();
			
			  $.ajax({
				url: "/update_tank_post",
				type:"POST",
				data:{
				  tank_id:tank_id,
				  product_idx:{{ $productID }},
				  tank_name:tank_name,
				  tank_capacity:tank_capacity,
				  branch_idx:branch_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#update_tank_nameError').text('');
					$('#update_tank_capacityError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					document.getElementById("AddProductTank").reset();
										
					$('#UpdateProductTankModal').modal('toggle');	
					
					LoadProductTank();
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.tank_name=="The tank name has already been taken."){
							  
				  $('#update_tank_nameError').html("<b>"+ tank_name +"</b> has already been taken.");
				  document.getElementById('update_tank_nameError').className = "invalid-feedback";
				  document.getElementById('update_tank_name').className = "form-control is-invalid";
				  $('#update_update_gateway_sn').val("");
				  
				}else{
				  $('#update_tank_nameError').text(error.responseJSON.errors.tank_name);
				  document.getElementById('update_tank_nameError').className = "invalid-feedback";
				}
				
				  $('#update_tank_capacityError').text(error.responseJSON.errors.tank_capacity);
				  document.getElementById('update_tank_capacityError').className = "invalid-feedback";			
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });		
	  });
	

	<!--Product Deletion Confirmation-->
	$('body').on('click','#ProductTank_delete',function(){
			
			event.preventDefault();
			let tankID = $(this).data('id');
			
			  $.ajax({
				url: "/product_tank_info",
				type:"POST",
				data:{
				  tankID:tankID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
											
					/*Set Details*/
					$('#delete_tank_name').text(response.tank_name);					
					$('#delete_tank_capacity').text(response.tank_capacity);
					
					document.getElementById("deleteTankInfoConfirmed").value = tankID;
					
					$('#TankInfoDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });	
	  
	  
	<!--Product Confirmed For Deletion-->
	$('body').on('click','#deleteTankInfoConfirmed',function(){
			
			event.preventDefault();

			let tankID = document.getElementById("deleteTankInfoConfirmed").value;
			
			  $.ajax({
				url: "/delete_product_tank_confirmed",
				type:"POST",
				data:{
				   tankID:tankID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Tank Information Deleted!");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					LoadProductTank();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	  
	
  </script>
	