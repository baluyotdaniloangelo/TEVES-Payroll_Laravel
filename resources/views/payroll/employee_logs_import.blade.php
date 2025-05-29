<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live CSV Import with Ajax</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Import CSV File and Display Data in Table</h1>

    <!-- Form for uploading CSV -->
    <form id="csv-form" enctype="multipart/form-data">
        @csrf
        <label for="csv_file">Choose a CSV file:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <button type="submit">Import CSV</button>
    </form>

    <!-- Feedback and loading status -->
    <div id="feedback"></div>

    <!-- Live Table to Display Imported Data -->
    <table id="csv-table" border="1" style="width:100%; margin-top: 20px; display:none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Department</th>
                <th>Import Status</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

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
                        '<td>' + row.id + '</td>' +
                        '<td>' + row.name + '</td>' +
                        '<td>' + row.email + '</td>' +
                        '<td>' + row.position + '</td>' +
                        '<td>' + row.department + '</td>' +
                        '<td>' + row.import_status + '</td>' +
                    '</tr>');
                });

                // Show the table after it has been populated
                $('#csv-table').show();
            }
        });
    </script>
</body>
</html>
