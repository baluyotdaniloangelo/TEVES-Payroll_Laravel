<script type="text/javascript">
	<!--Load Table-->
  	function LoadDepartmentList(branchID){
		
		 $.ajax({
				url: "{{ route('getDepartmentList') }}",
				type:"POST",
				data:{
				  branchID:branchID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					  
						DepartmentListTable.clear().draw();
						DepartmentListTable.rows.add(response.data).draw();	
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });	
		
	}

		let DepartmentListTable = $('#departmentlisttable').DataTable({
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
			responsive: false,
			paging: true,
			searching: false,
			info: false,
			data: [],
			//scrollCollapse: true,
			//scrollY: '500px',
			//scrollx: false,
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'department_name'},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					//{ className: 'text-center', targets: [0, 3] },
			]
		});
	
		//autoAdjustColumns_department(DepartmentListTable);

		 /*Adjust Table Column
		 function autoAdjustColumns_department(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }		*/
				
		// $('a.toggle-vis').on('click', function (e) {
        // e.preventDefault();
 
        // // Get the column API object
        // var column = table.column($(this).attr('data-column'));
 
        // // Toggle the visibility
        // column.visible(!column.visible());
		
		// });			
		

	

	<!--Save New Department-->
	$("#submit_deparment_details").click(function(event){
			
			event.preventDefault();
			
			/*Reset Warnings*/					  
			$('#department_nameError').text('');

			document.getElementById('BranchDepartmentForm').className = "g-3 needs-validation was-validated";
			
			let branch_idx			= $('#submit_deparment_details').data('id');
			let department_id 		= document.getElementById("submit_deparment_details").value;
			let department_name 	= $("input[name=department_name]").val();
			
			  $.ajax({
				url: "{{ route('SubmitDepartment') }}",
				type:"POST",
				data:{
				  department_id:department_id,
				  department_name:department_name,
				  branch_idx:branch_idx,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				 
				  if(response) {
					  
					alert("Department Information successfully "+response.success);
					LoadDepartmentList(branch_idx)
				  
					/*Department Form Reset*/
					ResetBranchDepartmentForm();
					
				  }
				},
				error: function(error) {
				 console.log(error);	
				
					/*Branch Name Error Notification*/
					 if(error.responseJSON.errors.department_name=="validation.unique"){
								  
					  $('#department_nameError').html("The <b>"+ department_name +"</b> has already been taken.");
					  document.getElementById('department_nameError').className = "invalid-feedback";
					  document.getElementById('department_name').className = "form-control is-invalid";
					  $('#department_name').val("");
				  
					}else{
						
					  $('#department_nameError').text('Department Name Required');
					  document.getElementById('department_nameError').className = "invalid-feedback";
					  
					}
				  
				  $('#InvalidModal').modal('toggle');				
				  
				}
			   });
		
	  });

	<!--Select Department For Update-->
	$('body').on('click','#edit_department',function(){
			
			event.preventDefault();
			let department_id = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DepartmentInfo') }}",
				type:"POST",
				data:{
				  department_id:department_id,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					$('#submit_deparment_details').html('Update');
					document.getElementById("submit_deparment_details").value = department_id;
					$('#submit_deparment_details').data('id',response.branch_idx);
					
					/*Set Department Details*/
					document.getElementById("department_name").value = response.department_name;	
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });

	<!--Department Deletion Confirmation-->
	$('body').on('click','#delete_department',function(){
			
			event.preventDefault();
			let departmentID = $(this).data('id');
			
			  $.ajax({
				url: "{{ route('DepartmentInfo') }}",
				type:"POST",
				data:{
				  department_id:departmentID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					document.getElementById("deleteDepartmentConfirmed").value = departmentID;
					
					/*Set Details*/
					$('#delete_department_complete_name').html(response.department_name);
				
					$('#DepartmentDeleteModal').modal('show');		
				  
				  }
				},
				error: function(error) {
				 console.log(error);
					alert(error);
				}
			   });		
	  });
	  
	  
	  <!--Department Confirmed For Deletion-->
	  $('body').on('click','#deleteDepartmentConfirmed',function(){
			
			event.preventDefault();

			let departmentID = document.getElementById("deleteDepartmentConfirmed").value;
			let branch_idx 	= document.getElementById("deleteDepartmentBranch_idx_Confirmed").value;
			
			$.ajax({
				url: "{{ route('DeleteDepartment') }}",
				type:"POST",
				data:{
				  department_id:departmentID,
				  _token: "{{ csrf_token() }}"
				},
				success:function(response){
				  console.log(response);
				  if(response) {
					
					
					
					//$('#modal_success_details').html("Department Information Succefully Deleted");
					alert("Department Information Succefully Deleted");
					LoadDepartmentList(branch_idx);
					/*Show Modal*/
					$('#branch_department_details_modal').modal('toggle');
					//$('#DepartmentDeleteModal').modal('hide');
					//$('#success-alert-modal').modal('show');
					
				  }
				},
				error: function(error) {
				 console.log(error);
				}
			   });		
	  });	  
	  
  	function ResetBranchDepartmentForm(){
			
			event.preventDefault();
			
					/*Reset Warnings*/
					$('#department_nameError').text('');
					
					document.getElementById('department_nameError').className = "";
					document.getElementById('department_name').className = "form-control";
					
					$('#BranchDepartmentForm')[0].reset();
					document.getElementById('BranchDepartmentForm').className = "g-3 needs-validation";
					
					$('#submit_deparment_details').html('Add');
					
					document.getElementById("submit_deparment_details").value = 0;
				
	}		
</script>