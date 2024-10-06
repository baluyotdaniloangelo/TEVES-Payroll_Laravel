   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   <script type="text/javascript">

	<!--Load Table-->
	$(function () {

		var SupplierListTable = $('#getsupplierList').DataTable({
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
			ajax: "{{ route('getSupplierList') }}",
			responsive: true,
			scrollCollapse: true,
			scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'supplier_name', className: "text-left"},   
					{data: 'supplier_address', className: "text-left"},
					{data: 'supplier_tin', className: "text-left"},
					{data: 'default_less_percentage', className: "text-left"},
					{data: 'default_net_percentage', className: "text-left"},
					{data: 'default_vat_percentage', className: "text-left"},
					{data: 'default_withholding_tax_percentage', className: "text-left"},
					{data: 'default_payment_terms', className: "text-left"},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0] },
			]
		});
				$('<div class="btn-group" role="group" aria-label="Basic outlined example" style="margin-top: -50px; position: absolute;">'+
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreatesupplierModal"></button>'+
				'</div>').appendTo('#supplier_option');
				
		autoAdjustColumns(SupplierListTable);

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
	<!--Save New supplier->
	$("#save-supplier").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#supplier_nameError').text('');
					$('#supplier_addressError').text('');
					$('#supplier_tinError').text('');

			document.getElementById('supplierformNew').className = "g-3 needs-validation was-validated";

			let supplier_name 		= $("input[name=supplier_name]").val();
			let supplier_address 		= $("input[name=supplier_address]").val();
			let supplier_tin 			= $("input[name=supplier_tin]").val();
			
			let default_less_percentage 			= $("input[name=default_less_percentage]").val();
			let default_net_percentage 				= $("input[name=default_net_percentage]").val();
			let default_vat_percentage 				= $("input[name=default_vat_percentage]").val();
			let default_withholding_tax_percentage 	= $("input[name=default_withholding_tax_percentage]").val();
			let default_payment_terms 				= $("input[name=default_payment_terms]").val();
			
			  $.ajax({
				url: "/create_supplier_post",
				type:"POST",
				data:{
				  supplier_name:supplier_name,
				  supplier_address:supplier_address,
				  supplier_tin:supplier_tin,
				  default_less_percentage:default_less_percentage,
				  default_net_percentage:default_net_percentage,
				  default_vat_percentage:default_vat_percentage,
				  default_withholding_tax_percentage:default_withholding_tax_percentage,
				  default_payment_terms:default_payment_terms,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('#supplier_nameError').text('');
					$('#supplier_addressError').text('');
					$('#supplier_tinError').text('');
					
					document.getElementById("supplierformNew").reset();
					
					document.getElementById('supplierformNew').className = "g-3 needs-validation";
					
					/*Refresh Table*/
					var table = $("#getsupplierList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.supplier_name=="The supplier name has already been taken."){
							  
				  $('#supplier_nameError').html("<b>"+ supplier_name +"</b> has already been taken.");
				  document.getElementById('supplier_nameError').className = "invalid-feedback";
				  document.getElementById('supplier_name').className = "form-control is-invalid";
				  $('#supplier_name').val("");
				  
				}else{
				  $('#supplier_nameError').text(error.responseJSON.errors.supplier_name);
				  document.getElementById('supplier_nameError').className = "invalid-feedback";
				}
				
				  $('#supplier_addressError').text(error.responseJSON.errors.supplier_address);
				  document.getElementById('supplier_addressError').className = "invalid-feedback";	
				  
				  $('#supplier_tinError').text(error.responseJSON.errors.supplier_tin);
				  document.getElementById('supplier_tinError').className = "invalid-feedback";			

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Select supplier For Update-->
	$('body').on('click','#editsupplier',function(){
			
			event.preventDefault();
			let supplierID = $(this).data('id');
			
			  $.ajax({
				url: "/supplier_info",
				type:"POST",
				data:{
				  supplierID:supplierID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-supplier").value = supplierID;
					
					/*Set Details*/
					document.getElementById("update_supplier_name").value = response.supplier_name;
					document.getElementById("update_supplier_address").value = response.supplier_address;
					document.getElementById("update_supplier_tin").value = response.supplier_tin;
					
					document.getElementById("update_default_less_percentage").value = response.default_less_percentage;
					document.getElementById("update_default_net_percentage").value = response.default_net_percentage;
					document.getElementById("update_default_vat_percentage").value = response.default_vat_percentage;
					document.getElementById("update_default_withholding_tax_percentage").value = response.default_withholding_tax_percentage;
					document.getElementById("update_default_payment_terms").value = response.default_payment_terms;
					
					$('#UpdatesupplierModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });


	$("#update-supplier").click(function(event){			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#update_supplier_nameError').text('');
					$('#update_supplier_addressError').text('');
					$('#update_supplier_tinError').text('');

			document.getElementById('supplierformEdit').className = "g-3 needs-validation was-validated";
			
			let supplierID 				= document.getElementById("update-supplier").value;
			let supplier_name 			= $("input[name=update_supplier_name]").val();
			let supplier_address 		= $("input[name=update_supplier_address]").val();
			let supplier_tin 			= $("input[name=update_supplier_tin]").val();
			
			let default_less_percentage 			= $("input[name=update_default_less_percentage]").val();
			let default_net_percentage 				= $("input[name=update_default_net_percentage]").val();
			let default_vat_percentage 				= $("input[name=update_default_vat_percentage]").val();
			let default_withholding_tax_percentage 	= $("input[name=update_default_withholding_tax_percentage]").val();
			let default_payment_terms 				= $("input[name=update_default_payment_terms]").val();
			
			  $.ajax({
				url: "/update_supplier_post",
				type:"POST",
				data:{
				  supplierID:supplierID,
				  supplier_name:supplier_name,
				  supplier_address:supplier_address,
				  supplier_tin:supplier_tin,
				  default_less_percentage:default_less_percentage,
				  default_net_percentage:default_net_percentage,
				  default_vat_percentage:default_vat_percentage,
				  default_withholding_tax_percentage:default_withholding_tax_percentage,
				  default_payment_terms:default_payment_terms,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#update_supplier_nameError').text('');	
					$('#update_supplier_addressError').text('');
					$('#update_supplier_tinError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					/*Close form*/
					$('#UpdatesupplierModal').modal('toggle');
					
					document.getElementById('supplierformEdit').className = "g-3 needs-validation";
					
					/*Refresh Table*/
					var table = $("#getsupplierList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.supplier_name=="The supplier name has already been taken."){
							  
					$('#supplier_nameError').html("<b>"+ supplier_name +"</b> has already been taken.");
					document.getElementById('supplier_nameError').className = "invalid-feedback";
					document.getElementById('supplier_name').className = "form-control is-invalid";
					$('#update_supplier_name').val("");
				  
				}else{
					$('#supplier_nameError').text(error.responseJSON.errors.supplier_name);
					document.getElementById('supplier_nameError').className = "invalid-feedback";
				}
				
					$('#supplier_addressError').text(error.responseJSON.errors.supplier_address);
					document.getElementById('supplier_addressError').className = "invalid-feedback";			
					
					$('#supplier_tinError').text(error.responseJSON.errors.supplier_tin);
					document.getElementById('supplier_tinError').className = "invalid-feedback";		
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);		  	  
				  
				}
			   });
		
	  });
	  
	<!--supplier Deletion Confirmation-->
	$('body').on('click','#deletesupplier',function(){
			
			event.preventDefault();
			let supplierID = $(this).data('id');
			
			  $.ajax({
				url: "/supplier_info",
				type:"POST",
				data:{
				  supplierID:supplierID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deletesupplierConfirmed").value = supplierID;
					
					/*Set Details*/
					$('#confirm_delete_supplier_name').text(response.supplier_name);
					$('#confirm_delete_supplier_address').text(response.supplier_address);	
					$('#confirm_delete_supplier_tin').text(response.supplier_tin);	
					
					$('#supplierDeleteModal').modal('toggle');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--supplier Confirmed For Deletion-->
	  $('body').on('click','#deletesupplierConfirmed',function(){
			
			event.preventDefault();

			let supplierID = document.getElementById("deletesupplierConfirmed").value;
			
			  $.ajax({
				url: "/delete_supplier_confirmed",
				type:"POST",
				data:{
				  supplierID:supplierID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("supplier Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getsupplierList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
  </script>