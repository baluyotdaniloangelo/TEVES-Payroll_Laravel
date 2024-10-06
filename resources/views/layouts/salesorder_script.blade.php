   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   <script type="text/javascript">

	<!--Load Table-->
	$(function () {

		var SalesOrderListTable = $('#getSalesOrderList').DataTable({
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
			responsive: true,
			scrollCollapse: true,
			scrollY: '500px',
			ajax: "{{ route('getSalesOrderList') }}",
			columns: [
				{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
				{data: 'sales_order_date'},
				{data: 'sales_order_control_number'},
				{data: 'client_name'},   
				{data: 'sales_order_payment_term'},   
				{data: 'sales_order_gross_amount', render: $.fn.dataTable.render.number( ',', '.', 2, '' )},  
				{data: ({sales_order_net_amount,sales_order_withholding_tax}) => (Number(sales_order_net_amount)*Number(sales_order_withholding_tax/100)), render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },	
				{data: 'sales_order_net_amount', render: $.fn.dataTable.render.number( ',', '.', 2, '' )},
				{data: 'sales_order_total_due', render: $.fn.dataTable.render.number( ',', '.', 2, '' ) },		
				{data: 'sales_order_delivery_status'},  
				{data: 'sales_order_payment_status'},  
				//{data: 'sales_order_payment_status', name: 'sales_order_payment_status', orderable: true, searchable: true},
				//{data: 'sales_order_payment_status', name: 'sales_order_payment_status', orderable: true, searchable: true},	
				{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			order: [[ 1, "desc" ]],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1, 2, 9, 10] },
			]
		});
		
				/**/
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateSalesOrderModal"></button>'+
				'</div>').appendTo('#sales_order_option');
				
				autoAdjustColumns(SalesOrderListTable);

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
	  
	function ClientInfo() {
		
			let clientID = ($('#client_name option[value="' + $('#client_id').val() + '"]').attr('data-id'));
			
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
  
	<!--Save New Sales Order-->
	$("#save-sales-order").click(function(event){

			event.preventDefault();
			
					/*Reset Warnings*/
					$('#client_idxError').text('');

			document.getElementById('SalesOrderformNew').className = "g-3 needs-validation was-validated";
	
			let client_idx 					= $('#client_name option[value="' + $('#client_id').val() + '"]').attr('data-id');
			
			let company_header 				= $("#company_header").val();
			let sales_order_payment_type 	= $("#sales_order_payment_type").val();
			let sales_order_date 			= $("input[name=sales_order_date]").val();

			let payment_term 				= $("input[name=payment_term]").val();
			let sales_order_net_percentage 	= $("input[name=sales_order_net_percentage]").val();
			let sales_order_withholding_tax = $("input[name=sales_order_withholding_tax]").val();
			
			  $.ajax({
				url: "/create_sales_order_post",
				type:"POST",
				data:{
				  client_idx:client_idx,
				  company_header:company_header,
				  sales_order_payment_type:sales_order_payment_type,
				  sales_order_date:sales_order_date,
				  payment_term:payment_term,
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
					
					$('#sales_order_dateError').text('');		
					$('#client_idxError').text('');
					
					sales_order_id = response.sales_order_id;
					/*Close Modal*/
					$('#CreateSalesOrderModal').modal('toggle');
					/*Open PDF for Printing*/
					var query = {
						sales_order_id:response.sales_order_id,
						_token: "{{ csrf_token() }}"
					}

					/*Open Cashier's Report*/
					var url = "{{URL::to('sales_order_form')}}";
					window.location.href = url+'?sales_order_id='+sales_order_id+'&tab=product';
					
				  }
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("save-sales-order").disabled = true;
					/*Show Status*/
					$('#loading_data').show();
					
					
				},
				complete: function(){
						
					/*Enable Submit Button*/
					document.getElementById("save-sales-order").disabled = false;
					/*Hide Status*/
					$('#loading_data').hide();	
					
				},
				error: function(error) {
					
				 console.log(error);	
				 
				  $('#sales_order_dateError').text(error.responseJSON.errors.sales_order_date);
				  document.getElementById('sales_order_dateError').className = "invalid-feedback";
				 
				  $('#client_idxError').text(error.responseJSON.errors.client_idx);
				  document.getElementById('client_idxError').className = "invalid-feedback";
				  
				  $('#product_idxError').html(error.responseJSON.errors.product_idx);
								
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);			  	  
				  
				}
			   });	
	  });
	
	<!--Product Deletion Confirmation-->
	$('body').on('click','#deleteSalesOrder',function(){
			
			event.preventDefault();
			let sales_order_id = $(this).data('id');
			
			  $.ajax({
				url: "/sales_order_info",
				type:"POST",
				data:{
				  sales_order_id:sales_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteSalesOrderConfirmed").value = sales_order_id;
											
					/*Set Details*/
					$('#confirm_delete_sales_order_date').text(response[0].sales_order_date);
					$('#confirm_delete_sales_control_number').text(response[0].sales_order_control_number);
					$('#confirm_delete_client_name').text(response[0].client_name);
					$('#confirm_delete_dr_number').text(response[0].sales_order_dr_number);					
					$('#confirm_delete_or_number').text(response[0].sales_order_or_number);
					$('#confirm_delete_total_due').text(response[0].sales_order_total_due);
					
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
	$('body').on('click','#deleteSalesOrderConfirmed',function(){
			
			event.preventDefault();

			let sales_order_id = document.getElementById("deleteSalesOrderConfirmed").value;
			
			  $.ajax({
				url: "/delete_sales_order_confirmed",
				type:"POST",
				data:{
				  sales_order_id:sales_order_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Sales Order Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getSalesOrderList").DataTable();
				    table.ajax.reload(null, false);
					/*Reload Page*/
					//location.reload();
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
	  
	/*Re-print*/
	$('body').on('click','#PrintSalesOrder',function(){	  
	  
			let salesOrderID = $(this).data('id');
			var query = {
						sales_order_id:salesOrderID,
						_token: "{{ csrf_token() }}"
					}

			var url = "{{URL::to('generate_sales_order_pdf')}}?" + $.param(query)
			window.open(url);
	  
	});
	  
	function sales_update_status(id){
		  
			event.preventDefault();
			var sales_status = document.getElementById("sales_order_status_"+id).value;
		
			  $.ajax({
				url: "/update_sales_status",
				type:"POST",
				data:{
				  sales_order_id:id,
				  sales_status:sales_status,
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

	function LoadInformationForReceivable(id){
		  
			event.preventDefault();
		
			  $.ajax({
				url: "/sales_order_info",
				type:"POST",
				data:{
				  sales_order_id:id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
							
				  console.log(response);
				  if(response!='') {
					
					document.getElementById("client_name_receivables").innerHTML = response[0].client_name;
					document.getElementById("client_address_receivables").innerHTML = response[0].client_address;
					document.getElementById("client_tin_receivables").innerHTML = response[0].client_tin;
					document.getElementById("amount_receivables").innerHTML = response[0].sales_order_total_due;
					
					document.getElementById("receivable_billing_date").value = response[0].sales_order_date;
					//document.getElementById("receivable_or_number").value = response[0].sales_order_or_number;
					document.getElementById("receivable_payment_term").value = response[0].sales_order_payment_term;
					document.getElementById("receivable_description").textContent = response[0].sales_order_control_number;	
					
					document.getElementById("add-to-receivables").value = id;
					
				  }else{
							/*No Result Found or Error*/	
				  }
				},
				error: function(error) {
				 console.log(error);	 
				}
			   });
		  
	}	
	
	<!--Save New receivables->
	$("#add-to-receivables").click(function(event){
			
			event.preventDefault();
			$('#receivable_descriptionError').text('');

			document.getElementById('ReceivableformAddFromSalesOrder').className = "g-3 needs-validation was-validated";
			
			let SalesOrderID 			= document.getElementById("add-to-receivables").value;

			let billing_date			= $("input[name=receivable_billing_date]").val();	
			//let or_number 				= $("input[name=receivable_or_number]").val();	
			//let ar_reference 				= $("input[name=ar_reference]").val();	
			let payment_term 			= $("input[name=receivable_payment_term]").val();
			let receivable_description 	= $("#receivable_description").val();
			
			$.ajax({
				url: "/create_receivables_from_sale_order_post",
				type:"POST",
				data:{
				  sales_order_idx:SalesOrderID,
				  //or_number:or_number,
				  //ar_reference:ar_reference,
				  billing_date:billing_date,
				  payment_term:payment_term,
				  receivable_description:receivable_description,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					/*Reset Warnings*/
					//$('#receivable_or_numberError').text('');
					$('#receivable_payment_termError').text('');
					$('#receivable_descriptionError').text('');
					
					/*Clear Form*/
					//$('#receivable_or_number').val("");
					$('#receivable_payment_term').val("");
					$('#receivable_description').val("");
					/*Close Form*/
					$('#SalesOrderDeliveredModal').modal('toggle');
					
					var table = $("#getSalesOrderList").DataTable();
					table.ajax.reload(null, false);
					
					var query = {
						receivable_id:response.receivable_id,
						_token: "{{ csrf_token() }}"
					}
					
					/*Reload Details or link for PDF*/
					
					var url = "{{URL::to('generate_receivable_pdf')}}?" + $.param(query)
					window.open(url);
					
				  }
				},
				beforeSend:function()
				{
					
					/*Disable Submit Button*/
					document.getElementById("add-to-receivables").disabled = true;
					/*Show Status*/
					$('#loading_data_save_receivables').show();
				
				},
				complete: function(){
					
					/*Enable Submit Button*/
					document.getElementById("add-to-receivables").disabled = false;
					/*Hide Status*/
					$('#loading_data_save_receivables').hide();
					
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
	    
	
	function sales_order_status(id){
		  
			event.preventDefault();
			var sales_order_status = document.getElementById("sales_order_status_"+id).value;
			
			if(sales_order_status == 'Delivered'){
				/*Load Information*/
				LoadInformationForReceivable(id);	
				/*Open Form*/
				$('#SalesOrderDeliveredModal').modal('toggle');
			}else{
				
			  $.ajax({
				url: "/update_sales_status",
				type:"POST",
				data:{
				  sales_order_id:id,
				  sales_order_status:sales_order_status,
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
			
	  }	    
	  
 </script>