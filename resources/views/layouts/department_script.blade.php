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
		

	


	
</script>