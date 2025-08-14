
    <script>
        $(document).ready(function () {

            $('#csv-form').on('submit', function (e) {
                e.preventDefault();

                // Prepare form data
                var formData = new FormData(this);

                // Show feedback while uploading
                $('#feedback').html('Uploading... Please wait.');

                // Send the form data via Ajax to the backend
                $.ajax({
                    url: '{{ route("csv.import") }}', // Define the route for import
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#feedback').html('CSV imported successfully!');
                        populateTable(response.data); // Populate the table with response data
                    },
                    error: function (xhr, status, error) {
                        $('#feedback').html('An error occurred while uploading the file.');
                    }
                });
            });

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
