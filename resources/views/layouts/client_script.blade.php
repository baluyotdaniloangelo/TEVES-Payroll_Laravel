   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
<script type="text/javascript">
<!--Load Table-->
	$(function () {

		var ClientListTable = $('#getclientList').DataTable({
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
			ajax: "{{ route('getClientList') }}",
			responsive: true,
			scrollCollapse: true,
			scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'client_name', className: "text-left"},   
					{data: 'client_address', className: "text-left"},
					{data: 'client_tin', className: "text-left"},
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
				'<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateClientModal"></button>'+
				'</div>').appendTo('#client_option');
		
		autoAdjustColumns(ClientListTable);

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
	
	<!--Save New Client->
	$("#save-client").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#client_nameError').text('');
					$('#client_addressError').text('');
					$('#client_tinError').text('');

			document.getElementById('ClientformNew').className = "g-3 needs-validation was-validated";

			let client_name 		= $("input[name=client_name]").val();
			let client_address 		= $("input[name=client_address]").val();
			let client_tin 			= $("input[name=client_tin]").val();
			
			let default_less_percentage 			= $("input[name=default_less_percentage]").val();
			let default_net_percentage 				= $("input[name=default_net_percentage]").val();
			let default_vat_percentage 				= $("input[name=default_vat_percentage]").val();
			let default_withholding_tax_percentage 	= $("input[name=default_withholding_tax_percentage]").val();
			let default_payment_terms 				= $("input[name=default_payment_terms]").val();
			
			  $.ajax({
				url: "/create_client_post",
				type:"POST",
				data:{
				  client_name:client_name,
				  client_address:client_address,
				  client_tin:client_tin,
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
					
					$('#client_nameError').text('');
					$('#client_addressError').text('');
					$('#client_tinError').text('');
					
					document.getElementById("ClientformNew").reset();
					
					document.getElementById('ClientformNew').className = "g-3 needs-validation";
					
					/*Refresh Table*/
					var table = $("#getclientList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				 if(error.responseJSON.errors.client_name=="The client name has already been taken."){
							  
				  $('#client_nameError').html("<b>"+ client_name +"</b> has already been taken.");
				  document.getElementById('client_nameError').className = "invalid-feedback";
				  document.getElementById('client_name').className = "form-control is-invalid";
				  $('#client_name').val("");
				  
				}else{
				  $('#client_nameError').text(error.responseJSON.errors.client_name);
				  document.getElementById('client_nameError').className = "invalid-feedback";
				}
				
				  $('#client_addressError').text(error.responseJSON.errors.client_address);
				  document.getElementById('client_addressError').className = "invalid-feedback";	
				  
				  $('#client_tinError').text(error.responseJSON.errors.client_tin);
				  document.getElementById('client_tinError').className = "invalid-feedback";			

				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);
				  
				}
			   });		
	  });

	<!--Select Client For Update-->
	$('body').on('click','#editclient',function(){
			
			event.preventDefault();
			let clientID = $(this).data('id');
			
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
					
					document.getElementById("update-client").value = clientID;
					
					/*Set Details*/
					document.getElementById("update_client_name").value = response.client_name;
					document.getElementById("update_client_address").value = response.client_address;
					document.getElementById("update_client_tin").value = response.client_tin;
					
					document.getElementById("update_default_less_percentage").value = response.default_less_percentage;
					document.getElementById("update_default_net_percentage").value = response.default_net_percentage;
					document.getElementById("update_default_vat_percentage").value = response.default_vat_percentage;
					document.getElementById("update_default_withholding_tax_percentage").value = response.default_withholding_tax_percentage;
					document.getElementById("update_default_payment_terms").value = response.default_payment_terms;
										
					$('#UpdateClientModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	$("#update-client").click(function(event){			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#update_client_nameError').text('');
					$('#update_client_addressError').text('');
					$('#update_client_tinError').text('');

			document.getElementById('ClientformEdit').className = "g-3 needs-validation was-validated";
			
			let clientID 			= document.getElementById("update-client").value;
			let client_name 		= $("input[name=update_client_name]").val();
			let client_address 		= $("input[name=update_client_address]").val();
			let client_tin 			= $("input[name=update_client_tin]").val();
			
			let default_less_percentage 			= $("input[name=update_default_less_percentage]").val();
			let default_net_percentage 				= $("input[name=update_default_net_percentage]").val();
			let default_vat_percentage 				= $("input[name=update_default_vat_percentage]").val();
			let default_withholding_tax_percentage 	= $("input[name=update_default_withholding_tax_percentage]").val();
			let default_payment_terms 				= $("input[name=update_default_payment_terms]").val();
			
			  $.ajax({
				url: "/update_client_post",
				type:"POST",
				data:{
				  clientID:clientID,
				  client_name:client_name,
				  client_address:client_address,
				  client_tin:client_tin,
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
					
					$('#update_client_nameError').text('');	
					$('#update_client_addressError').text('');
					$('#update_client_tinError').text('');
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					/*Close form*/
					$('#UpdateClientModal').modal('toggle');
					
					document.getElementById('ClientformEdit').className = "g-3 needs-validation";
					
					/*Refresh Table*/
					var table = $("#getclientList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.client_name=="The client name has already been taken."){
							  
					$('#client_nameError').html("<b>"+ client_name +"</b> has already been taken.");
					document.getElementById('client_nameError').className = "invalid-feedback";
					document.getElementById('client_name').className = "form-control is-invalid";
					$('#update_client_name').val("");
				  
				}else{
					$('#client_nameError').text(error.responseJSON.errors.client_name);
					document.getElementById('client_nameError').className = "invalid-feedback";
				}
				
					$('#client_addressError').text(error.responseJSON.errors.client_address);
					document.getElementById('client_addressError').className = "invalid-feedback";			
					
					$('#client_tinError').text(error.responseJSON.errors.client_tin);
					document.getElementById('client_tinError').className = "invalid-feedback";		
				
				$('#switch_notice_off').show();
				$('#sw_off').html("Invalid Input");
				setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);		  	  
				  
				}
			   });
		
	  });
	  
	<!--client Deletion Confirmation-->
	$('body').on('click','#deleteclient',function(){
			
			event.preventDefault();
			let clientID = $(this).data('id');
			
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
					
					document.getElementById("deleteClientConfirmed").value = clientID;
					
					/*Set Details*/
					$('#confirm_delete_client_name').text(response.client_name);
					$('#confirm_delete_client_address').text(response.client_address);	
					$('#confirm_delete_client_tin').text(response.client_tin);	
					
					$('#ClientDeleteModal').modal('toggle');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	  <!--client Confirmed For Deletion-->
	  $('body').on('click','#deleteClientConfirmed',function(){
			
			event.preventDefault();

			let clientID = document.getElementById("deleteClientConfirmed").value;
			
			  $.ajax({
				url: "/delete_client_confirmed",
				type:"POST",
				data:{
				  clientID:clientID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#switch_notice_off').show();
					$('#sw_off').html("Client Deleted");
					setTimeout(function() { $('#switch_notice_off').fadeOut('slow'); },1000);	
					/*
					If you are using server side datatable, then you can use ajax.reload() 
					function to reload the datatable and pass the true or false as a parameter for refresh paging.
					*/
					
					var table = $("#getclientList").DataTable();
				    table.ajax.reload(null, false);
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });
</script>