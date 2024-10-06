   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   <script type="text/javascript">

	<!--Load Table-->
	$(function () {

		var ProductListTable = $('#getProductList').DataTable({
			"language": {
						"lengthMenu":'<select class="dt-input">'+
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
			stateSave: true,/*Remember Searches*/
			scrollCollapse: true,
			scrollY: '500px',
			ajax: "{{ route('getProductList') }}",
			info: true,
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'product_name'},   
					{data: 'product_price', orderable: false, render: $.fn.dataTable.render.number( ',', '.', 2, '' ), className: "text-right" },
					{data: 'product_unit_measurement', className: "text-center"},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			]
			
		});
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateProductModal"></button>'+
				'</div>').appendTo('#product_option');
				
				autoAdjustColumns(ProductListTable);

				 /*Adjust Table Column*/
				 function autoAdjustColumns(table) {
					 var container = table.table().container();
					 var resizeObserver = new ResizeObserver(function () {
						 table.columns.adjust();
					 });
					 resizeObserver.observe(container);
				 }		
	});
	
	<!--Save New product->
	$("#save-product").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#product_nameError').text('');
					$('#product_priceError').text('');

			document.getElementById('ProductformNew').className = "g-3 needs-validation was-validated";

			let product_name 				= $("input[name=product_name]").val();
			let product_price 				= $("input[name=product_price]").val();
			let product_unit_measurement 	= $("#product_unit_measurement").val();
			
			  $.ajax({
				url: "/create_product_post",
				type:"POST",
				data:{
				  product_name:product_name,
				  product_price:product_price,
				  product_unit_measurement:product_unit_measurement,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#product_nameError').text('');
					$('#product_priceError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					document.getElementById("ProductformNew").reset();
					
					/*Refresh Table*/
					var table = $("#getProductList").DataTable();
				    table.ajax.reload(null, false);
					
					/*Load Receivable Payment Table*/
					loadPricingPerBranch(response.productID);
					loadProductInfo(response.productID);
										
					$('#CreateProductModal').modal('toggle');	
					
					$('#ProductBranchPriceModal').modal('toggle');	
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.product_name=="The product name has already been taken."){
							  
				  $('#product_nameError').html("<b>"+ product_name +"</b> has already been taken.");
				  document.getElementById('product_nameError').className = "invalid-feedback";
				  document.getElementById('product_name').className = "form-control is-invalid";
				  $('#update_gateway_sn').val("");
				  
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

	<!--Select Product For Update-->
	$('body').on('click','#editProduct',function(){
			
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
	  });


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
	  
	<!--Product Deletion Confirmation-->
	$('body').on('click','#deleteProduct',function(){
			
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
					
					document.getElementById("deleteProductConfirmed").value = productID;
					
					/*Set Details*/
					$('#confirm_delete_product_name').text(response.product_name);
					$('#confirm_delete_product_price').text(response.product_price);
					
					$('#ProductDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--Product Confirmed For Deletion-->
	  $('body').on('click','#deleteProductConfirmed',function(){
			
			event.preventDefault();

			let productID = document.getElementById("deleteProductConfirmed").value;
			
			  $.ajax({
				url: "/delete_product_confirmed",
				type:"POST",
				data:{
				  productID:productID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Product Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getProductList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
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


	 
	function loadPricingPerBranch(productID) {
		
		event.preventDefault();
		
			  $.ajax({
				url: "{{ route('ProductPricingPerBranch') }}",
				type:"POST",
				data:{
				  productID:productID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
						
								
				  console.log(response);
				  if(response!='') {
					  
					  document.getElementById("save-product-price-per-branch").value = productID;
					  
					    $("#branch_pricing_table_body_data tr").remove();
						$('<tr style="display: none;"><td>HIDDEN</td></tr>').appendTo('#branch_pricing_table_body_data');
					  
						var len = response.length;
						for(var i=0; i<len; i++){
							
							var id = response[i].branch_price_id;
							
							//var branch_id 			= response[i].branch_id;
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
				
				let productID 			= document.getElementById("save-product-price-per-branch").value;
				
				//var branch_id = [];
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
			
			//alert(branch_price_id);
			
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
					
					//loadReceivablesPayment(receivable_id);
					
					//var table = $("#getReceivablesList").DataTable();
				    //table.ajax.reload(null, false);
		
					/*Close Modal*/
					//$('#CreateSalesOrderModal').modal('toggle');
					
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

	function loadProductInfo(productID) {
		
		event.preventDefault();
		
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
				  }
					
					
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
	  } 	  
  </script>
	