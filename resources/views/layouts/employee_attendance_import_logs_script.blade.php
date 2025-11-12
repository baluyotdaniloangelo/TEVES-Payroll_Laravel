
    <script>
$(document).ready(function () {
    let actionType = '';

    $('#submit_logs_view').on('click', function() {
        actionType = 'view';
    });

    $('#submit_logs_import').on('click', function(e) {
        e.preventDefault();
        actionType = 'import';

        // Show confirmation only after table data is loaded (below in Ajax success)
        if (LoadToImportData.data().count() === 0) {
            alert('Please load and review the data before importing.');
        } else {
            if (confirm('Are you sure you want to import all loaded logs?')) {
                $('#csv-form').submit();
            } else {
                alert('Import cancelled.');
            }
        }
    });

$('#csv-form').on('submit', function(e) {
    e.preventDefault();

    const spinner = $('#loading-spinner');
    const feedback = $('#feedback');
    const formData = new FormData(this);

    spinner.show();
    feedback.text('Uploading and processing... please wait.');

    $.ajax({
        url: actionType === 'view' ? '{{ route("csv.view") }}' : '{{ route("csv.import") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            spinner.hide();

            if (response.data && response.data.data) {
                LoadToImportData.clear().draw();
                LoadToImportData.rows.add(response.data.data).draw();
                feedback.text('Data loaded successfully!').css('color', 'green');

                if (actionType === 'import') {
                    alert('Import completed successfully!');
                }
            } else {
                feedback.text('No data found in CSV.').css('color', 'red');
            }
        },
        error: function() {
            spinner.hide();
            feedback.text('An error occurred while uploading the file.').css('color', 'red');
        }
    });
});


    /* Initialize DataTable */
    let LoadToImportData = $('#csv-table').DataTable({
        language: {
            emptyTable: "No Result Found",
            infoEmpty: "No entries to show"
        },
        responsive: true,
        paging: true,
        searching: true,
        info: true,
        data: [],
        scrollCollapse: true,
        scrollY: '500px',
        columns: [
            {data: 'DT_RowIndex', className: "text-right", orderable: false},
            {data: 'employee_no', className: "text-left", orderable: false},
            {data: 'employee_full_name', className: "text-center", orderable: false},
            {data: 'log_date', className: "text-center", orderable: false},
            {data: 'log_type', className: "text-left", orderable: false},
            {data: 'time_in', className: "text-left", orderable: false},
            {data: 'breaktime_start', className: "text-left", orderable: false},
            {data: 'breaktime_end', className: "text-left", orderable: false},
            {data: 'time_out', className: "text-left", orderable: false},
            {data: 'time_in_ot', className: "text-left", orderable: false},
            {data: 'time_out_ot', className: "text-left", orderable: false},
            {data: 'regular_hours', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'regular_overtime_hours', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'restday_hours', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'restday_overtime_hours', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'night_diff_hours', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'regular_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'regular_overtime_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'restday_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'restday_overtime_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'night_diff_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'regular_holiday_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false},
            {data: 'special_non_working_holiday_pay', className: "text-right", render: $.fn.dataTable.render.number(',', '.', 1, ''), orderable: false}
        ]
    });


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
