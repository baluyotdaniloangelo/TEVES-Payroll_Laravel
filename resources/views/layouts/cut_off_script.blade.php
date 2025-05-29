<!-- Page level plugins -->
<script src="{{asset('Datatables/2.0.8/js/dataTables.js')}}"></script>
<script src="{{asset('Datatables/responsive/3.0.2/js/dataTables.responsive.js')}}"></script>
<script type="text/javascript">
	<!--Load Table for Branches-->
	$(function () {

		var HolidayListTable = $('#getCutOffList').DataTable({
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
			ajax: "{{ route('getCutOffList') }}",
			responsive: true,
			//scrollCollapse: true,
			//scrollY: '500px',
			columns: [
					{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false},
					{data: 'branch_code'},   
					{data: 'cut_off_period_start'},
					{data: 'cut_off_period_end'},
					{data: 'cut_off_gross_salary'},
					{data: 'cut_off_net_salary'},
					{data: 'prepared_by_name'},
					{data: 'reviewed_by_name'},
					{data: 'approved_by_name'},
					{data: 'action', name: 'action', orderable: false, searchable: false, className: "text-center"},
			],
			columnDefs: [
					{ className: 'text-center', targets: [0, 1] },
			]
		});
	
		autoAdjustColumns(HolidayListTable);

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

	function print_payroll(cutoff_idx){
	  
		var query = {
			cutoff_idx:cutoff_idx,
			_token: "{{ csrf_token() }}"
		}

		var url = "{{URL::to('generate_employees_saved_payroll_pdf')}}?" + $.param(query)
		window.open(url);
	  
	}
</script>
