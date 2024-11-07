   <!-- Page level plugins -->
   <script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
   <script src="{{asset('Datatables/responsive/3.0.2/js/responsive.dataTables.js')}}"></script>
   <script type="text/javascript">
   // $(window).resize(function(){location.reload();});
	<!--Load Table-->				
	$(function () {
				
		var switchTable = $('#companyList').DataTable({
			processing: true,
			responsive: true,
			serverSide: true,
			stateSave: true,/*Remember Searches*/
			scrollCollapse: true,
			scrollY: '500px',
			//scrollX: '100%',
			ajax: {
				url : "{{ route('CompanyList') }}",
				method : 'POST',
				data: { _token: "{{ csrf_token() }}" },
			},
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},    
					{data: 'company_name'},  		
					{data: 'created_at_dt_format', name: 'switch_status', orderable: true, searchable: false},
					{data: 'updated_at_dt_format', name: 'switch_status', orderable: true, searchable: false},
					{data: 'action', name: 'action', orderable: false, searchable: false},
			],
			columnDefs: [
					 { className: 'text-center', targets: [0, 2, 3, 4] },
			],
			
		});

		  /*Add Options*/
		  $('<div class="btn-group" role="group" aria-label="Basic outlined example"style="margin-top: -50px; position: absolute;">'+
		  '<button type="button" class="btn btn-success new_item bi bi-plus-circle" data-bs-toggle="modal" data-bs-target="#CreateCompanyModal" onclick="ResetFormCompany();"> Company</button>'+
		  '</div>').appendTo('#company_option');

	autoAdjustColumns(switchTable);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	

	});
	
	
	<!--Save New Site-->
	$("#save-company").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/					  
					$('#company_nameError').text('');

			document.getElementById('CreateCompanyform').className = "g-3 needs-validation was-validated";
			
			let company_name 			= $("input[name=company_name]").val();
			
			  $.ajax({
				url: "/create_company_post",
				type:"POST",
				data:{
				  company_name:company_name,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				 
				  if(response) {
					  
					$('#company_nameError').text('');
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
					
					ResetFormCompany();
					
					var table = $("#companyList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				
				if(error.responseJSON.errors.company_name=="The company name has already been taken."){
							  
				  $('#company_nameError').html("<b>"+ company_name +"</b> has already been taken.");
				  document.getElementById('company_nameError').className = "invalid-feedback";
				  document.getElementById('company_name').className = "form-control is-invalid";
				  $('#company_name').val("");
				  
				}else{
					
				  $('#company_nameError').text(error.responseJSON.errors.company_name);
				  document.getElementById('company_nameError').className = "invalid-feedback";		
				
				}
				  
				  $('#InvalidModal').modal('toggle');				
				  
				}
			   });
		
	  });
	  
	function ResetFormCompany(){
			
			event.preventDefault();
			$('#CreateCompanyform')[0].reset();
			
			$('#company_nameError').text('');
			document.getElementById('company_nameError').className = "valid-feedback";		
			
			document.getElementById('company_name').className = "form-control";

			document.getElementById('CreateCompanyform').className = "g-3 needs-validation";
			
			
	}
	
	<!--Select Site For Update-->
	$('body').on('click','#editCompany',function(){
			
			event.preventDefault();
			let CompanyID = $(this).data('id');
			
			  $.ajax({
				url: "/company_info",
				type:"POST",
				data:{
				  CompanyID:CompanyID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("update-company").value = CompanyID;
					document.getElementById("update-company").disabled = true;
					
					/*Set Switch Details*/
					document.getElementById("update_company_name").value = response.company_name;
					$('#UpdateCompanyModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });
	  
	  
	document.getElementById("update_company_name").addEventListener('change', doThing_company_management);
	
	function doThing_company_management(){

			let CompanyID = document.getElementById("update-company").value;
			
			let company_name 			= $("input[name=update_company_name]").val();
		
		$.ajax({
				url: "/company_info",
				type:"POST",
				data:{
				  CompanyID:CompanyID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {				

					  if(response.company_name===company_name){
							
							document.getElementById("update-company").disabled = true;
							
						}else{
							
							document.getElementById("update-company").disabled = false;
							
						}
					  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		 
	   
    }
	
	$("#update-company").click(function(event){
			
			event.preventDefault();
			
					/*Reset Warnings*/
			let CompanyID = document.getElementById("update-company").value;	
			
			document.getElementById('UpdateCompanyform').className = "g-2 needs-validation was-validated";

			let company_name 			= $("input[name=update_company_name]").val();
			
			$.ajax({
				url: "/update_company_post",
				type:"POST",
				data:{
				  CompanyID:CompanyID,
				  company_name:company_name,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
					$('#update_company_nameError').text('');	
					
					$('#switch_notice_on').show();
					$('#sw_on').html(response.success);
					setTimeout(function() { $('#switch_notice_on').fadeOut('fast'); },1000);
					
					$('.success_modal_bg').html(response.success);
					$('#SuccessModal').modal('toggle');	
					
					$('#UpdateUserModal').modal('toggle');
					
					var table = $("#companyList").DataTable();
				    table.ajax.reload(null, false);
				  
				  }
				},
				error: function(error) {
				 console.log(error);	
				 
				if(error.responseJSON.errors.company_name=="The company name has already been taken."){
							  
				  $('#update_company_nameError').html("<b>"+ company_name +"</b> has already been taken.");
				  document.getElementById('update_company_nameError').className = "invalid-feedback";
				  document.getElementById('update_company_name').className = "form-control is-invalid";
				  $('#update_company_name').val("");
				  
				}else{
					
				  $('#update_company_nameError').text(error.responseJSON.errors.company_name);
				  document.getElementById('update_company_nameError').className = "invalid-feedback";		
				
				}
				
				$('#InvalidModal').modal('toggle');
				  
				}
			   });
	  });
	  
	<!--Switch Deletion Confirmation-->
	$('body').on('click','#deleteCompany',function(){
			
			event.preventDefault();
			let CompanyID = $(this).data('id');
			
			  $.ajax({
				url: "/company_info",
				type:"POST",
				data:{
				  CompanyID:CompanyID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteCompanyConfirmed").value = CompanyID;
					
					$('#company_name_info_confirm').html(response.company_name);
					
					
					$('#CompanyDeleteModal').modal('toggle');					
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
	  });

	<!--User Confirmed For Deletion-->
	$('body').on('click','#deleteCompanyConfirmed',function(){
			
			event.preventDefault();

			let CompanyID = document.getElementById("deleteCompanyConfirmed").value;
			
				$.ajax({
				url: "/delete_company_confirmed",
				type:"POST",
				data:{
				  CompanyID:CompanyID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  
				  if(response) {
					  
					$('.success_modal_bg').html("Company Deleted!");
					$('#SuccessModal').modal('toggle');	

					var table = $("#companyList").DataTable();
				    table.ajax.reload(null, false);
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });	
		
	  });

</script>