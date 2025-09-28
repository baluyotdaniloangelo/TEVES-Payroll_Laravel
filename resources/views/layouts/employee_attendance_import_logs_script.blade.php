
    <script>
        $(document).ready(function () {

        let actionType = '';

        $('#submit_logs_view').on('click', function() {
            actionType = 'view';
        });
        $('#submit_logs_import').on('click', function() {
            actionType = 'import';
        });


            $('#csv-form').on('submit', function (e) {
                e.preventDefault();

                // Prepare form data
                var formData = new FormData(this);

                // Show feedback while uploading
                $('#feedback').html('Uploading... Please wait.');

                // Send the form data via Ajax to the backend
                $.ajax({
                    url: actionType === 'view' ? '{{ route("csv.view") }}' : '{{ route("csv.import") }}',// Define the route for import
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#feedback').html('CSV imported successfully!');
                        //populateTable(response.data); // Populate the table with response data

                        console.log(response.data.data);
                        if(response.data.data!='') {
                             LoadToImportData.clear().draw();
						     LoadToImportData.rows.add(response.data.data).draw();

                             var len = response.data.length;
						var total_current_consumption = 0;
						
						 for(var i=0; i<len; i++){
							
							 var total_regular_hours_f = response.data.data[i].total_regular_hours;
							 //var start_reading = Number(response.data[i].start_reading * meter_multiplier).toFixed(3);
							// var ending_reading = Number(response.data[i].ending_reading * meter_multiplier).toFixed(3);
							
							 var _current_consumption = (total_regular_hours_f);
							 current_consumption = _current_consumption.toFixed(3);
							
							 total_current_consumption += _current_consumption;
							
						 }			
									
							$('#total_current_consumption_top').text(total_current_consumption.toLocaleString("en-PH", {maximumFractionDigits: 3}));
						
                        }
                       // console.log(JSON.stringify(response.data, null, 2));

                        //alert(response.data);

                       

                    },
                    error: function (xhr, status, error) {
                        $('#feedback').html('An error occurred while uploading the file.');
                    }
                });
            });

		/*Load to Datatables*/	
		let LoadToImportData = $('#csv-table').DataTable( {
				"language": {
						"emptyTable": "No Result Found",
						"infoEmpty": "No entries to show"
			    }, 
				// processing: true,
				//serverSide: true,
				//stateSave: true,/*Remember Searches*/
				responsive: false,
				paging: true,
				searching: true,
				info: true,
				data: [],
				scrollCollapse: true,
				scrollY: '500px',
				"columns": [
				/*0*/	{data: 'DT_RowIndex', name: 'DT_RowIndex' , orderable: false, searchable: false, className: "text-right",},  
				/*1*/	{data: 'employee_no', className: "text-left", orderable: false},
				/*2*/	{data: 'employee_full_name', className: "text-center", orderable: false},
				/*3*/	{data: 'log_date', className: "text-center", orderable: false },
				/*4*/	{data: 'log_type', className: "text-left", orderable: false},	
				/*4*/	{data: 'time_in', className: "text-left", orderable: false},	
				/*4*/	{data: 'breaktime_start', className: "text-left", orderable: false},	
				/*4*/	{data: 'breaktime_end', className: "text-left", orderable: false},	
				/*4*/	{data: 'time_out', className: "text-left", orderable: false},	
				/*4*/	{data: 'time_in_ot', className: "text-left", orderable: false},	
				/*4*/	{data: 'time_out_ot', className: "text-left", orderable: false},	
				/*8*/	{data: 'regular_hours', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*8*/	{data: 'regular_overtime_hours', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },	
				/*10*/	{data: 'restday_hours', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'restday_overtime_hours', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'night_diff_hours', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'regular_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'regular_overtime_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'restday_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'restday_overtime_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'night_diff_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'regular_holiday_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false },
				/*10*/	{data: 'special_non_working_holiday_pay', className: "text-right", render: $.fn.dataTable.render.number( ',', '.', 1, '' ), orderable: false }
				]
		} );	
		
		autoAdjustColumns(LoadToImportData);

		 /*Adjust Table Column*/
		 function autoAdjustColumns(table) {
			 var container = table.table().container();
			 var resizeObserver = new ResizeObserver(function () {
				 table.columns.adjust();
			 });
			 resizeObserver.observe(container);
		 }	



            // Function to populate the table with data from the CSV
            function populateTable(data) {
                // Clear existing table data
                $('#csv-table tbody').empty();
                
                // Loop through the data and add rows to the table
                data.forEach(function (row) {
                    $('#csv-table tbody').append('<tr>' +
                        '<td>' + row.item_no + '</td>' +
                        '<td>' + row.employee_no + '</td>' +
                        '<td>' + row.employee_full_name + '</td>' +
                        '<td>' + row.log_date + '</td>' +
                        '<td>' + row.log_type + '</td>' +
                        '<td>' + row.time_in + '</td>' +
                        '<td>' + row.breaktime_start + '</td>' +
                        '<td>' + row.breaktime_end + '</td>' +
                        '<td>' + row.time_out + '</td>' +
                        '<td>' + row.time_in_ot + '</td>' +
                        '<td>' + row.time_out_ot + '</td>' +
                        '<td>' + row.total_regular_hours + '</td>' +
                        '<td>' + row.ot_hours + '</td>' +
                    '</tr>');
                });

                // Show the table after it has been populated
                $('#csv-table').show();
            }
        });
    </script>
