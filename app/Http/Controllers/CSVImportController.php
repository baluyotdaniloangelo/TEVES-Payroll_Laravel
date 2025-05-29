<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class CSVImportController extends Controller
{
    // Show the CSV upload form
    public function showImportForm()
    {
        return view('payroll.employee_logs_import');
    }

    // Handle CSV import
    public function importCSV(Request $request)
    {
        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid file format or size'], 400);
        }

        // Get the uploaded file
        $file = $request->file('csv_file');

        // Parse the CSV file using the League CSV package
        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0); // First row is the header

            $data = [];
            foreach ($csv as $row) {
                $data[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'position' => $row['position'],
                    'department' => $row['department'],
                    'import_status' => "Success",
                ];
            }

            // Return the parsed data to populate the table
            return response()->json(['message' => 'CSV imported successfully', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error processing CSV file: ' . $e->getMessage()], 500);
        }
    }
}
